<?php

namespace App\Services\Whatsapp\Flows;

use App\Services\Whatsapp\WhatsappClient;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Shared pieces for the multi-step flows: long pick-lists split into pages, and saving files the user sends.
 */
trait FlowHelpers
{
    private const MAX_FILE_BYTES = 10 * 1024 * 1024;

    private const ALLOWED_MIMES = ['application/pdf' => 'pdf', 'image/jpeg' => 'jpg', 'image/png' => 'png'];

    /**
     * Send a pick-list. WhatsApp shows at most 10 rows, so longer lists are cut into pages with a "More" row.
     *
     * @param  array<int, array{id:string,title:string,description?:string}>  $items
     */
    protected function sendPagedList(WhatsappClient $wa, string $phone, string $body, string $button, array $items, int $page, string $pagePrefix): void
    {
        $perPage = 9;
        $pages = max(1, (int) ceil(count($items) / $perPage));
        $page = max(1, min($page, $pages));
        $rows = array_slice($items, ($page - 1) * $perPage, $perPage);

        if ($page < $pages) {
            $rows[] = ['id' => $pagePrefix.($page + 1), 'title' => 'More...', 'description' => 'Page '.($page + 1).' of '.$pages];
        }

        $wa->sendList($phone, $body.($pages > 1 ? " (page {$page} of {$pages})" : ''), $button, $rows);
    }

    /**
     * Download a file the user sent and save it on the configured disk (public on local, the bucket on live).
     * Returns the stored path, or an error message string prefixed with "ERROR:".
     */
    protected function storeMedia(WhatsappClient $wa, array $media, string $folder = 'documents'): string
    {
        $file = $wa->downloadMedia($media['id']);
        if (! $file) {
            return 'ERROR:We could not download that file from WhatsApp. Please send it again.';
        }

        $mime = strtolower(trim(explode(';', (string) ($file['mime'] ?: ($media['mime'] ?? '')))[0]));
        if (! isset(self::ALLOWED_MIMES[$mime])) {
            return 'ERROR:Only PDF, JPG or PNG files are accepted. Please send the file again as a PDF or a photo.';
        }
        if (strlen($file['bytes']) > self::MAX_FILE_BYTES) {
            return 'ERROR:That file is larger than 10 MB. Please send a smaller one.';
        }

        $path = $folder.'/'.Str::uuid().'.'.self::ALLOWED_MIMES[$mime];
        Storage::disk(config('filesystems.default'))->put($path, $file['bytes']);

        return $path;
    }

    protected function isError(string $result): bool
    {
        return str_starts_with($result, 'ERROR:');
    }

    protected function errorText(string $result): string
    {
        return substr($result, 6);
    }

    protected function money($amount): string
    {
        return number_format((float) $amount, 2);
    }
}

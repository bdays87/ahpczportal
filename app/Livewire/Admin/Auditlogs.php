<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Read-only view of the audit log: who did what, when, and what the record looked like before.
 */
class Auditlogs extends Component
{
    use WithPagination;

    public $breadcrumbs = [];

    public $search = '';

    public $action = '';

    public $table = '';

    public $username = '';

    public $from;

    public $to;

    public bool $modal = false;

    public $selected = null;

    public function mount()
    {
        abort_unless(auth()->user()?->can('datamanagement.access'), 403);

        $this->breadcrumbs = [
            ['label' => 'Dashboard', 'icon' => 'o-home', 'link' => route('dashboard')],
            ['label' => 'Audit log'],
        ];
    }

    public function updating($name)
    {
        if (in_array($name, ['search', 'action', 'table', 'username', 'from', 'to'], true)) {
            $this->resetPage();
        }
    }

    private function filtered()
    {
        return AuditLog::query()
            ->when($this->action, fn ($q) => $q->where('action', $this->action))
            ->when($this->table, fn ($q) => $q->where('table_name', $this->table))
            ->when(trim((string) $this->username) !== '', fn ($q) => $q->where(function ($w) {
                $w->where('username', 'like', '%'.trim($this->username).'%')->orWhere('user_name', 'like', '%'.trim($this->username).'%');
            }))
            ->when($this->from, fn ($q) => $q->where('created_at', '>=', $this->from.' 00:00:00'))
            ->when($this->to, fn ($q) => $q->where('created_at', '<=', $this->to.' 23:59:59'))
            ->when(trim((string) $this->search) !== '', fn ($q) => $q->where(function ($w) {
                $s = '%'.trim($this->search).'%';
                $w->where('record_id', 'like', $s)->orWhere('operation', 'like', $s)->orWhere('notes', 'like', $s)
                    ->orWhere('old_values', 'like', $s)->orWhere('new_values', 'like', $s);
            }))
            ->orderByDesc('id');
    }

    public function view($id)
    {
        $this->selected = AuditLog::find($id);
        $this->modal = (bool) $this->selected;
    }

    public function clear()
    {
        $this->reset(['search', 'action', 'table', 'username', 'from', 'to']);
        $this->resetPage();
    }

    /** Download what is on screen (current filters) as a spreadsheet-friendly CSV, newest first, up to 5,000 lines. */
    public function export(): StreamedResponse
    {
        $query = $this->filtered()->limit(5000);

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['When', 'Username', 'Name', 'Action', 'Table', 'Record', 'Operation', 'IP', 'Before', 'After', 'Notes']);
            foreach ($query->cursor() as $row) {
                fputcsv($out, [
                    $row->created_at?->format('Y-m-d H:i:s'), $row->username, $row->user_name, $row->action, $row->table_name, $row->record_id,
                    $row->operation, $row->ip,
                    $row->old_values ? json_encode($row->old_values, JSON_UNESCAPED_UNICODE) : '',
                    $row->new_values ? json_encode($row->new_values, JSON_UNESCAPED_UNICODE) : '',
                    $row->notes,
                ]);
            }
            fclose($out);
        }, 'audit-log-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv']);
    }

    public function render()
    {
        return view('livewire.admin.auditlogs', [
            'logs' => $this->filtered()->paginate(30),
            'tables' => AuditLog::query()->whereNotNull('table_name')->distinct()->orderBy('table_name')->pluck('table_name'),
        ]);
    }
}

<?php

namespace App;

enum RenewalType: string
{
    case RENEW = 'renew';
    case MAINTAIN = 'maintain';

    public function label(): string
    {
        return match($this) {
            self::RENEW => 'Renew',
            self::MAINTAIN => 'Maintain',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::RENEW => 'Select option to obtain a valid practising certificate',
            self::MAINTAIN => 'Select option to be included in the maintance register',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->map(fn($case) => [
                'id' => $case->value,
                'name' => $case->label(),
                'description' => $case->description()
            ])
            ->toArray();
    }
}

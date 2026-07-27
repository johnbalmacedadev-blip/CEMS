<?php

namespace App\Support;

class ShowroomBadge
{
    public static function badgeClass(?string $name): string
    {
        $normalized = strtolower(trim((string) $name));

        return match ($normalized) {
            'annex' => 'bg-warning text-dark',
            'flagship' => 'bg-info text-white',
            default => 'bg-secondary text-white',
        };
    }

    public static function isKnownShowroom(?string $name): bool
    {
        $normalized = strtolower(trim((string) $name));

        return in_array($normalized, ['annex', 'flagship'], true);
    }
}

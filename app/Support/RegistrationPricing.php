<?php

namespace App\Support;

use Illuminate\Support\Collection;

/**
 * The association's registration price list.
 *
 * Registration is priced by category rather than a flat fee, and only two of the
 * four categories may bring guests. Every part of the system — the public form,
 * the validator, the admin and the confirmation email — computes money through
 * here, so the rules cannot drift apart.
 */
class RegistrationPricing
{
    /** @return Collection<string, array> */
    public static function categories(): Collection
    {
        return collect(config('rcmaa.registration.categories'));
    }

    public static function keys(): array
    {
        return self::categories()->keys()->all();
    }

    public static function has(?string $key): bool
    {
        return $key !== null && self::categories()->has($key);
    }

    public static function get(?string $key): ?array
    {
        return self::categories()->get($key);
    }

    public static function label(?string $key): string
    {
        return self::get($key)['label'] ?? (string) $key;
    }

    public static function fee(?string $key): int
    {
        return (int) (self::get($key)['fee'] ?? 0);
    }

    public static function allowsGuests(?string $key): bool
    {
        return (bool) (self::get($key)['allows_guests'] ?? false);
    }

    public static function guestFee(): int
    {
        return (int) config('rcmaa.registration.guest_fee');
    }

    /**
     * What a registration in this category with this many guests should cost.
     * Guests are silently worth nothing for categories that cannot bring them —
     * the validator is what rejects those, not the pricing.
     */
    public static function total(?string $key, int $guests = 0): int
    {
        $guests = self::allowsGuests($key) ? max(0, $guests) : 0;

        return self::fee($key) + ($guests * self::guestFee());
    }
}

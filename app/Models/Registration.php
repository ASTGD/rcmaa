<?php

namespace App\Models;

use App\Support\RegistrationPricing;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Registration extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_VERIFIED = 'verified';

    public const STATUS_REJECTED = 'rejected';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'guests' => 'array',
            'cultural_program' => 'boolean',
            'listed_in_directory' => 'boolean',
            'verified_at' => 'datetime',
            'passing_year' => 'integer',
            'amount_paid' => 'integer',
            'amount_due' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $registration) {
            $registration->reference ??= self::generateReference();
        });
    }

    /**
     * Sessions are typed by hand ("e.g. 2008-09"), so the same batch arrives as
     * 2008-09, 2008-2009 and ২০০৮-০৯. The directory groups by this value, and
     * without a canonical form one batch would appear as three.
     */
    public static function normaliseSession(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        $latin = strtr(trim($value), [
            '০' => '0', '১' => '1', '২' => '2', '৩' => '3', '৪' => '4',
            '৫' => '5', '৬' => '6', '৭' => '7', '৮' => '8', '৯' => '9',
        ]);

        if (! preg_match('/(\d{4})\s*[-–—\/]?\s*(\d{2,4})?/u', $latin, $m)) {
            return $latin;
        }

        if (! isset($m[2]) || $m[2] === '') {
            return $m[1];
        }

        // 2008-2009 and 2008-09 both mean the same batch.
        return sprintf('%04d-%02d', (int) $m[1], ((int) $m[2]) % 100);
    }

    public function setSessionAttribute($value): void
    {
        $this->attributes['session'] = self::normaliseSession($value);
    }

    /** Human-quotable over the phone: RC26-4F9K2B. */
    public static function generateReference(): string
    {
        do {
            $reference = 'RC26-'.Str::upper(Str::random(6));
        } while (self::where('reference', $reference)->exists());

        return $reference;
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // --- Scopes -----------------------------------------------------------

    public function scopePending(Builder $query): Builder
    {
        return $query->where('payment_status', self::STATUS_PENDING);
    }

    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('payment_status', self::STATUS_VERIFIED);
    }

    /** Verified AND not opted out — what the public directory may show. */
    public function scopeListed(Builder $query): Builder
    {
        return $query->verified()->where('listed_in_directory', true);
    }

    public function scopeOfCategory(Builder $query, ?string $category): Builder
    {
        return $category ? $query->where('category', $category) : $query;
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('full_name_en', 'like', "%{$term}%")
                ->orWhere('full_name_bn', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('mobile', 'like', "%{$term}%")
                ->orWhere('reference', 'like', "%{$term}%")
                ->orWhere('transaction_id', 'like', "%{$term}%");
        });
    }

    // --- Accessors --------------------------------------------------------

    public function getGuestTotalAttribute(): int
    {
        return count($this->guests ?? []);
    }

    public function getCategoryLabelAttribute(): string
    {
        return RegistrationPricing::label($this->category) ?: '';
    }

    public function getCategoryLabelBnAttribute(): ?string
    {
        return RegistrationPricing::get($this->category)['label_bn'] ?? null;
    }

    public function getDegreeLabelAttribute(): string
    {
        return (string) config("rcmaa.options.degrees.{$this->degree}", $this->degree ?? '');
    }

    public function getEmploymentLabelAttribute(): string
    {
        return (string) config("rcmaa.options.employment_statuses.{$this->employment_status}", $this->employment_status ?? '');
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return (string) config("rcmaa.payment.methods.{$this->payment_method}.label", $this->payment_method ?? '');
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path ? Storage::disk('public')->url($this->photo_path) : null;
    }

    public function getPaymentReceiptUrlAttribute(): ?string
    {
        return $this->payment_receipt_path
            ? Storage::disk('public')->url($this->payment_receipt_path)
            : null;
    }

    /** A PDF bank slip cannot be shown inline the way a screenshot can. */
    public function getPaymentReceiptIsPdfAttribute(): bool
    {
        return str_ends_with(strtolower((string) $this->payment_receipt_path), '.pdf');
    }

    public function getIsVerifiedAttribute(): bool
    {
        return $this->payment_status === self::STATUS_VERIFIED;
    }

    /** Negative when they underpaid, positive when they overpaid. */
    public function getBalanceAttribute(): int
    {
        return $this->amount_paid - $this->amount_due;
    }
}

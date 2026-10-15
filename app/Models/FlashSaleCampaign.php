<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class FlashSaleCampaign extends Model
{
    protected $guarded = [];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'recurring_dates' => 'array',
        'is_active' => 'boolean',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(FlashSaleItem::class);
    }

    public function isActiveAt(?CarbonInterface $at = null): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $at ??= now();

        if ($this->schedule_type === 'range') {
            return $this->starts_at !== null
                && $this->ends_at !== null
                && $at->greaterThanOrEqualTo($this->starts_at)
                && $at->lessThan($this->ends_at);
        }

        $dateKey = $at->format('m/d');
        $matchesDate = collect($this->recurring_dates ?? [])->contains(
            static fn (mixed $date): bool => is_string($date) && $date === $dateKey
        );

        if (! $matchesDate) {
            return false;
        }

        $startsAt = $this->timeOnDate($at, $this->daily_starts_at ?: '00:00:00');
        $endsAt = $this->timeOnDate($at, $this->daily_ends_at ?: '23:59:59');

        if ($endsAt->lessThanOrEqualTo($startsAt)) {
            $endsAt->addDay();
        }

        return $at->greaterThanOrEqualTo($startsAt) && $at->lessThan($endsAt);
    }

    public function isOpenForApplicationsAt(?CarbonInterface $at = null): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $at ??= now();

        if ($this->schedule_type === 'range') {
            return $this->ends_at !== null && $at->lessThan($this->ends_at);
        }

        return ! empty($this->recurring_dates);
    }

    public function endsAtFor(?CarbonInterface $at = null): ?Carbon
    {
        $at ??= now();

        if ($this->schedule_type === 'range') {
            return $this->ends_at?->copy();
        }

        if (! $this->isActiveAt($at)) {
            return null;
        }

        $startsAt = $this->timeOnDate($at, $this->daily_starts_at ?: '00:00:00');
        $endsAt = $this->timeOnDate($at, $this->daily_ends_at ?: '23:59:59');

        if ($endsAt->lessThanOrEqualTo($startsAt)) {
            $endsAt->addDay();
        }

        return $endsAt;
    }

    /**
     * @return Collection<int, int>
     */
    public static function activeIdsAt(?CarbonInterface $at = null): Collection
    {
        $at ??= now();

        return static::query()
            ->where('is_active', true)
            ->get()
            ->filter(fn (self $campaign): bool => $campaign->isActiveAt($at))
            ->pluck('id')
            ->values();
    }

    private function timeOnDate(CarbonInterface $at, string $time): Carbon
    {
        return Carbon::parse($at->toDateString().' '.$time, $at->getTimezone());
    }
}

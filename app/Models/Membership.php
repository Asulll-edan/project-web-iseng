<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    const TIER_NONE     = 'none';
    const TIER_SILVER   = 'silver';
    const TIER_GOLD     = 'gold';
    const TIER_PLATINUM = 'platinum';

    const TIERS = [
        'none'     => ['label' => 'Non-Member', 'color' => 'gray',   'cashback' => 0,    'min_orders' => 0],
        'silver'   => ['label' => 'Silver',      'color' => 'blue',   'cashback' => 2,    'min_orders' => 10],
        'gold'     => ['label' => 'Gold',         'color' => 'amber',  'cashback' => 5,    'min_orders' => 30],
        'platinum' => ['label' => 'Platinum',     'color' => 'purple', 'cashback' => 10,   'min_orders' => 100],
    ];

    protected $fillable = [
        'user_id', 'tier', 'completed_orders', 'cashback_rate',
        'is_active', 'tier_achieved_at', 'platinum_expires_at',
    ];

    protected $casts = [
        'is_active'           => 'boolean',
        'tier_achieved_at'    => 'datetime',
        'platinum_expires_at' => 'datetime',
        'cashback_rate'       => 'float',
    ];

    public function user() { return $this->belongsTo(User::class); }

    public function getTierLabelAttribute(): string
    {
        return self::TIERS[$this->tier]['label'] ?? 'Non-Member';
    }

    public function getTierColorAttribute(): string
    {
        return self::TIERS[$this->tier]['color'] ?? 'gray';
    }

    public function getNextTierAttribute(): ?array
    {
        $tiers = array_keys(self::TIERS);
        $current = array_search($this->tier, $tiers);
        if ($current !== false && isset($tiers[$current + 1])) {
            return self::TIERS[$tiers[$current + 1]];
        }
        return null;
    }

    // Check eligibility based on completed orders
    public function checkAndUpgradeTier(): bool
    {
        $orders = $this->completed_orders;
        $newTier = self::TIER_NONE;

        if ($orders >= 10)  $newTier = self::TIER_SILVER;
        if ($orders >= 30)  $newTier = self::TIER_GOLD;
        // Platinum requires purchase, so skip automatic upgrade

        if ($newTier !== $this->tier && $newTier !== self::TIER_NONE) {
            $tierData = self::TIERS[$newTier];
            $this->update([
                'tier'             => $newTier,
                'cashback_rate'    => $tierData['cashback'],
                'tier_achieved_at' => now(),
            ]);
            return true;
        }
        return false;
    }
}
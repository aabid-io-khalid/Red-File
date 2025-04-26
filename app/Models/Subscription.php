<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'stripe_customer_id',
        'stripe_subscription_id',
        'status',
        'plan_name',
        'amount',
        'trial_ends_at',
        'current_period_starts_at',
        'current_period_ends_at',
        'canceled_at',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'current_period_starts_at' => 'datetime',
        'current_period_ends_at' => 'datetime',
        'canceled_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isActive()
    {
        return $this->status === 'active' || ($this->status === 'canceled' && $this->isValid());
    }

    public function isCanceled()
    {
        return !is_null($this->canceled_at);
    }

    public function isOnTrial()
    {
        return !is_null($this->trial_ends_at) && $this->trial_ends_at->isFuture();
    }

    public function isValid()
    {
        if (is_string($this->current_period_ends_at)) {
            $this->current_period_ends_at = Carbon::parse($this->current_period_ends_at);
        }
        return $this->current_period_ends_at && $this->current_period_ends_at->isFuture();
    }
}
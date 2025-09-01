<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookEvent extends Model
{
    protected $table = 'webhook_events';
    protected $primaryKey = 'id';

    protected $fillable = [
        'stripe_event_id',
        'event_type',
        'pedido_id',
        'payload',
        'status',
        'attempts',
        'last_attempt_at',
        'processed_at',
        'error_message'
    ];

    protected $casts = [
        'payload' => 'array',
        'last_attempt_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeProcessed($query)
    {
        return $query->where('status', 'processed');
    }
}

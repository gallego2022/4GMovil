<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookEvent extends Model
{
    protected $table = 'webhook_events';
    protected $primaryKey = 'webhook_id';

    protected $fillable = [
        'stripe_id',
        'type',
        'livemode',
        'data',
        'request_id',
        'processed',
        'processed_at',
        'status',
        'attempts',
        'last_attempt_at',
        'error_message',
        'pedido_id'
    ];

    protected $casts = [
        'data' => 'array',
        'processed' => 'boolean',
        'processed_at' => 'datetime',
        'last_attempt_at' => 'datetime',
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

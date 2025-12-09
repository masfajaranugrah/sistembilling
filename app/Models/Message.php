<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class Message extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = ['sender', 'receiver'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * Get sender - could be from users or pelanggans table
     */
    public function getSenderAttribute()
    {
        // Cache key untuk menghindari query berulang
        $cacheKey = "message_sender_{$this->sender_id}";
        
        return Cache::remember($cacheKey, 300, function() {
            // Try users table first
            $user = User::find($this->sender_id);
            if ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email ?? null,
                    'role' => $user->role ?? null,
                ];
            }
            
            // Try pelanggans table
            $pelanggan = Pelanggan::find($this->sender_id);
            if ($pelanggan) {
                return [
                    'id' => $pelanggan->id,
                    'name' => $pelanggan->nama_lengkap ?? $pelanggan->name ?? 'Pelanggan',
                    'email' => $pelanggan->email ?? null,
                    'role' => 'pelanggan',
                ];
            }
            
            return null;
        });
    }

    /**
     * Get receiver - could be from users or pelanggans table
     */
    public function getReceiverAttribute()
    {
        // Cache key untuk menghindari query berulang
        $cacheKey = "message_receiver_{$this->receiver_id}";
        
        return Cache::remember($cacheKey, 300, function() {
            // Try users table first
            $user = User::find($this->receiver_id);
            if ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email ?? null,
                    'role' => $user->role ?? null,
                ];
            }
            
            // Try pelanggans table
            $pelanggan = Pelanggan::find($this->receiver_id);
            if ($pelanggan) {
                return [
                    'id' => $pelanggan->id,
                    'name' => $pelanggan->nama_lengkap ?? $pelanggan->name ?? 'Pelanggan',
                    'email' => $pelanggan->email ?? null,
                    'role' => 'pelanggan',
                ];
            }
            
            return null;
        });
    }
}
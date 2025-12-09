<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Ticket extends Model
{
    protected $fillable = [
        'pelanggan_id',
        'phone',
        'location_link',
        'category',
        'issue_description',
        'additional_note',
        'cs_note',
        'technician_note',
        'technician_attachment',
        'attachment',
        'complaint_source',
        'priority',
        'technician_group_id',
        'user_id',
        'status',
        'created_by',
        'progress_started_at',
        'finished_at',
    ];

    // UUID settings
    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->getKey()) {
                $model->setAttribute($model->getKeyName(), (string) Str::uuid());
            }
        });
    }

    /**
     * Relasi ke pelanggan
     */
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }

    /**
     * Relasi ke user (teknisi / admin)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke log status
     */
    public function statusLogs()
    {
        return $this->hasMany(TicketStatusLog::class)->orderBy('created_at', 'asc');
    }

    /**
     * Relasi ke pembuat tiket (CS/Admin)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

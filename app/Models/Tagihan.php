<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use NotificationChannels\WebPush\HasPushSubscriptions;

class Tagihan extends Model
{
    use HasFactory, HasPushSubscriptions, Notifiable;

    protected $table = 'tagihans';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'pelanggan_id',
        'paket_id',
        'tanggal_mulai',
        'tanggal_berakhir',
        'status_pembayaran',
        'catatan',
        'bukti_pembayaran',
        'kwitansi',
        'type_pembayaran',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function paket()
    {
        return $this->belongsTo(Paket::class);
    }

    public function rekening()
    {
        return $this->belongsTo(Rekening::class, 'type_pembayaran', 'id');
    }
}

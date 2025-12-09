<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CustomerTagihan extends Model
{
    use HasFactory;

    public $incrementing = false; // karena UUID bukan auto increment

    protected $keyType = 'string'; // primary key berupa string

    protected $fillable = [
        'id',
        'user_id',
        'tagihan_id',
        'status_pembayaran',
        'tanggal_mulai',
        'tanggal_berakhir',
        'tanggal_pembayaran',
        'bukti_pembayaran',
        'kwitansi',
        'keterangan',
        'type_pembayaran', // tambahkan ini
    ];

    // Generate UUID otomatis saat membuat data baru
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class);
    }

    public function rekening()
    {
        return $this->belongsTo(Rekening::class, 'type_pembayaran', 'id');
    }
}

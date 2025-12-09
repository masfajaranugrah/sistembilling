<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BarangMasuk extends Model
{
    use HasFactory;

    // Gunakan UUID
    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'barang_masuks';

    // Kolom yang bisa diisi massal
    protected $fillable = [
        'id',
        'barang_id',
        'jumlah',
        'jenis',
        'keterangan',
        'tanggal_masuk',
    ];

    // Event boot untuk auto-generate UUID
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::uuid()->toString();
            }
        });
    }

    // Relasi ke tabel barang
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}

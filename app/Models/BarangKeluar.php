<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BarangKeluar extends Model
{
    use HasFactory;

    // Primary key bukan auto increment
    public $incrementing = false;

    // Tipe primary key string (UUID)
    protected $keyType = 'string';

    // Mass assignable fields
    protected $fillable = [
        'barang_id',
        'jumlah',
        'diambil_oleh',
        'keterangan',
        'tanggal',
    ];

    // Boot method untuk generate UUID otomatis
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    // Relasi ke Barang
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Paket extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_paket',
        'harga',
        'masa_pembayaran',
        'cycle',
        'kecepatan',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function pelanggans()
    {
        return $this->hasMany(Pelanggan::class);
    }

    public $incrementing = false;

    protected $keyType = 'string';
}

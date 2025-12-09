<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Income extends Model
{
    use HasFactory;

    // Gunakan UUID sebagai primary key
    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'kode',
        'kategori',
        'jumlah',
        'keterangan',
        'tanggal_masuk',
    ];

    // Generate UUID dan kode otomatis
    public static function boot()
    {
        parent::boot();

        // Generate UUID
        static::creating(function ($income) {
            if (! $income->getKey()) {
                $income->{$income->getKeyName()} = (string) Str::uuid();
            }

            // Generate kode untuk kategori DLL
            if ($income->kategori === 'DLL') {
                $last = self::where('kategori', 'DLL')->orderBy('id', 'desc')->first();
                $nextNumber = $last ? intval(substr($last->kode, 2)) + 1 : 1;
                $income->kode = 'DL'.str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            }
        });
    }
}

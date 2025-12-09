<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Expense extends Model
{
    use HasFactory;

    // Matikan auto increment
    public $incrementing = false;

    // Tipe primary key adalah string (UUID)
    protected $keyType = 'string';

    protected $fillable = ['id', 'kode', 'kategori', 'jumlah', 'keterangan', 'tanggal_keluar'];

    protected static function boot()
    {
        parent::boot();

        // Generate UUID sebelum create
        static::creating(function ($expense) {
            if (! $expense->id) {
                $expense->id = (string) Str::uuid();
            }

            // Kode untuk kategori DLL
            if ($expense->kategori === 'DLL') {
                $last = self::where('kategori', 'DLL')->orderBy('id', 'desc')->first();
                $nextNumber = $last ? intval(substr($last->kode, 2)) + 1 : 1;
                $expense->kode = 'DL'.str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            }
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LedgerDaily extends Model
{
    protected $table = 'ledger_dailies';

    protected $fillable = ['tanggal', 'total_masuk', 'total_keluar', 'saldo'];

    public $timestamps = true;

    public $incrementing = false; // Karena UUID bukan auto-increment

    protected $keyType = 'string'; // UUID berupa string

    // Generate UUID otomatis saat membuat record baru
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (! $model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}

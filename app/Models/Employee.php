<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'nik',
        'full_name',
        'full_address',
        'place_of_birth',
        'date_of_birth',
        'no_hp',
        'tanggal_masuk',
        'jabatan',
        'bank',
        'no_rekening',
        'atas_nama',
    ];

    // Gunakan UUID sebagai primary key
    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }
}

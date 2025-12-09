<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';

    // Laravel tidak memakai auto increment UUID, jadi kita matikan incrementing
    public $incrementing = false;

    // UUID bertipe string
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'date',
        'time_in',
        'photo_in',
        'lat_in',
        'lng_in',
        'time_out',
        'photo_out',
        'lat_out',
        'lng_out',
        'lembur_in',
        'lembur_out',
        'lat_lembur_in',
        'lng_lembur_in',
        'lat_lembur_out',
        'lng_lembur_out',
        'photo_lembur_in',
        'photo_lembur_out',
        'total_hours',
        'overtime_hours',
        'note',
    ];

    protected $casts = [
        'time_in' => 'datetime',
        'time_out' => 'datetime',
        'lembur_in' => 'datetime',
        'lembur_out' => 'datetime',
        'date'     => 'date',
    ];

    // Generate UUID otomatis
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
}



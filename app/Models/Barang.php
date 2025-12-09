<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['id', 'nama_barang', 'stok', 'keterangan'];

    public function barangMasuks()
    {
        return $this->hasMany(BarangMasuk::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class pegawai extends Model
{
    protected $table = 'pegawai';

    // Nonaktifkan timestamps jika tidak diperlukan
    public $timestamps = false;

    // Relasi dengan model Absensi
    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }

    // Kolom yang dapat diisi secara massal
    protected $fillable = [
        'nama',
        'no_hp',
        'alamat', // Tambahkan kolom alamat jika tersedia dalam tabel
        'email'   // Tambahkan kolom email jika tersedia dalam tabel
    ];

    // Tambahkan atribut tambahan atau casting jika diperlukan
    protected $casts = [
        'created_at' => 'datetime', // Jika ada kolom created_at dan membutuhkan casting waktu
        'updated_at' => 'datetime', // Jika ada kolom updated_at dan membutuhkan casting waktu
    ];

    // Metode untuk mencari pegawai berdasarkan nama atau atribut lain
    public function scopeSearch($query, $search)
    {
        return $query->where('nama', 'like', "%$search%")
                     ->orWhere('no_hp', 'like', "%$search%")
                     ->orWhere('email', 'like', "%$search%");
    }
}

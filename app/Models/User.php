<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'telepon',
        'alamat',
        'status_tugas'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // --- TAMBAHAN RELASI (Hubungan Antar Tabel) ---

    // 1. Seorang Petugas (User) bisa melayani BANYAK Transaksi
    public function transaksis()
    {
        // Pastikan 'petugas_id' sesuai dengan nama kolom di tabel transaksis
        return $this->hasMany(Transaksi::class, 'petugas_id');
    }

    // 2. Seorang Petugas (User) punya BANYAK Riwayat Absensi
    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }

    // Di file User.php
    public function nasabah()
    {
        // Asumsi: tabel nasabahs punya kolom user_id
        return $this->hasOne(Nasabah::class); 
    }
}

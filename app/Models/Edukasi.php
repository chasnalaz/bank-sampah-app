<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Edukasi extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    // Helper: Mengambil ID Youtube dari Link panjang
    public function getYoutubeIdAttribute()
    {
        if ($this->kategori == 'video' && $this->link_url) {
            // Logika regex sederhana untuk ambil ID video
            preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $this->link_url, $matches);
            return $matches[1] ?? null;
        }
        return null;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
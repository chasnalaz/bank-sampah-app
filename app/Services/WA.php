<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WA
{
    /**
     * Kirim Pesan WhatsApp via Fonnte
     * @param string $target Nomor HP tujuan (08xx atau 62xx)
     * @param string $message Isi pesan
     */
    public static function kirim($target, $message)
    {
        // Ambil token dari .env
        $token = env('FONNTE_TOKEN');

        // Kirim request ke API Fonnte
        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62', // Otomatis ubah 08 jadi 62
            ]);

            return $response->json();
        } catch (\Exception $e) {
            // Kalau gagal (misal internet mati), jangan bikin error, return false aja
            return false;
        }
    }
}
<?php

namespace App\Services;

use App\Models\Setting;

class ChatbotService
{
    private array $responses = [
        'jam buka'    => "Kami buka setiap hari dari jam {open} sampai {close}. Yuk mampir! 😊",
        'open'        => "Kami buka setiap hari dari jam {open} sampai {close}. Yuk mampir! 😊",
        'reservasi'   => "Kamu bisa reservasi meja di halaman Reservasi atau hubungi kami di WhatsApp {wa}. 📋",
        'booking'     => "Kamu bisa reservasi meja di halaman Reservasi atau hubungi kami di WhatsApp {wa}. 📋",
        'promo'       => "Cek promo terbaru kami di halaman Menu! Ada promo spesial untuk pelajar setiap hari. 🎉",
        'diskon'      => "Cek promo terbaru kami di halaman Menu! Ada promo spesial untuk pelajar setiap hari. 🎉",
        'best seller' => "Best seller kami saat ini adalah Salad Wrap Sehat & Coffee Anak Sekolah - Kopi Susu! ⭐",
        'favorit'     => "Menu favorit pelanggan kami: Nasi Ayam Geprek, Salad Wrap, dan Kopi Susu! ❤️",
        'menu'        => "Kami punya berbagai menu seru! Dari Nasi Geprek, Mie Instan Premium, Rice Bowl, sampai Coffee Kekinian. Cek halaman Menu ya!",
        'harga'       => "Harga mulai dari Rp 6.000 aja! Menu lengkap dan harganya bisa dilihat di halaman Menu. 💰",
        'pembayaran'  => "Kami menerima pembayaran via SOHIBA Wallet, tunai, dan transfer bank. 💳",
        'wallet'      => "SOHIBA Wallet adalah dompet digital kami. Topup dan bayar lebih mudah! 👛",
        'membership'  => "Kami punya program membership! Silver setelah 10 order, Gold setelah 30 order, dan Platinum setelah 100 order! Dapat cashback lho! 🏆",
        'cashback'    => "Silver dapat 2% cashback, Gold 5%, Platinum 10% dari setiap order! 🎁",
        'lokasi'      => "Kami berlokasi di {alamat}. 📍",
        'alamat'      => "Kami berlokasi di {alamat}. 📍",
        'default'     => "Halo! Saya RAS Bot 🤖. Saya bisa bantu kamu soal jam buka, menu, reservasi, promo, membership, dan wallet. Tanya apa aja ya!",
    ];

    public function respond(string $message): string
    {
        $lower = strtolower($message);

        $open   = Setting::get('open_time', '07:00');
        $close  = Setting::get('close_time', '21:00');
        $wa     = Setting::get('whatsapp_number', '6281234567890');
        $alamat = Setting::get('restaurant_address', 'Jl. Pelajar No. 1');

        foreach ($this->responses as $keyword => $response) {
            if ($keyword !== 'default' && str_contains($lower, $keyword)) {
                return str_replace(
                    ['{open}', '{close}', '{wa}', '{alamat}'],
                    [$open,    $close,    $wa,    $alamat],
                    $response
                );
            }
        }

        return $this->responses['default'];
    }
}
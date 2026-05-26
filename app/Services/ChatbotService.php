<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class ChatbotService
{
    private array $context = [];

    // ── Intent patterns ────────────────────────────────────────
    private array $intents = [
        'greeting'       => ['halo','hai','hi','hello','selamat','pagi','siang','sore','malam','permisi','assalamu'],
        'jam_buka'       => ['jam','buka','tutup','open','close','operasional','waktu','kapan'],
        'reservasi'      => ['reservasi','booking','pesan meja','meja','tempat','book','reserve'],
        'promo'          => ['promo','diskon','voucher','discount','hemat','murah','tawaran'],
        'menu'           => ['menu','makanan','minuman','makan','minum','ada apa','tersedia','pilihan'],
        'best_seller'    => ['best','terlaris','populer','favorit','rekomen','enak','hits'],
        'harga'          => ['harga','price','berapa','cost','tarif'],
        'pembayaran'     => ['bayar','payment','transfer','qris','wallet','tunai','cash'],
        'status_order'   => ['status','pesanan','order','tracking','lacak','mana','sudah'],
        'membership'     => ['member','membership','silver','gold','platinum','tier','cashback'],
        'wallet'         => ['wallet','saldo','topup','dompet','sohiba'],
        'lokasi'         => ['lokasi','alamat','dimana','where','maps','address','jalan'],
        'kontak'         => ['kontak','telepon','wa','whatsapp','hubungi','contact'],
        'complain'       => ['komplain','kecewa','buruk','jelek','lambat','lama','salah','ganti','refund'],
        'thanks'         => ['terima kasih','makasih','thanks','thank','thx','oke','ok','baik','sip'],
    ];

    // ── Quick replies per intent ───────────────────────────────
    private array $quickReplies = [
        'menu'       => ['Lihat semua menu','Menu best seller','Harga menu'],
        'reservasi'  => ['Cara reservasi','Meja tersedia','Jam operasional'],
        'pembayaran' => ['Cara bayar QRIS','Transfer Bank','SOHIBA Wallet'],
        'membership' => ['Syarat membership','Cashback berapa?','Cara naik tier'],
    ];

    public function respond(string $message, array $history = []): array
    {
        $lower   = mb_strtolower(trim($message));
        $intent  = $this->detectIntent($lower);
        $reply   = $this->generateReply($intent, $lower);
        $quick   = $this->quickReplies[$intent] ?? [];

        return [
            'reply'       => $reply,
            'intent'      => $intent,
            'quick_replies' => $quick,
            'timestamp'   => now()->timezone('Asia/Jakarta')->format('H:i'),
        ];
    }

    private function detectIntent(string $msg): string
    {
        $scores = [];
        foreach ($this->intents as $intent => $keywords) {
            $score = 0;
            foreach ($keywords as $kw) {
                if (str_contains($msg, $kw)) $score++;
            }
            if ($score > 0) $scores[$intent] = $score;
        }

        if (empty($scores)) return 'default';
        arsort($scores);
        return array_key_first($scores);
    }

    private function generateReply(string $intent, string $msg): string
    {
        $open  = Setting::get('open_time', '07:00');
        $close = Setting::get('close_time', '21:00');
        $wa    = Setting::get('whatsapp_number', '6281234567890');
        $addr  = Setting::get('restaurant_address', 'Jl. Pelajar No. 1');

        switch ($intent) {
            case 'greeting':
                $greets = [
                    'Halo! Selamat datang di Rumahnya Anak Sekolah 🏫 Ada yang bisa saya bantu?',
                    'Hai! Saya RAS Bot, siap membantu kamu 😊 Mau pesan makanan, reservasi, atau ada pertanyaan?',
                    'Halo kak! Ada yang bisa RAS Bot bantu hari ini? 🌟',
                ];
                return $greets[array_rand($greets)];

            case 'jam_buka':
                $now = now()->timezone('Asia/Jakarta');
                $openTime  = \Carbon\Carbon::createFromTimeString($open,  'Asia/Jakarta');
                $closeTime = \Carbon\Carbon::createFromTimeString($close, 'Asia/Jakarta');
                $isOpen    = $now->between($openTime, $closeTime);
                return "🕐 Kami buka setiap hari pukul **{$open} – {$close} WIB**.\n\n"
                    . ($isOpen ? "✅ Saat ini kami **BUKA**! Yuk mampir 😊" : "❌ Saat ini kami sedang **tutup**. Sampai jumpa besok ya!");

            case 'reservasi':
                return "📅 Untuk reservasi meja, kamu bisa:\n\n"
                    . "1. Klik menu **Reservasi** di navbar\n"
                    . "2. Pilih meja, tanggal, dan jam yang diinginkan\n"
                    . "3. Isi jumlah tamu & request khusus\n"
                    . "4. Admin akan konfirmasi dalam beberapa saat\n\n"
                    . "Atau hubungi kami di WhatsApp: wa.me/{$wa} 📱";

            case 'promo':
                $vouchers = \App\Models\Voucher::active()->take(3)->get();
                if ($vouchers->isEmpty()) {
                    return "🎉 Cek promo terbaru kami di halaman Menu! Ada diskon spesial untuk pelajar setiap hari.\n\nJangan lupa aktifkan membership untuk dapat cashback otomatis! 💰";
                }
                $list = $vouchers->map(fn($v) => "• **{$v->code}** — " . ($v->type === 'percent' ? "{$v->value}% diskon" : "Hemat Rp " . number_format($v->value, 0, ',', '.')))->join("\n");
                return "🏷️ Voucher aktif saat ini:\n\n{$list}\n\nGunakan saat checkout ya!";

            case 'menu':
$categories = \App\Models\MenuCategory::active()
    ->with('activeMenus')
    ->withCount('activeMenus')
    ->get();
                    if ($categories->isEmpty()) return "🍱 Cek halaman Menu untuk lihat semua pilihan kami ya!";
                $list = $categories->map(fn($c) => "{$c->icon} **{$c->name}** ({$c->active_menus_count} menu)")->join("\n");
                return "🍽️ Menu kami terdiri dari:\n\n{$list}\n\nBuka halaman **Menu** untuk lihat detail & harga!";

            case 'best_seller':
                $menus = Menu::available()->where('is_best_seller', true)->orderBy('order_count','desc')->take(4)->get();
                if ($menus->isEmpty()) return "⭐ Semua menu kami enak-enak! Favorit pelanggan: Nasi Ayam Geprek, Salad Wrap, dan Kopi Susu.";
                $list = $menus->map(fn($m) => "⭐ **{$m->name}** — Rp " . number_format($m->effective_price, 0, ',', '.') . " ({$m->order_count}x dipesan)")->join("\n");
                return "🔥 Menu Best Seller kami:\n\n{$list}\n\nYuk segera pesan sebelum kehabisan!";

            case 'harga':
                $cheapest = Menu::available()->orderBy('price','asc')->first();
                $mostPop  = Menu::available()->orderBy('order_count','desc')->first();
                return "💰 Harga menu kami mulai dari **Rp " . ($cheapest ? number_format($cheapest->price,0,',','.') : '6.000') . "** aja!\n\n"
                    . ($mostPop ? "Menu terpopuler: **{$mostPop->name}** — Rp " . number_format($mostPop->effective_price,0,',','.') . "\n\n" : '')
                    . "Buka halaman Menu untuk lihat semua harga lengkap ya!";

            case 'pembayaran':
                return "💳 Metode pembayaran yang tersedia:\n\n"
                    . "📱 **QRIS** — Scan & bayar instan\n"
                    . "🏦 **Transfer Bank** — BCA, Mandiri, BNI, BRI (Virtual Account)\n"
                    . "💳 **SOHIBA Wallet** — Dompet digital internal\n"
                    . "💵 **Tunai / Cash** — Bayar di kasir\n\n"
                    . "Topup SOHIBA Wallet di menu **Wallet** ya!";

            case 'status_order':
                if (Auth::check()) {
                    $activeOrder = Order::where('user_id', Auth::id())
                        ->whereIn('status', ['menunggu','cooking','selesai'])
                        ->latest()->first();
                    if ($activeOrder) {
                        $statusText = [
                            'menunggu' => '⏳ Menunggu dikonfirmasi kasir',
                            'cooking'  => '👨‍🍳 Sedang dimasak oleh dapur',
                            'selesai'  => '✅ Siap! Silakan ambil pesananmu',
                        ];
                        return "📦 Order aktif kamu: **#{$activeOrder->order_number}**\n\n"
                            . "Status: " . ($statusText[$activeOrder->status] ?? $activeOrder->status) . "\n\n"
                            . "Buka halaman **Pesanan** untuk tracking detail ya!";
                    }
                    return "📭 Kamu tidak memiliki order aktif saat ini.\n\nMau pesan sekarang? Buka halaman **Menu**!";
                }
                return "🔐 Untuk cek status order, kamu perlu **login** dulu ya!\n\nSetelah login, buka menu **Pesanan** untuk tracking real-time.";

            case 'membership':
                return "🏆 Program Membership Rumahnya Anak Sekolah:\n\n"
                    . "🥈 **Silver** — 10 order selesai → Cashback 2%\n"
                    . "🥇 **Gold** — 30 order selesai → Cashback 5%\n"
                    . "💎 **Platinum** — 100 order + beli premium → Cashback 10%\n\n"
                    . "Semakin banyak order, semakin besar cashback yang kamu dapat!";

            case 'wallet':
                return "💰 **SOHIBA Wallet** adalah dompet digital kami!\n\n"
                    . "✅ Topup via transfer bank (BCA, Mandiri, BNI, BRI)\n"
                    . "✅ Bayar order lebih mudah & cepat\n"
                    . "✅ Dapat cashback otomatis sesuai tier membership\n\n"
                    . "Topup sekarang di menu **Wallet**!";

            case 'lokasi':
                return "📍 Kami berlokasi di:\n**{$addr}**\n\nBuka Google Maps untuk petunjuk arah!";

            case 'kontak':
                return "📞 Hubungi kami:\n\n"
                    . "💬 **WhatsApp**: wa.me/{$wa}\n"
                    . "📧 **Email**: " . Setting::get('restaurant_email','hello@ras.com') . "\n"
                    . "📍 **Alamat**: {$addr}\n\n"
                    . "Tim kami siap membantu jam {$open}–{$close} WIB!";

            case 'complain':
                return "😔 Maaf atas pengalaman yang kurang menyenangkan!\n\n"
                    . "Kami sangat menghargai masukan kamu. Silakan hubungi kami langsung via WhatsApp: **wa.me/{$wa}** agar kami bisa segera menyelesaikan masalahmu.\n\n"
                    . "Terima kasih sudah mau memberitahu kami! 🙏";

            case 'thanks':
                $replies = [
                    'Sama-sama! Jangan sungkan bertanya ya 😊',
                    'Dengan senang hati! Ada lagi yang bisa saya bantu? 🌟',
                    'Oke kak! Selamat menikmati 🍱',
                ];
                return $replies[array_rand($replies)];

            default:
                return "Halo! Saya RAS Bot 🤖 Saya bisa bantu kamu soal:\n\n"
                    . "🕐 Jam buka\n🍽️ Menu & harga\n📅 Reservasi\n💳 Pembayaran\n📦 Status order\n🏆 Membership\n💰 Wallet\n📍 Lokasi & kontak\n\n"
                    . "Silakan tanya apa saja ya!";
        }
    }
}
<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Voucher;
use App\Models\MenuCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotService
{
   private string $apiKey;

public function __construct()
{
    $this->apiKey = config('services.groq.key', '');
}

public function respond(string $message, array $history = []): array
{
    try {
        $context  = $this->buildContext();
        $messages = $this->buildMessages($history, $message, $context);

        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type'  => 'application/json',
        ])->timeout(15)->post('https://api.groq.com/openai/v1/chat/completions', [
            'model'       => 'llama-3.1-8b-instant',
            'messages'    => $messages,
            'max_tokens'  => 500,
            'temperature' => 0.7,
        ]);

        if ($response->failed()) {
            Log::error('Groq API error: ' . $response->body());
            return $this->fallbackResponse($message);
        }

        $reply = $response->json('choices.0.message.content');
        if (!$reply) return $this->fallbackResponse($message);

        return [
            'reply'         => trim($reply),
            'intent'        => 'ai',
            'quick_replies' => $this->getQuickReplies($message),
            'timestamp'     => now()->timezone('Asia/Jakarta')->format('H:i'),
        ];
    } catch (\Exception $e) {
        Log::error('Chatbot error: ' . $e->getMessage());
        return $this->fallbackResponse($message);
    }
}

    private function buildContext(): string
    {
        $open   = Setting::get('open_time', '07:00');
        $close  = Setting::get('close_time', '21:00');
        $wa     = Setting::get('whatsapp_number', '6281234567890');
        $addr   = Setting::get('restaurant_address', 'Jl. in aja dulu');
        $now    = now()->timezone('Asia/Jakarta');
        $isOpen = $now->between(
            \Carbon\Carbon::createFromTimeString($open,  'Asia/Jakarta'),
            \Carbon\Carbon::createFromTimeString($close, 'Asia/Jakarta')
        );

        // Data menu per kategori
        $categories = MenuCategory::with(['activeMenus' => function($q) {
            $q->orderBy('order_count', 'desc');
        }])->get();

        $menuList = $categories->map(function ($cat) {
            $items = $cat->activeMenus->map(fn($m) =>
                "  - {$m->name}: Rp " . number_format($m->effective_price, 0, ',', '.')
                . ($m->is_best_seller ? ' [BEST SELLER]' : '')
                . ($m->stock <= 5 ? " [STOK TERBATAS: {$m->stock}]" : '')
                . ($m->stock == 0 ? ' [HABIS]' : '')
                . ($m->description ? " | {$m->description}" : '')
            )->join("\n");
            return "== {$cat->name} ==\n{$items}";
        })->join("\n\n");

        // Best seller
        $bestSellers = Menu::available()->where('is_best_seller', true)
            ->orderBy('order_count', 'desc')->take(5)->get()
            ->map(fn($m) => "- {$m->name}: Rp " . number_format($m->effective_price, 0, ',', '.') . " (dipesan {$m->order_count}x)")
            ->join("\n");

        // Voucher aktif
        $vouchers = Voucher::active()->take(5)->get()
            ->map(fn($v) =>
                "- Kode: {$v->code} | "
                . ($v->type === 'percent' ? "Diskon {$v->value}%" : "Hemat Rp " . number_format($v->value, 0, ',', '.'))
                . " | Min. order: Rp " . number_format($v->min_order, 0, ',', '.')
                . " | Berlaku sampai: " . ($v->expired_at ? $v->expired_at->format('d M Y') : 'Tidak terbatas')
            )->join("\n");

        // Info user
        $userInfo = 'User belum login (guest).';
        if (Auth::check()) {
            $user        = Auth::user();
            $membership  = $user->membership;
            $wallet      = $user->wallet;
            $activeOrder = Order::where('user_id', $user->id)
                ->whereIn('status', ['menunggu', 'cooking', 'selesai'])
                ->latest()->first();
            $recentOrders = Order::where('user_id', $user->id)
                ->latest()->take(3)->get()
                ->map(fn($o) => "  #{$o->order_number} | {$o->status} | Rp " . number_format($o->total_amount, 0, ',', '.') . " | {$o->created_at->format('d M Y')}")
                ->join("\n");

            $userInfo = "User sudah login:\n"
                . "- Nama: {$user->name}\n"
                . "- Email: {$user->email}\n"
                . "- Membership: " . ($membership ? strtoupper($membership->tier) . " (cashback {$membership->cashback_rate}%, {$membership->completed_orders} order selesai)" : "Tidak ada") . "\n"
                . "- Saldo Wallet: Rp " . number_format($wallet->balance ?? 0, 0, ',', '.') . "\n"
                . "- Order aktif: " . ($activeOrder ? "#{$activeOrder->order_number} (status: {$activeOrder->status})" : "Tidak ada") . "\n"
                . "- Riwayat order terakhir:\n{$recentOrders}";
        }

        return <<<PROMPT
Kamu adalah RAS Bot 🤖, asisten virtual cerdas dan ramah untuk restoran "Rumahnya Anak Sekolah" (RAS). Kamu seperti customer service Shopee — cepat, helpful, friendly, dan sedikit playful. Gunakan emoji secukupnya agar terasa hangat.

=== INFORMASI RESTORAN ===
- Nama: Rumahnya Anak Sekolah (RAS)
- Tagline: Premium Kuliner Anak Sekolah
- Alamat: {$addr}
- Jam buka: {$open} - {$close} WIB (setiap hari)
- Status sekarang: {$now->format('H:i')} WIB — " . ($isOpen ? "BUKA ✅" : "TUTUP ❌") . "
- WhatsApp: {$wa}
- Waktu sekarang: {$now->format('l, d F Y H:i')} WIB

=== MENU TERSEDIA ===
{$menuList}

=== BEST SELLER ===
{$bestSellers}

=== VOUCHER AKTIF ===
{$vouchers}

=== METODE PEMBAYARAN ===
- QRIS (scan & bayar instan)
- Transfer Bank: BCA, Mandiri, BNI, BRI (Virtual Account)
- SOHIBA Wallet (dompet digital internal, bisa dapat cashback)
- Cash / Tunai (bayar di kasir)

=== PROGRAM MEMBERSHIP ===
- Silver: 10 order selesai → Cashback 2% setiap order
- Gold: 30 order selesai → Cashback 5% setiap order
- Platinum: 100 order selesai → Cashback 10% setiap order
- Loyalty Points: Setiap order selesai dapat 20 poin

=== INFO USER SAAT INI ===
{$userInfo}

=== PANDUAN MENJAWAB ===
1. Gunakan Bahasa Indonesia yang natural, friendly, dan tidak kaku
2. Jawab berdasarkan data di atas — jangan mengarang informasi
3. Jika user tanya menu spesifik, berikan harga, deskripsi, dan status stoknya
4. Jika user sudah login, personalisasi jawaban (sebut nama, info order aktif, saldo, dll)
5. Untuk komplain serius, arahkan ke WhatsApp
6. Jawaban ringkas (2-4 kalimat) kecuali diminta detail
7. Jangan pernah bilang kamu AI, Claude, atau Gemini — kamu adalah RAS Bot
8. Jika pertanyaan di luar konteks restoran, arahkan kembali ke topik RAS dengan sopan
9. Format teks: gunakan **bold** untuk highlight penting, gunakan emoji yang relevan
10. Jika ada order aktif user, proaktif tanyakan apakah mau cek statusnya
PROMPT;
    }

   private function buildMessages(array $history, string $newMessage, string $context): array
{
    $messages = [];

    // System prompt
    $messages[] = [
        'role'    => 'system',
        'content' => $context,
    ];

    // History percakapan (max 10 pesan terakhir)
    foreach (array_slice($history, -10) as $h) {
        if (empty($h['role']) || empty($h['content'])) continue;
        $messages[] = [
            'role'    => $h['role'] === 'bot' ? 'assistant' : 'user',
            'content' => $h['content'],
        ];
    }

    // Pesan terbaru
    $messages[] = ['role' => 'user', 'content' => $newMessage];

    return $messages;
}

    private function getQuickReplies(string $message): array
    {
        $lower = mb_strtolower($message);

        if (str_contains($lower, 'menu') || str_contains($lower, 'makan') || str_contains($lower, 'minum')) {
            return ['Menu best seller ⭐', 'Cek harga 💰', 'Ada promo? 🎁'];
        }
        if (str_contains($lower, 'bayar') || str_contains($lower, 'harga') || str_contains($lower, 'berapa')) {
            return ['Cara bayar QRIS 📱', 'Topup Wallet 💰', 'Lihat voucher 🎟️'];
        }
        if (str_contains($lower, 'reservasi') || str_contains($lower, 'meja') || str_contains($lower, 'booking')) {
            return ['Cara reservasi 📅', 'Jam buka ⏰', 'Hubungi kami 📞'];
        }
        if (str_contains($lower, 'member') || str_contains($lower, 'poin') || str_contains($lower, 'cashback')) {
            return ['Syarat naik tier 📈', 'Cashback berapa? 💰', 'Cek membership saya 🏆'];
        }
        if (str_contains($lower, 'order') || str_contains($lower, 'pesanan') || str_contains($lower, 'status')) {
            return ['Cek order aktif 📦', 'Riwayat pesanan 📋', 'Hubungi kasir 📞'];
        }

        return ['Lihat menu 🍱', 'Promo hari ini 🎁', 'Jam buka ⏰'];
    }

    private function fallbackResponse(string $message): array
    {
        $lower = mb_strtolower($message);
        $open  = Setting::get('open_time', '07:00');
        $close = Setting::get('close_time', '21:00');
        $wa    = Setting::get('whatsapp_number', '6281234567890');
        $now   = now()->timezone('Asia/Jakarta');
        $isOpen = $now->between(
            \Carbon\Carbon::createFromTimeString($open, 'Asia/Jakarta'),
            \Carbon\Carbon::createFromTimeString($close, 'Asia/Jakarta')
        );

        if (str_contains($lower, 'jam') || str_contains($lower, 'buka')) {
            $reply = "🕐 Kami buka setiap hari pukul **{$open} – {$close} WIB**!\n\n"
                . ($isOpen ? "✅ Saat ini **BUKA**! Yuk mampir 😊" : "❌ Saat ini **tutup**. Buka lagi jam {$open} WIB ya!");
        } elseif (str_contains($lower, 'halo') || str_contains($lower, 'hai') || str_contains($lower, 'hi')) {
            $name = Auth::check() ? ', ' . Auth::user()->name : '';
            $reply = "Halo{$name}! 👋 Saya RAS Bot, siap membantu kamu. Ada yang bisa dibantu?";
        } elseif (str_contains($lower, 'promo') || str_contains($lower, 'voucher')) {
            $vouchers = Voucher::active()->take(2)->get();
            $reply = $vouchers->isEmpty()
                ? "🎉 Aktifkan Membership untuk cashback otomatis! Pantau terus promo kami ya 👀"
                : "🏷️ Voucher aktif: " . $vouchers->map(fn($v) => "**{$v->code}**")->join(', ') . ". Gunakan saat checkout!";
        } else {
            $reply = "Maaf, saya sedang gangguan sementara 🙏 Hubungi kami via WhatsApp **wa.me/{$wa}** ya!";
        }

        return [
            'reply'         => $reply,
            'intent'        => 'fallback',
            'quick_replies' => ['Lihat menu 🍱', 'Jam buka ⏰', 'Hubungi kami 📞'],
            'timestamp'     => now()->timezone('Asia/Jakarta')->format('H:i'),
        ];
    }
    
}
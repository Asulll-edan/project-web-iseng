<div id="chat-wrap" style="position:fixed;bottom:24px;right:24px;z-index:400;display:flex;flex-direction:column;align-items:flex-end;gap:10px">

    {{-- Chat Window --}}
    <div id="chat-window" style="display:none;width:340px;background:var(--warm-white);border-radius:20px;border:1px solid var(--border);box-shadow:0 20px 60px rgba(90,124,101,.2);overflow:hidden;flex-direction:column;max-height:480px;animation:slideUp .25s ease">
        {{-- Header --}}
        <div style="background:linear-gradient(135deg,var(--sage-dark),var(--sage));padding:14px 16px;display:flex;align-items:center;justify-content:space-between">
            <div style="display:flex;align-items:center;gap:10px">
                <div style="width:36px;height:36px;background:rgba(255,255,255,.2);border-radius:50%;display:flex;align-items:center;justify-content:center">
                    <i class="ti ti-robot" style="color:#fff;font-size:18px"></i>
                </div>
                <div>
                    <div style="font-weight:700;font-size:14px;color:#fff">RAS Bot</div>
                    <div style="font-size:11px;color:rgba(255,255,255,.75);display:flex;align-items:center;gap:4px">
                        <span style="width:6px;height:6px;border-radius:50%;background:#4ade80;display:inline-block"></span>
                        Online — siap membantu
                    </div>
                </div>
            </div>
            <button onclick="toggleChat()" style="background:rgba(255,255,255,.15);border:none;cursor:pointer;color:#fff;width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;transition:background .15s" onmouseover="this.style.background='rgba(255,255,255,.25)'" onmouseout="this.style.background='rgba(255,255,255,.15)'">
                <i class="ti ti-x" style="font-size:15px"></i>
            </button>
        </div>

        {{-- Messages --}}
        <div id="chat-messages" style="flex:1;overflow-y:auto;padding:14px;display:flex;flex-direction:column;gap:10px;max-height:300px;min-height:120px;background:var(--cream)">
            <div class="chat-msg bot">
                Halo! Saya <strong>RAS Bot</strong> 🤖<br>
                Ada yang bisa saya bantu hari ini?
            </div>
        </div>

        {{-- Quick replies --}}
        <div id="quick-replies" style="padding:8px 12px;display:flex;gap:6px;flex-wrap:wrap;background:var(--cream);border-top:1px solid var(--border)">
            @foreach(['Jam buka?','Menu best seller','Cara reservasi','Status order saya'] as $qr)
            <button onclick="sendQuickReply('{{ $qr }}')" style="padding:5px 12px;border-radius:20px;border:1.5px solid var(--border);background:var(--warm-white);font-size:11px;font-weight:600;cursor:pointer;color:var(--sage-dark);transition:all .15s;font-family:inherit" onmouseover="this.style.background='var(--sage)';this.style.color='#fff';this.style.borderColor='var(--sage)'" onmouseout="this.style.background='var(--warm-white)';this.style.color='var(--sage-dark)';this.style.borderColor='var(--border)'">
                {{ $qr }}
            </button>
            @endforeach
        </div>

        {{-- Input --}}
        <div style="padding:10px 12px;border-top:1px solid var(--border);display:flex;gap:8px;background:var(--warm-white)">
            <input id="chat-input" type="text" placeholder="Ketik pesan..." maxlength="300"
                style="flex:1;padding:9px 14px;border-radius:20px;border:1.5px solid var(--border);background:var(--beige);color:var(--text-main);font-size:13px;font-family:inherit;outline:none;transition:border-color .2s"
                onfocus="this.style.borderColor='var(--sage)'" onblur="this.style.borderColor='var(--border)'"
                onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendChat();}">
            <button onclick="sendChat()" id="send-btn" style="width:38px;height:38px;background:var(--sage);border:none;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all .2s" onmouseover="this.style.background='var(--sage-dark)'" onmouseout="this.style.background='var(--sage)'">
                <i class="ti ti-send" style="color:#fff;font-size:16px"></i>
            </button>
        </div>
    </div>

    {{-- Buttons Row --}}
    <div style="display:flex;gap:10px;align-items:center">
        {{-- WhatsApp --}}
        @php $wa = \App\Models\Setting::get('whatsapp_number','6281234567890'); @endphp
        <a href="https://wa.me/{{ $wa }}" target="_blank"
           style="width:48px;height:48px;background:#25d366;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 20px rgba(37,211,102,.35);transition:all .2s;text-decoration:none"
           onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 28px rgba(37,211,102,.45)'"
           onmouseout="this.style.transform='none';this.style.boxShadow='0 4px 20px rgba(37,211,102,.35)'"
           title="Chat WhatsApp">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="#fff"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        </a>

        {{-- Chat toggle --}}
        <button onclick="toggleChat()" id="chat-toggle-btn"
           style="width:54px;height:54px;background:var(--sage);border:none;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 24px rgba(90,124,101,.4);cursor:pointer;transition:all .2s;position:relative"
           onmouseover="this.style.transform='translateY(-3px)';this.style.background='var(--sage-dark)'"
           onmouseout="this.style.transform='none';this.style.background='var(--sage)'">
            <i id="chat-icon" class="ti ti-message-chatbot" style="color:#fff;font-size:24px"></i>
            <span id="chat-notif-dot" style="position:absolute;top:2px;right:2px;width:12px;height:12px;background:#e07a5f;border-radius:50%;border:2px solid var(--cream);display:none;animation:notifPulse 2s infinite"></span>
        </button>
    </div>
</div>

<style>
.chat-msg{max-width:84%;padding:9px 13px;border-radius:14px;font-size:13px;line-height:1.55;word-break:break-word}
.chat-msg.bot{background:var(--warm-white);color:var(--text-main);border-bottom-left-radius:4px;align-self:flex-start;border:1px solid var(--border)}
.chat-msg.user{background:var(--sage);color:#fff;border-bottom-right-radius:4px;align-self:flex-end}
.chat-msg.typing{opacity:.6;font-style:italic}
.chat-quick-reply{display:flex;gap:6px;flex-wrap:wrap;align-self:flex-start;max-width:100%}
.chat-qr-btn{padding:5px 12px;border-radius:20px;border:1.5px solid var(--sage);background:transparent;font-size:11px;font-weight:600;cursor:pointer;color:var(--sage-dark);transition:all .15s;font-family:inherit}
.chat-qr-btn:hover{background:var(--sage);color:#fff}
@keyframes slideUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:none}}
@keyframes notifPulse{0%,100%{transform:scale(1)}50%{transform:scale(1.2)}}
</style>

<script>
let chatHistory = [];
let chatOpen = false;

function toggleChat() {
    chatOpen = !chatOpen;
    const w = document.getElementById('chat-window');
    const icon = document.getElementById('chat-icon');
    const dot  = document.getElementById('chat-notif-dot');
    w.style.display = chatOpen ? 'flex' : 'none';
    icon.className = chatOpen ? 'ti ti-x' : 'ti ti-message-chatbot';
    icon.style.fontSize = '24px';
    if (chatOpen) { dot.style.display='none'; scrollChat(); document.getElementById('chat-input').focus(); }
}

function scrollChat() {
    const c = document.getElementById('chat-messages');
    setTimeout(()=>{ c.scrollTop = c.scrollHeight; }, 50);
}

function addMsg(text, cls) {
    const el = document.createElement('div');
    el.className = 'chat-msg ' + cls;
    el.innerHTML = text.replace(/\*\*(.*?)\*\*/g,'<strong>$1</strong>').replace(/\n/g,'<br>');
    document.getElementById('chat-messages').appendChild(el);
    scrollChat();
    return el;
}

function addQuickReplies(replies) {
    if (!replies || !replies.length) return;
    const wrap = document.createElement('div');
    wrap.className = 'chat-quick-reply';
    replies.forEach(r => {
        const btn = document.createElement('button');
        btn.className = 'chat-qr-btn';
        btn.textContent = r;
        btn.onclick = () => { wrap.remove(); sendQuickReply(r); };
        wrap.appendChild(btn);
    });
    document.getElementById('chat-messages').appendChild(wrap);
    scrollChat();
}

function sendQuickReply(text) {
    document.getElementById('chat-input').value = text;
    sendChat();
}

function sendChat() {
    const inp = document.getElementById('chat-input');
    const msg = inp.value.trim();
    if (!msg) return;
    inp.value = '';
    inp.disabled = true;
    document.getElementById('send-btn').disabled = true;

    addMsg(msg, 'user');
    chatHistory.push({ role: 'user', content: msg });

    // Hide default quick replies
    const qrDefault = document.getElementById('quick-replies');
    if (qrDefault) qrDefault.style.display = 'none';

    const typing = addMsg('Mengetik...', 'bot typing');

    fetch('/api/chatbot', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ message: msg, history: chatHistory.slice(-6) })
    })
    .then(r => r.json())
    .then(d => {
        typing.remove();
        inp.disabled = false;
        document.getElementById('send-btn').disabled = false;
        inp.focus();
        const botMsg = d.reply || 'Maaf, ada gangguan sementara. Coba lagi ya!';
        addMsg(botMsg, 'bot');
        chatHistory.push({ role: 'assistant', content: botMsg });
        if (d.quick_replies && d.quick_replies.length) addQuickReplies(d.quick_replies);
    })
    .catch(() => {
        typing.remove();
        inp.disabled = false;
        document.getElementById('send-btn').disabled = false;
        addMsg('Maaf, ada gangguan koneksi. Coba beberapa saat lagi ya! 🙏', 'bot');
    });
}

// Show notif dot after 5s if chat not opened
setTimeout(() => {
    if (!chatOpen) {
        document.getElementById('chat-notif-dot').style.display = 'block';
    }
}, 5000);
</script>


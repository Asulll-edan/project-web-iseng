<div id="chat-wrap" style="position:fixed;bottom:28px;right:28px;z-index:400;display:flex;flex-direction:column;align-items:flex-end;gap:10px">

    {{-- Chat window --}}
    <div id="chat-window" style="display:none;width:320px;background:var(--warm-white);border-radius:var(--radius-lg);border:1px solid var(--border);box-shadow:var(--shadow-lg);overflow:hidden;flex-direction:column;max-height:440px">
        <div style="background:var(--sage);padding:14px 16px;display:flex;align-items:center;justify-content:space-between">
            <div style="display:flex;align-items:center;gap:10px">
                <div style="width:34px;height:34px;background:rgba(255,255,255,.2);border-radius:50%;display:flex;align-items:center;justify-content:center">
                    <i class="ti ti-robot" style="color:#fff;font-size:18px"></i>
                </div>
                <div>
                    <div style="font-weight:700;font-size:14px;color:#fff">RAS Bot</div>
                    <div style="font-size:11px;color:rgba(255,255,255,.8)">Online sekarang</div>
                </div>
            </div>
            <button onclick="toggleChat()" style="background:none;border:none;cursor:pointer;color:rgba(255,255,255,.8)"><i class="ti ti-x" style="font-size:18px"></i></button>
        </div>

        <div id="chat-messages" style="flex:1;overflow-y:auto;padding:14px;display:flex;flex-direction:column;gap:10px;max-height:280px;min-height:120px">
            <div class="chat-msg bot">Halo! Saya RAS Bot 🤖 Ada yang bisa saya bantu?</div>
        </div>

        <div style="padding:12px;border-top:1px solid var(--border);display:flex;gap:8px">
            <input id="chat-input" type="text" placeholder="Ketik pertanyaan..." maxlength="200"
                style="flex:1;padding:8px 12px;border-radius:20px;border:1px solid var(--border);background:var(--beige);color:var(--text-main);font-size:13px;outline:none;font-family:inherit"
                onkeydown="if(event.key==='Enter')sendChat()">
            <button onclick="sendChat()" style="width:36px;height:36px;background:var(--sage);border:none;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="ti ti-send" style="color:#fff;font-size:16px"></i>
            </button>
        </div>
    </div>

    {{-- Buttons row --}}
    <div style="display:flex;gap:10px;align-items:center">
        {{-- WhatsApp --}}
        @php $wa = \App\Models\Setting::get('whatsapp_number','6281234567890'); @endphp
        <a href="https://wa.me/{{ $wa }}" target="_blank"
           style="width:48px;height:48px;background:#25d366;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 20px rgba(37,211,102,.35);transition:all .2s"
           onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='none'">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="#fff"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        </a>

        {{-- Chat toggle --}}
        <button onclick="toggleChat()"
           style="width:54px;height:54px;background:var(--sage);border:none;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 24px rgba(90,124,101,.4);cursor:pointer;transition:all .2s"
           onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='none'">
            <i id="chat-icon" class="ti ti-message-chatbot" style="color:#fff;font-size:24px"></i>
        </button>
    </div>
</div>

<style>
.chat-msg{max-width:82%;padding:9px 13px;border-radius:14px;font-size:13px;line-height:1.5;word-break:break-word}
.chat-msg.bot{background:var(--beige);color:var(--text-main);border-bottom-left-radius:4px;align-self:flex-start}
.chat-msg.user{background:var(--sage);color:#fff;border-bottom-right-radius:4px;align-self:flex-end}
.chat-msg.typing{opacity:.6}
</style>
<script>
function toggleChat(){
    const w=document.getElementById('chat-window');
    const icon=document.getElementById('chat-icon');
    const show=w.style.display==='none'||w.style.display==='';
    w.style.display=show?'flex':'none';
    icon.className=show?'ti ti-x':'ti ti-message-chatbot';
}
function sendChat(){
    const inp=document.getElementById('chat-input');
    const msg=inp.value.trim();
    if(!msg)return;
    addMsg(msg,'user');
    inp.value='';
    const typing=addMsg('...','bot typing');
    fetch('/api/chatbot',{method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Content-Type':'application/json'},body:JSON.stringify({message:msg})})
    .then(r=>r.json()).then(d=>{typing.remove();addMsg(d.reply,'bot');}).catch(()=>{typing.remove();addMsg('Maaf, ada gangguan sementara.','bot');});
}
function addMsg(text,cls){
    const el=document.createElement('div');
    el.className=`chat-msg ${cls}`;
    el.textContent=text;
    const container=document.getElementById('chat-messages');
    container.appendChild(el);
    container.scrollTop=container.scrollHeight;
    return el;
}
</script>
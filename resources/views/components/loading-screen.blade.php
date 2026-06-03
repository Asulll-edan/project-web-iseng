<div id="loading-screen" style="position:fixed;inset:0;background:var(--cream);z-index:9999;display:flex;flex-direction:column;align-items:center;justify-content:center;transition:opacity .5s ease">
    <div style="text-align:center">
        <div style="width:56px;height:56px;background:var(--sage);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;animation:loadPulse 1.2s ease infinite">
                    <img src="{{ asset('images/logo.png') }}" alt="Rumahnya Anak Sekolah" style="width:100%;height:auto">
        </div>
        <div style="font-family:'INeedCoffee',serif;font-size:22px;font-weight:600;color:var(--text-main);margin-bottom:4px">Rumahnya Anak Sekolah</div>
        <div style="font-size:13px;color:var(--text-muted)">Rumah keduanya anak sekolah</div>
        <div style="margin-top:24px;display:flex;gap:6px;justify-content:center">
            <span style="width:8px;height:8px;border-radius:50%;background:var(--sage);animation:loadDot 1.2s ease infinite 0s"></span>
            <span style="width:8px;height:8px;border-radius:50%;background:var(--sage);animation:loadDot 1.2s ease infinite .2s"></span>
            <span style="width:8px;height:8px;border-radius:50%;background:var(--sage);animation:loadDot 1.2s ease infinite .4s"></span>
        </div>
    </div>
</div>
<style>
@keyframes loadPulse{0%,100%{transform:scale(1)}50%{transform:scale(.92)}}
@keyframes loadDot{0%,100%{opacity:.3;transform:translateY(0)}50%{opacity:1;transform:translateY(-5px)}}
</style>
<script>
window.addEventListener('load',()=>{
    const ls=document.getElementById('loading-screen');
    if(ls){ls.style.opacity='0';setTimeout(()=>ls.remove(),500);}
});
</script>


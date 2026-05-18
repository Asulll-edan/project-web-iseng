<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Kitchen Display — RAS</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Plus Jakarta Sans',sans-serif;background:#0f1a16;color:#e8f0eb;min-height:100vh;overflow-x:hidden}
.kitchen-header{background:#1a2b22;border-bottom:1px solid rgba(90,124,101,.2);padding:14px 24px;display:flex;align-items:center;justify-content:space-between}
.kitchen-brand{font-weight:700;font-size:18px;color:#8aaa92;display:flex;align-items:center;gap:10px}
.kitchen-clock{font-size:22px;font-weight:700;color:#e8f0eb;font-variant-numeric:tabular-nums}
.kitchen-body{padding:20px;min-height:calc(100vh - 64px)}
.live-badge{display:flex;align-items:center;gap:6px;background:rgba(74,222,128,.1);border:1px solid rgba(74,222,128,.2);border-radius:20px;padding:5px 14px;font-size:12px;font-weight:600;color:#4ade80}
.live-dot{width:7px;height:7px;border-radius:50%;background:#4ade80;animation:pulse 1.5s infinite}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}
</style>
@stack('styles')
</head>
<body>
<div class="kitchen-header">
    <div class="kitchen-brand">
        <i class="ti ti-chef-hat" style="font-size:24px"></i>
        RAS Kitchen Display
    </div>
    <div class="live-badge"><span class="live-dot"></span> LIVE</div>
    <div class="kitchen-clock" id="kitchen-clock">--:--:--</div>
</div>
<div class="kitchen-body">@yield('content')</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
function ajax(url,method='GET',data=null){
    const opts={method,headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json','Content-Type':'application/json'}};
    if(data)opts.body=JSON.stringify(data);
    return fetch(url,opts).then(r=>r.json());
}
// Clock
function tick(){
    const now=new Date();
    document.getElementById('kitchen-clock').textContent=now.toLocaleTimeString('id-ID');
}
tick(); setInterval(tick,1000);
</script>
@stack('scripts')
</body>
</html>
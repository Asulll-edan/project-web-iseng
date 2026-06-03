{{--
    Standalone toast component — sudah inline di layouts/app.blade.php via JS showToast().
    File ini tersedia sebagai blade component jika perlu dipanggil manual dari view.
--}}
@if(session('success'))
<script>document.addEventListener('DOMContentLoaded',function(){if(typeof showToast==='function')showToast(@json(session('success')),'success');})</script>
@endif
@if(session('error'))
<script>document.addEventListener('DOMContentLoaded',function(){if(typeof showToast==='function')showToast(@json(session('error')),'error');})</script>
@endif
@if(session('info'))
<script>document.addEventListener('DOMContentLoaded',function(){if(typeof showToast==='function')showToast(@json(session('info')),'info');})</script>
@endif


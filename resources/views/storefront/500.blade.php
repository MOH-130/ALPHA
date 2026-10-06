@extends('layouts.storefront')

@section('content')
<div class="container" style="padding-top: 4rem; padding-bottom: 6rem; text-align:center; max-width:600px;">
    <div style="font-size:5rem; font-weight:900; color:var(--color-danger); line-height:1; margin-bottom:1rem; text-shadow:0 0 25px rgba(239,68,68,0.4);">500</div>
    <h1 style="font-size:1.75rem; font-weight:800; margin-bottom:1rem;">عذراً، حدث خطأ غير متوقع</h1>
    <p style="color:var(--text-muted); font-size:1.05rem; line-height:1.7; margin-bottom:2rem;">
        فريق الدعم الفني يعمل على حل المشكلة فوراً. يرجى إعادة المحاولة بعد لحظات أو العودة إلى الصفحة الرئيسية.
    </p>
    <div style="display:flex; justify-content:center; gap:12px; flex-wrap:wrap;">
        <a href="<?= url('/') ?>" class="btn btn-primary btn-lg">العودة للرئيسية 🏠</a>
        <a href="<?= url('contact') ?>" class="btn btn-outline btn-lg" style="border-color:rgba(255,255,255,0.2);">تواصل مع الدعم 💬</a>
    </div>
</div>
@endsection

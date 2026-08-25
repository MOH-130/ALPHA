@extends('layouts.storefront')

@section('content')
<div class="container" style="padding-top: 4rem; padding-bottom: 6rem; text-align:center; max-width:600px;">
    <div style="font-size:5rem; font-weight:900; color:var(--color-primary); line-height:1; margin-bottom:1rem;">404</div>
    <h1 style="font-size:1.75rem; font-weight:800; margin-bottom:1rem;">الصفحة غير موجودة</h1>
    <p style="color:var(--text-muted); font-size:1.05rem; line-height:1.7; margin-bottom:2rem;">
        عذراً، الصفحة التي تحاول الوصول إليها قد تكون حُذفت أو تم تغيير رابطها.
    </p>
    <a href="<?= url('/') ?>" class="btn btn-primary btn-lg">العودة للرئيسية 🏠</a>
</div>
@endsection

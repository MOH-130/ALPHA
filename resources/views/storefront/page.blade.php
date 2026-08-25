@extends('layouts.storefront')

@section('content')
<div class="container" style="padding-top: 3rem; padding-bottom: 5rem; max-width: 900px;">
    <div style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:3rem 2.5rem; box-shadow:var(--shadow-lg);">
        <h1 style="font-size:2rem; font-weight:900; margin-bottom:1.5rem; color:#fff; border-bottom:2px solid var(--border-subtle); padding-bottom:1rem;">
            <?= $page->getTitleAttribute() ?>
        </h1>
        <div style="line-height:1.9; color:#cbd5e1; font-size:1.05rem;">
            {!! $page->getContentAttribute() !!}
        </div>
    </div>
</div>
@endsection

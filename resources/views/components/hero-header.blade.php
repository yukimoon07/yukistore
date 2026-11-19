<section class="yk-page-hero">
    <div class="container">
        @if(!isset($hideBreadcrumb) || !$hideBreadcrumb)
            <nav class="yk-breadcrumb" aria-label="Breadcrumb">
                <a href="/"><i class="fa-solid fa-house" aria-hidden="true"></i> Trang chủ</a>
                <span class="yk-breadcrumb__sep">/</span>
                <span class="yk-breadcrumb__current">{{ $title }}</span>
            </nav>
        @endif

        <h1 class="yk-page-hero__title">{{ $title }}</h1>

        @if (isset($description) && $description)
            <div class="yk-page-hero__desc">{!! $description !!}</div>
        @endif
    </div>
</section>

@once
@push('css')
<style>
    .yk-page-hero {
        padding: var(--space-6) 0 var(--space-4);
        border-bottom: 1px solid var(--yuki-line);
        background: linear-gradient(180deg, var(--yuki-ink-900), transparent);
    }
    .yk-page-hero__title {
        font: var(--font-h1);
        margin: 0 0 var(--space-2);
        letter-spacing: -0.02em;
    }
    .yk-page-hero__desc {
        max-width: 72ch;
        color: var(--yuki-text-soft);
        font: var(--font-body-text);
    }
    .yk-page-hero__desc p:last-child { margin-bottom: 0; }
</style>
@endpush
@endonce

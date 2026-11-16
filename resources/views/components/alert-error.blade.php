@if (session('error'))
    <div class="service__alert service__alert--error">
        <i class="fas fa-exclamation-circle"></i>
        <div>
            <span>{{ session('error') }}</span>
        </div>
        <button type="button" class="service__alert-close">&times;</button>
    </div>
@endif

@unless (empty($errors->all()))
    <div class="service__alert service__alert--error">
        <i class="fas fa-exclamation-circle"></i>
        <div>
            @foreach ($errors->all() as $err)
                <div>{{ $err }}</div>
            @endforeach
        </div>
        <button type="button" class="service__alert-close">&times;</button>
    </div>
@endunless

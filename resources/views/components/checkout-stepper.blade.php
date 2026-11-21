@props([
    'id' => 'steps-' . uniqid(),
    'activeStep' => 2, // Zero-based index (arrival state: 2, Payment)
    'steps' => null,
    'clickable' => true,
])

@php
    $defaultSteps = [
        [
            'title' => 'Chọn gói',
            'desc' => 'Dịch vụ game',
        ],
        [
            'title' => 'Thông tin',
            'desc' => 'Tài khoản & Server',
        ],
        [
            'title' => 'Thanh toán',
            'desc' => 'Payment & Ưu đãi',
        ],
        [
            'title' => 'Hoàn tất',
            'desc' => 'Xác nhận & Bàn giao',
        ],
    ];
    $stages = $steps ?: $defaultSteps;
    $total = count($stages);
@endphp

<style>
.steps-nav {
    width: 100%;
    margin: 0 0 24px 0;
    user-select: none;
}

.steps-indicator {
    display: flex;
    align-items: center;
    list-style: none;
    margin: 0;
    padding: 12px 18px;
    background: rgba(19, 20, 28, 0.75);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 18px;
    box-shadow: 0 8px 24px -4px rgba(0, 0, 0, 0.35);
}

[data-theme="light"] .steps-indicator {
    background: #ffffff;
    border-color: rgba(22, 21, 27, 0.09);
    box-shadow: 0 4px 20px -2px rgba(40, 30, 80, 0.08);
}

.step-stage {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: default;
    transition: transform 0.2s ease, opacity 0.2s ease;
}

.step-stage.is-clickable {
    cursor: pointer;
}

.step-stage.is-clickable:hover {
    transform: translateY(-1px);
}

.step-indicator-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

/* Steps.Indicator (Material UI StepIcon circle) */
.step-indicator-circle {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    font-weight: 800;
    font-variant-numeric: tabular-nums;
    transition: all 0.3s cubic-bezier(0.34, 1.45, 0.64, 1);
}

/* Completed stage */
.step-stage.is-completed .step-indicator-circle {
    background: var(--yuki-success, #10b981);
    color: #ffffff;
    box-shadow: 0 2px 10px rgba(16, 185, 129, 0.35);
}

.step-stage.is-completed .step-title {
    color: var(--yuki-text, #F4F0E8);
    font-weight: 600;
}

.step-stage.is-completed .step-desc {
    color: var(--yuki-text-muted, rgba(244, 240, 232, 0.5));
}

[data-theme="light"] .step-stage.is-completed .step-title {
    color: #16151B;
}

/* Current active stage (aria-current="step") */
.step-stage.is-current .step-indicator-circle {
    background: linear-gradient(135deg, var(--yuki-gold, #E8B33C), #F5C453);
    color: var(--yuki-gold-ink, #241C08);
    box-shadow: 0 0 0 3px rgba(232, 179, 60, 0.25), 0 4px 14px rgba(232, 179, 60, 0.4);
    transform: scale(1.08);
}

.step-stage.is-current .step-title {
    color: var(--yuki-gold, #E8B33C);
    font-weight: 750;
}

.step-stage.is-current .step-desc {
    color: var(--yuki-text-soft, rgba(244, 240, 232, 0.75));
}

[data-theme="light"] .step-stage.is-current .step-title {
    color: #C2922A;
}

/* Upcoming muted stage */
.step-stage.is-upcoming {
    opacity: 0.55;
}

.step-stage.is-upcoming .step-indicator-circle {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.14);
    color: var(--yuki-text-muted, rgba(244, 240, 232, 0.45));
}

[data-theme="light"] .step-stage.is-upcoming .step-indicator-circle {
    background: #F5F3EF;
    border-color: rgba(22, 21, 27, 0.15);
    color: #8E8A97;
}

.step-stage.is-upcoming .step-title {
    color: var(--yuki-text-muted, rgba(244, 240, 232, 0.5));
    font-weight: 500;
}

.step-stage.is-upcoming .step-desc {
    color: var(--yuki-text-muted, rgba(244, 240, 232, 0.35));
}

/* StepConnector (Steps.Separator joining adjacent stages) */
.step-connector {
    flex: 1 1 0;
    min-width: 24px;
    height: 2px;
    margin: 0 12px;
    position: relative;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.1);
    overflow: hidden;
    transition: background 0.3s ease;
}

[data-theme="light"] .step-connector {
    background: rgba(22, 21, 27, 0.1);
}

.step-connector.is-filled {
    background: linear-gradient(90deg, var(--yuki-success, #10b981), var(--yuki-gold, #E8B33C));
}

.step-connector-line {
    position: absolute;
    inset: 0;
}

/* Content labels */
.step-content {
    display: flex;
    flex-direction: column;
    gap: 2px;
    line-height: 1.2;
}

.step-title {
    font-size: 0.88rem;
    letter-spacing: -0.01em;
    white-space: nowrap;
    transition: color 0.2s ease;
}

.step-desc {
    font-size: 0.72rem;
    white-space: nowrap;
    transition: color 0.2s ease;
}

/* Checkmark SVG Icon */
.step-check-svg {
    width: 16px;
    height: 16px;
    stroke-dasharray: 24;
    stroke-dashoffset: 0;
    animation: step-check-draw 0.35s ease forwards;
}

@keyframes step-check-draw {
    from { stroke-dashoffset: 24; }
    to { stroke-dashoffset: 0; }
}

/* Screen reader text */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .steps-indicator {
        padding: 10px 12px;
        overflow-x: auto;
    }
    .step-desc {
        display: none;
    }
    .step-connector {
        min-width: 14px;
        margin: 0 8px;
    }
    .step-indicator-circle {
        width: 30px;
        height: 30px;
        font-size: 0.8rem;
    }
    .step-title {
        font-size: 0.8rem;
    }
}

@media (prefers-reduced-motion: reduce) {
    .step-stage, .step-indicator-circle, .step-connector, .step-check-svg {
        transition: none !important;
        animation: none !important;
    }
}
</style>

<nav class="steps-nav" aria-label="Tiến trình thanh toán">
    <ol class="steps-indicator" id="{{ $id }}" data-active="{{ $activeStep }}">
        @foreach($stages as $index => $stage)
            @php
                $isCompleted = $index < $activeStep;
                $isCurrent = $index === (int)$activeStep;
                $isUpcoming = $index > $activeStep;
                $stateClass = $isCompleted ? 'is-completed' : ($isCurrent ? 'is-current' : 'is-upcoming');
            @endphp

            {{-- StepConnector between stages --}}
            @if($index > 0)
                <li class="step-connector {{ $index <= $activeStep ? 'is-filled' : 'is-muted' }}" 
                    id="{{ $id }}-connector-{{ $index }}" 
                    aria-hidden="true">
                    <span class="step-connector-line"></span>
                </li>
            @endif

            {{-- Stage Item --}}
            <li class="step-stage {{ $stateClass }} {{ $clickable ? 'is-clickable' : '' }}" 
                id="{{ $id }}-stage-{{ $index }}"
                data-step="{{ $index }}"
                @if($isCurrent) aria-current="step" @endif>
                <div class="step-indicator-wrapper">
                    <div class="step-indicator-circle" id="{{ $id }}-circle-{{ $index }}">
                        @if($isCompleted)
                            <svg class="step-check-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        @else
                            <span class="step-number">{{ $index + 1 }}</span>
                        @endif
                    </div>
                </div>
                <div class="step-content">
                    <span class="step-title">{{ $stage['title'] }}</span>
                    @if(!empty($stage['desc']))
                        <span class="step-desc">{{ $stage['desc'] }}</span>
                    @endif
                </div>
                <span class="sr-only" id="{{ $id }}-sr-{{ $index }}">
                    @if($isCompleted) (Đã hoàn thành) @elseif($isCurrent) (Bước hiện tại: {{ $stage['title'] }}) @else (Bước kế tiếp) @endif
                </span>
            </li>
        @endforeach
    </ol>
</nav>

<script>
(function () {
    var root = document.getElementById('{{ $id }}');
    if (!root) return;

    var total = {{ $total }};
    var currentActive = {{ (int)$activeStep }};
    var isClickable = {{ $clickable ? 'true' : 'false' }};

    var checkSvgHtml = '<svg class="step-check-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>';

    function setStep(newStep) {
        newStep = Math.max(0, Math.min(total - 1, parseInt(newStep, 10) || 0));
        currentActive = newStep;
        root.setAttribute('data-active', String(currentActive));

        for (var i = 0; i < total; i++) {
            var stage = document.getElementById('{{ $id }}-stage-' + i);
            var circle = document.getElementById('{{ $id }}-circle-' + i);
            var sr = document.getElementById('{{ $id }}-sr-' + i);
            if (!stage || !circle) continue;

            stage.classList.remove('is-completed', 'is-current', 'is-upcoming');
            stage.removeAttribute('aria-current');

            if (i < currentActive) {
                // Completed stage
                stage.classList.add('is-completed');
                circle.innerHTML = checkSvgHtml;
                if (sr) sr.textContent = '(Đã hoàn thành)';
            } else if (i === currentActive) {
                // Current active stage
                stage.classList.add('is-current');
                stage.setAttribute('aria-current', 'step');
                circle.innerHTML = '<span class="step-number">' + (i + 1) + '</span>';
                if (sr) sr.textContent = '(Bước hiện tại)';
            } else {
                // Upcoming muted stage
                stage.classList.add('is-upcoming');
                circle.innerHTML = '<span class="step-number">' + (i + 1) + '</span>';
                if (sr) sr.textContent = '(Bước kế tiếp)';
            }

            // Connector updates
            if (i > 0) {
                var conn = document.getElementById('{{ $id }}-connector-' + i);
                if (conn) {
                    if (i <= currentActive) {
                        conn.classList.add('is-filled');
                        conn.classList.remove('is-muted');
                    } else {
                        conn.classList.remove('is-filled');
                        conn.classList.add('is-muted');
                    }
                }
            }
        }

        // Dispatch change event
        root.dispatchEvent(new CustomEvent('step:change', {
            bubbles: true,
            detail: { activeStep: currentActive }
        }));
    }

    if (isClickable) {
        root.querySelectorAll('.step-stage').forEach(function (el) {
            el.addEventListener('click', function () {
                var idx = el.getAttribute('data-step');
                if (idx !== null) {
                    setStep(idx);
                }
            });
        });
    }

    // Expose programmatic controls
    root.setActiveStep = setStep;
    root.getActiveStep = function () {
        return currentActive;
    };
})();
</script>

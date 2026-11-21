<!-- Lucian UX/UI Prompt #016 · MIT License (c) 2026 Seokwoooo · https://lucianlabs.dev/uxui-prompt/#016 -->
@props([
    'id' => 'u016-' . uniqid(),
    'question' => 'How was your order?',
    'value' => 0,
    'inputName' => 'rating',
    'words' => null,
    'colors' => null,
    'compact' => false,
])

@php
    $defaultWords = ['Tap a star to rate', 'Poor', 'Fair', 'Good', 'Great', 'Excellent'];
    $activeWords = $words ?: $defaultWords;
@endphp

<style>
:root{
  color-scheme:light;
  --ux-page:#F4F2EE;
  --ux-surface:#FFFFFF;
  --ux-surface-2:#F5F3EF;
  --ux-surface-3:#EAE7E1;
  --ux-ink:#16151B;
  --ux-ink-2:#57535F;
  --ux-ink-3:#8E8A97;
  --ux-line:rgba(22,21,27,.09);
  --ux-line-2:rgba(22,21,27,.17);
  --ux-accent:var(--yuki-gold-deep, #C2922A);
  --ux-accent-2:var(--yuki-gold, #E8B33C);
  --ux-accent-soft:rgba(194,146,42,.12);
  --ux-accent-ink:#FFFFFF;
  --ux-mint:#119C72;
  --ux-warn:#D6731E;
  --ux-danger:#D93A55;
  --ux-glass:rgba(255,255,255,.58);
  --ux-glass-line:rgba(255,255,255,.85);
  --ux-shadow:rgba(40,30,80,.14);
  --ux-shadow-strong:rgba(40,30,80,.3);
  --ux-grad:linear-gradient(120deg,var(--yuki-gold-deep, #C2922A),var(--yuki-gold, #E8B33C) 55%,#1FB89A);
  --ux-ease:cubic-bezier(.2,.8,.2,1);
  --ux-spring:cubic-bezier(.34,1.45,.64,1);
}
@media (prefers-color-scheme:dark){
  :root:not([data-theme="light"]){
    color-scheme:dark;
    --ux-page:var(--yuki-ink-1000, #07080D);
    --ux-surface:var(--yuki-ink-900, #13141C);
    --ux-surface-2:var(--yuki-ink-850, #1A1B25);
    --ux-surface-3:var(--yuki-ink-800, #252632);
    --ux-ink:var(--yuki-text, #F4F0E8);
    --ux-ink-2:var(--yuki-text-soft, rgba(244,240,232,.68));
    --ux-ink-3:var(--yuki-text-muted, rgba(244,240,232,.42));
    --ux-line:var(--yuki-line, rgba(255,255,255,.08));
    --ux-line-2:var(--yuki-line-strong, rgba(255,255,255,.16));
    --ux-accent:var(--yuki-gold, #E8B33C);
    --ux-accent-2:var(--yuki-gold-strong, #F5C453);
    --ux-accent-soft:rgba(232,179,60,.16);
    --ux-accent-ink:var(--yuki-gold-ink, #241C08);
    --ux-mint:var(--yuki-success, #3FBF7F);
    --ux-warn:var(--yuki-warning, #FFB26B);
    --ux-danger:var(--yuki-danger, #FF7D8C);
    --ux-glass:rgba(255,255,255,.07);
    --ux-glass-line:rgba(255,255,255,.18);
    --ux-shadow:rgba(0,0,0,.45);
    --ux-shadow-strong:rgba(0,0,0,.72);
    --ux-grad:linear-gradient(120deg,var(--yuki-gold, #E8B33C),#F5C453 55%,#3FBF7F);
  }
}
:root[data-theme="dark"]{
  color-scheme:dark;
  --ux-page:var(--yuki-ink-1000, #07080D);
  --ux-surface:var(--yuki-ink-900, #13141C);
  --ux-surface-2:var(--yuki-ink-850, #1A1B25);
  --ux-surface-3:var(--yuki-ink-800, #252632);
  --ux-ink:var(--yuki-text, #F4F0E8);
  --ux-ink-2:var(--yuki-text-soft, rgba(244,240,232,.68));
  --ux-ink-3:var(--yuki-text-muted, rgba(244,240,232,.42));
  --ux-line:var(--yuki-line, rgba(255,255,255,.08));
  --ux-line-2:var(--yuki-line-strong, rgba(255,255,255,.16));
  --ux-accent:var(--yuki-gold, #E8B33C);
  --ux-accent-2:var(--yuki-gold-strong, #F5C453);
  --ux-accent-soft:rgba(232,179,60,.16);
  --ux-accent-ink:var(--yuki-gold-ink, #241C08);
  --ux-mint:var(--yuki-success, #3FBF7F);
  --ux-warn:var(--yuki-warning, #FFB26B);
  --ux-danger:var(--yuki-danger, #FF7D8C);
  --ux-glass:rgba(255,255,255,.07);
  --ux-glass-line:rgba(255,255,255,.18);
  --ux-shadow:rgba(0,0,0,.45);
  --ux-shadow-strong:rgba(0,0,0,.72);
  --ux-grad:linear-gradient(120deg,var(--yuki-gold, #E8B33C),#F5C453 55%,#3FBF7F);
}

.ux,.ux *,.ux *::before,.ux *::after{box-sizing:border-box}
.ux{
  font-family:inherit;
  color:var(--ux-ink);-webkit-font-smoothing:antialiased;letter-spacing:-.01em;
  -webkit-tap-highlight-color:transparent;
  word-break:keep-all;overflow-wrap:break-word;
}

:where(.ux) :is(button,input,textarea,select){font:inherit;color:inherit}
:where(.ux) button{cursor:pointer}
.ux :focus-visible{outline:2px solid var(--ux-accent);outline-offset:3px}
.ux [hidden]{display:none!important}

@media (prefers-reduced-motion:reduce){
  .ux,.ux *,.ux *::before,.ux *::after{
    animation-duration:.001ms!important;animation-iteration-count:1!important;
    transition-duration:.001ms!important;scroll-behavior:auto!important;
  }
}
</style>

<style>
.u016{--gold:linear-gradient(160deg,#FFD66B,#FFAA2C 60%,#FF7A45);position:relative;width:100%;max-width:340px;height:220px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:16px;margin:0 auto;user-select:none}
.u016-q{margin:0;font-size:15px;font-weight:650;color:var(--ux-ink-2);text-align:center}
.u016-stars{display:flex;gap:6px;border-radius:16px}
.u016-stars:focus-visible{outline:2px solid var(--ux-accent);outline-offset:6px}
.u016-s{position:relative;width:52px;height:52px;padding:0;border:0;background:none;transition:transform .35s var(--ux-spring);cursor:pointer}
.u016-s svg{position:absolute;inset:0;width:100%;height:100%;overflow:visible}
.u016-s .bg{fill:var(--ux-surface-3);stroke:var(--ux-line-2);stroke-width:1}
.u016-s .fg{transform-origin:50% 55%;transform:scale(0);transition:transform .4s var(--ux-spring)}
.u016-s.is-pre .fg{transform:scale(.72);opacity:.55}
.u016-s.is-on .fg{transform:scale(1);opacity:1}
.u016-s:hover{transform:scale(1.12) rotate(-6deg)}
.u016-s.is-pop{animation:u016-pop .55s var(--ux-spring)}
@keyframes u016-pop{0%{transform:scale(1)}35%{transform:scale(1.32) rotate(12deg)}100%{transform:scale(1)}}
.u016-s i{position:absolute;left:50%;top:50%;width:6px;height:6px;margin:-3px;border-radius:50%;background:#FFB547;opacity:0;pointer-events:none}
.u016-s.is-pop i{animation:u016-spark .6s var(--ux-ease) forwards}
@keyframes u016-spark{0%{opacity:1;transform:rotate(var(--a)) translateY(-14px) scale(1)}100%{opacity:0;transform:rotate(var(--a)) translateY(-40px) scale(.3)}}
.u016-out{display:flex;align-items:baseline;gap:10px;height:40px}
.u016-score{font-size:34px;font-weight:800;letter-spacing:-.04em;font-variant-numeric:tabular-nums;transition:color .4s var(--ux-ease)}
.u016-word{position:relative;height:24px;width:160px;overflow:hidden;font-size:16px;font-weight:700}
.u016-word span{position:absolute;left:0;top:0;line-height:24px;white-space:nowrap;transition:transform .5s var(--ux-spring),opacity .3s var(--ux-ease)}
</style>

<div class="ux u016" id="{{ $id }}">
  <p class="u016-q">{{ $question }}</p>
  <div class="u016-stars" role="slider" tabindex="0" aria-label="Rating" aria-valuemin="0" aria-valuemax="5" aria-valuenow="{{ $value }}" aria-valuetext="{{ $value ? $value . ' stars' : 'Not rated' }}">
    <svg width="0" height="0" style="position:absolute" aria-hidden="true"><defs><linearGradient id="u016-g-{{ $id }}" x1="0" y1="0" x2=".6" y2="1"><stop offset="0" stop-color="#FFD66B"/><stop offset=".6" stop-color="#FFAA2C"/><stop offset="1" stop-color="#FF7A45"/></linearGradient></defs></svg>
  </div>
  <div class="u016-out" aria-hidden="true">
    <span class="u016-score">{{ number_format($value, 1) }}</span>
    <span class="u016-word"><span>{{ $activeWords[$value] ?? $activeWords[0] }}</span></span>
  </div>

  @if($inputName)
    <input type="hidden" name="{{ $inputName }}" class="u016-val-input" value="{{ $value }}">
  @endif
</div>

<script>
(function () {
  var root = document.getElementById('{{ $id }}');
  if (!root) return;
  var wrap = root.querySelector('.u016-stars');
  var score = root.querySelector('.u016-score');
  var word = root.querySelector('.u016-word');
  var hiddenInp = root.querySelector('.u016-val-input');
  var rm = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var gid = 'u016-g-{{ $id }}';
  
  var WORDS = @json($activeWords);
  var COLORS = @json($colors) || ['var(--ux-ink-3)', '#E5484D', '#F07A3E', '#E8A317', '#2FB37A', '#7B61FF'];
  var STAR = 'M26 4.5l6.4 13.2 14.4 2-10.5 10.1 2.6 14.3L26 37.3l-12.9 6.8 2.6-14.3L5.2 19.7l14.4-2z';
  var initialVal = {{ (int)$value }};

  var stars = [];
  for (var i = 0; i < 5; i++) {
    var b = document.createElement('span');
    b.className = 'u016-s' + (i < initialVal ? ' is-on' : '');
    var sparks = '';
    for (var k = 0; k < 5; k++) sparks += '<i style="--a:' + (k * 72) + 'deg"></i>';
    b.innerHTML = '<svg viewBox="0 0 52 52"><path class="bg" d="' + STAR + '" stroke-linejoin="round"/><path class="fg" d="' + STAR + '" style="fill:url(#' + gid + ')"/></svg>' + sparks;
    b.dataset.v = i + 1;
    wrap.appendChild(b);
    stars.push(b);
  }

  var val = initialVal;
  if (val > 0) {
    score.style.color = COLORS[val] || '';
  }

  function preview(n) { 
    stars.forEach(function (s, i) { 
      s.classList.toggle('is-pre', i < n && i >= val); 
    }); 
  }

  function setWord(n) {
    var old = word.lastElementChild;
    var s = document.createElement('span');
    s.textContent = WORDS[n] || '';
    s.style.color = COLORS[n] || '';
    s.style.transform = 'translateY(100%)'; 
    s.style.opacity = '0';
    word.appendChild(s);
    void s.offsetWidth;
    s.style.transform = 'none'; 
    s.style.opacity = '1';
    if (old) {
      old.style.transform = 'translateY(-100%)'; 
      old.style.opacity = '0';
      setTimeout(function () { old.remove(); }, 500);
    }
  }

  function set(n) {
    if (n === val) return;
    var prev = val;
    val = n;
    stars.forEach(function (s, i) {
      var on = i < n;
      s.classList.remove('is-pre');
      if (on && i >= prev) {
        setTimeout(function () {
          if (!root.isConnected) return;
          s.classList.add('is-on');
          if (!rm) { 
            s.classList.remove('is-pop'); 
            void s.offsetWidth; 
            s.classList.add('is-pop'); 
          }
        }, rm ? 0 : (i - prev) * 70);
      } else if (!on) {
        s.classList.remove('is-on', 'is-pop');
      }
    });

    score.textContent = n.toFixed(1);
    score.style.color = n ? COLORS[n] : '';
    setWord(n);
    wrap.setAttribute('aria-valuenow', String(n));
    wrap.setAttribute('aria-valuetext', n ? n + ' stars, ' + (WORDS[n] || '') : 'Not rated');
    if (hiddenInp) hiddenInp.value = String(n);

    // Dispatch event
    var evt = new CustomEvent('u016:rate', {
      bubbles: true,
      detail: {
        value: n,
        word: WORDS[n] || '',
        color: COLORS[n] || ''
      }
    });
    root.dispatchEvent(evt);
  }

  stars.forEach(function (s) {
    s.addEventListener('pointerenter', function () { preview(+s.dataset.v); });
    s.addEventListener('click', function () { set(+s.dataset.v); });
  });

  wrap.addEventListener('pointerleave', function () { preview(0); });
  wrap.addEventListener('keydown', function (e) {
    var d = { ArrowRight: 1, ArrowUp: 1, ArrowLeft: -1, ArrowDown: -1 }[e.key];
    if (e.key === 'Home') { e.preventDefault(); set(0); }
    if (e.key === 'End') { e.preventDefault(); set(5); }
    if (d) { e.preventDefault(); set(Math.max(0, Math.min(5, val + d))); }
    if (/^[0-5]$/.test(e.key)) set(+e.key);
  });

  // Expose programmatic setter
  root.u016Set = function (n) {
    set(Math.max(0, Math.min(5, Number(n) || 0)));
  };
})();
</script>

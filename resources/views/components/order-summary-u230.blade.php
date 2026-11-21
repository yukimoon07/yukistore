<!-- Lucian UX/UI Prompt #230 · MIT License (c) 2026 Seokwoooo · https://lucianlabs.dev/uxui-prompt/#230 -->
@props([
    'id' => 'u230-' . uniqid(),
    'items' => null,
    'shipping' => 0,
    'shippingLabel' => 'Shipping',
    'currency' => 'đ',
    'title' => 'Order summary',
    'promoCode' => 'WELCOME10',
    'discountPercent' => 10,
    'buttonText' => 'Pay now',
    'formId' => null,
    'inputName' => 'giftcode',
    'loginUrl' => null,
])

@php
    $defaultItems = [
        ['Linen shirt', 'White · M', 39, 'linear-gradient(140deg,#E0E7FF,#A5B4FC)'],
        ['Cotton tote', 'Natural', 18, 'linear-gradient(140deg,#FEF3C7,#FCD34D)'],
    ];
    $displayItems = $items ?: $defaultItems;
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
  --ux-grad:linear-gradient(120deg,#7B61FF,#3D8BFF 55%,#1FB89A);
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
  font-family:"Space Grotesk","Be Vietnam Pro",-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;
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

.u230{position:relative;width:100%;max-width:320px;height:480px;border-radius:32px;overflow:hidden;background:var(--ux-surface);box-shadow:0 0 0 1px var(--ux-line),0 30px 60px -36px var(--ux-shadow-strong);color:var(--ux-ink);margin:0 auto}
.u230-hd{display:flex;align-items:baseline;justify-content:space-between;padding:24px 20px 0}
.u230-hd b{font-size:17px;font-weight:900;letter-spacing:-.03em}
.u230-hd span{font-size:11px;font-weight:750;color:var(--ux-ink-3)}
.u230-ls{margin:10px 0 0;padding:0 20px;list-style:none}
.u230-ls li{display:flex;align-items:center;gap:11px;padding:5px 0}
.u230-ls i{flex:none;width:42px;height:42px;border-radius:11px;display:flex;align-items:center;justify-content:center;overflow:hidden}
.u230-ls i img{width:100%;height:100%;object-fit:cover}
.u230-ls div{flex:1;min-width:0}
.u230-ls b{display:block;font-size:12.5px;font-weight:850;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.u230-ls small{display:block;margin-top:2px;font-size:10.5px;font-weight:650;color:var(--ux-ink-3);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.u230-ls em{font-style:normal;font-size:12px;font-weight:850;font-variant-numeric:tabular-nums}
.u230-pr{margin:8px 20px 0;padding:12px 0;border-top:1px solid var(--ux-line);border-bottom:1px solid var(--ux-line)}
.u230-lk{display:flex;align-items:center;gap:7px;width:100%;height:24px;padding:0;border:0;background:none;color:var(--ux-ink-2);font:inherit;font-size:12px;font-weight:800;cursor:pointer}
.u230-lk svg{width:15px;height:15px;color:var(--ux-accent)}
.u230-lk .cv{margin-left:auto;width:12px;height:12px;color:var(--ux-ink-3);transition:transform .3s}
.u230.is-open .u230-lk .cv{transform:rotate(180deg)}
.u230-lk:focus-visible,.u230 button:focus-visible{outline:2px solid var(--ux-accent);outline-offset:2px;border-radius:6px}
.u230-ex{display:grid;grid-template-rows:0fr;transition:grid-template-rows .4s cubic-bezier(.3,1,.4,1)}
.u230.is-open .u230-ex{grid-template-rows:1fr}
.u230-ex>div{min-height:0;overflow:hidden}
.u230-rw{display:flex;gap:6px;margin-top:10px}
.u230-in{flex:1;min-width:0;height:38px;padding:0 12px;border:0;border-radius:10px;background:var(--ux-surface-2);box-shadow:inset 0 0 0 1.5px var(--ux-line);color:var(--ux-ink);font:inherit;font-size:12px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;outline:none;transition:box-shadow .2s}
.u230-in::placeholder{letter-spacing:0;text-transform:none;font-weight:650;color:var(--ux-ink-3)}
.u230-in:focus{box-shadow:inset 0 0 0 1.5px var(--ux-accent)}
.u230-in.is-bad{box-shadow:inset 0 0 0 1.5px #E11D48;animation:u230-sh .4s}
@keyframes u230-sh{20%,60%{transform:translateX(-5px)}40%,80%{transform:translateX(5px)}}
.u230-ap{position:relative;flex:none;width:58px;height:38px;border:0;border-radius:10px;background:var(--ux-ink);color:var(--ux-surface);font:inherit;font-size:12px;font-weight:850;cursor:pointer}
.u230-ap i{position:absolute;left:50%;top:50%;width:14px;height:14px;margin:-7px;border-radius:50%;border:2px solid color-mix(in srgb,var(--ux-surface) 35%,transparent);border-top-color:var(--ux-surface);animation:u230-sp .7s linear infinite;display:none}
.u230-ap.is-busy span{visibility:hidden}
.u230-ap.is-busy i{display:block}
@keyframes u230-sp{to{transform:rotate(360deg)}}
.u230-er{height:0;margin:0;overflow:hidden;font-size:10.5px;font-weight:750;color:#E11D48;opacity:0;transition:opacity .2s,height .2s,margin .2s}
.u230-er.is-on{height:14px;margin-top:6px;opacity:1}
.u230-cp{display:none;align-items:center;gap:6px;margin-top:10px;height:34px;padding:0 6px 0 11px;border-radius:999px;background:color-mix(in srgb,#16A34A 12%,transparent);color:#15803D;font-size:11.5px;font-weight:850;width:max-content;max-width:100%}
.u230-cp svg{width:13px;height:13px}
.u230.is-on .u230-rw{display:none}
.u230.is-on .u230-cp{display:inline-flex;animation:u230-in .45s cubic-bezier(.3,1.5,.5,1)}
@keyframes u230-in{from{transform:scale(.7);opacity:0}}
.u230-rm{display:grid;place-items:center;width:24px;height:24px;padding:0;border:0;border-radius:50%;background:none;color:inherit;cursor:pointer}
.u230-rm:hover,.u230-rm.g-hov{background:color-mix(in srgb,#16A34A 18%,transparent)}
.u230-rm svg{width:10px;height:10px}
.u230-sm{margin:12px 20px 0;font-size:12px;font-weight:700;color:var(--ux-ink-2)}
.u230-ln{display:flex;justify-content:space-between;padding:4px 0;font-variant-numeric:tabular-nums}
.u230-dc{display:grid;grid-template-rows:0fr;opacity:0;transition:grid-template-rows .4s cubic-bezier(.3,1,.4,1),opacity .3s}
.u230-dc>div{min-height:0;overflow:hidden}
.u230.is-on .u230-dc{grid-template-rows:1fr;opacity:1}
.u230-dc .u230-ln{color:#16A34A;font-weight:850}
.u230-tt{display:flex;justify-content:space-between;align-items:flex-end;margin:10px 20px 0;padding-top:12px;border-top:1px solid var(--ux-line)}
.u230-tt>div:first-child{display:flex;flex-direction:column;gap:5px;font-size:13px;font-weight:850}
.u230-sv{align-self:flex-start;padding:3px 8px;border-radius:999px;background:#16A34A;color:#fff;font-size:10px;font-weight:900;transform:scale(0);transform-origin:0 50%;transition:transform .4s cubic-bezier(.3,1.7,.5,1)}
.u230.is-on .u230-sv{transform:none;transition-delay:.5s}
.u230-tv{display:flex;align-items:baseline;gap:7px}
.u230-tv s{display:inline-block;max-width:0;overflow:hidden;white-space:nowrap;font-size:11.5px;font-weight:700;color:var(--ux-ink-3);opacity:0;transition:max-width .4s,opacity .3s;font-variant-numeric:tabular-nums}
.u230.is-on .u230-tv s{max-width:120px;opacity:1}
.u230-tv b{display:block;font-size:22px;font-weight:900;letter-spacing:-.03em;font-variant-numeric:tabular-nums}
.u230-go{position:absolute;left:16px;right:16px;bottom:16px;height:48px;border:0;border-radius:14px;background:var(--ux-ink);color:var(--ux-surface);font:inherit;font-size:13.5px;font-weight:850;cursor:pointer;font-variant-numeric:tabular-nums;transition:transform .15s ease,filter .15s ease}
.u230-go:hover{filter:brightness(1.1)}
.u230-go:active{transform:scale(0.98)}
@media (prefers-reduced-motion: reduce){.u230-ex,.u230-dc,.u230-sv,.u230-tv s,.u230-lk .cv{transition:none}.u230-in.is-bad,.u230.is-on .u230-cp,.u230-ap i{animation:none}}
</style>

<div class="ux u230" id="{{ $id }}">
  <div class="u230-hd"><b>{{ $title }}</b><span class="u230-cnt">{{ count($displayItems) }} items</span></div>
  <ul class="u230-ls"></ul>
  <div class="u230-pr">
    <button type="button" class="u230-lk" aria-expanded="false">
      <svg viewBox="0 0 16 16" aria-hidden="true"><path d="M2 5.5V4a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v1.5a2.5 2.5 0 0 0 0 5V12a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1v-1.5a2.5 2.5 0 0 0 0-5z" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M9.5 3.5v9" stroke="currentColor" stroke-width="1.3" stroke-dasharray="1.5 1.5"/></svg>
      Have a promo code?
      <svg class="cv" viewBox="0 0 12 12" aria-hidden="true"><path d="M3 4.5l3 3 3-3" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </button>
    <div class="u230-ex"><div>
      <div class="u230-rw">
        <input class="u230-in" aria-label="Promo code" placeholder="Enter code" spellcheck="false" autocomplete="off">
        <button type="button" class="u230-ap"><span>Apply</span><i aria-hidden="true"></i></button>
      </div>
      <p class="u230-er" role="alert"></p>
      <span class="u230-cp">
        <svg viewBox="0 0 13 13" aria-hidden="true"><path d="M3 6.8l2.3 2.3L10 4.4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span class="u230-cn"></span>
        <button type="button" class="u230-rm" aria-label="Remove promo code">
          <svg viewBox="0 0 10 10" aria-hidden="true"><path d="M2.5 2.5l5 5M7.5 2.5l-5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
        </button>
      </span>
    </div></div>
  </div>
  <div class="u230-sm">
    <div class="u230-ln"><span>Subtotal</span><span class="u230-sub"></span></div>
    <div class="u230-ln"><span>{{ $shippingLabel }}</span><span class="u230-sh"></span></div>
    <div class="u230-dc"><div><div class="u230-ln"><span>Discount</span><span class="u230-dv"></span></div></div></div>
  </div>
  <div class="u230-tt">
    <div><span>Total</span><span class="u230-sv"></span></div>
    <div class="u230-tv"><s class="u230-ol"></s><b class="u230-tot" aria-live="polite"></b></div>
  </div>
  <button type="button" class="u230-go"></button>

  @if($inputName)
    <input type="hidden" name="{{ $inputName }}" class="u230-hidden-code" value="">
  @endif
</div>

<script>
(function () {
  var root = document.getElementById('{{ $id }}');
  if (!root) return;
  var q = function (s) { return root.querySelector(s); };
  var ls = q('.u230-ls'), lk = q('.u230-lk'), inp = q('.u230-in'), ap = q('.u230-ap'), er = q('.u230-er'), cn = q('.u230-cn'), rmv = q('.u230-rm'), tot = q('.u230-tot'), ol = q('.u230-ol'), sv = q('.u230-sv'), go = q('.u230-go');
  var hiddenInput = q('.u230-hidden-code');
  var rm = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var currency = @json($currency);
  var validPromo = @json($promoCode);
  var discPercent = {{ (float)$discountPercent }};
  var IT = @json($displayItems);
  var SHIP = {{ (float)$shipping }};
  var formId = @json($formId);
  var loginUrl = @json($loginUrl);

  function won(v) {
    if (currency === '$') return '$' + Number(v).toFixed(2);
    return new Intl.NumberFormat('vi-VN').format(Math.round(v)) + (currency ? ' ' + currency : ' đ');
  }

  var sub = 0;
  function renderList() {
    ls.innerHTML = '';
    sub = 0;
    IT.forEach(function (t, i) {
      var li = document.createElement('li');
      var icon = t[3] || 'linear-gradient(140deg,#E0E7FF,#A5B4FC)';
      var imgHtml = '';
      if (typeof icon === 'string' && (icon.startsWith('http') || icon.startsWith('/') || icon.includes('.'))) {
        imgHtml = '<img src="' + icon + '" alt="' + t[0] + '">';
      }
      li.innerHTML = '<i style="' + (imgHtml ? '' : 'background:' + icon) + '">' + imgHtml + '</i><div><b></b><small></small></div><em></em>';
      li.querySelector('b').textContent = t[0];
      li.querySelector('small').textContent = t[1] || '';
      li.querySelector('em').textContent = won(t[2]);
      ls.appendChild(li);
      sub += Number(t[2]) || 0;
    });
  }

  renderList();

  var full = sub + SHIP, cut = Math.round(sub * (discPercent / 100) * 100) / 100, shown = full, on = false, raf = 0;

  function refreshValues() {
    full = sub + SHIP;
    cut = Math.round(sub * (discPercent / 100) * 100) / 100;
    q('.u230-sub').textContent = won(sub);
    q('.u230-sh').textContent = won(SHIP);
    q('.u230-dv').textContent = '−' + won(cut);
    ol.textContent = won(full);
    sv.textContent = won(cut) + ' saved';
    if (!on) {
      shown = full;
      paint(full);
    } else {
      shown = full - cut;
      paint(full - cut);
    }
  }

  refreshValues();

  function paint(v) {
    tot.textContent = won(v);
    go.textContent = won(v) + ' · {{ $buttonText }}';
  }

  function tween(to) {
    cancelAnimationFrame(raf);
    var from = shown, t0 = performance.now(), dur = rm ? 0 : 750;
    (function f(now) {
      var k = dur ? Math.min(1, (now - t0) / dur) : 1;
      var e = 1 - Math.pow(1 - k, 3);
      shown = from + (to - from) * e;
      paint(Math.round(shown * 100) / 100);
      if (k < 1 && root.isConnected) {
        raf = requestAnimationFrame(f);
      } else {
        shown = to;
        paint(to);
      }
    })(t0);
  }

  lk.addEventListener('click', function () {
    var open = !root.classList.contains('is-open');
    root.classList.toggle('is-open', open);
    lk.setAttribute('aria-expanded', open);
    if (open && !on) {
      setTimeout(function () {
        inp.focus({ preventScroll: true });
      }, rm ? 0 : 250);
    }
  });

  inp.addEventListener('input', function () {
    inp.classList.remove('is-bad');
    er.classList.remove('is-on');
  });

  function apply() {
    if (ap.classList.contains('is-busy')) return;
    var code = inp.value.trim().toUpperCase();
    if (!code) {
      inp.focus({ preventScroll: true });
      return;
    }
    ap.classList.add('is-busy');

    setTimeout(function () {
      ap.classList.remove('is-busy');
      if (code === validPromo.toUpperCase()) {
        on = true;
        cn.textContent = code + ' · ' + discPercent + '% off';
        root.classList.add('is-on');
        er.classList.remove('is-on');
        if (hiddenInput) hiddenInput.value = code;
        tween(full - cut);
        return;
      }

      // Check server API fallback if available
      var csrf = document.querySelector('meta[name="csrf-token"]');
      if (csrf && window.fetch) {
        fetch('/api/discount-codes/validate', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf.getAttribute('content')
          },
          body: JSON.stringify({ code: code, context: 'service' })
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
          if (data && data.success) {
            on = true;
            var savings = data.data && data.data.savings ? Number(data.data.savings) : Math.round(sub * (discPercent / 100));
            cut = savings;
            q('.u230-dv').textContent = '−' + won(cut);
            sv.textContent = won(cut) + ' saved';
            cn.textContent = code + ' · Giảm ' + won(cut);
            root.classList.add('is-on');
            er.classList.remove('is-on');
            if (hiddenInput) hiddenInput.value = code;
            tween(Math.max(0, full - cut));
          } else {
            inp.classList.remove('is-bad');
            void inp.offsetWidth;
            inp.classList.add('is-bad');
            er.textContent = (data && data.message) ? data.message : 'This code isn’t valid';
            er.classList.add('is-on');
          }
        })
        .catch(function() {
          inp.classList.remove('is-bad');
          void inp.offsetWidth;
          inp.classList.add('is-bad');
          er.textContent = 'This code isn’t valid';
          er.classList.add('is-on');
        });
      } else {
        inp.classList.remove('is-bad');
        void inp.offsetWidth;
        inp.classList.add('is-bad');
        er.textContent = 'This code isn’t valid';
        er.classList.add('is-on');
      }
    }, rm ? 0 : 650);
  }

  ap.addEventListener('click', apply);

  inp.addEventListener('keydown', function (e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      apply();
    }
  });

  rmv.addEventListener('click', function () {
    on = false;
    root.classList.remove('is-on');
    inp.value = '';
    if (hiddenInput) hiddenInput.value = '';
    tween(full);
    setTimeout(function () {
      if (root.classList.contains('is-open')) inp.focus({ preventScroll: true });
    }, 50);
  });

  go.addEventListener('click', function () {
    if (loginUrl) {
      window.location.href = loginUrl;
      return;
    }
    if (formId) {
      var targetForm = document.getElementById(formId);
      if (targetForm) {
        if (typeof targetForm.requestSubmit === 'function') {
          targetForm.requestSubmit();
        } else {
          targetForm.submit();
        }
        return;
      }
    }
    // Custom event dispatch
    var evt = new CustomEvent('u230:checkout', {
      detail: {
        total: on ? full - cut : full,
        subtotal: sub,
        discount: on ? cut : 0,
        code: on ? validPromo : null
      }
    });
    root.dispatchEvent(evt);
  });

  // Expose update API on root element for dynamic updates
  root.u230Update = function (newItems, newShipping) {
    if (newItems) IT = newItems;
    if (typeof newShipping === 'number') SHIP = newShipping;
    renderList();
    refreshValues();
  };

  paint(full);
})();
</script>

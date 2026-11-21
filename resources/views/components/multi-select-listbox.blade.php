@props([
    'id' => 'msl-' . uniqid(),
    'name' => 'items',
    'label' => null,
    'placeholder' => 'Chọn các mục...',
    'options' => [],
    'selected' => [],
    'required' => false,
    'disabled' => false,
])

@php
    // Normalize options: ensure each item has value and label
    $normalizedOptions = [];
    foreach ($options as $key => $opt) {
        if (is_array($opt)) {
            $val = $opt['value'] ?? $key;
            $lbl = $opt['label'] ?? $val;
            $grp = $opt['group'] ?? null;
        } else {
            $val = is_numeric($key) ? $opt : $key;
            $lbl = $opt;
            $grp = null;
        }
        $normalizedOptions[] = [
            'value' => (string)$val,
            'label' => (string)$lbl,
            'group' => $grp,
        ];
    }
    $selectedValues = array_map('strval', (array)$selected);
@endphp

<div class="msl-wrapper" id="{{ $id }}" data-name="{{ $name }}">
    @if($label)
        <label class="msl-form-label" id="{{ $id }}-label" for="{{ $id }}-trigger">
            {{ $label }}
            @if($required) <span class="text-danger">*</span> @endif
        </label>
    @endif

    <!-- Compact Trigger showing selected count -->
    <div class="msl-control">
        <button type="button" 
                class="msl-trigger" 
                id="{{ $id }}-trigger"
                aria-haspopup="listbox" 
                aria-expanded="false" 
                aria-labelledby="{{ $label ? $id . '-label ' : '' }}{{ $id }}-trigger"
                @if($disabled) disabled @endif>
            
            <span class="msl-trigger-text">
                <span class="msl-placeholder">{{ $placeholder }}</span>
                <span class="msl-count-badge" id="{{ $id }}-count-badge" style="{{ count($selectedValues) > 0 ? '' : 'display:none;' }}">
                    <span class="msl-count-num">{{ count($selectedValues) }}</span> mục đã chọn
                </span>
            </span>

            <span class="msl-trigger-icons">
                <button type="button" class="msl-clear-all-btn" id="{{ $id }}-clear-btn" title="Xóa tất cả" style="{{ count($selectedValues) > 0 ? '' : 'display:none;' }}">
                    <svg viewBox="0 0 16 16" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 4l8 8M12 4l-8 8"/></svg>
                </button>
                <svg class="msl-chevron" viewBox="0 0 16 16" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 6l4 4 4-4"/>
                </svg>
            </span>
        </button>

        <!-- Selected values rendered as removable chips -->
        <div class="msl-chips-tray" id="{{ $id }}-chips" aria-live="polite">
            @foreach($normalizedOptions as $opt)
                @if(in_array($opt['value'], $selectedValues))
                    <span class="msl-chip" data-val="{{ $opt['value'] }}">
                        <span class="msl-chip-label">{{ $opt['label'] }}</span>
                        <button type="button" class="msl-chip-remove" aria-label="Xóa {{ $opt['label'] }}" data-val="{{ $opt['value'] }}">
                            <svg viewBox="0 0 14 14" width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 3l8 8M11 3l-8 8"/></svg>
                        </button>
                    </span>
                @endif
            @endforeach
        </div>

        <!-- Searchable Dropdown Listbox (Keeps open during selection) -->
        <div class="msl-dropdown" id="{{ $id }}-dropdown" hidden>
            <div class="msl-search-bar">
                <svg class="msl-search-icon" viewBox="0 0 16 16" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="7" cy="7" r="4.5"/><path d="M10.5 10.5L14 14"/></svg>
                <input type="text" 
                       class="msl-search-input" 
                       id="{{ $id }}-search"
                       placeholder="Tìm kiếm..." 
                       role="searchbox" 
                       autocomplete="off" 
                       aria-autocomplete="list"
                       aria-controls="{{ $id }}-listbox">
                <button type="button" class="msl-search-clear" title="Xóa tìm kiếm" hidden>
                    <svg viewBox="0 0 14 14" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 3l8 8M11 3l-8 8"/></svg>
                </button>
            </div>

            <div class="msl-actions-bar">
                <button type="button" class="msl-btn-link msl-btn-select-all">Chọn tất cả</button>
                <span class="msl-dot-sep">·</span>
                <button type="button" class="msl-btn-link msl-btn-deselect-all">Bỏ chọn</button>
                <span class="msl-summary-text" id="{{ $id }}-summary">0/{{ count($normalizedOptions) }}</span>
            </div>

            <ul class="msl-listbox" 
                id="{{ $id }}-listbox"
                role="listbox" 
                aria-multiselectable="true" 
                tabindex="-1"
                aria-label="{{ $label ?: 'Danh sách tùy chọn' }}">
                
                @foreach($normalizedOptions as $idx => $opt)
                    @php
                        $isSelected = in_array($opt['value'], $selectedValues);
                    @endphp
                    <li class="msl-option {{ $isSelected ? 'is-selected' : '' }}" 
                        id="{{ $id }}-opt-{{ $idx }}"
                        role="option" 
                        data-val="{{ $opt['value'] }}"
                        data-label="{{ $opt['label'] }}"
                        aria-selected="{{ $isSelected ? 'true' : 'false' }}">
                        
                        <span class="msl-checkbox" aria-hidden="true">
                            <svg viewBox="0 0 14 14" width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="2.5 7 5.5 10 11.5 4"/>
                            </svg>
                        </span>
                        <span class="msl-option-text">{{ $opt['label'] }}</span>
                    </li>
                @endforeach
            </ul>

            <div class="msl-empty-message" style="display: none;">
                Không tìm thấy tùy chọn phù hợp
            </div>
        </div>
    </div>

    <!-- Hidden native inputs for form submission -->
    <div class="msl-hidden-inputs" id="{{ $id }}-hidden-inputs">
        @foreach($selectedValues as $v)
            <input type="hidden" name="{{ $name }}[]" value="{{ $v }}">
        @endforeach
    </div>
</div>

<style>
.msl-wrapper {
    position: relative;
    width: 100%;
    margin-bottom: 1rem;
    font-family: inherit;
}
.msl-form-label {
    display: block;
    font-size: 0.88rem;
    font-weight: 600;
    margin-bottom: 6px;
    color: var(--yuki-text, #F4F0E8);
}
[data-theme="light"] .msl-form-label,
body:not([data-pc-theme="dark"]):not([data-theme="dark"]) .msl-form-label {
    color: #1e293b;
}
.msl-control {
    position: relative;
    width: 100%;
}
.msl-trigger {
    width: 100%;
    min-height: 44px;
    padding: 8px 14px;
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 10px;
    color: var(--yuki-text, #F4F0E8);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    cursor: pointer;
    font-size: 0.9rem;
    transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
    outline: none;
    text-align: left;
}
[data-theme="light"] .msl-trigger,
body:not([data-pc-theme="dark"]):not([data-theme="dark"]) .msl-trigger {
    background: #ffffff;
    border-color: #cbd5e1;
    color: #1e293b;
}
.msl-trigger:hover {
    border-color: var(--yuki-gold, #E8B33C);
    background: rgba(255, 255, 255, 0.06);
}
.msl-trigger:focus-visible,
.msl-wrapper.is-open .msl-trigger {
    border-color: var(--yuki-gold, #E8B33C);
    box-shadow: 0 0 0 3px rgba(232, 179, 60, 0.22);
}
.msl-placeholder {
    color: rgba(244, 240, 232, 0.45);
}
[data-theme="light"] .msl-placeholder,
body:not([data-pc-theme="dark"]):not([data-theme="dark"]) .msl-placeholder {
    color: #94a3b8;
}
.msl-count-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 8px;
    background: rgba(232, 179, 60, 0.18);
    border: 1px solid rgba(232, 179, 60, 0.35);
    border-radius: 20px;
    font-size: 0.78rem;
    font-weight: 700;
    color: var(--yuki-gold, #E8B33C);
}
.msl-trigger-icons {
    display: flex;
    align-items: center;
    gap: 6px;
    color: rgba(244, 240, 232, 0.5);
}
.msl-clear-all-btn {
    background: none;
    border: none;
    padding: 2px;
    color: inherit;
    cursor: pointer;
    display: flex;
    align-items: center;
    border-radius: 4px;
}
.msl-clear-all-btn:hover {
    color: #ef4444;
}
.msl-chevron {
    transition: transform 0.2s ease;
}
.msl-wrapper.is-open .msl-chevron {
    transform: rotate(180deg);
}

/* Chips tray */
.msl-chips-tray {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 8px;
}
.msl-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 8px 3px 10px;
    background: rgba(232, 179, 60, 0.15);
    border: 1px solid rgba(232, 179, 60, 0.32);
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--yuki-gold, #E8B33C);
    animation: msl-chip-in 0.2s cubic-bezier(0.2, 0.8, 0.2, 1);
}
[data-theme="light"] .msl-chip,
body:not([data-pc-theme="dark"]):not([data-theme="dark"]) .msl-chip {
    background: #f1f5f9;
    border-color: #cbd5e1;
    color: #0f172a;
}
@keyframes msl-chip-in {
    from { opacity: 0; transform: scale(0.85); }
    to { opacity: 1; transform: scale(1); }
}
.msl-chip-remove {
    background: none;
    border: none;
    padding: 1px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    cursor: pointer;
    color: inherit;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: background 0.15s;
}
.msl-chip-remove:hover {
    background: rgba(239, 68, 68, 0.2);
    color: #ef4444;
}

/* Dropdown */
.msl-dropdown {
    position: absolute;
    top: calc(100% + 6px);
    left: 0;
    right: 0;
    z-index: 1050;
    background: #13141C;
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 12px;
    box-shadow: 0 16px 36px -4px rgba(0, 0, 0, 0.5);
    overflow: hidden;
    animation: msl-dropdown-in 0.2s cubic-bezier(0.2, 0.8, 0.2, 1);
}
[data-theme="light"] .msl-dropdown,
body:not([data-pc-theme="dark"]):not([data-theme="dark"]) .msl-dropdown {
    background: #ffffff;
    border-color: #e2e8f0;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
}
@keyframes msl-dropdown-in {
    from { opacity: 0; transform: translateY(-6px); }
    to { opacity: 1; transform: translateY(0); }
}
.msl-search-bar {
    position: relative;
    padding: 10px 12px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}
[data-theme="light"] .msl-search-bar,
body:not([data-pc-theme="dark"]):not([data-theme="dark"]) .msl-search-bar {
    border-bottom-color: #f1f5f9;
}
.msl-search-icon {
    position: absolute;
    left: 22px;
    top: 50%;
    transform: translateY(-50%);
    color: rgba(244, 240, 232, 0.4);
    pointer-events: none;
}
.msl-search-input {
    width: 100%;
    height: 36px;
    padding: 0 32px 0 32px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    color: var(--yuki-text, #F4F0E8);
    font-size: 0.85rem;
    outline: none;
}
[data-theme="light"] .msl-search-input,
body:not([data-pc-theme="dark"]):not([data-theme="dark"]) .msl-search-input {
    background: #f8fafc;
    border-color: #e2e8f0;
    color: #1e293b;
}
.msl-search-input:focus {
    border-color: var(--yuki-gold, #E8B33C);
}
.msl-actions-bar {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 14px;
    background: rgba(255, 255, 255, 0.02);
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    font-size: 0.76rem;
}
.msl-btn-link {
    background: none;
    border: none;
    padding: 0;
    color: var(--yuki-gold, #E8B33C);
    cursor: pointer;
    font-weight: 600;
}
.msl-btn-link:hover {
    text-decoration: underline;
}
.msl-dot-sep {
    color: rgba(255, 255, 255, 0.2);
}
.msl-summary-text {
    margin-left: auto;
    color: rgba(244, 240, 232, 0.5);
}

/* Listbox & Options */
.msl-listbox {
    list-style: none;
    margin: 0;
    padding: 6px;
    max-height: 230px;
    overflow-y: auto;
    outline: none;
}
.msl-option {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 0.88rem;
    color: var(--yuki-text, #F4F0E8);
    cursor: pointer;
    user-select: none;
    transition: background 0.15s;
}
[data-theme="light"] .msl-option,
body:not([data-pc-theme="dark"]):not([data-theme="dark"]) .msl-option {
    color: #1e293b;
}
/* Separate keyboard focus from selection */
.msl-option.is-focused {
    outline: 2px solid var(--yuki-gold, #E8B33C);
    outline-offset: -1px;
    background: rgba(232, 179, 60, 0.1);
}
.msl-option:hover {
    background: rgba(255, 255, 255, 0.06);
}
[data-theme="light"] .msl-option:hover,
body:not([data-pc-theme="dark"]):not([data-theme="dark"]) .msl-option:hover {
    background: #f1f5f9;
}
.msl-option.is-selected {
    background: rgba(232, 179, 60, 0.12);
    font-weight: 600;
}
.msl-checkbox {
    width: 18px;
    height: 18px;
    border-radius: 5px;
    border: 1.5px solid rgba(255, 255, 255, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all 0.15s;
}
[data-theme="light"] .msl-checkbox,
body:not([data-pc-theme="dark"]):not([data-theme="dark"]) .msl-checkbox {
    border-color: #94a3b8;
}
.msl-checkbox svg {
    opacity: 0;
    transform: scale(0.6);
    transition: transform 0.15s ease, opacity 0.15s ease;
}
.msl-option.is-selected .msl-checkbox {
    background: var(--yuki-gold, #E8B33C);
    border-color: var(--yuki-gold, #E8B33C);
    color: #241C08;
}
.msl-option.is-selected .msl-checkbox svg {
    opacity: 1;
    transform: scale(1);
}
.msl-empty-message {
    padding: 24px 16px;
    text-align: center;
    font-size: 0.85rem;
    color: rgba(244, 240, 232, 0.45);
}
</style>

<script>
(function () {
    var root = document.getElementById('{{ $id }}');
    if (!root) return;

    var trigger = root.querySelector('.msl-trigger');
    var dropdown = root.querySelector('.msl-dropdown');
    var listbox = root.querySelector('.msl-listbox');
    var searchInput = root.querySelector('.msl-search-input');
    var chipsTray = root.querySelector('.msl-chips-tray');
    var hiddenInputsContainer = root.querySelector('.msl-hidden-inputs');
    var countBadge = root.querySelector('.msl-count-badge');
    var countNum = root.querySelector('.msl-count-num');
    var clearAllBtn = root.querySelector('.msl-clear-all-btn');
    var summaryText = root.querySelector('.msl-summary-text');
    var emptyMessage = root.querySelector('.msl-empty-message');
    var btnSelectAll = root.querySelector('.msl-btn-select-all');
    var btnDeselectAll = root.querySelector('.msl-btn-deselect-all');
    var fieldName = root.getAttribute('data-name') || 'items';

    var selectedMap = new Map();
    var allOptions = [];

    // Initialize all options cache
    root.querySelectorAll('.msl-option').forEach(function (optEl) {
        var val = optEl.getAttribute('data-val');
        var lbl = optEl.getAttribute('data-label');
        var isSel = optEl.getAttribute('aria-selected') === 'true';
        allOptions.push({ element: optEl, value: val, label: lbl });
        if (isSel) {
            selectedMap.set(val, lbl);
        }
    });

    var focusedIndex = -1;

    function updateSummary() {
        var selCount = selectedMap.size;
        var totalCount = allOptions.length;
        if (summaryText) summaryText.textContent = selCount + '/' + totalCount;

        if (countNum) countNum.textContent = selCount;
        if (countBadge) countBadge.style.display = selCount > 0 ? 'inline-flex' : 'none';
        if (clearAllBtn) clearAllBtn.style.display = selCount > 0 ? 'flex' : 'none';
    }

    function renderHiddenInputs() {
        hiddenInputsContainer.innerHTML = '';
        selectedMap.forEach(function (lbl, val) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = fieldName + '[]';
            input.value = val;
            hiddenInputsContainer.appendChild(input);
        });
    }

    function renderChips() {
        chipsTray.innerHTML = '';
        selectedMap.forEach(function (lbl, val) {
            var chip = document.createElement('span');
            chip.className = 'msl-chip';
            chip.setAttribute('data-val', val);

            var labelSpan = document.createElement('span');
            labelSpan.className = 'msl-chip-label';
            labelSpan.textContent = lbl;

            var removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'msl-chip-remove';
            removeBtn.setAttribute('aria-label', 'Xóa ' + lbl);
            removeBtn.setAttribute('data-val', val);
            removeBtn.innerHTML = '<svg viewBox="0 0 14 14" width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 3l8 8M11 3l-8 8"/></svg>';

            chip.appendChild(labelSpan);
            chip.appendChild(removeBtn);
            chipsTray.appendChild(chip);
        });
    }

    function toggleOption(val, force) {
        var opt = allOptions.find(function (o) { return o.value === val; });
        if (!opt) return;

        var isCurrentlySel = selectedMap.has(val);
        var willBeSel = (typeof force === 'boolean') ? force : !isCurrentlySel;

        if (willBeSel) {
            selectedMap.set(val, opt.label);
            opt.element.classList.add('is-selected');
            opt.element.setAttribute('aria-selected', 'true');
        } else {
            selectedMap.delete(val);
            opt.element.classList.remove('is-selected');
            opt.element.setAttribute('aria-selected', 'false');
        }

        renderChips();
        renderHiddenInputs();
        updateSummary();

        // Dispatch change event
        root.dispatchEvent(new CustomEvent('msl:change', {
            bubbles: true,
            detail: {
                values: Array.from(selectedMap.keys()),
                selected: Array.from(selectedMap.entries()).map(function (e) { return { value: e[0], label: e[1] }; })
            }
        }));
    }

    function openDropdown() {
        dropdown.hidden = false;
        root.classList.add('is-open');
        trigger.setAttribute('aria-expanded', 'true');
        searchInput.focus();
    }

    function closeDropdown() {
        dropdown.hidden = true;
        root.classList.remove('is-open');
        trigger.setAttribute('aria-expanded', 'false');
        clearFocus();
    }

    trigger.addEventListener('click', function (e) {
        if (e.target.closest('.msl-clear-all-btn')) return;
        if (dropdown.hidden) {
            openDropdown();
        } else {
            closeDropdown();
        }
    });

    clearAllBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        selectedMap.clear();
        allOptions.forEach(function (opt) {
            opt.element.classList.remove('is-selected');
            opt.element.setAttribute('aria-selected', 'false');
        });
        renderChips();
        renderHiddenInputs();
        updateSummary();
    });

    // Remove chip click
    chipsTray.addEventListener('click', function (e) {
        var btn = e.target.closest('.msl-chip-remove');
        if (btn) {
            var val = btn.getAttribute('data-val');
            toggleOption(val, false);
        }
    });

    // Option row click: Keeps open and toggles aria-selected
    listbox.addEventListener('click', function (e) {
        var optionEl = e.target.closest('.msl-option');
        if (optionEl) {
            var val = optionEl.getAttribute('data-val');
            toggleOption(val);
        }
    });

    // Search filter
    function filterOptions() {
        var q = searchInput.value.toLowerCase().trim();
        var visibleCount = 0;
        allOptions.forEach(function (opt) {
            var match = opt.label.toLowerCase().includes(q) || opt.value.toLowerCase().includes(q);
            opt.element.style.display = match ? 'flex' : 'none';
            if (match) visibleCount++;
        });
        emptyMessage.style.display = visibleCount === 0 ? 'block' : 'none';
        clearFocus();
    }

    searchInput.addEventListener('input', filterOptions);

    btnSelectAll.addEventListener('click', function () {
        allOptions.forEach(function (opt) {
            if (opt.element.style.display !== 'none') {
                toggleOption(opt.value, true);
            }
        });
    });

    btnDeselectAll.addEventListener('click', function () {
        allOptions.forEach(function (opt) {
            if (opt.element.style.display !== 'none') {
                toggleOption(opt.value, false);
            }
        });
    });

    // Keyboard navigation: Separates focus from selection
    function getVisibleOptions() {
        return allOptions.filter(function (opt) {
            return opt.element.style.display !== 'none';
        });
    }

    function clearFocus() {
        allOptions.forEach(function (opt) {
            opt.element.classList.remove('is-focused');
        });
        focusedIndex = -1;
        listbox.removeAttribute('aria-activedescendant');
    }

    function setFocusIndex(idx) {
        var visible = getVisibleOptions();
        if (visible.length === 0) return;
        idx = Math.max(0, Math.min(visible.length - 1, idx));
        clearFocus();
        focusedIndex = idx;
        var focusedOpt = visible[focusedIndex];
        focusedOpt.element.classList.add('is-focused');
        listbox.setAttribute('aria-activedescendant', focusedOpt.element.id);
        focusedOpt.element.scrollIntoView({ block: 'nearest' });
    }

    dropdown.addEventListener('keydown', function (e) {
        var visible = getVisibleOptions();
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            setFocusIndex(focusedIndex + 1);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            setFocusIndex(focusedIndex <= 0 ? visible.length - 1 : focusedIndex - 1);
        } else if (e.key === 'Home') {
            e.preventDefault();
            setFocusIndex(0);
        } else if (e.key === 'End') {
            e.preventDefault();
            setFocusIndex(visible.length - 1);
        } else if (e.key === ' ' || e.key === 'Enter') {
            if (focusedIndex >= 0 && focusedIndex < visible.length) {
                // If focus is in search input and key is Space, allow typing Space unless in listbox
                if (e.target === searchInput && e.key === ' ') {
                    return;
                }
                e.preventDefault();
                toggleOption(visible[focusedIndex].value);
            }
        } else if (e.key === 'Escape') {
            e.preventDefault();
            closeDropdown();
            trigger.focus();
        }
    });

    // Click outside to close
    document.addEventListener('click', function (e) {
        if (!root.contains(e.target)) {
            closeDropdown();
        }
    });

    // Public JS API
    root.getValues = function () { return Array.from(selectedMap.keys()); };
    root.setValues = function (keys) {
        selectedMap.clear();
        var keyArr = Array.isArray(keys) ? keys.map(String) : [];
        allOptions.forEach(function (opt) {
            var willBeSel = keyArr.includes(opt.value);
            opt.element.classList.toggle('is-selected', willBeSel);
            opt.element.setAttribute('aria-selected', willBeSel ? 'true' : 'false');
            if (willBeSel) selectedMap.set(opt.value, opt.label);
        });
        renderChips();
        renderHiddenInputs();
        updateSummary();
    };

    updateSummary();
})();
</script>

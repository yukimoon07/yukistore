@props([
    'id' => 'transfer-' . uniqid(),
    'name' => 'targetKeys',
    'dataSource' => [],
    'targetKeys' => [],
    'titles' => ['Available', 'Selected'],
    'searchable' => true,
    'disabled' => false,
])

@php
    // Normalize dataSource items: key, title, description, disabled
    $normalizedData = [];
    foreach ($dataSource as $k => $item) {
        if (is_array($item)) {
            $key = $item['key'] ?? ($item['id'] ?? $k);
            $title = $item['title'] ?? ($item['name'] ?? $key);
            $desc = $item['description'] ?? ($item['desc'] ?? '');
            $itemDisabled = !empty($item['disabled']);
        } else {
            $key = is_numeric($k) ? $item : $k;
            $title = $item;
            $desc = '';
            $itemDisabled = false;
        }
        $normalizedData[] = [
            'key' => (string)$key,
            'title' => (string)$title,
            'description' => (string)$desc,
            'disabled' => $itemDisabled,
        ];
    }
    $initialTargetKeys = array_map('strval', (array)$targetKeys);
    $leftTitle = $titles[0] ?? 'Available';
    $rightTitle = $titles[1] ?? 'Selected';
@endphp

<div class="ant-transfer" id="{{ $id }}" data-name="{{ $name }}">
    <!-- Left Pane: Available -->
    <div class="ant-transfer-list" id="{{ $id }}-pane-source" data-role="source">
        <div class="ant-transfer-list-header">
            <label class="ant-transfer-checkbox-wrapper">
                <input type="checkbox" class="ant-transfer-select-all" aria-label="Select all available">
                <span class="ant-transfer-checkbox-inner"></span>
            </label>
            <span class="ant-transfer-list-header-title">{{ $leftTitle }}</span>
            <span class="ant-transfer-list-header-count" id="{{ $id }}-source-count">0/0</span>
        </div>

        @if($searchable)
            <div class="ant-transfer-list-body-search-wrapper">
                <input type="text" class="ant-transfer-list-search" placeholder="Search available..." aria-label="Search available">
                <span class="ant-transfer-search-icon">
                    <svg viewBox="0 0 16 16" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="7" cy="7" r="4.5"/><path d="M10.5 10.5L14 14"/></svg>
                </span>
            </div>
        @endif

        <div class="ant-transfer-list-body">
            <ul class="ant-transfer-list-content" role="listbox" aria-multiselectable="true" aria-label="{{ $leftTitle }}">
                <!-- Rendered dynamically via JS -->
            </ul>
            <div class="ant-transfer-empty" style="display: none;">No data available</div>
        </div>
    </div>

    <!-- Center Operations (Move Buttons disabled when nothing checked) -->
    <div class="ant-transfer-operation">
        <button type="button" 
                class="ant-transfer-btn ant-transfer-btn-to-target" 
                id="{{ $id }}-btn-to-target"
                disabled 
                aria-label="Move selected items to {{ $rightTitle }}"
                title="Chuyển sang {{ $rightTitle }}">
            <svg viewBox="0 0 16 16" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 3l5 5-5 5"/>
            </svg>
        </button>

        <button type="button" 
                class="ant-transfer-btn ant-transfer-btn-to-source" 
                id="{{ $id }}-btn-to-source"
                disabled 
                aria-label="Move selected items to {{ $leftTitle }}"
                title="Chuyển sang {{ $leftTitle }}">
            <svg viewBox="0 0 16 16" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10 13l-5-5 5-5"/>
            </svg>
        </button>
    </div>

    <!-- Right Pane: Selected -->
    <div class="ant-transfer-list" id="{{ $id }}-pane-target" data-role="target">
        <div class="ant-transfer-list-header">
            <label class="ant-transfer-checkbox-wrapper">
                <input type="checkbox" class="ant-transfer-select-all" aria-label="Select all chosen">
                <span class="ant-transfer-checkbox-inner"></span>
            </label>
            <span class="ant-transfer-list-header-title">{{ $rightTitle }}</span>
            <span class="ant-transfer-list-header-count" id="{{ $id }}-target-count">0/0</span>
        </div>

        @if($searchable)
            <div class="ant-transfer-list-body-search-wrapper">
                <input type="text" class="ant-transfer-list-search" placeholder="Search selected..." aria-label="Search selected">
                <span class="ant-transfer-search-icon">
                    <svg viewBox="0 0 16 16" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="7" cy="7" r="4.5"/><path d="M10.5 10.5L14 14"/></svg>
                </span>
            </div>
        @endif

        <div class="ant-transfer-list-body">
            <ul class="ant-transfer-list-content" role="listbox" aria-multiselectable="true" aria-label="{{ $rightTitle }}">
                <!-- Rendered dynamically via JS -->
            </ul>
            <div class="ant-transfer-empty" style="display: none;">No data selected</div>
        </div>
    </div>

    <!-- Hidden Native Inputs for TargetKeys Form Submission -->
    <div class="ant-transfer-hidden-inputs" id="{{ $id }}-hidden-inputs"></div>
</div>

<style>
.ant-transfer {
    display: flex;
    align-items: center;
    gap: 16px;
    width: 100%;
    margin-bottom: 1.2rem;
    font-family: inherit;
    user-select: none;
}

/* Two Panes */
.ant-transfer-list {
    flex: 1 1 0;
    min-width: 200px;
    height: 320px;
    display: flex;
    flex-direction: column;
    background: #13141C;
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.25);
    transition: border-color 0.2s, box-shadow 0.2s;
}

[data-theme="light"] .ant-transfer-list,
body:not([data-pc-theme="dark"]):not([data-theme="dark"]) .ant-transfer-list {
    background: #ffffff;
    border-color: #e2e8f0;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.05);
}

.ant-transfer-list-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    background: rgba(255, 255, 255, 0.03);
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}

[data-theme="light"] .ant-transfer-list-header,
body:not([data-pc-theme="dark"]):not([data-theme="dark"]) .ant-transfer-list-header {
    background: #f8fafc;
    border-bottom-color: #e2e8f0;
}

.ant-transfer-list-header-title {
    font-size: 0.88rem;
    font-weight: 700;
    color: var(--yuki-text, #F4F0E8);
}

[data-theme="light"] .ant-transfer-list-header-title,
body:not([data-pc-theme="dark"]):not([data-theme="dark"]) .ant-transfer-list-header-title {
    color: #1e293b;
}

.ant-transfer-list-header-count {
    margin-left: auto;
    font-size: 0.78rem;
    font-weight: 600;
    color: rgba(244, 240, 232, 0.5);
    font-variant-numeric: tabular-nums;
}

[data-theme="light"] .ant-transfer-list-header-count,
body:not([data-pc-theme="dark"]):not([data-theme="dark"]) .ant-transfer-list-header-count {
    color: #64748b;
}

/* Search Wrapper */
.ant-transfer-list-body-search-wrapper {
    position: relative;
    padding: 8px 12px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
}

[data-theme="light"] .ant-transfer-list-body-search-wrapper,
body:not([data-pc-theme="dark"]):not([data-theme="dark"]) .ant-transfer-list-body-search-wrapper {
    border-bottom-color: #f1f5f9;
}

.ant-transfer-list-search {
    width: 100%;
    height: 32px;
    padding: 0 28px 0 10px;
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 6px;
    color: var(--yuki-text, #F4F0E8);
    font-size: 0.82rem;
    outline: none;
}

[data-theme="light"] .ant-transfer-list-search,
body:not([data-pc-theme="dark"]):not([data-theme="dark"]) .ant-transfer-list-search {
    background: #f8fafc;
    border-color: #e2e8f0;
    color: #1e293b;
}

.ant-transfer-list-search:focus {
    border-color: var(--yuki-gold, #E8B33C);
}

.ant-transfer-search-icon {
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    color: rgba(244, 240, 232, 0.4);
    pointer-events: none;
}

/* List Body & Items */
.ant-transfer-list-body {
    flex: 1;
    overflow-y: auto;
    position: relative;
}

.ant-transfer-list-content {
    list-style: none;
    margin: 0;
    padding: 4px;
}

.ant-transfer-list-content-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 7px 10px;
    border-radius: 6px;
    font-size: 0.85rem;
    color: var(--yuki-text, #F4F0E8);
    cursor: pointer;
    transition: background 0.15s;
}

[data-theme="light"] .ant-transfer-list-content-item,
body:not([data-pc-theme="dark"]):not([data-theme="dark"]) .ant-transfer-list-content-item {
    color: #1e293b;
}

.ant-transfer-list-content-item:hover {
    background: rgba(255, 255, 255, 0.05);
}

[data-theme="light"] .ant-transfer-list-content-item:hover,
body:not([data-pc-theme="dark"]):not([data-theme="dark"]) .ant-transfer-list-content-item:hover {
    background: #f1f5f9;
}

.ant-transfer-list-content-item.is-checked {
    background: rgba(232, 179, 60, 0.1);
}

.ant-transfer-list-content-item.is-disabled {
    opacity: 0.4;
    cursor: not-allowed;
    pointer-events: none;
}

.ant-transfer-empty {
    padding: 40px 16px;
    text-align: center;
    font-size: 0.82rem;
    color: rgba(244, 240, 232, 0.4);
}

/* Custom Checkbox */
.ant-transfer-checkbox-wrapper {
    position: relative;
    display: inline-flex;
    align-items: center;
    cursor: pointer;
    margin: 0;
}

.ant-transfer-checkbox-wrapper input {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.ant-transfer-checkbox-inner {
    width: 16px;
    height: 16px;
    border-radius: 4px;
    border: 1.5px solid rgba(255, 255, 255, 0.35);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.15s;
    background: transparent;
}

[data-theme="light"] .ant-transfer-checkbox-inner,
body:not([data-pc-theme="dark"]):not([data-theme="dark"]) .ant-transfer-checkbox-inner {
    border-color: #94a3b8;
}

.ant-transfer-checkbox-wrapper input:checked + .ant-transfer-checkbox-inner {
    background: var(--yuki-gold, #E8B33C);
    border-color: var(--yuki-gold, #E8B33C);
}

.ant-transfer-checkbox-wrapper input:checked + .ant-transfer-checkbox-inner::after {
    content: '';
    width: 5px;
    height: 9px;
    border: solid #241C08;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg) translate(-0.5px, -1px);
}

/* Operation Center Buttons */
.ant-transfer-operation {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.ant-transfer-btn {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.15);
    color: var(--yuki-text, #F4F0E8);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.2, 0.8, 0.2, 1);
    outline: none;
}

[data-theme="light"] .ant-transfer-btn,
body:not([data-pc-theme="dark"]):not([data-theme="dark"]) .ant-transfer-btn {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #1e293b;
}

.ant-transfer-btn:not(:disabled):hover {
    background: var(--yuki-gold, #E8B33C);
    border-color: var(--yuki-gold, #E8B33C);
    color: #241C08;
    transform: scale(1.06);
    box-shadow: 0 4px 12px rgba(232, 179, 60, 0.35);
}

.ant-transfer-btn:disabled {
    opacity: 0.3;
    cursor: not-allowed;
    border-color: transparent;
    background: rgba(255, 255, 255, 0.03);
}

@media (max-width: 600px) {
    .ant-transfer {
        flex-direction: column;
    }
    .ant-transfer-operation {
        flex-direction: row;
    }
    .ant-transfer-list {
        width: 100%;
        height: 250px;
    }
    .ant-transfer-btn-to-target svg {
        transform: rotate(90deg);
    }
    .ant-transfer-btn-to-source svg {
        transform: rotate(90deg);
    }
}
</style>

<script>
(function () {
    var root = document.getElementById('{{ $id }}');
    if (!root) return;

    var dataSource = @json($normalizedData);
    var targetKeys = new Set(@json($initialTargetKeys));
    var sourceCheckedKeys = new Set();
    var targetCheckedKeys = new Set();

    var paneSource = root.querySelector('#{{ $id }}-pane-source');
    var paneTarget = root.querySelector('#{{ $id }}-pane-target');

    var sourceContent = paneSource.querySelector('.ant-transfer-list-content');
    var targetContent = paneTarget.querySelector('.ant-transfer-list-content');

    var sourceEmpty = paneSource.querySelector('.ant-transfer-empty');
    var targetEmpty = paneTarget.querySelector('.ant-transfer-empty');

    var sourceSearch = paneSource.querySelector('.ant-transfer-list-search');
    var targetSearch = paneTarget.querySelector('.ant-transfer-list-search');

    var sourceSelectAll = paneSource.querySelector('.ant-transfer-select-all');
    var targetSelectAll = paneTarget.querySelector('.ant-transfer-select-all');

    var sourceCount = root.querySelector('#{{ $id }}-source-count');
    var targetCount = root.querySelector('#{{ $id }}-target-count');

    var btnToTarget = root.querySelector('#{{ $id }}-btn-to-target');
    var btnToSource = root.querySelector('#{{ $id }}-btn-to-source');
    var hiddenInputsContainer = root.querySelector('#{{ $id }}-hidden-inputs');
    var fieldName = root.getAttribute('data-name') || 'targetKeys';

    function renderList(paneType) {
        var isSource = paneType === 'source';
        var container = isSource ? sourceContent : targetContent;
        var emptyEl = isSource ? sourceEmpty : targetEmpty;
        var searchInput = isSource ? sourceSearch : targetSearch;
        var checkedSet = isSource ? sourceCheckedKeys : targetCheckedKeys;
        var q = searchInput ? searchInput.value.toLowerCase().trim() : '';

        container.innerHTML = '';

        var items = dataSource.filter(function (item) {
            var inTarget = targetKeys.has(item.key);
            return isSource ? !inTarget : inTarget;
        });

        var visibleCount = 0;
        var checkedVisibleCount = 0;

        items.forEach(function (item) {
            var matches = item.title.toLowerCase().includes(q) || item.key.toLowerCase().includes(q);
            if (!matches) return;
            visibleCount++;

            var isChecked = checkedSet.has(item.key);
            if (isChecked) checkedVisibleCount++;

            var li = document.createElement('li');
            li.className = 'ant-transfer-list-content-item' + (isChecked ? ' is-checked' : '') + (item.disabled ? ' is-disabled' : '');
            li.setAttribute('role', 'option');
            li.setAttribute('aria-selected', isChecked ? 'true' : 'false');
            li.setAttribute('data-key', item.key);

            li.innerHTML = `
                <label class="ant-transfer-checkbox-wrapper">
                    <input type="checkbox" ${isChecked ? 'checked' : ''} ${item.disabled ? 'disabled' : ''}>
                    <span class="ant-transfer-checkbox-inner"></span>
                </label>
                <span class="ant-transfer-item-title">${item.title}</span>
                ${item.description ? `<span class="ant-transfer-item-desc" style="margin-left:auto; font-size:0.75rem; opacity:0.5;">${item.description}</span>` : ''}
            `;

            li.addEventListener('click', function (e) {
                if (item.disabled) return;
                var willCheck = !checkedSet.has(item.key);
                if (willCheck) {
                    checkedSet.add(item.key);
                } else {
                    checkedSet.delete(item.key);
                }
                updateUI();
            });

            container.appendChild(li);
        });

        emptyEl.style.display = visibleCount === 0 ? 'block' : 'none';

        // Update select all checkbox state
        var selectAllCb = isSource ? sourceSelectAll : targetSelectAll;
        if (selectAllCb) {
            selectAllCb.checked = visibleCount > 0 && checkedVisibleCount === visibleCount;
            selectAllCb.indeterminate = checkedVisibleCount > 0 && checkedVisibleCount < visibleCount;
        }

        // Update count badge
        var countEl = isSource ? sourceCount : targetCount;
        if (countEl) {
            countEl.textContent = checkedVisibleCount + '/' + items.length;
        }
    }

    function updateButtons() {
        // Disabled when nothing eligible is checked!
        btnToTarget.disabled = sourceCheckedKeys.size === 0;
        btnToSource.disabled = targetCheckedKeys.size === 0;
    }

    function renderHiddenInputs() {
        hiddenInputsContainer.innerHTML = '';
        targetKeys.forEach(function (k) {
            var inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = fieldName + '[]';
            inp.value = k;
            hiddenInputsContainer.appendChild(inp);
        });
    }

    function updateUI() {
        renderList('source');
        renderList('target');
        updateButtons();
        renderHiddenInputs();

        // Dispatch change event
        root.dispatchEvent(new CustomEvent('transfer:change', {
            bubbles: true,
            detail: {
                targetKeys: Array.from(targetKeys),
                sourceCount: dataSource.length - targetKeys.size,
                targetCount: targetKeys.size
            }
        }));
    }

    // Move to target
    btnToTarget.addEventListener('click', function () {
        if (sourceCheckedKeys.size === 0) return;
        sourceCheckedKeys.forEach(function (k) {
            targetKeys.add(k);
        });
        sourceCheckedKeys.clear();
        updateUI();
    });

    // Move to source
    btnToSource.addEventListener('click', function () {
        if (targetCheckedKeys.size === 0) return;
        targetCheckedKeys.forEach(function (k) {
            targetKeys.delete(k);
        });
        targetCheckedKeys.clear();
        updateUI();
    });

    // Select-all in Source
    sourceSelectAll.addEventListener('change', function () {
        var q = sourceSearch ? sourceSearch.value.toLowerCase().trim() : '';
        dataSource.forEach(function (item) {
            if (targetKeys.has(item.key) || item.disabled) return;
            var matches = item.title.toLowerCase().includes(q) || item.key.toLowerCase().includes(q);
            if (!matches) return;
            if (sourceSelectAll.checked) {
                sourceCheckedKeys.add(item.key);
            } else {
                sourceCheckedKeys.delete(item.key);
            }
        });
        updateUI();
    });

    // Select-all in Target
    targetSelectAll.addEventListener('change', function () {
        var q = targetSearch ? targetSearch.value.toLowerCase().trim() : '';
        dataSource.forEach(function (item) {
            if (!targetKeys.has(item.key) || item.disabled) return;
            var matches = item.title.toLowerCase().includes(q) || item.key.toLowerCase().includes(q);
            if (!matches) return;
            if (targetSelectAll.checked) {
                targetCheckedKeys.add(item.key);
            } else {
                targetCheckedKeys.delete(item.key);
            }
        });
        updateUI();
    });

    // Search events
    if (sourceSearch) sourceSearch.addEventListener('input', function () { renderList('source'); });
    if (targetSearch) targetSearch.addEventListener('input', function () { renderList('target'); });

    // Public API
    root.getTargetKeys = function () { return Array.from(targetKeys); };
    root.setTargetKeys = function (newKeys) {
        targetKeys = new Set(Array.isArray(newKeys) ? newKeys.map(String) : []);
        sourceCheckedKeys.clear();
        targetCheckedKeys.clear();
        updateUI();
    };

    updateUI();
})();
</script>

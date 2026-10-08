@php
    $id = $id ?? ($name ?? 'select') . '-' . uniqid();
    $options = $options ?? [];
    $placeholder = $placeholder ?? 'Select...';
    $value = $value ?? old($name ?? 'select');
    $valueStr = is_scalar($value) ? (string) $value : '';
@endphp

<div class="relative custom-select" data-custom-select>
    <input
        type="hidden"
        id="{{ $id }}-input"
        name="{{ $name ?? 'select' }}"
        value="{{ $valueStr }}"
        @if(isset($required)) required @endif
    >

    <button
        type="button"
        class="input-field custom-select-trigger flex items-center justify-between text-left"
        aria-haspopup="listbox"
        aria-expanded="false"
    >
        <span class="custom-select-value {{ $valueStr ? 'text-[var(--text)]' : 'text-[var(--text-muted)]' }}">{{ $valueStr ? ($options[$valueStr] ?? $valueStr) : $placeholder }}</span>
        <x-icon name="chevron-down" class="w-4 h-4 flex-shrink-0 text-[var(--text-muted)] transition-transform duration-200 custom-select-arrow" />
    </button>

    <div
        class="custom-select-dropdown absolute z-50 w-full mt-1 bg-white border border-[var(--line)] rounded-xl shadow-lg overflow-hidden"
        role="listbox"
    >
        <div class="max-h-60 overflow-y-auto py-1">
            @foreach($options as $optionValue => $optionLabel)
                <button
                    type="button"
                    data-value="{{ $optionValue }}"
                    class="custom-select-option w-full text-left px-4 py-2.5 text-sm transition-colors text-[var(--text)] hover:bg-[var(--line-soft)]"
                    role="option"
                >
                    {{ $optionLabel }}
                </button>
            @endforeach
        </div>
    </div>
</div>

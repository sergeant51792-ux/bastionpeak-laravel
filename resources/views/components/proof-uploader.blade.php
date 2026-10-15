@props([
    'accept' => 'image/*',
    'maxSize' => '5MB',
])

<div x-data="{
    file: null,
    preview: null,
    dragging: false,
    handleDrop(e) {
        e.preventDefault();
        this.dragging = false;
        const files = e.dataTransfer.files;
        if (files.length > 0) this.processFile(files[0]);
    },
    handleSelect(e) {
        const files = e.target.files;
        if (files.length > 0) this.processFile(files[0]);
    },
    processFile(file) {
        if (!file.type.startsWith('image/')) return;
        this.file = file;
        const reader = new FileReader();
        reader.onload = (e) => this.preview = e.target.result;
        reader.readAsDataURL(file);
        @this.set('proofFile', file);
    },
    remove() {
        this.file = null;
        this.preview = null;
        @this.set('proofFile', null);
    }
}">
    <div
        @dragover.prevent="dragging = true"
        @dragleave.prevent="dragging = false"
        @drop.prevent="handleDrop($event)"
        class="relative rounded-xl border-2 border-dashed p-6 text-center transition-colors {{ $preview ?? 'dragging' ? 'border-[var(--accent)]/50 bg-[var(--accent-subtle)]' : 'border-[var(--line)] hover:border-[var(--accent)]' }}">
        <input type="file" accept="{{ $accept }}" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" @change="handleSelect($event)">
        @if($preview)
            <div class="relative inline-block">
                <img src="{{ $preview }}" alt="Preview" class="max-h-32 rounded-lg">
                <button type="button" @click="remove" class="absolute -top-2 -right-2 w-6 h-6 bg-[var(--danger)] text-white rounded-full flex items-center justify-center hover:opacity-90 transition-colors">
                    <x-icon name="x" class="h-4 w-4" />
                </button>
            </div>
        @else
            <div class="flex flex-col items-center gap-2">
                <x-icon name="upload" class="h-8 w-8 text-[var(--text-muted)]" />
                <p class="text-sm text-[var(--text-muted)]">Drag & drop or tap to upload</p>
                <p class="text-xs text-[var(--text-muted)]">{{ $maxSize }} max</p>
            </div>
        @endif
    </div>
</div>

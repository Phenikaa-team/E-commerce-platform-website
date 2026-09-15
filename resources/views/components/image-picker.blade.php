@props([
    'name',
    'id' => null,
    'label' => null,
    'value' => null,
    'placeholder' => null,
    'previewShape' => 'rounded', // 'circle', 'rounded', 'banner'
    'maxSizeMb' => 3,
    'helpText' => null,
    'required' => false,
])

@php
    $inputId = $id ?? $name;
    $initialSrc = !empty($value) ? $value : (!empty($placeholder) ? $placeholder : '');
    
    // Shape classes
    $containerClasses = match($previewShape) {
        'circle' => 'w-24 h-24 rounded-full',
        'banner' => 'w-full h-36 sm:h-44 rounded-2xl',
        default => 'w-24 h-24 rounded-2xl',
    };
@endphp

<div class="image-picker-component" id="picker-wrapper-{{ $inputId }}" data-initial-src="{{ $initialSrc }}" data-max-size="{{ $maxSizeMb }}">
    @if($label)
        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
            {{ $label }}
            @if($required)
                <span class="text-rose-500">*</span>
            @endif
        </label>
    @endif

    <div class="flex {{ $previewShape === 'banner' ? 'flex-col' : 'items-center' }} gap-4">
        <!-- Preview Container -->
        <div class="relative group shrink-0 overflow-hidden border-2 border-gray-200 bg-gray-50 flex items-center justify-center {{ $containerClasses }} transition-all hover:border-primary">
            <img 
                id="preview-img-{{ $inputId }}" 
                src="{{ $initialSrc ?: 'data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'40\' height=\'40\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%2394a3b8\' stroke-width=\'1.5\'><rect width=\'18\' height=\'18\' x=\'3\' y=\'3\' rx=\'2\' ry=\'2\'/><circle cx=\'9\' cy=\'9\' r=\'2\'/><path d=\'m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21\'/></svg>' }}" 
                alt="Preview" 
                class="w-full h-full {{ $previewShape === 'banner' ? 'object-cover' : 'object-cover' }}"
            >
            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white pointer-events-none">
                <span class="text-[11px] font-bold">Chọn ảnh mới</span>
            </div>
        </div>

        <!-- Controls & File Input -->
        <div class="flex-1 space-y-2">
            <div class="flex items-center gap-2 flex-wrap">
                <label 
                    for="{{ $inputId }}" 
                    class="px-4 py-2 bg-white hover:bg-gray-50 border border-gray-200 hover:border-gray-300 rounded-xl text-xs font-bold text-gray-700 shadow-2xs transition-all cursor-pointer inline-flex items-center gap-1.5"
                >
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Chọn tệp từ máy</span>
                </label>

                <button 
                    type="button" 
                    id="btn-clear-{{ $inputId }}" 
                    class="px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 rounded-xl transition-colors hidden cursor-pointer"
                >
                    Hủy chọn
                </button>
            </div>

            <!-- Hidden File Input -->
            <input 
                type="file" 
                name="{{ $name }}" 
                id="{{ $inputId }}" 
                accept="image/jpeg,image/png,image/webp,image/gif,image/avif" 
                class="hidden"
                {{ $required ? 'required' : '' }}
            >

            <div class="text-[11px] text-gray-400 leading-relaxed">
                {{ $helpText ?? "Định dạng: JPG, PNG, WEBP, GIF. Tối đa {$maxSizeMb}MB." }}
            </div>

            <p id="error-msg-{{ $inputId }}" class="text-[11px] font-semibold text-rose-500 hidden"></p>
            @error($name)
                <p class="text-[11px] font-semibold text-rose-500 mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.image-picker-component').forEach(wrapper => {
        const fileInput = wrapper.querySelector('input[type="file"]');
        const img = wrapper.querySelector('img');
        const clearBtn = wrapper.querySelector('button[id^="btn-clear-"]');
        const errorMsg = wrapper.querySelector('p[id^="error-msg-"]');
        const initialSrc = wrapper.getAttribute('data-initial-src');
        const maxSizeMb = parseFloat(wrapper.getAttribute('data-max-size') || '3');

        if (!fileInput || !img) return;

        fileInput.addEventListener('change', (e) => {
            const file = e.target.files && e.target.files[0];
            if (errorMsg) errorMsg.classList.add('hidden');

            if (!file) {
                return;
            }

            // Client-side file size check
            if (file.size > maxSizeMb * 1024 * 1024) {
                if (errorMsg) {
                    errorMsg.textContent = `Dung lượng ảnh vượt quá giới hạn ${maxSizeMb}MB. Vui lòng chọn ảnh nhỏ hơn.`;
                    errorMsg.classList.remove('hidden');
                }
                fileInput.value = '';
                return;
            }

            // Client-side MIME check
            const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/avif'];
            if (!allowedTypes.includes(file.type)) {
                if (errorMsg) {
                    errorMsg.textContent = 'Định dạng tệp không được hỗ trợ. Vui lòng chọn ảnh JPG, PNG, WEBP hoặc GIF.';
                    errorMsg.classList.remove('hidden');
                }
                fileInput.value = '';
                return;
            }

            const objectUrl = URL.createObjectURL(file);
            img.src = objectUrl;
            if (clearBtn) clearBtn.classList.remove('hidden');
        });

        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                fileInput.value = '';
                img.src = initialSrc || '';
                clearBtn.classList.add('hidden');
                if (errorMsg) errorMsg.classList.add('hidden');
            });
        }
    });
});
</script>
@endpush
@endonce

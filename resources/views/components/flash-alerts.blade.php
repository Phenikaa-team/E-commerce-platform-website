@props([
    'context' => 'global'
])

@php
    $hasAlerts = session('success') || session('error') || session('warning') || session('info') || ($errors->any() && !in_array($context, ['inline-form']));
@endphp

@if($hasAlerts)
    <div class="global-alerts-container" id="global-flash-toasts" data-context="{{ $context }}" role="region" aria-label="Thông báo hệ thống">
        @if(session('success'))
            <div class="alert-box alert-box-success" role="alert">
                <div class="alert-box-icon">
                    <x-icon name="check" class="w-4 h-4 text-emerald-600" />
                </div>
                <div class="alert-box-content">
                    <span class="alert-box-title">Thành công</span>
                    <span class="alert-box-message">{{ session('success') }}</span>
                </div>
                <button type="button" class="alert-box-close" aria-label="Đóng thông báo" onclick="this.closest('.alert-box').remove()">
                    <x-icon name="close" class="w-3.5 h-3.5" />
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert-box alert-box-error" role="alert">
                <div class="alert-box-icon">
                    <x-icon name="close" class="w-4 h-4 text-rose-600" />
                </div>
                <div class="alert-box-content">
                    <span class="alert-box-title">Lỗi xử lý</span>
                    <span class="alert-box-message">{{ session('error') }}</span>
                </div>
                <button type="button" class="alert-box-close" aria-label="Đóng thông báo" onclick="this.closest('.alert-box').remove()">
                    <x-icon name="close" class="w-3.5 h-3.5" />
                </button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert-box alert-box-warning" role="alert">
                <div class="alert-box-icon">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="alert-box-content">
                    <span class="alert-box-title">Lưu ý</span>
                    <span class="alert-box-message">{{ session('warning') }}</span>
                </div>
                <button type="button" class="alert-box-close" aria-label="Đóng thông báo" onclick="this.closest('.alert-box').remove()">
                    <x-icon name="close" class="w-3.5 h-3.5" />
                </button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert-box alert-box-info" role="alert">
                <div class="alert-box-icon">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="alert-box-content">
                    <span class="alert-box-title">Thông tin</span>
                    <span class="alert-box-message">{{ session('info') }}</span>
                </div>
                <button type="button" class="alert-box-close" aria-label="Đóng thông báo" onclick="this.closest('.alert-box').remove()">
                    <x-icon name="close" class="w-3.5 h-3.5" />
                </button>
            </div>
        @endif

        @if($errors->any() && !in_array($context, ['inline-form']))
            <div class="alert-box alert-box-error" role="alert">
                <div class="alert-box-icon">
                    <x-icon name="close" class="w-4 h-4 text-rose-600" />
                </div>
                <div class="alert-box-content">
                    <span class="alert-box-title">Có lỗi nhập liệu</span>
                    <span class="alert-box-message">
                        @if($errors->count() === 1)
                            {{ $errors->first() }}
                        @else
                            {{ $errors->first() }} (và {{ $errors->count() - 1 }} lỗi khác)
                        @endif
                    </span>
                </div>
                <button type="button" class="alert-box-close" aria-label="Đóng thông báo" onclick="this.closest('.alert-box').remove()">
                    <x-icon name="close" class="w-3.5 h-3.5" />
                </button>
            </div>
        @endif
    </div>
@endif

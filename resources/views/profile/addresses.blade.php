@extends('layouts.app')

@section('title', 'Địa chỉ của tôi - ' . ($user->username ?? $user->name) . ' | ShopMart')
@section('meta_description', 'Quản lý sổ địa chỉ nhận hàng tiện lợi với ShopMart.')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    .leaflet-container {
        font-family: inherit;
        z-index: 10 !important;
    }
</style>
@endpush

@section('content')
<div class="page-container py-6">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
        <!-- LEFT COLUMN: SIDEBAR MENU -->
        <div class="lg:col-span-3">
            <x-user-sidebar active="addresses" />
        </div>

        <!-- RIGHT COLUMN: ADDRESS LIST -->
        <div class="lg:col-span-9 space-y-6">
            
            <div class="bg-white rounded-2xl border border-gray-100 p-6 sm:p-8 shadow-xs">
                <!-- Top Bar -->
                <div class="pb-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Địa chỉ của tôi</h1>
                        <p class="text-xs text-gray-500 mt-0.5">Quản lý các địa chỉ giao nhận hàng dùng khi thanh toán</p>
                    </div>
                    <button 
                        type="button" 
                        onclick="window.AddressModalManager?.openCreateModal()" 
                        class="btn btn-primary btn-sm flex items-center gap-1.5"
                    >
                        <x-icon name="plus" class="w-4 h-4" />
                        <span>Thêm địa chỉ</span>
                    </button>
                </div>

                <!-- Address List Items (Compact Marketplace Rows) -->
                <div class="divide-y divide-gray-100">
                    @forelse($user->addresses as $address)
                        <div class="py-4 flex flex-col sm:flex-row sm:items-start justify-between gap-3 group">
                            <div class="space-y-1.5 flex-1 min-w-0">
                                <div class="flex items-center gap-2.5 flex-wrap">
                                    <span class="font-bold text-sm text-gray-900">{{ $address->recipient_name }}</span>
                                    <span class="text-gray-300">|</span>
                                    <span class="text-xs text-gray-600 font-medium font-mono">{{ $address->phone }}</span>
                                    @if($address->is_default)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-200">
                                            Mặc định
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-600 leading-relaxed max-w-2xl flex items-start gap-1.5">
                                    <x-icon name="map-pin" class="w-3.5 h-3.5 text-gray-400 shrink-0 mt-0.5" />
                                    <span>{{ $address->address_line }}</span>
                                </p>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-3 shrink-0 self-start sm:self-center text-xs">
                                <button 
                                    type="button" 
                                    onclick='window.AddressModalManager?.openEditModal(@json($address))' 
                                    class="text-blue-600 hover:text-blue-700 font-semibold cursor-pointer hover:underline"
                                >
                                    Sửa
                                </button>

                                @if(! $address->is_default)
                                    <form action="{{ route('profile.address.delete', $address->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa địa chỉ này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-500 hover:text-rose-700 font-semibold cursor-pointer hover:underline">
                                            Xóa
                                        </button>
                                    </form>

                                    <form action="{{ route('profile.address.default', $address->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-gray-600 hover:text-primary font-medium border border-gray-200 hover:border-primary px-2.5 py-1 rounded-lg transition-colors cursor-pointer">
                                            Thiết lập mặc định
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="w-14 h-14 rounded-full bg-rose-50 text-primary mx-auto flex items-center justify-center mb-3">
                                <x-icon name="map-pin" class="w-7 h-7 text-primary" />
                            </div>
                            <h3 class="text-sm font-bold text-gray-800">Chưa có địa chỉ nào</h3>
                            <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">Thêm địa chỉ nhận hàng để việc đặt hàng và thanh toán diễn ra nhanh chóng hơn.</p>
                            <button 
                                type="button" 
                                onclick="window.AddressModalManager?.openCreateModal()" 
                                class="btn btn-primary btn-sm mt-4"
                            >
                                + Thêm địa chỉ mới
                            </button>
                        </div>
                    @endforelse
                </div>

            </div>

        </div>

    </div>
</div>

<!-- Shared Reusable Address Form Modal (No giant map, compact 2-column) -->
<x-address-form-modal id="address-form-modal" />
@endsection

@push('scripts')
    <!-- Leaflet Map JS for optional map selection -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="{{ asset('js/address-manager.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.AddressModalManager) {
                window.AddressModalManager.init('profile');
            }
        });
    </script>
@endpush

<tr class="hover:bg-gray-50/70">
    <td class="px-4 py-4 font-bold text-gray-900">{{ $campaign->name }}</td>
    @if($schedule === 'manual')
        <td class="px-4 py-4 text-gray-600">{{ $campaign->starts_at?->format('d/m/Y H:i') }}</td>
        <td class="px-4 py-4 text-gray-600">{{ $campaign->ends_at?->format('d/m/Y H:i') }}</td>
    @else
        <td class="px-4 py-4 text-gray-600">
            {{ collect($campaign->recurring_dates ?? [])->map(fn ($date) => substr($date, 3, 2).'/'.substr($date, 0, 2))->implode(', ') }}
        </td>
        <td class="px-4 py-4 text-gray-600">{{ substr((string) $campaign->daily_starts_at, 0, 5) }} – {{ substr((string) $campaign->daily_ends_at, 0, 5) }}</td>
    @endif
    <td class="px-4 py-4 font-semibold text-gray-700">{{ $campaign->items_count }}</td>
    <td class="px-4 py-4">
        <span class="rounded-full border px-2 py-1 text-[10px] font-bold {{ $campaign->is_active ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-gray-200 bg-gray-100 text-gray-500' }}">
            {{ $campaign->is_active ? 'Đang bật' : 'Đã tắt' }}
        </span>
    </td>
    <td class="px-4 py-4 text-right">
        <div class="inline-flex items-center gap-2">
            <a href="{{ route('admin.flash-sales.edit', $campaign->id) }}" class="rounded-lg px-2 py-1 text-sky-600 hover:bg-sky-50">Sửa</a>
            <form action="{{ route('admin.flash-sales.toggle', $campaign->id) }}" method="POST">
                @csrf
                <button type="submit" class="rounded-lg px-2 py-1 text-amber-600 hover:bg-amber-50">{{ $campaign->is_active ? 'Tắt' : 'Bật' }}</button>
            </form>
            <form action="{{ route('admin.flash-sales.destroy', $campaign->id) }}" method="POST" onsubmit="return confirm('Xóa lịch Flash Sale này và các sản phẩm đã đăng ký?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-lg px-2 py-1 text-rose-600 hover:bg-rose-50">Xóa</button>
            </form>
        </div>
    </td>
</tr>

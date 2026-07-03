<div class="flex flex-wrap gap-1">
    @php
        $old = $getRecord()->properties['old'] ?? [];
        $new = $getRecord()->properties['attributes'] ?? [];
        $changes = collect($new)->map(function($val, $key) use ($old) {
            if (!isset($old[$key]) || $old[$key] !== $val) {
                return $key;
            }
            return null;
        })->filter()->values();
    @endphp

    @if($changes->isNotEmpty())
        <span class="text-xs text-gray-500">{{ $changes->take(3)->join(', ') }}{{ $changes->count() > 3 ? '...' : '' }}</span>
    @else
        <span class="text-xs text-gray-400">-</span>
    @endif
</div>

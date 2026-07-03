<div class="p-4 space-y-4">
    <div class="grid grid-cols-2 gap-4 text-sm">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Waktu</span>
            <p class="font-semibold mt-1">{{ $record->created_at->format('d M Y H:i:s') }}</p>
        </div>
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-gray-400">User</span>
            <p class="font-semibold mt-1">{{ $record->causer?->name ?? 'System' }}</p>
        </div>
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Deskripsi</span>
            <p class="mt-1">{{ $record->description }}</p>
        </div>
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Model</span>
            <p class="mt-1">{{ class_basename($record->subject_type) }} ({{ $record->subject_id ?? '-' }})</p>
        </div>
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-gray-400">IP</span>
            <p class="font-mono mt-1">{{ $record->properties['ip'] ?? '-' }}</p>
        </div>
    </div>

    @if(!empty($old) || !empty($new))
        <div class="border-t border-gray-200 dark:border-gray-800 pt-4">
            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">Perubahan Data</h4>

            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-400 border-b border-gray-200 dark:border-gray-800">
                        <th class="pb-2 font-semibold">Field</th>
                        <th class="pb-2 font-semibold">Sebelum</th>
                        <th class="pb-2 font-semibold">Sesudah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($new as $field => $val)
                        @php
                            $before = $old[$field] ?? '-';
                            $changed = (!isset($old[$field]) || $old[$field] !== $val);
                        @endphp
                        <tr class="{{ $changed ? 'bg-amber-50 dark:bg-amber-900/10' : '' }}">
                            <td class="py-2 pr-4 font-semibold">{{ $field }}</td>
                            <td class="py-2 pr-4 {{ $changed ? 'text-red-600 line-through' : 'text-gray-400' }}">
                                {{ is_scalar($before) ? $before : json_encode($before) }}
                            </td>
                            <td class="py-2 {{ $changed ? 'text-emerald-600 font-bold' : '' }}">
                                {{ is_scalar($val) ? $val : json_encode($val) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

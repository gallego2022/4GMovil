@props(['delta' => null])

@php
    $isNull = is_null($delta);
    $isUp = !$isNull && $delta >= 0;
    $classes = $isNull
        ? 'bg-gray-100 text-gray-700 dark:bg-gray-700/30 dark:text-gray-300'
        : ($isUp
            ? 'bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500'
            : 'bg-red-50 text-red-600 dark:bg-red-500/15 dark:text-red-500');
    $value = $isNull ? 0 : number_format(abs($delta), 2);
@endphp

<span class="flex items-center gap-1 rounded-full {{ $classes }} py-0.5 pl-2 pr-2.5 text-sm font-medium">
    <svg class="fill-current" width="12" height="12" viewBox="0 0 12 12" aria-hidden="true">
        @if($isNull)
            <!-- símbolo neutro (guion) -->
            <rect x="2" y="5.5" width="8" height="1" rx="0.5" />
        @elseif($isUp)
            <path fill-rule="evenodd" clip-rule="evenodd" d="M5.56462 1.62393C5.70193 1.47072 5.90135 1.37432 6.12329 1.37432C6.1236 1.37432 6.12391 1.37432 6.12422 1.37432C6.31631 1.37415 6.50845 1.44731 6.65505 1.59381L9.65514 4.5918C9.94814 4.88459 9.94831 5.35947 9.65552 5.65246C9.36273 5.94546 8.88785 5.94562 8.59486 5.65283L6.87329 3.93247V10.125C6.87329 10.5392 6.53751 10.875 6.12329 10.875C5.70908 10.875 5.37329 10.5392 5.37329 10.125V3.93578L3.65516 5.65282C3.36218 5.94562 2.8873 5.94547 2.5945 5.65248C2.3017 5.35949 2.30185 4.88462 2.59484 4.59182L5.56462 1.62393Z" />
        @else
            <g transform="rotate(180 6 6)">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M5.56462 1.62393C5.70193 1.47072 5.90135 1.37432 6.12329 1.37432C6.1236 1.37432 6.12391 1.37432 6.12422 1.37432C6.31631 1.37415 6.50845 1.44731 6.65505 1.59381L9.65514 4.5918C9.94814 4.88459 9.94831 5.35947 9.65552 5.65246C9.36273 5.94546 8.88785 5.94562 8.59486 5.65283L6.87329 3.93247V10.125C6.87329 10.5392 6.53751 10.875 6.12329 10.875C5.70908 10.875 5.37329 10.5392 5.37329 10.125V3.93578L3.65516 5.65282C3.36218 5.94562 2.8873 5.94547 2.5945 5.65248C2.3017 5.35949 2.30185 4.88462 2.59484 4.59182L5.56462 1.62393Z" />
            </g>
        @endif
    </svg>
    {{ $value }}%
</span>



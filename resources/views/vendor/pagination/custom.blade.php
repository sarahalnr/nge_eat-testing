@if ($paginator->hasPages())
    <div class="flex justify-between items-center mt-4 px-6 py-3 bg-white rounded-b-xl shadow-inner text-sm">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="text-gray-400">&laquo; Sebelumnya</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="text-gray-500 hover:text-orange-600 font-medium">&laquo; Sebelumnya</a>
        @endif

        {{-- Page Numbers --}}
        <nav class="flex items-center gap-2">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="text-gray-400">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="inline-flex items-center justify-center w-8 h-8 text-white bg-orange-500 rounded-full font-semibold">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="w-8 h-8 flex items-center justify-center rounded-full text-gray-700 hover:bg-gray-100 hover:text-orange-500 font-medium">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </nav>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="text-gray-500 hover:text-orange-600 font-medium">Selanjutnya &raquo;</a>
        @else
            <span class="text-gray-400">Selanjutnya &raquo;</span>
        @endif
    </div>
@endif

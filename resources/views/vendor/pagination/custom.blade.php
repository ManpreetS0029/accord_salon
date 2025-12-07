@if ($paginator->hasPages())
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="per_page">Records per page:</label>
                <select name="per_page" id="per_page" class="form-control" style="width: 120px; display: inline-block;" onchange="changePerPage(this.value)">
                    <option value="10" {{ request('per_page', 50) == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page', 50) == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page', 50) == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page', 50) == 100 ? 'selected' : '' }}>100</option>
                    <option value="200" {{ request('per_page', 50) == 200 ? 'selected' : '' }}>200</option>
                </select>
            </div>
        </div>
        <div class="col-md-6 text-center">
            <nav>
                <ul class="pagination" style="margin-bottom: 0;">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                            <span class="page-link" aria-hidden="true">&lsaquo;</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
                        </li>
                    @else
                        <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                            <span class="page-link" aria-hidden="true">&rsaquo;</span>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
        <div class="col-md-3 text-right">
            <p style="margin-top: 8px;">
                Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} records
            </p>
        </div>
    </div>

    <script>
    function changePerPage(perPage) {
        var url = new URL(window.location.href);
        url.searchParams.set('per_page', perPage);
        url.searchParams.set('page', '1'); // Reset to first page
        window.location.href = url.toString();
    }
    </script>
@endif


@if($model->hasPages())
<nav>
    <ul class="pagination">
        {{-- Nút Previous --}}
        @if ($model->onFirstPage())
            <li class="page-item disabled"><span class="page-link">‹</span></li>
        @else
            @php
                $prevUrl = str_replace('?page=', '/trang-', $model->previousPageUrl());
                $prevUrl = str_replace('/trang-1', '', $prevUrl);
            @endphp
            <li class="page-item">
                <a class="page-link" href="{{ $prevUrl }}" rel="prev" aria-label="« Previous">‹</a>
            </li>
        @endif

        {{-- Các số trang --}}
        @foreach($model->getUrlRange(1, $model->lastPage()) as $page => $url)
            @php
                $paginationUrl = str_replace('?page=', '/trang-', $url);
                $paginationUrl = ($page == 1) ? str_replace('/trang-'.$page, '', $paginationUrl) : $paginationUrl;
            @endphp
            <li class="page-item {{ ($page == $model->currentPage()) ? 'active' : '' }}">
                <a class="page-link" href="{{ $paginationUrl }}">{{ $page }}</a>
            </li>
        @endforeach

        {{-- Nút Next --}}
        @if ($model->hasMorePages())
            @php
                $nextUrl = str_replace('?page=', '/trang-', $model->nextPageUrl());
                $nextUrl = str_replace('/trang-1', '', $nextUrl);
            @endphp
            <li class="page-item">
                <a class="page-link" href="{{ $nextUrl }}" rel="next" aria-label="Next »">›</a>
            </li>
        @else
            <li class="page-item disabled"><span class="page-link">›</span></li>
        @endif
    </ul>
</nav>
@endif

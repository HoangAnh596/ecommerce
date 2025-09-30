@php
    $coreJs = [
        'frontend/resources/plugins/wow/dist/wow.min.js',
        'frontend/resources/uikit/js/uikit.min.js',
        'frontend/resources/uikit/js/components/sticky.min.js',
        'frontend/core/plugins/jquery-nice-select-1.1.0/js/jquery.nice-select.min.js',
        'frontend/resources/function.js'
    ];
@endphp
@foreach($coreJs as $item)
<script src="{{ asset($item) }}"></script>
@endforeach
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<div id="fb-root"></div>
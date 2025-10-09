@php
    $coreJs = [
        'backend/js/plugins/toastr/toastr.min.js',
        'frontend/resources/plugins/wow/dist/wow.min.js',
        'frontend/resources/uikit/js/uikit.min.js',
        'frontend/resources/uikit/js/components/sticky.min.js',
        'frontend/core/plugins/jquery-nice-select-1.1.0/js/jquery.nice-select.min.js',
        'frontend/resources/function.js'
    ];

    if(isset($config['js'])) {
        foreach($config['js'] as $key => $val) {
            array_push($coreJs, $val);
        }
    }
@endphp
@foreach($coreJs as $item)
<script src="{{ asset($item) }}"></script>
@endforeach
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<div id="fb-root"></div>
<base href="{{ config('app.url') }}">
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<meta name="author" content="{{ $system['homepage_company'] }}">
<meta name="copyright" content="{{ $system['homepage_company'] }}">
<meta http-equiv="refresh" content="1800">
<link rel="icon" href="" type="image/png" sizes="30x30">
<meta name="theme-color" content="">
<!-- GOOGLE -->
<title>{{ $system['seo_meta_title'] }}</title>
<meta name="keywords" content="{{ $system['seo_meta_keyword'] }}">
<meta name="description" content="{{ $system['seo_meta_description'] }}">
<link rel="canonical" href="{{ config('app.url') }}">
<meta property="og:locale" content="vi_VN">
<!-- for Facebook -->
<meta property="og:title" content="{{ $system['seo_meta_title'] }}">
<meta property="og:type" content="website">
<meta property="og:image" content="{{ $system['seo_meta_images'] }}">
<meta property="og:url" content="{{ config('app.url') }}">
<meta property="og:description" content="{{ $system['seo_meta_description'] }}">
<meta property="og:site_name" content="">
<meta property="fb:admins" content="">
<meta property="fb:app_id" content="">
<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="{{ $system['seo_meta_title'] }}">
<meta name="twitter:description" content="{{ $system['seo_meta_description'] }}">
<meta name="twitter:image" content="{{ $system['seo_meta_images'] }}">

@php
    $coreCss = [
        'frontend/resources/library/css/library.css',
        'frontend/resources/style.css',
        'frontend/resources/fonts/font-awesome-4.7.0/css/font-awesome.min.css',
        'frontend/resources/uikit/css/uikit.modify.css',
        'frontend/resources/plugins/wow/css/libs/animate.css'
    ];
@endphp
@foreach($coreCss as $item)
<link rel="stylesheet" href="{{ asset($item) }}">
@endforeach
<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
<script src="{{ asset('frontend/resources/library/js/jquery.js') }}"></script>
<title>Home 2 | Economic Marketplace</title>
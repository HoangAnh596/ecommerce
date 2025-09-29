<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\System;

class FrontendController extends Controller
{
    protected $language;
    protected $system;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;

            $system = System::where('language_id', $language->id)->get();
            $this->system = convert_array($system, 'keyword', 'content');

            return $next($request);
        });
    }
}

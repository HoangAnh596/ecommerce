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
        $this->setLanguage();
        $this->setSystem();
    }

    public function setLanguage()
    {
        $locale = app()->getLocale();
        $language = Language::where('canonical', $locale)->first();
        $this->language = $language->id;
    }

    public function setSystem()
    {
        $system = System::where('language_id', $this->language)->get();
        $this->system = convert_array($system, 'keyword', 'content');
    }
}

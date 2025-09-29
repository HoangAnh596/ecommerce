<?php
namespace App\Enums;

enum SlideEnum: string {
    const BANNER = 'banner';
    const MAIN_SLIDE = 'main-slide';

    public static function toArray(): array {
        return [
            self::BANNER => 'banner',
            self::MAIN_SLIDE => 'main-slide',
        ];
    }
}
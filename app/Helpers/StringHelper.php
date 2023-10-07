<?php

namespace App\Helpers;

class StringHelper{
    public static function normalizeString($string){
        $string = str_replace(["<b>", "</b>"], "", $string);
        $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
        $string = mb_strtolower($string, 'UTF-8');
        $string = trim($string);
        return $string;
    }

    public static function normalizeHotel($string){
        return preg_replace('/\d+\.\s+/', '', $string);
    }
}
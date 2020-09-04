<?php
namespace App\Lib;
use Illuminate\Http\Request;

final class Token {
    public static function get(Request $request) : string
    {
        return $request->cookie('api_token');
    }
}
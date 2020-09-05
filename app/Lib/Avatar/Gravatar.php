<?php
namespace App\Lib\Avatar;

class Gravatar implements IAvatar{
    public function generate(string $value): string
    {
        $result = 'https://www.gravatar.com/avatar/';
        $result .= md5(strtolower(trim($value)));
        $result .= '?d=wavatar&s=200';
        return $result;
    }
}
<?php 
namespace App\Lib\Avatar;

interface IAvatar {
    function generate(string $value) : string;
}
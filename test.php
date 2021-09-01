<?php

include "vendor/autoload.php";


$dir = new \DirShow\tool\DirShow('./test');

$res = $dir->setFilter(function($file){
    return [
        'type'  => 'xls',
        'name'  => $file
    ];
})->show();

var_dump($res);

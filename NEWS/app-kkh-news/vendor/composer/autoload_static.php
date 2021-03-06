<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit10a407a3029e1e5b33f48e14f717228a
{
    public static $files = array (
        '7b11c4dc42b3b3023073cb14e519683c' => __DIR__ . '/..' . '/ralouphie/getallheaders/src/getallheaders.php',
        'a0edc8309cc5e1d60e3047b5df6b7052' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/functions_include.php',
    );

    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Psr\\Http\\Message\\' => 17,
        ),
        'M' => 
        array (
            'Mobile\\' => 7,
        ),
        'I' => 
        array (
            'Intervention\\Image\\' => 19,
        ),
        'G' => 
        array (
            'GuzzleHttp\\Psr7\\' => 16,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Psr\\Http\\Message\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-message/src',
        ),
        'Mobile\\' => 
        array (
            0 => __DIR__ . '/../..' . '/classes',
        ),
        'Intervention\\Image\\' => 
        array (
            0 => __DIR__ . '/..' . '/intervention/image/src/Intervention/Image',
        ),
        'GuzzleHttp\\Psr7\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/psr7/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'E' => 
        array (
            'Eluceo\\iCal' => 
            array (
                0 => __DIR__ . '/..' . '/eluceo/ical/src',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit10a407a3029e1e5b33f48e14f717228a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit10a407a3029e1e5b33f48e14f717228a::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit10a407a3029e1e5b33f48e14f717228a::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}

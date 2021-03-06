<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitd544c891b9a8673dece8ebe037319ca3
{
    public static $files = array (
        'b50336562d531777993d90ca775abd88' => __DIR__ . '/../..' . '/controller.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Sober\\Controller\\Module\\' => 24,
            'Sober\\Controller\\' => 17,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Sober\\Controller\\Module\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/Module',
        ),
        'Sober\\Controller\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitd544c891b9a8673dece8ebe037319ca3::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitd544c891b9a8673dece8ebe037319ca3::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}

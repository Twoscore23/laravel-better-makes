<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit99751d70109ccfd7b49d6a2176ac70ce
{
    public static $files = array (
        '81aaed3db3b4cd11ca2c66f81de54dd3' => __DIR__ . '/../..' . '/helpers.php',
    );

    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'Twoscore23\\LaravelBetterMakes\\Commands\\' => 39,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Twoscore23\\LaravelBetterMakes\\Commands\\' => 
        array (
            0 => __DIR__ . '/../..' . '/packages/twoscore23/laravel-better-makes/Commands',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit99751d70109ccfd7b49d6a2176ac70ce::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit99751d70109ccfd7b49d6a2176ac70ce::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit99751d70109ccfd7b49d6a2176ac70ce::$classMap;

        }, null, ClassLoader::class);
    }
}

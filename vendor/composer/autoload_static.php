<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitea6d81b316b191cd7143c38deabee74d
{
    public static $prefixLengthsPsr4 = array (
        'J' => 
        array (
            'Jmrashed\\Zkteco\\' => 16,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Jmrashed\\Zkteco\\' => 
        array (
            0 => __DIR__ . '/..' . '/jmrashed/zkteco/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitea6d81b316b191cd7143c38deabee74d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitea6d81b316b191cd7143c38deabee74d::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitea6d81b316b191cd7143c38deabee74d::$classMap;

        }, null, ClassLoader::class);
    }
}

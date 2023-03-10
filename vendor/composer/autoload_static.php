<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc58dbd53802e240dcf2aa495a8ffcbfa
{
    public static $files = array (
        '290dd4ba42f11019134caca05dbefe3f' => __DIR__ . '/..' . '/teamtnt/tntsearch/helper/helpers.php',
    );

    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'TeamTNT\\TNTSearch\\' => 18,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'TeamTNT\\TNTSearch\\' => 
        array (
            0 => __DIR__ . '/..' . '/teamtnt/tntsearch/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitc58dbd53802e240dcf2aa495a8ffcbfa::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitc58dbd53802e240dcf2aa495a8ffcbfa::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitc58dbd53802e240dcf2aa495a8ffcbfa::$classMap;

        }, null, ClassLoader::class);
    }
}

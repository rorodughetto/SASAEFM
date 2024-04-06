<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitb088a00a1279378cb7059d5917f5f32d
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitb088a00a1279378cb7059d5917f5f32d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitb088a00a1279378cb7059d5917f5f32d::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}

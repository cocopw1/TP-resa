<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit3f4d099c387ce6fdf591e418e0bd7bdc
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        require __DIR__ . '/platform_check.php';

        spl_autoload_register(array('ComposerAutoloaderInit3f4d099c387ce6fdf591e418e0bd7bdc', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit3f4d099c387ce6fdf591e418e0bd7bdc', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInit3f4d099c387ce6fdf591e418e0bd7bdc::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}

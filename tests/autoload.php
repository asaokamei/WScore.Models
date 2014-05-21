<?php
$loader = include( dirname( __DIR__ ) . '/vendor/autoload.php' );
/** @var Composer\Autoload\ClassLoader $loader */
$loader->addPsr4( 'tests\\', __DIR__.'');
$loader->register();
<?php

require_once( dirname(__DIR__).'/autoload.php' );
require_once( __DIR__ . '/UsersModel/Users.php' );
require_once( __DIR__ . '/UsersModel/UsersDao.php' );

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Events\Dispatcher;

class ConfigDB
{
    /**
     * @var Manager
     */
    public static $capsule;
    
    /**
     * @return Manager
     */
    public static function buildCapsule()
    {
        $capsule = new Manager();
        $capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'test_models',
            'username'  => 'admin',
            'password'  => 'admin',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => ''
        ]);
        $capsule->setEventDispatcher(new Dispatcher(new Container()));
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        return $capsule;
    }

    /**
     * @return Manager
     */
    public static function getCapsule()
    {
        if( !static::$capsule ) {
            static::$capsule = static::buildCapsule();
        }
        return static::$capsule;
    }

    /**
     * 
     */
    public static function setupTable()
    {
        $capsule = static::getCapsule();
        $connection = $capsule->getConnection();
        $connection->setSchemaGrammar(new Illuminate\Database\Schema\Grammars\MySqlGrammar() );
        $schema = new \Illuminate\Database\Schema\Builder( $connection );

        $schema->dropIfExists( 'users' );

        $schema->create( 'users', function ( $table ) {
            /** @var Blueprint $table */
            $table->increments( 'user_id' );
            $table->integer( 'status' );
            $table->string( 'password', 512 );
            $table->string( 'gender', 1 );
            $table->string( 'name', 1024 );
            $table->date( 'birth_date' );
            $table->string( 'email', 512 );
            $table->timestamps();
        } );
    }
}

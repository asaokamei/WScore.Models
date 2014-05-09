<?php

require_once( dirname(__DIR__).'/autoload.php' );
require_once( __DIR__.'/migrate.php' );

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Events\Dispatcher;

$capsule = new Manager();
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'database'  => 'test_WScore',
    'username'  => 'admin',
    'password'  => 'admin',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => 'models_'
]);
$capsule->setEventDispatcher(new Dispatcher(new Container()));
$capsule->setAsGlobal();
$capsule->bootEloquent();

//$capsule->table('tests')->get();
//Manager::table('users')->get();

//$migrate = new CreatePostTable();
//$migrate->up();

$connection = $capsule->getConnection();
$connection->setSchemaGrammar(new Illuminate\Database\Schema\Grammars\MySqlGrammar() );
$schema = new \Illuminate\Database\Schema\Builder( $connection );

$schema->create( 'model_users', function ( $table ) {
    /** @var Blueprint $table */
    $table->increments( 'post_id' );
    $table->integer( 'status' );
    $table->string( 'title', 1024 );
    $table->string( 'content', 1024 * 10 );
    $table->timestamp( 'publishAt' );
    $table->timestamps();
} );

return $capsule;
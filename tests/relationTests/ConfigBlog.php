<?php

require_once( dirname(__DIR__).'/autoload.php' );

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Illuminate\Events\Dispatcher;

ConfigBlog::setupTables();

class ConfigBlog
{
    /**
     * @var Manager
     */
    public static $capsule;

    /**
     * @var Builder
     */
    public static $schema;
    
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
    public static function setupTables()
    {
        static::$capsule = static::getCapsule();
        $connection = static::$capsule->getConnection();
        $connection->setSchemaGrammar(new Illuminate\Database\Schema\Grammars\MySqlGrammar() );
        static::$schema = new Builder( $connection );

        static::setAuthorTable();
        static::setBlogTable();
        static::setCommentTable();
        static::setRoleTable();
        static::setAuthorRoleTable();
    }

    /**
     *
     */
    public static function setAuthorTable()
    {
        static::$schema->dropIfExists( 'blog_author' );

        static::$schema->create( 'blog_author', function ( $table ) {
            /** @var Blueprint $table */
            $table->increments( 'author_id' );
            $table->integer(    'status' );
            $table->string(     'password', 512 );
            $table->string(     'gender', 1 );
            $table->string(     'name', 512 );
            $table->date(       'birth_date' );
            $table->string(     'email', 1024 );
            $table->timestamps();
        } );
    }

    /**
     *
     */
    public static function setRoleTable()
    {
        static::$schema->dropIfExists( 'blog_role' );

        static::$schema->create( 'blog_role', function ( $table ) {
            /** @var Blueprint $table */
            $table->increments( 'role_id' );
            $table->string(     'status', 1 );
            $table->string(     'role', 128 );
            $table->timestamps();
        } );
    }

    /**
     *
     */
    public static function setAuthorRoleTable()
    {
        static::$schema->dropIfExists( 'blog_author_role' );

        static::$schema->create( 'blog_author_role', function ( $table ) {
            /** @var Blueprint $table */
            $table->integer( 'role_id' );
            $table->integer( 'author_id' );
            $table->timestamps();
        } );
    }

    /**
     *
     */
    public static function setBlogTable()
    {
        static::$schema->dropIfExists( 'blog_blog' );

        static::$schema->create( 'blog_blog', function ( $table ) {
            /** @var Blueprint $table */
            $table->increments( 'blog_id' );
            $table->integer(    'author_id' );
            $table->integer(    'status' );
            $table->string(     'title', 512 );
            $table->text(       'content' );
            $table->timestamps();
        } );
    }

    /**
     *
     */
    public static function setCommentTable()
    {
        static::$schema->dropIfExists( 'blog_comment' );

        static::$schema->create( 'blog_comment', function ( $table ) {
            /** @var Blueprint $table */
            $table->increments( 'comment_id' );
            $table->integer(    'blog_id' );
            $table->integer(    'author_id' );
            $table->integer(    'status' );
            $table->text(       'comment' );
            $table->timestamps();
        } );
    }
}

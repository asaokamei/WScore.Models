<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreatePostTable extends Migration
{

    protected $table = 'post';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( $this->table, function ( $table ) {
            /** @var Blueprint $table */
            $table->increments( 'post_id' );
            $table->integer( 'status' );
            $table->string( 'title', 1024 );
            $table->string( 'content', 1024 * 10 );
            $table->timestamp( 'publishAt' );
            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop( $this->table );
    }

}

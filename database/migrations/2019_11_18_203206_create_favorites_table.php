<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFavoritesTable extends Migration
{
   
    public function up()
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->integer('micropost_id')->unsigned()->index();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            //favoritesテーブルのuser_idにはusersテーブルの関係する列
            
            $table->foreign('micropost_id')->references('id')->on('microposts')->onDelete('cascade');
            //favoritesテーブルのmicropost_idにはmicropostsテーブルの関係する列
            
            $table->unique(['user_id','micropost_id']);

        });
    }

    
    public function down()
    {
        Schema::dropIfExists('favorites');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserFollowTable extends Migration
{
    public function up()
    {
        
        
//------------------------------------------------------------------------------中間テーブルを作る。
        Schema::create('user_follow', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->integer('follow_id')->unsigned()->index();
            $table->timestamps();
            
            //外部キー設定
            $table->foreign('user_id')->references('id')->on('users')->onDlete('cascade');
            $table->foreign('follow_id')->references('id')->on('users')->onDlete('cascade');
            
            //follow_idは名前は違えどuser_idと同じ立ち位置。
            //onDelete 参照先のデータ削除時の挙動を制御。
            //種類は4つ。set null-IDをNULLへ。 no action-マリモ⑨ cascade - tableのデータも消える。restrict-参照先のデータが消せない。
            
            // user_id follo_idの組み合わせで重複が無いようにする。
            //一度保存したフォロー関係を何度も保存しないようにする。
            $table->unique(['user_id', 'follow_id']);
            
        });
    }

   
    public function down()
    {
        Schema::dropIfExists('user_follow');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Micropost extends Model
{
    protected $fillable = ['content', 'user_id'];
    //取り扱うカラムの指定
    
    public function user()
    {
        return $this->belongsTo(User::class);
        //一対多の関係 $micropost->user or $micropost->user()->first()　で投稿者情報を呼び出せる。
    }
    
//------------------------------------------------------------------------------多対多の関係を紐付けする。
//お気に入り
    public function favorite_of_user()
    {
        return $this->belongsToMany(User::class,'favorites','micropost_id','user_id')->withTimestamps();
        //Micropostクラスにおいて、中間テーブルfavoritesに保存されている、自分のid micropost_idカラムと関係先のuser_idカラムを紐付けする。
        //<1>得られるモデルクラス <2>中間テーブル <3>中間テーブル自分のid <4>中間テーブル関係先のid
    }
}
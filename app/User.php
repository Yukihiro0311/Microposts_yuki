<?php
//------------------------------------------------------------------------------名前空間
namespace App;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
//------------------------------------------------------------------------------トレイトを選択する。
    use Notifiable;

//------------------------------------------------------------------------------扱うカラムを選択する。
    protected $fillable = [
        'name', 'email', 'password',
    ];

//------------------------------------------------------------------------------非表示にするカラムを選択する。
    protected $hidden = [
        'password', 'remember_token',
    ];
    
//------------------------------------------------------------------------------一対多の関係を紐付けする。    
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
        //$user->microposts or $user->microposts()->get()　　userがもつmicropostsを取得できる。
    }
    
//------------------------------------------------------------------------------多対多の関係を紐付けする。
    public function followings()
    {
        return $this->belongsToMany(User::class,'user_follow','user_id','follow_id')->withTimestamps();
    }
    //Userクラスにおいて、中間テーブルのuser_followカラムが、自分のidが保存されているuser_idカラムとfllow_idカラムと連携していることを示す。
    
    public function followers()
    {
        return $this->belongsToMany(User::class,'user_follow','follow_id','user_id')->withTimestamps();
    }
    //Userクラスにおいて、中間テーブルのuser_followカラムが自分のidが保存されているfollow_idカラムとuser_idカラムと連携していることを示す。
    
    //withTimestampsは中間テーブルにtimestampsを設置する。
    //$user->followings フォローしているuserを取得できる。
    //$user->followers　フォローされいるuserを取得できる。


//------------------------------------------------------------------------------中間テーブルのレコードを保存、削除するメソッドを作る。
//注意点　1ー既にレコードが存在していないか。　2ー相手が自分ではないか。

    public function is_following($userId)//-------------------------------確認
    {//userId仮引数　user_id実引数　両者は同じ。
        return $this->followings()->where('follow_id', $userId)->exists();
        // 既にフォローしているかを確認するためのメソッドを作る。
        //where('A','B') AとBが同じ
        //userがフォローしているuserのidが、フォローしているuserのidが与えられたuserのidと同じか。
    }
    
//------------------------------------------------------------------------------フォローしている人の
        public function feed_microposts()
    {
            $follow_user_ids = $this->followings()->pluck('users.id')->toArray();
            //UserがフォローしているUserのidの配列を取得。
            //pluck()は与えられた引数のテーブルのカラム名だけを抜き出す。
            //toArray()は通常の配列に変換する。
            
            $follow_user_ids[] = $this->id;
            //自分のidも配列に追加する。自分自身のマイクロポストも表示させるため。
            
            return Micropost::whereIn('user_id', $follow_user_ids);
            //Class Micropostにおいてmicropostsテーブルのuser_idで$follow_user_idsの中にあるユーザidを含むもの全てを取得しreturnさせる。
    }

    public function follow($userId)//--------------------------------------保存
    {
       
        $exist = $this->is_following($userId);
        // is_followingメソッドを使って、既にレコードがあるか。
        $its_me = $this->id == $userId;
        // 自分のidが仮引数$userIdと同じか。（自分と相手が同じか。）
    
    
        if ($exist || $its_me) {//---------------------------条件分岐(または)
            return false;
            // 既にレコードが存在。何もしない。
            
        } else {
            $this->followings()->attach($userId);
            // レコードが存在しない。保存。
            return true;
        }
    }
    
    public function unfollow($userId)//-----------------------------------削除
    {

        $exist = $this->is_following($userId);
         // is_followingメソッドを使って、既にレコードがあるか。
       
        $its_me = $this->id == $userId;
         // 自分のidが仮引数$userIdと同じか。（自分と相手が同じか。）
    
    
        if ($exist && !$its_me) {//-------------------------------条件分岐
           
            $this->followings()->detach($userId);
            // 既にレコードが存在。削除。
            return true;

        } else {
            // レコードが存在しない。何もしない。
            return false;
        }
    }

}


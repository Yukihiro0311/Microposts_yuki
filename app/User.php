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
//フォロー、アンフォロー
    public function followings()
    {
        return $this->belongsToMany(User::class,'user_follow','user_id','follow_id')->withTimestamps();
         //Userクラスにおいて、中間テーブルuser_followに保存されている、自分のid user_idカラムと関係先のfollow_idカラムを紐付けする。
         //<1>得られるモデルクラス <2>中間テーブル <3>中間テーブル自分のid <4>中間テーブル関係先のid
         
         /*
         現在の User インスタンスのユーザーが数で渡された $userId をもつユーザーをフォローしているかどうかをチェックするメソッドです。
         $this->followings() は、現在の User インスタンスのユーザーがフォローしているユーザーたちを表します。
         それに対してwhere('follow_id', $userId) で絞り込みを行います。 
         follow_id カラムの値が $userId であるレコードに絞り込んでいます（実際にSQLの WHERE 句で絞り込まれます）。
         つまり、 $userId をもつユーザーに絞り込んでいます。
         最後に、それに対して ->exists() で存在をチェックしています。そのようなユーザーのレコードが存在する場合は true 、存在しない場合は false を返してくれます。
         また、その true / false を、このメソッド is_following の戻り値として呼び出し元に返しています。
         */
         
    }
   
    public function followers()
    {
        return $this->belongsToMany(User::class,'user_follow','follow_id','user_id')->withTimestamps();
        //Userクラスにおいて、中間テーブルuser_followに保存されている、自分のid follow_idカラムと関係先のuser_idカラムを紐付けする。
    }
    
    
    //withTimestampsは中間テーブルにtimestampsを設置する。
    //$user->followings フォローしているuserを取得できる。
    //$user->followers　フォローされいるuserを取得できる。


//------------------------------------------------------------------------------中間テーブルのレコードを保存、削除するメソッドを作る。
//フォロー、アンフォロー
//注意点　1ー既にレコードが存在していないか。　2ー相手が自分ではないか。

    public function is_following($userId)//-------------------------------確認
    {//userId仮引数　user_id実引数　両者は同じ。
        return $this->followings()->where('follow_id', $userId)->exists();
        // 既にフォローしているかを確認するためのメソッドを作る。
        //where('A','B') AとBが同じ
        //userがフォローしているuserのidが、フォローしているuserのidが与えられたuserのidと同じか。
    }
    
//------------------------------------------------------------------------------フォローしている人のタイムラインを取得する。
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
//------------------------------------------------------------------------------多対多の関係を紐付けする。
//お気に入り
    public function my_favorite()
    {
        return $this->belongsToMany(Micropost::class,'favorites','user_id','micropost_id')->withTimestamps();
        //Userクラスにおいて、中間テーブルfavoritesに保存されている、自分のid user_idカラムと関係先のmicropost_idカラムを紐付けする。
        //<1>得られるモデルクラス <2>中間テーブル <3>中間テーブル自分のid <4>中間テーブル関係先のid
    }
//------------------------------------------------------------------------------中間テーブルのレコードを保存、削除するメソッドを作る。
//お気に入り、お気に入り削除
//注意点　1ー既にレコードが存在していないか。　2ー相手が自分ではないか。

    public function check_favorite($micropost_id)//---------------------確認
    {
        return $this->my_favorite()->where('micropost_id', $micropost_id)->exists();
        // 既にお気に入りしているかを確認するためのメソッドを作る。
        
    }
        public function favorite($micropost_id)//-----------------------保存
    {
       
        $exist = $this->check_favorite($micropost_id);
        // check_favoriteメソッドを使って、既にレコードがあるか。
    
        if ($exist) {//---------------------------条件分岐(または)
            return false;
            // 既にレコードが存在。何もしない。
            
        } else {
            $this->my_favorite()->attach($micropost_id);
            // レコードが存在しない。保存。
            return true;
        }
    }
    
    public function unfavorite($micropost_id)//-------------------------削除
    {

        $exist = $this->check_favorite($micropost_id);
         // check_favoriteメソッドを使って、既にレコードがあるか。
    
        if ($exist) {//-------------------------------条件分岐
           
            $this->my_favorite()->detach($micropost_id);
            // 既にレコードが存在。削除。
            return true;

        } else {
            // レコードが存在しない。何もしない。
            return false;
        }
    }
    
}

<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Micropost;
use DB;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    /**
     * このユーザが所有する投稿。（ Micropostモデルとの関係を定義）
    */
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    
    /**
     * このユーザに関係するモデルの件数をロードする。
     */
    public function loadRelationshipCounts()
    {
        //$this->loadCount('microposts');
        //dd($this->loadCount(['microposts', 'followings', 'followers' , 'favorites']));
        $this->loadCount(['microposts', 'followings', 'followers' , 'favorites']);
    }
    
    
    /**
     * このユーザがフォロー中のユーザ。（ Userモデルとの関係を定義）
     */
    public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }

    /**
     * このユーザをフォロー中のユーザ。（ Userモデルとの関係を定義）
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    

    
    
    
    /**
     * $userIdで指定されたユーザをフォローする。
     *
     * @param  int  $userId
     * @return bool
     */
    public function follow($userId)
    {
        // すでにフォローしているかの確認
        $exist = $this->is_following($userId);
        // 相手が自分自身かどうかの確認
        $its_me = $this->id == $userId;

        if ($exist || $its_me) {
            // すでにフォローしていれば何もしない
            return false;
        } else {
            // 未フォローであればフォローする
            $this->followings()->attach($userId);
            return true;
        }
    }

    /**
     * $userIdで指定されたユーザをアンフォローする。
     *
     * @param  int  $userId
     * @return bool
     */
    public function unfollow($userId)
    {
        // すでにフォローしているかの確認
        $exist = $this->is_following($userId);
        // 相手が自分自身かどうかの確認
        $its_me = $this->id == $userId;

        if ($exist && !$its_me) {
            // すでにフォローしていればフォローを外す
            $this->followings()->detach($userId);
            return true;
        } else {
            // 未フォローであれば何もしない
            return false;
        }
    }

    /**
     * 指定された $userIdのユーザをこのユーザがフォロー中であるか調べる。フォロー中ならtrueを返す。
     *
     * @param  int  $userId
     * @return bool
     */
    public function is_following($userId)
    {
        // フォロー中ユーザの中に $userIdのものが存在するか
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    
    /**
     * このユーザとフォロー中ユーザの投稿に絞り込む。
     */
    public function feed_microposts()
    {
        // このユーザがフォロー中のユーザのidを取得して配列にする
        $userIds = $this->followings()->pluck('users.id')->toArray();
        // このユーザのidもその配列に追加
        
        $userIds[] = $this->id;
        //dd($userIds);
        // それらのユーザが所有する投稿に絞り込む
        return Micropost::whereIn('user_id', $userIds);
    }
    
    
    
    
    
    
    
    
    
    
        
    /**
     * $userIdで指定されたユーザをフォローする。
     *
     * @param  int  $userId
     * @return bool
     */
    public function favorite($micropostId)
    {
        // すでにフォローしているかの確認
        $exist = $this->is_favoriteing($micropostId);
        
        //dd($exist);
        
        // 相手が自分自身かどうかの確認
        $its_me = $this->id == $micropostId;
        //dd($micropostId);
        //dd($its_me);
        if ($exist || $its_me) {
            // すでにフォローしていれば何もしない
            return false;
        } else {
            // 未フォローであればフォローする
            //dd($micropostId);  
            $this->is_favorites()->attach($micropostId);
           
            return true;
        }
    }

    /**
     * $userIdで指定されたユーザをアンフォローする。
     *
     * @param  int  $userId
     * @return bool
     */
    public function unfavorite($micropostId)
    {
        // すでにフォローしているかの確認
        $exist = $this->is_favoriteing($micropostId);
        // 相手が自分自身かどうかの確認
        $its_me = $this->id == $micropostId;

        if ($exist && !$its_me) {
            // すでにフォローしていればフォローを外す
            $this->is_favorites()->detach($micropostId);
            return true;
        } else {
            // 未フォローであれば何もしない
            return false;
        }
    }

    /**
     * 指定された $userIdのユーザをこのユーザがフォロー中であるか調べる。フォロー中ならtrueを返す。
     *
     * @param  int  $userId
     * @return bool
     */
    public function is_favoriteing($micropostId)
    {   
        //return $this->favorites()->where('maicropost_id', $micropostId)->exists();
        return DB::table('favorites')->where('user_id',\Auth::user()->id)->where('maicropost_id',$micropostId)->exists();
    }
    
 
    public function is_user_favoriteing($userId)
    {
        // フォロー中ユーザの中に $userIdのものが存在するか
        return $this->favorites()->where('user_id', $userId)->get();
    }
    
    
    /**
     * お気に入り投稿の登録。（ Userモデルとの関係を定義）
     */
    public function favorites()
    {
        //dd($this->belongsToMany(User::class, 'favorites', 'user_id','maicropost_id'));
        //dd($this->belongsToMany('App\Micropost','favorites')->where('user_id',1));
        $c = $this->belongsToMany(User::class, 'favorites', 'user_id')->withTimestamps();
        //dd($c);
        return $this->belongsToMany(User::class, 'favorites', 'user_id')->withTimestamps();
        //return $this->belongsToMany(User::class, 'favorites', 'maicropost_id' ,'user_id')->withTimestamps();
        //return $this->hasMany(Micropost::class,'favorite_users');
    }

    public function is_favorites()
    {
        //return $this->belongsToMany(Micropost::class, 'favorites', 'user_id' ,'maicropost_id')->withTimestamps();
        return $this->belongsToMany(User::class, 'favorites', 'user_id','maicropost_id')->withTimestamps();
    }

    
    /**
     * このユーザとフォロー中ユーザの投稿に絞り込む。
     */
    public function feed_favorite()
    {
        
        // このユーザがフォロー中のユーザのidを取得して配列にする
        $userIds = $this->favorites()->pluck('users.id')->toArray();
        
        // このユーザのidもその配列に追加
        $userIds[] = $this->id;
        
        // それらのユーザが所有する投稿に絞り込む
        return Micropost::whereIn('user_id', $userIds);
        
        
    }
}

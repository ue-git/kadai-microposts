<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Micropost extends Model
{
    protected $fillable = ['content','microposts_id'];

    /**
     * この投稿を所有するユーザ。（ Userモデルとの関係を定義）
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function favorite_users(){
        dd('test');
        //return $this->hasMany(User::class);
        //return $this->belongsToMany(User::class);
        return $this->belongsToMany(User::class, 'favorites', 'user_id','maicropost_id')->withTimestamps();
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User; // 追加
use DB;

class UsersController extends Controller
{
    public function index()
    {
        // ユーザ一覧をidの降順で取得
        $users = User::orderBy('id', 'desc')->paginate(10);

        // ユーザ一覧ビューでそれを表示
        return view('users.index', [
            'users' => $users,
        ]);
    }
    
    public function show($id)
    {
        // idの値でユーザを検索して取得
        $user = User::findOrFail($id);

        // 関係するモデルの件数をロード
        $user->loadRelationshipCounts();

        // ユーザの投稿一覧を作成日時の降順で取得
        $microposts = $user->microposts()->orderBy('created_at', 'desc')->paginate(10);
        
        // ユーザ詳細ビューでそれらを表示
        return view('users.show', [
            'user' => $user,
            'microposts' => $microposts,
        ]);
    }
    
    /**
     * ユーザのフォロー一覧ページを表示するアクション。
     *
     * @param  $id  ユーザのid
     * @return \Illuminate\Http\Response
     */
    public function followings($id)
    {
        // idの値でユーザを検索して取得
        $user = User::findOrFail($id);

        // 関係するモデルの件数をロード
        $user->loadRelationshipCounts();

        // ユーザのフォロー一覧を取得
        $followings = $user->followings()->paginate(10);

        // フォロー一覧ビューでそれらを表示
        return view('users.followings', [
            'user' => $user,
            'users' => $followings,
        ]);
    }

    /**
     * ユーザのフォロワー一覧ページを表示するアクション。
     *
     * @param  $id  ユーザのid
     * @return \Illuminate\Http\Response
     */
    public function followers($id)
    {
        // idの値でユーザを検索して取得
        $user = User::findOrFail($id);

        // 関係するモデルの件数をロード
        $user->loadRelationshipCounts();

        // ユーザのフォロワー一覧を取得
        $followers = $user->followers()->paginate(10);

        // フォロワー一覧ビューでそれらを表示
        return view('users.followers', [
            'user' => $user,
            'users' => $followers,
        ]);
    }
    
    
    //お気に入り
    public function favorites($id)
    {
        // idの値でユーザを検索して取得
        $user = User::findOrFail($id);
        //$user = \Auth::user();

        // 関係するモデルの件数をロード
        $user->loadRelationshipCounts();

        $favorite_array = DB::table('favorites')->where('user_id',$id)->pluck('maicropost_id')->toArray();
        //dd($favorite_array);
        //ログインユーザも含めるみたい

        //$favorites = $user->feed_microposts()->whereIn('id',$favorite_array)->whereNotIn('user_id',[$id])->orderBy('created_at', 'desc')->paginate(10);
        //$favorites = $user->feed_microposts()->whereIn('id',$favorite_array)->orderBy('created_at', 'desc')->paginate(10);
        $favorites = $user->feed_favorite()->paginate(10);
        //$favorites = $user->feed_favorite()->whereIn('id',$favorite_array)->orderBy('created_at', 'desc')->paginate(10);
        
        //$favorites1 = DB::table('microposts')->whereIn('id',$favorite_array)->orderBy('created_at', 'desc')->paginate(10);
        //$favorites = $user->feed_favorite()->whereIn('id',$favorite_array)->orderBy('created_at', 'desc')->paginate(10);
        //$favorites = $user->feed_favorite()->orderBy('created_at', 'desc')->paginate(10);
        //$favorites = $user->favorites()->orderBy('created_at', 'desc')->paginate(10);
        //dd($favorites);

        // フォロワー一覧ビューでそれらを表示
        return view('users.favorites', [
            'user' => $user,
            'microposts' => $favorites,
        ]);
    }
}

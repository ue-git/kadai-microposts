<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    public function store($id)
    {
        \Auth::user()->favorite($id);

        // 前のURLへリダイレクトさせる
        return back();
    }
    
    //public function destroy(Request $request)
    public function destroy($id)
    {
        \Auth::user()->unfavorite($id);

        // 前のURLへリダイレクトさせる
        return back();
    }
}

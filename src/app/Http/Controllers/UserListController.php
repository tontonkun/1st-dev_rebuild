<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;


class UserListController extends Controller
{
   public function userList()
   {
      // ユーザー情報を取得
      $users = User::select('name', 'email', 'created_at', 'updated_at')->paginate(10); // ページネーション

      // ビューにユーザー情報を渡す
      return view('user_list', compact('users'));
   }
}
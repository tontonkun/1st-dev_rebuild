@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user_list.css') }}">
@endsection

@section('content')


<div class="titleArea">
    <div class="title">ユーザー情報一覧</div>
</div>
<table class="userList">
    <tr>
        <th>ユーザー名</th>
        <th>メールアドレス</th>
        <th>登録日</th>
        <th>更新日</th>
        <th>勤怠詳細</th>
    </tr>
    @foreach ($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->created_at }}</td>
            <td>{{ $user->updated_at }}</td>
            <td>
                <form class="form" action="/time_record" method="GET">
                    @csrf
                    <button class="work_detail">勤怠詳細</button>
                </form>
            </td>
        </tr>
    @endforeach
</table>
</div>
<div class="pagination">
    {{ $users->links() }} <!-- カスタムビューを指定 -->
    @endsection
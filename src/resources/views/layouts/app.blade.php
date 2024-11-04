<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>

<body>
    <div class="header">
        <div class="headerTitle">
            Atte
        </div>
        <div class="headerLinks">
            <form class="form" action="/" method="GET">
                @csrf
                <button class="header-nav__button">ホーム</button>
            </form>
            <form class="form" action="/time_record" method="GET">
                @csrf
                <button class="header-nav__button">日付一覧</button>
            </form>
            <form class="form" action="/user_list" method="GET">
                @csrf
                <button class="header-nav__button">ユーザー一覧</button>
            </form>
            <form class="form" action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="header-nav__button">ログアウト</button>
            </form>

        </div>
    </div>

    <div class="main">
        <div class="content">
            @yield('content')
        </div>
    </div>

</body>

</html>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>会員登録</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>
    <div class="header">
        <div class="headerTitle">
            Atte
        </div>
    </div>

    <div class="main">
        <div class="content">
            <form class="form" action="login" method="POST">
                @csrf
                <div class="titleArea">
                    <div class="title">
                        ログイン
                    </div>
                </div>

                <div class="inputArea">
                    <input class="mail" type="email" name="email" placeholder="メールアドレス" value="{{ old('email') }}" />
                </div>
                <div class="form__error">
                    @error('email')
                        {{ $message }}
                    @enderror
                </div>

                <div class="inputArea">
                    <input class="password" type="password" name="password" placeholder="パスワード" />
                </div>
                <div class="form__error">
                    @error('password')
                        {{ $message }}
                    @enderror
                </div>

                <div class="buttonArea">
                    <button class="loginButton" type="submit">ログイン</button>
                </div>

                <div class="annonceForNoAccount">
                    <div>
                        アカウントをお持ちでない方はこちらから
                    </div>

                    <a href="register" class="registerLink">
                        会員登録
                    </a>
                </div>

            </form>
        </div>
</body>

</html>
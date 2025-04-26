<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>coachtech勤怠管理アプリ</title>
	<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
	<link rel="stylesheet" href="{{ asset('css/layouts/common.css') }}">
	@yield('css')
</head>

<body>
<header class="header">
	<div class="header__inner">
        <img class="header--item" src="{{ asset('storage/logo/logo.svg') }}" alt="ロゴ" />
	</div>
    @if(Auth::guard('web')->check())
    <div class="nav__container">
        <div class="nav__box">
            <a class="nav__link" href="{{ route('user.attend') }}" >出勤状況</a>
        </div>
        <div class="nav__box">
            <a class="nav__link" href="{{ route('user.index') }}">勤務一覧</a>
        </div>
        <div class="nav__box">
            <a class="nav__link" href="{{ route('user.apply') }}">修正一覧</a>
        </div>
        <div class="nav__box">
            <form action="/logout" method="post">
            @csrf
                <button class="logout__link--button">ログアウト</button>  
            </form>
        </div>
    </div>
    @endif
    @if(Auth::guard('admin')->check())
    <div class="nav__container">
        <div class="nav__box">
            <a class="nav__link" href="{{ route('admin.attendance.list') }}" >勤務一覧</a>
        </div>
        <div class="nav__box">
            <a class="nav__link" href="{{ route('staff.index') }}">スタッフ一覧</a>
        </div>
        <div class="nav__box">
            <a class="nav__link" href="">申請一覧</a>
        </div>
        <div class="nav__box">
            <form action="{{ route('admin.logout') }}" method="post">
            @csrf
                <button class="logout__link--button">ログアウト</button>  
            </form>
        </div>
    </div>
    @endif
</header>

<main>
	@yield('content')
</main>
</body>

</html>
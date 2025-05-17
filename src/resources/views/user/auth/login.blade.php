@extends('layouts.app')

@section('css')
	<link rel="stylesheet" href="{{ asset('css/user/login.css') }}">
@endsection

@section('content')
<div class="header__logo">
    <h1>ログイン<h1>
</div>
<form action="{{ route('user.login') }}" method="post">
@csrf
    <div class="form__container">
        <p>メールアドレス</p>
        <input class="form--text" type="email" name="email" value="{{ old('email') }}" />
    </div>
    <div class="form__error">
        @error('email')
            {{ $message }}
        @enderror
    </div>
    <div class="form__container">
        <p>パスワード</p>
        <input class="form--text" type="password" name="password" />
    </div>
    <div class="form__error">
        @error('password')
            {{ $message }}
        @enderror
    </div>
    <div class="form__button">
        <button class="form__button--submit" type="submit">ログインする</button>
    </div>
</form>
<div class="register__link">
    <a class="register__link--button" href="/register">会員登録はこちら</a>
</div>
@endsection
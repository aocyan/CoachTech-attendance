@extends('layouts.app')

@section('css')
	<link rel="stylesheet" href="{{ asset('css/user/register.css') }}">
@endsection

@section('content')
<div class="header__logo">
    <h1>会員登録<h1>
</div>
<form action="{{ route('user.store') }}" method="post">
@csrf
    <div class="form__container">
        <p>名前</p>
        <input class="form--text" type="text" name="name" value="{{ old('name') }}" />
    </div>
    <div class="form__error">
        @error('name')
            {{ $message }}
        @enderror
    </div>
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
        <input class="form--text" type="password" name="password_confirmation" />
    </div>
    <div class="form__error">
        @error('password')
            {{ $message }}
        @enderror
    </div>
    <div class="form__container">
        <p>パスワード確認</p>
        <input class="form--text" type="password" name="password" />
    </div>
    <div class="form__error">
        @error('password')
            {{ $message }}
        @enderror
    </div>
    <div class="form__button">
        <button class="form__button--submit" type="submit">登録する</button>
    </div>
</form>
<div class="login__link">
    <a class="login__link--button" href="/login">ログインはこちら</a>
</div>
@endsection
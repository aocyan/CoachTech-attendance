@extends('layouts.app')

@section('css')
	<link rel="stylesheet" href="{{ asset('css/admin/login.css') }}">
@endsection

@section('content')
<div class="header__logo">
    <h1>管理者ログイン<h1>
</div>
{{--<form action="{{ route('admin.store') }}" method="post">
@csrf--}}
    <div class="form__container">
        <p>メールアドレス</p>
        <input class="form--text" type="email" name="email" />
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
        <button class="form__button--submit" type="submit">管理者ログインする</button>
    </div>
{{--</form>--}}
@endsection
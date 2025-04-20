@extends('layouts.app')

@section('css')
	<link rel="stylesheet" href="{{ asset('css/user/attend.css') }}">
@endsection

@section('content')
<div class="header__logo">
    <h1>出勤状況</h1>
</div>
@if(Auth::user()->status === 'clockIn')
<div class="attendance__status">
    <p class="attendance__status--text">勤務外<p>
</div>
<form action="{{ route('user.clockIn') }}" method="post">
@csrf
    <div class="form__date">
        <input class="form__date--text" type="text" name="date" value="{{ $dateTime['date'] }}" readonly />
    </div>
    <div class="form__time">
        <input class="form__date--text" type="time" name="time" value="{{ $dateTime['time'] }}" readonly />
    </div>
    <div class="form__button">
        <button class="form__button--submit" type="submit">出勤する</button>
    </div>
</form>
@elseif(Auth::user()->status === 'working')
<div class="attendance__status">
    <p class="attendance__status--text">勤務中<p>
</div>
<div class="form__date">
    <input class="form__date--text" type="text" name="date" value="{{ $dateTime['date'] }}" readonly />
</div>
<div class="form__time">
    <input class="form__date--text" type="time" name="time" value="{{ $dateTime['time'] }}" readonly />
</div>
<div class="form__button-container">
    <div class="button__box-left">
        <form action="{{ route('user.clockOut') }}" method="post">
        @csrf
            <button class="box__left--submit" type="submit">退勤する</button>
        </form>
    </div>
    <div class="button__box-right">
        <form action="{{ route('user.intervalIn') }}" method="post">
        @csrf
            <button class="box__right--submit" type="submit">休憩する</button>
        </form>
    </div>   
</div>
@elseif(Auth::user()->status === 'intervalIn')
<div class="attendance__status">
    <p class="attendance__status--text">休憩中<p>
</div>
<form action="{{ route('user.intervalOut') }}" method="post">
@csrf
    <div class="form__date">
        <input class="form__date--text" type="text" name="date" value="{{ $dateTime['date'] }}" readonly />
    </div>
    <div class="form__time">
        <input class="form__date--text" type="time" name="time" value="{{ $dateTime['time'] }}" readonly />
    </div>
    <div class="form__button">
        <button class="form__button--submit" type="submit">休憩終わり</button>
    </div>
</form>
@elseif(Auth::user()->status === 'clockOut')
<div class="attendance__status">
    <p class="attendance__status--text">退勤済<p>
</div>
<div class="clock-out__status">
    <p class="clock-out__status--text">一日お疲れさまでした</p>
    <div class="clock-out__link">
        <form action="/logout" method="post">
        @csrf
            <button class="clock-out__link--button">ログアウトする</button>  
        </form>
    </div>
</div>
@endif
@endsection
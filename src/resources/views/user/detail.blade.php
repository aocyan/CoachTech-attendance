@extends('layouts.app')

@section('css')
	<link rel="stylesheet" href="{{ asset('css/user/detail.css') }}">
@endsection

@section('content')
<form action="" method="">
<div class="header__logo">
    <h1>┃ 勤務詳細</h1>
</div>
<table>
    <tr>
        <th class="table__th--text">名前</th>
        <td>
            <input class="name__td--text" type="text" name="name" value="{{ $user -> name }}" readonly />
        </td>
    </tr>
    <tr>
        <th class="table__th--text">日付</th>
        <td>
            <input class="year__td--text" type="text" name="year" value="{{ \Carbon\Carbon::parse($id) -> format('Y年') }}" readonly />
            <input class="date__td--text" type="text" name="date" value="{{ \Carbon\Carbon::parse($id) -> format('n月 j日') }}" readonly />
        </td>
    </tr>
    <tr>
        <th class="table__th--text">出勤・退勤</th>
        <td>
            <input class="clock-start__td--text" type="text" name="clockIn" value="{{ $attendance->clock_in_at ? $attendance->clock_in_at->format('H:i') : '' }}" placeholder="08:00" />
            <span class="clock__mark">～</span>
            <input class="clock-end__td--text" type="text" name="clockOut" value="{{ $attendance->clock_out_at ? $attendance->clock_out_at->format('H:i') : '' }}" />
        </td>
    </tr>
    <tr>
        <th class="table__th--text">休憩</th>
        <td>
            @if ($intervals -> isEmpty())
                <input class="clock-start__td--text" type="text" name="intervalIn" placeholder="09:30" />
                <span class="clock__mark">～</span>
                <input class="clock-end__td--text" type="text" name="intervalOut" />
                <p class="empty--text">※　１日のおおよその休憩時間を入力してください</p>
            @else
                 @foreach ($intervals as $interval)
                    <input class="clock-start__td--text" type="text" name="intervalIn" value="{{ $interval->interval_in_at ? $interval -> interval_in_at -> format('H:i') : '' }}" placeholder="09:30" />
                    <span class="clock__mark">～</span> 
                    <input class="clock-end__td--text" type="text" name="intervalOut" value="{{ $interval->interval_out_at ? $interval -> interval_out_at -> format('H:i') : '' }}" /><br>
                @endforeach
            @endif
        </td>
    </tr>
    <tr>
        <th class="table__th--text">備考</th>
        <td><textarea class="table__comment--text"></textarea></td>
    </tr>
</table>
<div class="link__container">
    <div class="index__link">
        <a class="index__link--button" href="/attendance/list">勤務一覧に戻る</a>
    </div>
    <div class="correction__link">
        <button class="correction__link--button" name="submit">修正内容を申請する</button>
    </div>
</div>
</form>
    
@endsection
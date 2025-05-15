@extends('layouts.app')

@section('css')
	<link rel="stylesheet" href="{{ asset('css/user/index.css') }}">
@endsection

@section('content')
<div class="header__logo">
    <h1>┃ 勤務一覧</h1>
</div>
<nav>
    <div class="nav-calendar__container">
        <div class="nav__last-month">
            <a class="month__link--text" href="{{ route('user.index', ['year' => $prevDate -> year, 'month' => $prevDate -> month]) }}">⬅前月</a>
        </div>
        <div class="nav__calendar">
            <img class="nav__calendar--img" src="{{ asset('storage/item/calendar.png') }}" alt="カレンダーアイコン">
        </div>
        <div class="nav__date">
            <input class="nav__date--text" type="text" name="date" value="{{ $displayMonth -> format('Y年n月') }}" readonly />
        </div>
        <div class="nav__next-month">
            <a class="month__link--text" href="{{ route('user.index', ['year' => $nextDate->year, 'month' => $nextDate -> month]) }}">翌月➡</a>
        </div>
    </div>
</nav>
<table class="attend__table">
    <tr>
        <th class="table__th--text">日付</th>
        <th class="table__th--text">出勤時間</th>
        <th class="table__th--text">退勤時間</th>
        <th class="table__th--text">休憩時間</th>
        <th class="table__th--text">実働時間</th>
        <th class="table__th--text">詳細</th>
    </tr>
    @foreach ($dates as $day)

    <tr>
        <td class="{{ $loop -> last ? 'last-row__left' : '' }}">
            <input class="table__td--text" type="text" value="{{ $day['date'] -> format('m/d') }}（{{ $day['dayWeek'] }}）" readonly />
        </td>
        <td>
            <input class="table__td--text" type="text" value="{{ $clockInTimes[$day['date'] -> format('Y-m-d')] ?? '' }}" readonly />
        </td>
        <td>
            <input class="table__td--text" type="text" value="{{ $clockOutTimes[$day['date'] -> format('Y-m-d')] ?? '' }}" readonly />
        </td>
        <td>
            <input class="table__td--text" type="text" value="{{ $intervalTotalTimes[$day['date'] -> format('Y-m-d')] ?? '' }}" readonly />
        </td>
        <td>
            <input class="table__td--text" type="text" type="text" value="{{ $workingTotalTimes[$day['date'] -> format('Y-m-d')] ?? '' }}" readonly />
        </td>
        <td>
             <a class="table__link--button" href="{{ route('user.detail', ['id' => Auth::id()]) }}?date={{ $day['date_format'] }}@if($day['attendance_id'])&attendance_id={{ $day['attendance_id'] }}@endif">詳細ページへ</a>
        </td>
    </tr>
    @endforeach
</table>

@endsection
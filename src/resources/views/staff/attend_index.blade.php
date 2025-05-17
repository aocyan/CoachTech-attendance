@extends('layouts.app')

@section('css')
	<link rel="stylesheet" href="{{ asset('css/staff/attend_index.css') }}">
@endsection

@section('content')
<div class="header__logo">
    <h1>┃ {{ $user -> name }}さんの月次勤務</h1>
</div>
<nav>
    <div class="nav-calendar__container">
        <div class="nav__last-month">
            <a class="month__link--text" href="{{ route('staff.attendance', ['id' => $id, 'year' => $prevDate -> year, 'month' => $prevDate -> month]) }}">⬅前月</a>
        </div>
        <div class="nav__calendar">
            <img class="nav__calendar--img" src="{{ asset('storage/item/calendar.png') }}" alt="カレンダーアイコン">
        </div>
        <div class="nav__date">
            <input class="nav__date--text" type="text" name="date" value="{{ $displayMonth -> format('Y年n月') }}" readonly />
        </div>
        <div class="nav__next-month">
            <a class="month__link--text" href="{{ route('staff.attendance', ['id' => $id, 'year' => $nextDate->year, 'month' => $nextDate->month]) }}">翌月➡</a>
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
        <td class="{{ $loop->last ? 'last-row__right' : ''}}">
            <a class="table__link--button" href="{{ route('user.detail', ['id' => $user -> id]) }}?date={{ $day['date'] -> format('Y-m-d') }}">詳細ページへ</a>
        </td>
    </tr>
    @endforeach
</table>
<div class="csv__link">
    <a class="csv__link--button" href="{{ route('staff.attendance.csv', ['id' => $user -> id, 'year' => $displayMonth -> year, 'month' => $displayMonth -> month]) }}" class="btn btn-download">CSV出力</a>
</div>

@endsection
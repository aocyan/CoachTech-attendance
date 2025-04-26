@extends('layouts.app')

@section('css')
	<link rel="stylesheet" href="{{ asset('css/admin/index.css') }}">
@endsection

@section('content')
<div class="header__logo">
    <h1>┃ {{ \Carbon\Carbon::createFromFormat('Y/m/d', $formatDate)->format('Y年n月j日') }}の勤務一覧</h1>
</div>
<nav>
    <div class="nav-calendar__container">
        <div class="nav__last-day">
            <a class="day__link--text" href="{{ route('admin.attendance.list', ['year' => $prevDate -> year, 'month' => $prevDate -> month, 'day' => $prevDate -> day]) }}">⬅前日</a>
        </div>
        <form class="nav__calendar" id="searchForm" action="{{ route ('admin.index.search') }}" method="post">
        @csrf
            <input 
                class="hidden__date--text" 
                id="dateInput" type="date" 
                name="search_name" 
                value="{{ \Carbon\Carbon::createFromFormat('Y/m/d', $formatDate) -> format('Y-m-d') }}" 
                onchange="document.getElementById('searchForm').submit();" 
            />
            <img class="nav__calendar--img" id="calendarIcon" src="{{ asset('storage/item/calendar.png') }}" alt="カレンダーアイコン"  />
        </form>
        <div class="nav__date">
            <input class="nav__date--text" type="text" name="date" value="{{ $formatDate  }}" readonly />
        </div>
        <div class="nav__next-day">
            <a class="day__link--text" href="{{ route('admin.attendance.list', ['year' => $nextDate -> year, 'month' => $nextDate -> month, 'day' => $nextDate -> day]) }}">翌日➡</a>
        </div>
    </div>
</nav>
<table class="attend__table">
    <tr>
        <th class="table__th--text">名前</th>
        <th class="table__th--text">出勤時間</th>
        <th class="table__th--text">退勤時間</th>
        <th class="table__th--text">休憩時間</th>
        <th class="table__th--text">実働時間</th>
        <th class="table__th--text">詳細</th>
    </tr>
    @foreach ($users as $user)
        <input type="hidden"name="date" value="{{ $formatDate  }}" />
    <tr>
        <td class="{{ $loop -> last ? 'last-row__left' : '' }}">
            <input class="table__td--text" type="text" value="{{ $user -> name }}" readonly />
        </td>
        <td>
            <input class="table__td--text" type="text" value="{{ $clockInTimes[$userDataDate][$user -> id] ?? '' }}" readonly />
        </td>
        <td>
            <input class="table__td--text" type="text" value="{{ $clockOutTimes[$userDataDate][$user -> id] ?? '' }}" readonly />
        </td>
        <td>
            <input class="table__td--text" type="text" value="{{ $intervalTotalTimes[$userDataDate][$user -> id] ?? '' }}" readonly />
        </td>
        <td>
            <input class="table__td--text" type="text" type="text" value="{{ $workingTotalTimes[$userDataDate][$user -> id] ?? '' }}" readonly />
        </td>
        <td class="{{ $loop->last ? 'last-row__right' : '' }}">
            <a class="table__link--button" href="{{ route('user.detail', ['id' => $user -> id]) }}?date={{ $userDataDate }}">詳細ページへ</a>
        </td>
    </tr>
    @endforeach
</table>

<script>
    document.getElementById('calendarIcon').addEventListener('click', function () {
        document.getElementById('dateInput').showPicker?.();
        document.getElementById('dateInput').focus();
    });
</script>

@endsection
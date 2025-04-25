@extends('layouts.app')

@section('css')
	<link rel="stylesheet" href="{{ asset('css/admin/detail.css') }}">
@endsection

@section('content')

<div class="header__logo">
    <h1>┃ 勤務詳細</h1>
</div>
<form action="{{ route('admin.correction', ['id' => $id]) }}?date={{ $date }}" method="post">
@csrf
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
                <input class="year__td--text" type="text" name="year" value="{{ \Carbon\Carbon::parse($date) -> format('Y年') }}" readonly />
                <input class="date__td--text" type="text" name="date" value="{{ \Carbon\Carbon::parse($date) -> format('n月j日') }}" readonly />
            </td>
        </tr>
        <tr>
            <th class="table__th--text">出勤・退勤</th>
            <td>
                <input class="clock-start__td--text" type="text" name="clock_in" value="{{ optional($attendance->clock_in_at) -> format('H:i') }}" placeholder="08:00" />
                <span class="clock__mark">～</span>
                <input class="clock-end__td--text" type="text" name="clock_out" value="{{ optional($attendance->clock_out_at) -> format('H:i') }}" />
            </td>
        </tr>
        @if ($intervals -> isEmpty())
            <tr>
                <th class="table__th--text">休憩1</th>
                <td>          
                    <input class="clock-start__td--text" type="text" name="interval_in[]" placeholder="09:30" />
                    <span class="clock__mark">～</span>
                    <input class="clock-end__td--text" type="text" name="interval_out[]" />
                </td>
            </tr>
            <tr>
                <th class="table__th--text">休憩2</th>
                <td>
                    <input class="clock-start__td--text" type="text" name="interval_in[]" placeholder="09:30" />
                    <span class="clock__mark">～</span>
                    <input class="clock-end__td--text" type="text" name="interval_out[]" />
                </td>
            </tr>
            <tr>
                <th class="table__th--text">休憩3</th>
                <td>
                    <input class="clock-start__td--text" type="text" name="interval_in[]" placeholder="09:30" />
                    <span class="clock__mark">～</span>
                    <input class="clock-end__td--text" type="text" name="interval_out[]" />
                </td>
            </tr>
        @else         
            @foreach ($intervals as $index => $interval)
            <tr>
                <th class="table__th--text">         
                    休憩{{ $index + 1 }}
                </th>
                <td>
                    <input class="clock-start__td--text" type="text" name="interval_in[]" 
                    value="{{ optional($interval -> interval_in_at) -> format('H:i') }}" 
                    placeholder="09:30" />
                    <span class="clock__mark">～</span> 
                    <input class="clock-end__td--text" type="text" name="interval_out[]" value="{{ optional($interval -> interval_out_at) -> format('H:i') }}" /><br>
                </td>
            </tr>
            @endforeach
            <tr>
                <th class="table__th--text">休憩{{ count($intervals) + 1 }}</th>
                <td>
                    <input class="clock-start__td--text" type="text" name="interval_in[]" placeholder="09:30" />
                    <span class="clock__mark">～</span>
                    <input class="clock-end__td--text" type="text" name="interval_out[]" />
                </td>
            </tr>
        @endif
        <tr>
            <th class="table__th--text">備考</th>
            <td><textarea class="table__comment--text" name="comment"></textarea></td>
        </tr>
    </table>
    <div class="link__container">
        <div class="index__link">
            <a class="index__link--button" href="{{ route('admin.attendance.list') }}">勤務一覧</a>
        </div>
        <div class="correction__link">
            <button class="correction__link--button" name="submit">修正する</button>
        </div>
    </div>
</form>

@endsection
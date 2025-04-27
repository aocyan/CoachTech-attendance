@extends('layouts.app')

@section('css')
	<link rel="stylesheet" href="{{ asset('css/staff/correction.css') }}">
@endsection

@section('content')

<div class="header__logo">
    <h1>┃ 勤務詳細</h1>
</div>
<form action="{{ route('staff.correction', ['attendance_correct_request' => $correction -> id]) }}" method="post">
@csrf
    <table>
        <tr>
            <th class="table__th--text">名前</th>
            <td>
                <input class="name__td--text" type="text" name="name" value="{{ $correction -> name }}" readonly />
            </td>
        </tr>
        <tr>
            <th class="table__th--text">日付</th>
            <td>
                <input class="year__td--text" type="text" name="year" value="{{ \Carbon\Carbon::parse($correction->date) -> format('Y年') }}" readonly />
                <input class="date__td--text" type="text" name="date" value="{{ \Carbon\Carbon::parse($correction->date) -> format('n月j日') }}" readonly />
                <input type="hidden" name="date_data" value="{{ $correction -> date }}">
            </td>
        </tr>
        <tr>
            <th class="table__th--text">出勤・退勤</th>
            <td>
                <input class="clock-start__td--text" type="text" name="clock_in" value="{{ $correction -> clock_in_at ? $correction->clock_in_at->format('H:i') : '' }}" readonly />
                <span class="clock__mark">～</span>
                <input class="clock-end__td--text" type="text" name="clock_out" value="{{ $correction -> clock_out_at ? $correction->clock_out_at->format('H:i') : '' }}" readonly />
            </td>
        </tr>         
        @foreach ( $intervals as $index => $interval )
        <tr>
            <th class="table__th--text">         
                休憩{{ $index + 1 }}
            </th>
            <td>
                <input class="clock-start__td--text" type="text" name="interval_in[]" 
                    value="{{ optional($interval -> interval_in_at) -> format('H:i') }}" 
                    readonly />
                <span class="clock__mark">～</span> 
                <input class="clock-end__td--text" type="text" name="interval_out[]" value="{{ optional($interval -> interval_out_at) -> format('H:i') }}" readonly /><br>
            </td>
        </tr>
        @endforeach
        <tr>
            <th class="table__th--text">備考</th>
            <td><textarea class="table__comment--text" name="comment" readonly >{{ $correction -> comment }}</textarea></td>
        </tr>
    </table>
    <div class="link__container">
        <div class="index__link">
            <a class="index__link--button" href="{{ route('admin.attendance.list') }}">勤務一覧</a>
        </div>
        @if( $correction -> status === 'unapproved' )
            <div class="correction-admin__link">
                <button class="correction-admin__link--button" type="submit">承認する</button>
            </div>
        @elseif( $correction -> status === 'approved' )
            <div class="correction-admin__text">
                <p class="correction-admin__text--button">承認済み</p>
            </div>
        @endif
    </div>
</form>
 
@endsection
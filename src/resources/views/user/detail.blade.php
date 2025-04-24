@extends('layouts.app')

@section('css')
	<link rel="stylesheet" href="{{ asset('css/user/detail.css') }}">
@endsection

@section('content')

<div class="header__logo">
    <h1>┃ 勤務詳細</h1>
</div>
<form  action="{{ route('user.correction', ['id' => $id]) }}" method="post">
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
                <input class="year__td--text" type="text" name="year" value="{{ \Carbon\Carbon::parse($id) -> format('Y年') }}" readonly />
                <input class="date__td--text" type="text" name="date" value="{{ \Carbon\Carbon::parse($id) -> format('n月 j日') }}" readonly />
            </td>
        </tr>
        <tr>
            <th class="table__th--text">出勤・退勤</th>
            <td>
                <input class="clock-start__td--text" type="text" name="clock_in" value="{{ $attendance->clock_in_at ? $attendance->clock_in_at->format('H:i') : '' }}" placeholder="08:00" />
                <span class="clock__mark">～</span>
                <input class="clock-end__td--text" type="text" name="clock_out" value="{{ $attendance->clock_out_at ? $attendance->clock_out_at->format('H:i') : '' }}" />
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
            @for ($i = 0; $i < count($intervals) + 1; $i++)
            <tr>
                <th class="table__th--text">         
                    @if ($i === 0 && count($intervals) === 0)
                        休憩
                    @else
                        休憩{{ $i + 1 }}
                    @endif
                </th>
                <td>
                    <input class="clock-start__td--text" type="text" name="interval_in[]" 
                    value="{{ isset($intervals[$i]) && $intervals[$i]->interval_in_at ? $intervals[$i]->interval_in_at->format('H:i') : '' }}" 
                    placeholder="09:30" />
                    <span class="clock__mark">～</span> 
                    <input class="clock-end__td--text" type="text" name="interval_out[]" value="{{ isset($intervals[$i]) && $intervals[$i]->interval_out_at ? $intervals[$i]->interval_out_at->format('H:i') : '' }}" /><br>
                </td>
            </tr>
            @endfor
        @endif
        <tr>
            <th class="table__th--text">備考</th>
            <td><textarea class="table__comment--text" name="comment"></textarea></td>
        </tr>
    </table>
    <div class="link__container">
        <div class="index__link">
            <a class="index__link--button" href="/attendance/list">勤務一覧に戻る</a>
        </div>
        @if ($correctionMode)
            <p class="unapproved--text">※　承認待ちのため修正はできません</p>
        @else
        <div class="correction__link">
            <button class="correction__link--button" name="submit">修正内容を申請する</button>
        </div>
        @endif
    </div>
</form>
 
@endsection
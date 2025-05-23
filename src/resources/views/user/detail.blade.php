@extends('layouts.app')

@section('css')
	<link rel="stylesheet" href="{{ asset('css/user/detail.css') }}">
@endsection

@section('content')

@if( Auth::guard('web') -> check() )
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
                <input class="year__td--text" type="text" name="year" value="{{ \Carbon\Carbon::parse($targetDate) -> format('Y年') }}" readonly />
                <input class="date__td--text" type="text" name="date" value="{{ \Carbon\Carbon::parse($targetDate) -> format('n月 j日') }}" readonly />
                <input type="hidden" name="date_data" value="{{ \Carbon\Carbon::parse($targetDate) -> format('Y-m-d') }}">
            </td>
        </tr>
        <tr>
            <th class="table__th--text">出勤・退勤</th>
            <td>
                <input class="clock-start__td--text" type="text" name="clock_in" value="{{ old( 'clock_in', $attendance -> clock_in_at ? $attendance -> clock_in_at -> format('H:i') : '' ) }}" placeholder="08:00" />
                <span class="clock__mark">～</span>
                <input class="clock-end__td--text" type="text" name="clock_out" value="{{ old( 'clock_out', $attendance -> clock_out_at ? $attendance -> clock_out_at -> format('H:i') : '' ) }}" placeholder="18:00" />
                @error('clock_in')
                    <div class="form__error">
                        {{ $message }}
                    </div>
                @enderror
                @error('clock_out')
                    <div class="form__error">
                        {{ $message }}
                    </div>
                @enderror
            </td>
        </tr>
        @if ( $intervals -> isEmpty() )
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
            @for ( $i = 0; $i < count($intervals) + 1; $i++ )
            <tr>
                <th class="table__th--text">         
                    @if ( $i === 0 && count($intervals) === 0 )
                        休憩
                    @else
                        休憩{{ $i + 1 }}
                    @endif
                </th>
                <td>
                    <input class="clock-start__td--text" type="text" name="interval_in[]" 
                    value="{{ old('interval_in.' . $i, isset($intervals[$i]) && $intervals[$i]->interval_in_at ? $intervals[$i]->interval_in_at->format('H:i') : '') }}" placeholder="09:30" />
                    <span class="clock__mark">～</span> 
                    <input class="clock-end__td--text" type="text" name="interval_out[]" value="{{ old( 'interval_out.' . $i, isset($intervals[$i]) && $intervals[$i] -> interval_out_at ? $intervals[$i] -> interval_out_at -> format('H:i') : '' ) }}" placeholder="10:00" />
                    @error("interval_in.$i")
                        <div class="form__error">
                            {{ $message }}
                        </div>
                    @enderror
                    @error("interval_out.$i")
                        <div class="form__error">
                            {{ $message }}
                        </div>
                    @enderror
                </td>
            </tr>
            @endfor
        @endif
        <tr>
            <th class="table__th--text">備考</th>
                <td>
                    <textarea class="table__comment--text" name="comment">{{ old( 'comment', $comment ?? '' ) }}</textarea>
                    @error("comment")
                        <div class="form__error">
                            {{ $message }}
                        </div>
                    @enderror
                </td>
        </tr>
    </table>
    @if ( $correction === 'unapproved' )
        <p class="unapproved--text">※　承認待ちのため修正はできません</p>
    @elseif( $correction === 'approved' && $checkOtherCorrection -> status === 'unapproved' )
        <p class="unapproved--text">※　承認待ちのため修正はできません</p>
    @elseif( $correction === 'approved' && $checkOtherCorrection -> status === 'approved' )
        <div class="again-correction__link">
            <p class="approved--text">※　管理者が承認済みです</p>
            <button class="again-correction__link--button">再度修正する</button>
        </div>
    @else
        <div class="correction__link">
            <button class="correction__link--button" name="submit">修正</button>
        </div>
    @endif
</form>
@endif

@if( Auth::guard('admin') -> check() )
<div class="header__logo">
    <h1>┃ 勤務詳細</h1>
</div>
<form action="{{ route('admin.correction', ['id' => $id]) }}" method="post">
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
                <input class="year__td--text" type="text" name="year" value="{{ \Carbon\Carbon::parse($targetDate) -> format('Y年') }}" readonly />
                <input class="date__td--text" type="text" name="date" value="{{ \Carbon\Carbon::parse($targetDate) -> format('n月j日') }}" readonly />
                <input type="hidden" name="date_data" value="{{ \Carbon\Carbon::parse($targetDate) -> format('Y-m-d') }}">
            </td>
        </tr>
        <tr>
            <th class="table__th--text">出勤・退勤</th>
            <td>
                <input class="clock-start__td--text" type="text" name="clock_in" value="{{ old( 'clock_in', $attendance -> clock_in_at ? $attendance -> clock_in_at -> format('H:i') : '' ) }}" placeholder="08:00" />
                <span class="clock__mark">～</span>
                <input class="clock-end__td--text" type="text" name="clock_out" value="{{ old( 'clock_out', $attendance -> clock_out_at ? $attendance -> clock_out_at -> format('H:i') : '' ) }}" />
                @error('clock_in')
                    <div class="form__error">{{ $message }}</div>
                @enderror
                @error('clock_out')
                    <div class="form__error">{{ $message }}</div>
                @enderror
            </td>
        </tr>
        @if ( $intervals -> isEmpty() )
        <tr>
            <th class="table__th--text">休憩1</th>
            <td>          
                <input class="clock-start__td--text" type="text" name="interval_in[]" placeholder="09:30" />
                <span class="clock__mark">～</span>
                <input class="clock-end__td--text" type="text" name="interval_out[]" />
                @error('interval_in.0')
                    <div class="form__error">{{ $message }}</div>
                @enderror
                @error('interval_out.0')
                    <div class="form__error">{{ $message }}</div>
                @enderror
            </td>
        </tr>
        <tr>
            <th class="table__th--text">休憩2</th>
            <td>
                <input class="clock-start__td--text" type="text" name="interval_in[]" placeholder="09:30" />
                <span class="clock__mark">～</span>
                <input class="clock-end__td--text" type="text" name="interval_out[]" />
                @error('interval_in.1')
                    <div class="form__error">{{ $message }}</div>
                @enderror
                @error('interval_out.1')
                    <div class="form__error">{{ $message }}</div>
                @enderror
            </td>
        </tr>
        <tr>
            <th class="table__th--text">休憩3</th>
            <td>
                <input class="clock-start__td--text" type="text" name="interval_in[]" placeholder="09:30" />
                <span class="clock__mark">～</span>
                <input class="clock-end__td--text" type="text" name="interval_out[]" />
                @error('interval_in.2')
                    <div class="form__error">{{ $message }}</div>
                @enderror
                @error('interval_out.2')
                    <div class="form__error">{{ $message }}</div>
                @enderror
            </td>
        </tr>
        @else         
        @foreach ( $intervals as $index => $interval )
        <tr>
            <th class="table__th--text">         
                休憩{{ $index + 1 }}
            </th>
            <td>
                <input class="clock-start__td--text" type="text" name="interval_in[]" value="{{ old( 'interval_in.' . $index, optional($interval->interval_in_at)->format('H:i') ) }}" placeholder="09:30" />
                <span class="clock__mark">～</span> 
                <input class="clock-end__td--text" type="text" name="interval_out[]" value="{{ old( 'interval_out.' . $index, optional($interval->interval_out_at)->format('H:i') )  }}" placeholder="10:00" />
                @error('interval_in.' . $index)
                    <div class="form__error">{{ $message }}</div>
                @enderror
                @error('interval_out.' . $index)
                    <div class="form__error">{{ $message }}</div>
                @enderror
            </td>
        </tr>
        @endforeach
        <tr>
            <th class="table__th--text">休憩{{ count( $intervals ) + 1 }}</th>
            <td>
                <input class="clock-start__td--text" type="text" name="interval_in[]" placeholder="09:30" />
                <span class="clock__mark">～</span>
                <input class="clock-end__td--text" type="text" name="interval_out[]" placeholder="10:00" />
                @error('interval_in.' . count($intervals))
                    <div class="form__error">{{ $message }}</div>
                @enderror
                @error('interval_out.' . count($intervals))
                    <div class="form__error">{{ $message }}</div>
                @enderror
            </td>
        </tr>
        @endif
        <tr>
            <th class="table__th--text">備考</th>
            <td>
                <textarea class="table__comment--text" name="comment">{{ old( 'comment', $comment ?? '' ) }}</textarea>
                @error('comment')
                    <div class="form__error">
                        {{ $message }}
                    </div>
                @enderror     
            </td>
        </tr>
    </table>
    <div class="link__container">
        <div class="index__link">
            <a class="index__link--button" href="{{ route('admin.attendance.list') }}">勤務一覧</a>
        </div>
        <div class="correction-admin__link">
            <button class="correction-admin__link--button" name="submit">修正する</button>
        </div>
    </div>
</form>
@endif
 
@endsection
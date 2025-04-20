@extends('layouts.app')

@section('css')
	<link rel="stylesheet" href="{{ asset('css/user/apply.css') }}">
@endsection

@section('content')

<div class="header__logo">
    <h1>┃ 申請一覧</h1>
</div>
<nav>
    <div class="correction__container">
        <div class="correction__box">
            <form action="{{ route('user.search') }}" method="post">
            @csrf
                <input type="hidden" name="status" value="unapproved">
                <button type="submit" class="correction__link--button">承認待ち</button>
            </form>
        </div>
        <div class="correction__box">
            <form action="{{ route('user.search') }}" method="post">
            @csrf
                <input type="hidden" name="status" value="approved">
                <button type="submit" class="correction__link--button">承認済み</button>
            </form>
        </div>
    </div>
</nav>
<table>
    <tr>
        <th class="table__th--text">状態</th>
        <th class="table__th--text">名前</th>
        <th class="table__th--text">対象日時</th>
        <th class="table__th--text">申請理由</th>
        <th class="table__th--text">申請日時</th>
        <th class="table__th--text">詳細</th>
    </tr>
    <tr>
    @foreach ($searches as $correction)
        <td class="{{ $loop -> last ? 'last-row__left' : '' }}">
            <input class="table__td--text" type="text" value="承認待ち" readonly />
        </td>
        <td>
            <input class="table__td--text" type="text" value="{{ $correction -> name }}" readonly />
        </td>
        <td>
            <input class="table__td--text" type="text" value="{{ \Carbon\Carbon::parse($correction->date) -> format('Y/m/d') }}" readonly />
        </td>
        <td>
            <input class="table__td--text" type="text" value="{{ $correction -> comment }}" readonly />
        </td>
        <td>
            <input class="table__td--text" type="text" type="text" value="{{ $correction->created_at -> format('Y/m/d') }}" readonly />
        </td>
        <td>
            <a class="table__link--button" href="{{ route('user.detail', ['id' => \Carbon\Carbon::parse($correction->date) -> format('Y-m-d')]) }}">詳細ページへ</a>
        </td>
    </tr>
    @endforeach
</table>
@endsection
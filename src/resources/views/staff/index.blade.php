@extends('layouts.app')

@section('css')
	<link rel="stylesheet" href="{{ asset('css/staff/index.css') }}">
@endsection

@section('content')
<div class="header__logo">
    <h1>┃ スタッフ一覧</h1>
</div>

<table class="attend__table">
    <tr>
        <th class="table__th--text">名前</th>
        <th class="table__th--text">メールアドレス</th>
        <th class="table__th--text">月次勤務一覧</th>
    </tr>
    @foreach ($staffs as $staff)
    <tr>
        <td class="{{ $loop -> last ? 'last-row__left' : '' }}">
            <input class="table__td--text" type="text" value="{{ $staff -> name }}" readonly />
        </td>
        <td>
            <input class="table__td--text" type="text" value="{{ $staff -> email }}" readonly />
        </td>
        <td class="{{ $loop->last ? 'last-row__right' : '' }}">
            <a class="table__link--button" href="">月次勤務ページへ</a>
        </td>
    </tr>
    @endforeach
</table>

@endsection
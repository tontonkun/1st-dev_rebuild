@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/time_record.css') }}">
@endsection

@section('content')


<div class="titleArea">

    <form action="/time_record_yesterday" method="GET">
        <input type="hidden" name="date" value="{{ $work_date }}">
        <input type="hidden" name="user_id" value="{{ $user->id }}">
        <button class="yesterday">＜</button>
    </form>

    <div class="title">{{ $user->name }} の勤怠情報 - {{ $work_date }}</div>

    <form action="/time_record_tomorrow" method="GET">
        <input type="hidden" name="date" value="{{ $work_date }}">
        <input type="hidden" name="user_id" value="{{ $user->id }}">
        <button class="tomorrow">＞</button>
    </form>

</div>

<table class="timeRecord">
    <tr>
        <th>日付</th>
        <th>勤務開始</th>
        <th>勤務終了</th>
        <th>休憩時間</th>
        <th>勤務時間</th>
    </tr>
    @foreach ($works as $work)
        <tr>
            <td>{{ $work->work_date }}</td>
            <td>{{ $work->start_at }}</td>
            <td>{{ $work->end_at }}</td>
            <td>{{ $work->formatted_duration }}</td>
            <td>{{ $work->total_work_duration }}</td>
        </tr>
    @endforeach
</table>
</div>
<div class="pagination">
    {{ $works->links() }} <!-- カスタムビューを指定 -->

@endsection
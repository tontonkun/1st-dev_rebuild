@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/all_records.css') }}">
@endsection

@section('content')

<table class="allRecords">
    <thead>
        <tr>
            <th>ユーザー名</th>
            <th>日付</th>
            <th>勤務開始</th>
            <th>勤務終了</th>
            <th>休憩時間</th>
            <th>勤務時間</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($userWorkData as $data)
            @foreach ($data['works'] as $work)
                <tr>
                    <td>{{ $data['user']->name }}</td>
                    <td>{{ $work->work_date }}</td>
                    <td>{{ $work->start_at }}</td>
                    <td>{{ $work->end_at }}</td>
                    <td>{{ $work->formatted_duration }}</td>
                    <td>{{ $work->total_work_duration }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>

<div class="pagination">
    {{ $works->links() }} <!-- ページネーションリンクを表示 -->
</div>

@endsection
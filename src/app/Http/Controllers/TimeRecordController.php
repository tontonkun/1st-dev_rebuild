<?php

namespace App\Http\Controllers;

use App\Models\Work;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon; // Carbonをインポート

class TimeRecordController extends Controller
{
   public function __construct()
   {
      $this->middleware('auth');
   }

   public function timeRecord()
   {
      $userId = auth()->id();
      $user = User::findOrFail($userId); // ユーザーを取得
      $work_date = Carbon::today()->toDateString();

      // 今日の日付のWorkを取得し、restsも一緒にロード
      $works = Work::where('user_id', $userId)
         ->whereDate('work_date', $work_date)
         ->with(['user', 'rests']) // リレーションをロード
         ->paginate(5);

      $totalDuration = 0;

      foreach ($works as $work) {
         if ($work->start_at && $work->end_at) {
            $start = Carbon::parse($work->start_at);
            $end = Carbon::parse($work->end_at);
            $duration = $end->diffInSeconds($start); // 秒で計算
            $totalDuration += $duration;

            // 勤務時間を秒で保存
            $work->formatted_duration = $this->formatDuration($duration);

            // 休憩時間を計算
            $breakDuration = 0;
            foreach ($work->rests as $rest) {
               if ($rest->break_start_at && $rest->break_end_at) {
                  $breakStart = Carbon::parse($rest->break_start_at);
                  $breakEnd = Carbon::parse($rest->break_end_at);
                  $breakDuration += $breakEnd->diffInSeconds($breakStart);
               }
            }

            // 合計勤務時間を計算し、フォーマットして保存
            $totalWorkDuration = $duration - $breakDuration; // 休憩時間を差し引く
            $work->total_work_duration = $this->formatDuration($totalWorkDuration);

         } else {
            $work->formatted_duration = '休憩中'; // 開始・終了時刻が不明な場合
            $work->total_work_duration = '勤務中';
         }
      }

      // 合計勤務時間を計算
      $totalWorkDurationFormatted = $this->formatDuration($totalDuration);

      return view('time_record', compact('work_date', 'works', 'totalWorkDurationFormatted', 'user')); // $user を追加
   }


   public function yesterday(Request $request)
   {
      // 日付とユーザーIDを取得
      $work_date = Carbon::parse($request->date)->subDay()->format('Y-m-d');
      $user = User::findOrFail($request->user_id);
      // 勤怠情報を取得
      $works = $user->works()->where('work_date', $work_date)->paginate(10);

      return view('time_record', compact('works', 'work_date', 'user'));
   }

   public function tomorrow(Request $request)
   {
      // 日付とユーザーIDを取得
      $work_date = Carbon::parse($request->date)->addDay()->format('Y-m-d');
      $user = User::findOrFail($request->user_id);
      // 勤怠情報を取得
      $works = $user->works()->where('work_date', $work_date)->paginate(10);

      return view('time_record', compact('works', 'work_date', 'user'));
   }


   private function calculateDurations($works, $work_date)
   {
      $totalDuration = 0;

      foreach ($works as $work) {
         if ($work->start_at && $work->end_at) {
            $start = Carbon::parse($work->start_at);
            $end = Carbon::parse($work->end_at);
            $duration = $end->diffInSeconds($start);
            $totalDuration += $duration;

            $work->formatted_duration = $this->formatDuration($duration);

            $breakDuration = 0;
            foreach ($work->rests as $rest) {
               if ($rest->break_start_at && $rest->break_end_at) {
                  $breakStart = Carbon::parse($rest->break_start_at);
                  $breakEnd = Carbon::parse($rest->break_end_at);
                  $breakDuration += $breakEnd->diffInSeconds($breakStart);
               }
            }

            $totalWorkDuration = $duration - $breakDuration;
            $work->total_work_duration = $this->formatDuration($totalWorkDuration);
         } else {
            $work->formatted_duration = '休憩中';
            $work->total_work_duration = '勤務中';
         }
      }

      $totalWorkDurationFormatted = $this->formatDuration($totalDuration);

      return view('time_record', compact('work_date', 'works', 'totalWorkDurationFormatted'));
   }

   private function formatDuration($seconds)
   {
      // 秒を時間、分、秒に変換
      $hours = floor($seconds / 3600);
      $minutes = floor(($seconds % 3600) / 60);
      $remainingSeconds = $seconds % 60;

      return sprintf('%d:%d:%d', $hours, $minutes, $remainingSeconds);
   }

   public function timeRecord_forOneUser($id)
   {
      // ユーザーを取得
      $user = User::findOrFail($id);

      // 勤怠情報を取得（適切なクエリを実行）
      $works = $user->works()->paginate(10);

      // 日付を取得
      $work_date = now()->format('Y-m-d'); // 今日の日付をデフォルトにする

      return view('time_record', compact('works', 'work_date', 'user'));
   }
}
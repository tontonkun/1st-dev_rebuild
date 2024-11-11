<?php

namespace App\Http\Controllers;

use App\Models\Work;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon; // Carbonをインポート

class AllRecordsController extends Controller
{
   public function __construct()
   {
      $this->middleware('auth');
   }

   public function allRecords(Request $request)
   {
      $work_date = Carbon::today()->toDateString();

      // 全ユーザーを取得
      $users = User::all();

      $totalDuration = 0;
      $userWorkData = [];

      foreach ($users as $user) {
         // ユーザーの勤務データを取得（ページネーションを適用）
         $works = Work::where('user_id', $user->id)
            ->whereDate('work_date', $work_date)
            ->with(['rests'])
            ->paginate(5); // 1ページに10件表示

         $userTotalDuration = 0;

         foreach ($works as $work) {
            if ($work->start_at && $work->end_at) {
               $start = Carbon::parse($work->start_at);
               $end = Carbon::parse($work->end_at);
               $duration = $end->diffInSeconds($start);
               $userTotalDuration += $duration;

               // 勤務時間を秒で保存
               $work->formatted_duration = $this->formatDuration_forAllUsers($duration);

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
               $totalWorkDuration = $duration - $breakDuration;
               $work->total_work_duration = $this->formatDuration_forAllUsers($totalWorkDuration);
            } else {
               $work->formatted_duration = '休憩中'; // 開始・終了時刻が不明な場合
               $work->total_work_duration = '勤務中';
            }
         }

         // ユーザーごとの合計勤務時間をフォーマットして保存
         $userTotalDurationFormatted = $this->formatDuration_forAllUsers($userTotalDuration);
         $userWorkData[] = [
            'user' => $user,
            'works' => $works,
            'totalWorkDurationFormatted' => $userTotalDurationFormatted,
         ];

         // 全ユーザーの勤務時間の合計を計算
         $totalDuration += $userTotalDuration;
      }

      // 合計勤務時間をフォーマット
      $totalWorkDurationFormatted = $this->formatDuration_forAllUsers($totalDuration);

      return view('all_records', compact('work_date', 'userWorkData', 'totalWorkDurationFormatted', 'works'));
   }

   public function allRecords_yesterday(Request $request)
   {
      $work_date = Carbon::parse($request->date)->subDay()->format('Y-m-d');
      $users = User::all();

      $userWorkData = [];
      $totalDuration = 0;

      foreach ($users as $user) {
         // ユーザーの勤務データを取得（ページネーションを適用）
         $works = $user->works()->where('work_date', $work_date)->paginate(5); // 1ページに10件表示

         $userTotalDuration = 0;
         foreach ($works as $work) {
            if ($work->start_at && $work->end_at) {
               $start = Carbon::parse($work->start_at);
               $end = Carbon::parse($work->end_at);
               $duration = $end->diffInSeconds($start);
               $userTotalDuration += $duration;

               $work->formatted_duration = $this->formatDuration_forAllUsers($duration);

               $breakDuration = 0;
               foreach ($work->rests as $rest) {
                  if ($rest->break_start_at && $rest->break_end_at) {
                     $breakStart = Carbon::parse($rest->break_start_at);
                     $breakEnd = Carbon::parse($rest->break_end_at);
                     $breakDuration += $breakEnd->diffInSeconds($breakStart);
                  }
               }

               $totalWorkDuration = $duration - $breakDuration;
               $work->total_work_duration = $this->formatDuration_forAllUsers($totalWorkDuration);
            } else {
               $work->formatted_duration = '休憩中';
               $work->total_work_duration = '勤務中';
            }
         }

         $userWorkData[] = [
            'user' => $user,
            'works' => $works,
            'totalWorkDurationFormatted' => $this->formatDuration_forAllUsers($userTotalDuration),
         ];

         $totalDuration += $userTotalDuration;
      }

      $totalWorkDurationFormatted = $this->formatDuration_forAllUsers($totalDuration);

      return view('all_records', compact('work_date', 'userWorkData', 'totalWorkDurationFormatted', 'works'));
   }

   public function allRecords_tomorrow(Request $request)
   {
      $work_date = Carbon::parse($request->date)->addDay()->format('Y-m-d');
      $users = User::all();

      $userWorkData = [];
      $totalDuration = 0;

      foreach ($users as $user) {
         // ユーザーの勤務データを取得（ページネーションを適用）
         $works = $user->works()->where('work_date', $work_date)->paginate(5); // 1ページに10件表示

         $userTotalDuration = 0;
         foreach ($works as $work) {
            if ($work->start_at && $work->end_at) {
               $start = Carbon::parse($work->start_at);
               $end = Carbon::parse($work->end_at);
               $duration = $end->diffInSeconds($start);
               $userTotalDuration += $duration;

               $work->formatted_duration = $this->formatDuration_forAllUsers($duration);

               $breakDuration = 0;
               foreach ($work->rests as $rest) {
                  if ($rest->break_start_at && $rest->break_end_at) {
                     $breakStart = Carbon::parse($rest->break_start_at);
                     $breakEnd = Carbon::parse($rest->break_end_at);
                     $breakDuration += $breakEnd->diffInSeconds($breakStart);
                  }
               }

               $totalWorkDuration = $duration - $breakDuration;
               $work->total_work_duration = $this->formatDuration_forAllUsers($totalWorkDuration);
            } else {
               $work->formatted_duration = '休憩中';
               $work->total_work_duration = '勤務中';
            }
         }

         $userWorkData[] = [
            'user' => $user,
            'works' => $works,
            'totalWorkDurationFormatted' => $this->formatDuration_forAllUsers($userTotalDuration),
         ];

         $totalDuration += $userTotalDuration;
      }

      $totalWorkDurationFormatted = $this->formatDuration_forAllUsers($totalDuration);

      return view('all_records', compact('work_date', 'userWorkData', 'totalWorkDurationFormatted', 'works'));
   }

   private function formatDuration_forAllUsers($seconds)
   {
      $hours = floor($seconds / 3600);
      $minutes = floor(($seconds % 3600) / 60);
      $remainingSeconds = $seconds % 60;

      return sprintf('%d:%d:%d', $hours, $minutes, $remainingSeconds);
   }

}
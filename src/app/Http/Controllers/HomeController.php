<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Work;
use App\Models\Rest;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
   public function firstHomePage()
   {
      $userId = auth()->id();
      $currentDate = now()->format('Y-m-d');

      // 今日の作業を取得
      $currentWork = Work::where('user_id', $userId)
         ->where('work_date', $currentDate)
         ->whereNull('end_at') // まだ終了していない作業を確認
         ->first();

      // 今日の休憩を取得
      $currentRest = Rest::where('work_id', $currentWork->id ?? null)
         ->whereNull('break_end_time') // まだ終了していない休憩を確認
         ->first();

      // ボタンの有効/無効を決定
      $workStartDisabled = !is_null($currentWork);
      $workEndDisabled = is_null($currentWork);
      $restStartDisabled = is_null($currentWork) || !is_null($currentRest);
      $restEndDisabled = is_null($currentRest);

      $userName = Auth::user()->name;

      return view('home', compact('workStartDisabled', 'workEndDisabled', 'restStartDisabled', 'restEndDisabled', 'userName'));
   }

   public function afterStartWork(Request $request)
   {
      $userId = auth()->id();
      $userName = Auth::user()->name;
      $currentDate = now()->format('Y-m-d');
      $currentTime = now()->format('H:i:s');

      Work::create([
         'user_id' => $userId,
         'work_date' => $currentDate,
         'start_at' => $currentTime,
      ]);

      return $this->firstHomePage(); // 状態を更新してビューを返す
   }

   public function afterEndWork(Request $request)
   {
      // ログインしているユーザーのIDを取得
      $userId = auth()->id();

      $userName = Auth::user()->name;

      // 現在の日付と時刻を取得
      $currentDate = now()->format('Y-m-d'); // 必要に応じてフォーマットを変更
      $currentTime = now()->format('H:i:s'); // 必要に応じてフォーマットを変更

      // 作業を終了するために、該当するworkレコードを取得
      $currentWork = work::where('user_id', $userId)
         ->where('work_date', $currentDate)
         ->whereNull('end_at') // まだ終了していない作業を確認
         ->first();

      if ($currentWork) {
         // end_atを更新
         $currentWork->end_at = $currentTime;
         $currentWork->save();
      } else {
         // 作業が見つからない場合のエラーハンドリング
         return redirect()->back()->withErrors('現在の作業が見つかりません。');
      }

      $workStartDisabled = false;
      $workEndDisabled = true;
      $restStartDisabled = true;
      $restEndDisabled = true;
      return view('home', compact('workStartDisabled', 'workEndDisabled', 'restStartDisabled', 'restEndDisabled', 'userName'));
   }

   public function afterStartRest(Request $request)
   {
      // ログインしているユーザーのIDを取得
      $userId = auth()->id();

      $userName = Auth::user()->name;

      // 現在の日付と時刻を取得
      $currentDate = now()->format('Y-m-d'); // 必要に応じてフォーマットを変更
      $currentTime = now()->format('H:i:s'); // 必要に応じてフォーマットを変更

      // user_id と work_date を work テーブルから取得
      $work = Work::where('user_id', $userId)
         ->where('work_date', $currentDate)
         ->first();

      // work が存在する場合にのみデータを挿入
      if ($work) {
         rest::create([
            'work_id' => $work->id, // work テーブルの ID を使用
            'break_start_time' => $currentTime,
         ]);
      } else {
         // エラーハンドリングやメッセージをここに追加
         return redirect()->back()->withErrors(['message' => '作業データが見つかりません。先に作業を登録してください。']);
      }

      $workStartDisabled = true;
      $workEndDisabled = true;
      $restStartDisabled = true;
      $restEndDisabled = false;
      return view('home', compact('workStartDisabled', 'workEndDisabled', 'restStartDisabled', 'restEndDisabled', 'userName'));
   }



   public function afterEndRest(Request $request)
   {
      // ログインしているユーザーのIDを取得
      $userId = auth()->id();

      $userName = Auth::user()->name;

      // 現在の日付と時刻を取得
      $currentDate = now()->format('Y-m-d');
      $currentTime = now()->format('H:i:s');

      // ユーザーの最新の作業データを取得
      $work = Work::where('user_id', $userId)
         ->where('work_date', $currentDate)
         ->first();

      // 作業が存在する場合にのみ、休憩データを取得
      if ($work) {
         // 最後の休憩データを取得
         $rest = Rest::where('work_id', $work->id)
            ->whereNull('break_end_time') // 終了時間がまだ設定されていないレコードを取得
            ->first();

         if ($rest) {
            // 終了時間を更新
            $rest->break_end_time = $currentTime;
            $rest->save();
         } else {
            return redirect()->back()->withErrors(['message' => '現在の休憩データが見つかりません。']);
         }
      } else {
         return redirect()->back()->withErrors(['message' => '作業データが見つかりません。先に作業を登録してください。']);
      }

      $workStartDisabled = true;
      $workEndDisabled = false;
      $restStartDisabled = false;
      $restEndDisabled = true;
      return view('home', compact('workStartDisabled', 'workEndDisabled', 'restStartDisabled', 'restEndDisabled', 'userName'));
   }

}

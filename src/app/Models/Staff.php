<?php

namespace App\Models;

use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;
use App\Models\Correction;
use App\Models\Interval;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    public static function staffIndex()
    {
        $staffs = User::all();

        $staffs;

        return $staffs;
    }

    public static function exportMonthCsv(User $user, int $year, int $month): StreamedResponse
    {
        $dates = Attendance::indexTime($year, $month)['dates'];
        $clockInTimes = Attendance::getMonthClockTime($user, $year, $month)['clockInTimes'];
        $clockOutTimes = Attendance::getMonthClockTime($user, $year, $month)['clockOutTimes'];
        $intervalTotalTimes = Interval::getMonthIntervalTotalTime($user, $year, $month)['intervalTotalTimes'];
        $workingTotalTimes = Attendance::workingTotalTime($user, $year, $month)['workingTotalTimes'];

        $response = new StreamedResponse(function () use (
            $user,
            $year,
            $month,
            $dates,
            $clockInTimes,
            $clockOutTimes,
            $intervalTotalTimes,
            $workingTotalTimes
        ) {
            $handle = fopen('php://output', 'w');

            echo chr(0xEF) . chr(0xBB) . chr(0xBF);

            fputcsv($handle, ["{$user->name}さんの勤務一覧（{$year}年{$month}月）"]);
            fputcsv($handle, []);

            fputcsv($handle, ['日付', '出勤時間', '退勤時間', '休憩時間', '実働時間']);

            foreach ($dates as $day) {
                $dateKey = $day['date'] -> format('Y-m-d');
                fputcsv($handle, [
                    $day['date'] -> format('Y年m月d日（' . $day['dayWeek'] . '）'),
                    $clockInTimes[$dateKey] ?? '',
                    $clockOutTimes[$dateKey] ?? '',
                    $intervalTotalTimes[$dateKey] ?? '',
                    $workingTotalTimes[$dateKey] ?? '',
                ]);
            }

            fclose($handle);
        });

        $fileName = "{$user->name}_{$year}年{$month}月_勤務一覧.csv";

        $response
            -> headers
            -> set('Content-Type', 'text/csv; charset=UTF-8');

        $response
            -> headers
            -> set('Content-Disposition', "attachment; filename={$fileName}");

        return $response;
    }

    public static function detailData($correctionId)
    {
        $correction = Correction::findOrFail($correctionId);

        $intervals = Leave::where('correction_id', $correction->id) -> get();

        return ['intervals' => $intervals,];       
    }

    public static function attendanceDataUpdate($correctionId) 
    {
        $correction = Correction::findOrFail($correctionId);
        $userId = $correction->user_id;
        $date = $correction->date;

        $attendance = Attendance::where('user_id', $userId)
                        -> where('date', $date)
                        -> first();

        if (!$attendance) {
            $attendance = new Attendance();
            $attendance -> user_id = $userId;
            $attendance -> date = $date;
            $attendance -> clock_in_at = null;
            $attendance -> clock_out_at = null;
            $attendance -> save();
        }

        $attendance -> clock_in_at = $correction -> clock_in_at;
        $attendance -> clock_out_at = $correction -> clock_out_at;
        $attendance -> save();

        $actualIntervals = Interval::where('attendance_id', $attendance->id)
                            -> orderBy('id')
                            -> get();

        $correctionLeaves = Leave::where('correction_id', $correction->id)
                            -> orderBy('id')
                            -> get();

        foreach ($actualIntervals as $index => $interval) {
            if (isset($correctionLeaves[$index])) {

                $interval -> interval_in_at = $correctionLeaves[$index] -> interval_in_at;
                $interval -> interval_out_at = $correctionLeaves[$index] -> interval_out_at;
                $interval -> save();

            } else {

                $interval->delete();

            }
        }

        if (count($correctionLeaves) > count($actualIntervals)) {
            for ($i = count($actualIntervals); $i < count($correctionLeaves); $i++) {

                $newInterval = new Interval();
                $newInterval->attendance_id = $attendance->id;
                $newInterval->interval_in_at = $correctionLeaves[$i]->interval_in_at;
                $newInterval->interval_out_at = $correctionLeaves[$i]->interval_out_at;
                $newInterval->save();

            }
        }

        $correction->status = 'approved';
        $correction->save();
    }
}

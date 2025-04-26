<?php

namespace App\Models;

use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Interval;
use App\Models\Attendance;
use App\Models\User;
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
            ->headers
            ->set('Content-Type', 'text/csv; charset=UTF-8');

        $response
            ->headers
            ->set('Content-Disposition', "attachment; filename={$fileName}");

        return $response;
    }
}

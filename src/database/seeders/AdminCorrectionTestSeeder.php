<?php

namespace Database\Seeders;

use App\Models\Leave;
use App\Models\Correction;
use App\Models\Interval;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AdminCorrectionTestSeeder extends Seeder
{
    public function run()
    {
        $user1 = User::create([
            'name' => '山田　太郎',
            'email' => 'user1@example.com',
            'password' => bcrypt('1234abcd'),
            'status' => 'clockIn',
        ]);

        $clockInFirstTimeUser1 = Carbon::create(2025, 4, 1, 9, 0, 0);
        $clockOutFirstTimeUser1 = Carbon::create(2025, 4, 1, 18, 0, 0);

        $clockInSecondTimeUser1 = Carbon::create(2025, 4, 10, 9, 0, 0);
        $clockOutSecondTimeUser1 = Carbon::create(2025, 4, 10, 18, 0, 0);

        $clockInThirdTimeUser1 = Carbon::create(2025, 4, 20, 9, 0, 0);
        $clockOutThirdTimeUser1 = Carbon::create(2025, 4, 20, 18, 0, 0);

        $intervalInFirstTimeUser1 = Carbon::create(2025, 4, 1, 12, 0, 0);
        $intervalOutFirstTimeUser1 = Carbon::create(2025, 4, 1, 13, 0, 0);

        $intervalInSecondTimeUser1 = Carbon::create(2025, 4, 10, 12, 0, 0);
        $intervalOutSecondTimeUser1 = Carbon::create(2025, 4, 10, 13, 0, 0);

        $intervalInThirdTimeUser1 = Carbon::create(2025, 4, 20, 12, 0, 0);
        $intervalOutThirdTimeUser1 = Carbon::create(2025, 4, 20, 13, 0, 0);

        $correctionClockInFirstTimeUser1 = Carbon::create(2025, 4, 1, 10, 0, 0);
        $correctionClockOutFirstTimeUser1 = Carbon::create(2025, 4, 1, 19, 0, 0);

        $correctionClockInSecondTimeUser1 = Carbon::create(2025, 4, 10, 9, 0, 0);
        $correctionClockOutSecondTimeUser1 = Carbon::create(2025, 4, 10, 18, 0, 0);

        $correctionIntervalInFirstTimeUser1 = Carbon::create(2025, 4, 1, 12, 0, 0);
        $correctionIntervalOutFirstTimeUser1 = Carbon::create(2025, 4, 1, 13, 0, 0);

        $correctionIntervalInSecondTimeUser1 = Carbon::create(2025, 4, 10, 13, 0, 0);
        $correctionIntervalOutSecondTimeUser1 = Carbon::create(2025, 4, 10, 14, 0, 0);


        $firstAttendanceUser1 = Attendance::create([
            'user_id' => $user1 -> id,
            'date' => $clockInFirstTimeUser1 -> toDateString(),
            'clock_in_at' => $clockInFirstTimeUser1,
            'clock_out_at' => $clockOutFirstTimeUser1,
        ]);

        Interval::create([
            'attendance_id' => $firstAttendanceUser1 -> id,
            'interval_in_at' => $intervalInFirstTimeUser1,
            'interval_out_at' => $intervalOutFirstTimeUser1,
        ]);

        $firstCorrectionUser1 = Correction::create([
            'user_id' => $user1 -> id,
            'attendance_id' => $firstAttendanceUser1 -> id,
            'name' => $user1 -> name,
            'date' => $clockInFirstTimeUser1 -> toDateString(),
            'clock_in_at' => $correctionClockInFirstTimeUser1,
            'clock_out_at' => $correctionClockOutFirstTimeUser1,
            'comment' => '承認待ちコメント',
            'status' => 'unapproved',
        ]);

        Leave::create([
            'correction_id' => $firstCorrectionUser1 -> id,
            'interval_in_at' => $correctionIntervalInFirstTimeUser1,
            'interval_out_at' => $correctionIntervalOutFirstTimeUser1,
        ]);

        $secondAttendanceUser1 = Attendance::create([
            'user_id' => $user1 -> id,
            'date' => $clockInSecondTimeUser1 -> toDateString(),
            'clock_in_at' => $clockInSecondTimeUser1,
            'clock_out_at' => $clockOutSecondTimeUser1,
        ]);

        Interval::create([
            'attendance_id' => $secondAttendanceUser1 -> id,
            'interval_in_at' => $intervalInSecondTimeUser1,
            'interval_out_at' => $intervalOutSecondTimeUser1,
        ]);

        $secondCorrectionUser1 = Correction::create([
            'user_id' => $user1 -> id,
            'attendance_id' => $secondAttendanceUser1 -> id,
            'name' => $user1 -> name,
            'date' => $clockInSecondTimeUser1 -> toDateString(),
            'clock_in_at' => $correctionClockInSecondTimeUser1,
            'clock_out_at' => $correctionClockOutSecondTimeUser1,
            'comment' => '承認済みコメント',
            'status' => 'approved',
        ]);

        Leave::create([
            'correction_id' => $secondCorrectionUser1 -> id,
            'interval_in_at' => $correctionIntervalInSecondTimeUser1,
            'interval_out_at' => $correctionIntervalOutSecondTimeUser1,
        ]);

        $thirdAttendanceUser1 = Attendance::create([
            'user_id' => $user1 -> id,
            'date' => $clockInThirdTimeUser1 -> toDateString(),
            'clock_in_at' => $clockInThirdTimeUser1,
            'clock_out_at' => $clockOutThirdTimeUser1,
        ]);

        Interval::create([
            'attendance_id' => $thirdAttendanceUser1 -> id,
            'interval_in_at' => $intervalInThirdTimeUser1,
            'interval_out_at' => $intervalOutThirdTimeUser1,
        ]);

        $user2 = User::create([
            'name' => '森　花子',
            'email' => 'user2@example.com',
            'password' => bcrypt('1234abcd'),
            'status' => 'clockIn',
        ]);

        $clockInFirstTimeUser2 = Carbon::create(2025, 4, 1, 10, 0, 0);
        $clockOutFirstTimeUser2 = Carbon::create(2025, 4, 1, 19, 0, 0);

        $clockInSecondTimeUser2 = Carbon::create(2025, 4, 10, 10, 0, 0);
        $clockOutSecondTimeUser2 = Carbon::create(2025, 4, 10, 19, 0, 0);

        $clockInThirdTimeUser2 = Carbon::create(2025, 4, 20, 10, 0, 0);
        $clockOutThirdTimeUser2 = Carbon::create(2025, 4, 20, 19, 0, 0);

        $intervalInFirstTimeUser2 = Carbon::create(2025, 4, 1, 13, 0, 0);
        $intervalOutFirstTimeUser2 = Carbon::create(2025, 4, 1, 14, 0, 0);

        $intervalInSecondTimeUser2 = Carbon::create(2025, 4, 10, 13, 0, 0);
        $intervalOutSecondTimeUser2 = Carbon::create(2025, 4, 10, 14, 0, 0);

        $intervalInThirdTimeUser2 = Carbon::create(2025, 4, 20, 13, 0, 0);
        $intervalOutThirdTimeUser2 = Carbon::create(2025, 4, 20, 14, 0, 0);

        $correctionClockInFirstTimeUser2 = Carbon::create(2025, 4, 1, 11, 0, 0);
        $correctionClockOutFirstTimeUser2 = Carbon::create(2025, 4, 1, 20, 0, 0);

        $correctionClockInSecondTimeUser2 = Carbon::create(2025, 4, 10, 10, 0, 0);
        $correctionClockOutSecondTimeUser2 = Carbon::create(2025, 4, 10, 19, 0, 0);

        $correctionIntervalInFirstTimeUser2 = Carbon::create(2025, 4, 1, 13, 0, 0);
        $correctionIntervalOutFirstTimeUser2 = Carbon::create(2025, 4, 1, 14, 0, 0);

        $correctionIntervalInSecondTimeUser2 = Carbon::create(2025, 4, 10, 14, 0, 0);
        $correctionIntervalOutSecondTimeUser2 = Carbon::create(2025, 4, 10, 15, 0, 0);

        $firstAttendanceUser2 = Attendance::create([
            'user_id' => $user2 -> id,
            'date' => $clockInFirstTimeUser2 -> toDateString(),
            'clock_in_at' => $clockInFirstTimeUser2,
            'clock_out_at' => $clockOutFirstTimeUser2,
        ]);

        Interval::create([
            'attendance_id' => $firstAttendanceUser2 -> id,
            'interval_in_at' => $intervalInFirstTimeUser2,
            'interval_out_at' => $intervalOutFirstTimeUser2,
        ]);

        $firstCorrectionUser2 = Correction::create([
            'user_id' => $user2 -> id,
            'attendance_id' => $firstAttendanceUser2 -> id,
            'name' => $user2 -> name,
            'date' => $clockInFirstTimeUser2 -> toDateString(),
            'clock_in_at' => $correctionClockInFirstTimeUser2,
            'clock_out_at' => $correctionClockOutFirstTimeUser2,
            'comment' => '承認待ちコメント',
            'status' => 'unapproved',
        ]);

        Leave::create([
            'correction_id' => $firstCorrectionUser2 -> id,
            'interval_in_at' => $correctionIntervalInFirstTimeUser2,
            'interval_out_at' => $correctionIntervalOutFirstTimeUser2,
        ]);

        $secondAttendanceUser2 = Attendance::create([
            'user_id' => $user2 -> id,
            'date' => $clockInSecondTimeUser2 -> toDateString(),
            'clock_in_at' => $clockInSecondTimeUser2,
            'clock_out_at' => $clockOutSecondTimeUser2,
        ]);

        Interval::create([
            'attendance_id' => $secondAttendanceUser2 -> id,
            'interval_in_at' => $intervalInSecondTimeUser2,
            'interval_out_at' => $intervalOutSecondTimeUser2,
        ]);

        $secondCorrectionUser2 = Correction::create([
            'user_id' => $user2 -> id,
            'attendance_id' => $secondAttendanceUser2 -> id,
            'name' => $user2 -> name,
            'date' => $clockInSecondTimeUser2 -> toDateString(),
            'clock_in_at' => $correctionClockInSecondTimeUser2,
            'clock_out_at' => $correctionClockOutSecondTimeUser2,
            'comment' => '承認済みコメント',
            'status' => 'approved',
        ]);

        Leave::create([
            'correction_id' => $secondCorrectionUser2 -> id,
            'interval_in_at' => $correctionIntervalInSecondTimeUser2,
            'interval_out_at' => $correctionIntervalOutSecondTimeUser2,
        ]);

        $thirdAttendanceUser2 = Attendance::create([
            'user_id' => $user2 -> id,
            'date' => $clockInThirdTimeUser2 -> toDateString(),
            'clock_in_at' => $clockInThirdTimeUser2,
            'clock_out_at' => $clockOutThirdTimeUser2,
        ]);

        Interval::create([
            'attendance_id' => $thirdAttendanceUser2 -> id,
            'interval_in_at' => $intervalInThirdTimeUser2,
            'interval_out_at' => $intervalOutThirdTimeUser2,
        ]);

        $user3 = User::create([
            'name' => '伊藤　三郎',
            'email' => 'user3@example.com',
            'password' => bcrypt('1234abcd'),
            'status' => 'clockIn',
        ]);

        $clockInFirstTimeUser3 = Carbon::create(2025, 4, 1, 11, 0, 0);
        $clockOutFirstTimeUser3 = Carbon::create(2025, 4, 1, 20, 0, 0);

        $clockInSecondTimeUser3 = Carbon::create(2025, 4, 10, 11, 0, 0);
        $clockOutSecondTimeUser3 = Carbon::create(2025, 4, 10, 20, 0, 0);

        $clockInThirdTimeUser3 = Carbon::create(2025, 4, 20, 11, 0, 0);
        $clockOutThirdTimeUser3 = Carbon::create(2025, 4, 20, 20, 0, 0);

        $intervalInFirstTimeUser3 = Carbon::create(2025, 4, 1, 14, 0, 0);
        $intervalOutFirstTimeUser3 = Carbon::create(2025, 4, 1, 15, 0, 0);

        $intervalInSecondTimeUser3 = Carbon::create(2025, 4, 10, 14, 0, 0);
        $intervalOutSecondTimeUser3 = Carbon::create(2025, 4, 10, 15, 0, 0);

        $intervalInThirdTimeUser3 = Carbon::create(2025, 4, 20, 14, 0, 0);
        $intervalOutThirdTimeUser3 = Carbon::create(2025, 4, 20, 15, 0, 0);

        $correctionClockInFirstTimeUser3 = Carbon::create(2025, 4, 1, 12, 0, 0);
        $correctionClockOutFirstTimeUser3 = Carbon::create(2025, 4, 1, 21, 0, 0);

        $correctionClockInSecondTimeUser3 = Carbon::create(2025, 4, 10, 11, 0, 0);
        $correctionClockOutSecondTimeUser3 = Carbon::create(2025, 4, 10, 20, 0, 0);

        $correctionIntervalInFirstTimeUser3 = Carbon::create(2025, 4, 1, 14, 0, 0);
        $correctionIntervalOutFirstTimeUser3 = Carbon::create(2025, 4, 1, 15, 0, 0);

        $correctionIntervalInSecondTimeUser3 = Carbon::create(2025, 4, 10, 15, 0, 0);
        $correctionIntervalOutSecondTimeUser3 = Carbon::create(2025, 4, 10, 16, 0, 0);

        $firstAttendanceUser3 = Attendance::create([
            'user_id' => $user3 -> id,
            'date' => $clockInFirstTimeUser3 -> toDateString(),
            'clock_in_at' => $clockInFirstTimeUser3,
            'clock_out_at' => $clockOutFirstTimeUser3,
        ]);

        Interval::create([
            'attendance_id' => $firstAttendanceUser3 -> id,
            'interval_in_at' => $intervalInFirstTimeUser3,
            'interval_out_at' => $intervalOutFirstTimeUser3,
        ]);

        $firstCorrectionUser3 = Correction::create([
            'user_id' => $user3 -> id,
            'attendance_id' => $firstAttendanceUser3 -> id,
            'name' => $user3 -> name,
            'date' => $clockInFirstTimeUser3 -> toDateString(),
            'clock_in_at' => $correctionClockInFirstTimeUser3,
            'clock_out_at' => $correctionClockOutFirstTimeUser3,
            'comment' => '承認待ちコメント',
            'status' => 'unapproved',
        ]);

        Leave::create([
            'correction_id' => $firstCorrectionUser3 -> id,
            'interval_in_at' => $correctionIntervalInFirstTimeUser3,
            'interval_out_at' => $correctionIntervalOutFirstTimeUser3,
        ]);

        $secondAttendanceUser3 = Attendance::create([
            'user_id' => $user3 -> id,
            'date' => $clockInSecondTimeUser3 -> toDateString(),
            'clock_in_at' => $clockInSecondTimeUser3,
            'clock_out_at' => $clockOutSecondTimeUser3,
        ]);

        Interval::create([
            'attendance_id' => $secondAttendanceUser3 -> id,
            'interval_in_at' => $intervalInSecondTimeUser3,
            'interval_out_at' => $intervalOutSecondTimeUser3,
        ]);

        $secondCorrectionUser3 = Correction::create([
            'user_id' => $user3 -> id,
            'attendance_id' => $secondAttendanceUser3 -> id,
            'name' => $user3 -> name,
            'date' => $clockInSecondTimeUser3 -> toDateString(),
            'clock_in_at' => $correctionClockInSecondTimeUser3,
            'clock_out_at' => $correctionClockOutSecondTimeUser3,
            'comment' => '承認済みコメント',
            'status' => 'approved',
        ]);

        Leave::create([
            'correction_id' => $secondCorrectionUser3 -> id,
            'interval_in_at' => $correctionIntervalInSecondTimeUser3,
            'interval_out_at' => $correctionIntervalOutSecondTimeUser3,
        ]);

        $thirdAttendanceUser3 = Attendance::create([
            'user_id' => $user3 -> id,
            'date' => $clockInThirdTimeUser3 -> toDateString(),
            'clock_in_at' => $clockInThirdTimeUser3,
            'clock_out_at' => $clockOutThirdTimeUser3,
        ]);

        Interval::create([
            'attendance_id' => $thirdAttendanceUser3 -> id,
            'interval_in_at' => $intervalInThirdTimeUser3,
            'interval_out_at' => $intervalOutThirdTimeUser3,
        ]);
    }
}

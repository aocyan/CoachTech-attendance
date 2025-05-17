<?php

namespace Tests\Feature;

use Database\Seeders\AttendanceIndexTestSeeder;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AttendanceDetailUserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_attendance_detail_displays_logged_in_user_name()
    {
        $this -> seed(AttendanceIndexTestSeeder::class);

        $response = $this -> get('/login');

        $response -> assertStatus(200);

        $response = $this -> post('/login/certification', [
            'email' => 'user@example.com',
            'password' => '1234abcd',
        ]);

        $user = Auth::user();

        $this -> assertDatabaseHas('users', [
            'name' => '山田　太郎',
        ]);

        $this -> assertDatabaseHas('attendances', [
            'user_id' => $user -> id,
            'date' => '2025-04-01',
            'clock_in_at' => '2025-04-01 09:00:00',
            'clock_out_at' => '2025-04-01 18:00:00',
        ]);

        $attendance = Attendance::where('user_id', $user -> id)
            -> where('date', '2025-04-01')
            -> where('clock_in_at', '2025-04-01-09:00:00')
            -> where('clock_out_at', '2025-04-01-18:00:00')
            -> first();

        $this -> assertDatabaseHas('intervals', [
            'attendance_id' => $attendance -> id,
            'interval_in_at' => '2025-04-01 12:00:00',
            'interval_out_at' => '2025-04-01 13:00:00',
        ]);

        $response = $this -> get('/attendance');
        $response -> assertSee('勤務外');
        $response -> assertSee('勤務一覧');

        $response = $this -> get('/attendance/list?year=2025&month=4');

        $response -> assertStatus(200);

        $detailDate = Carbon::create(2025, 4, 1) -> toDateString();

        $response -> assertSee('┃ 勤務一覧');
        $response -> assertSee('04/01');
        $response -> assertSee('詳細ページへ');

        $response = $this -> get("/attendance/{$user -> id}?date={$detailDate}");

        $response -> assertStatus(200);

        $response -> assertSee('┃ 勤務詳細');
        $response -> assertSee('名前');
        $response -> assertSee('山田　太郎');
    }

    /** @test */
    public function test_attendance_detail_displays_selected_date()
    {
        $this -> seed(AttendanceIndexTestSeeder::class);

        $response = $this -> get('/login');

        $response -> assertStatus(200);

        $response = $this -> post('/login/certification', [
            'email' => 'user@example.com',
            'password' => '1234abcd',
        ]);

        $user = Auth::user();

        $this -> assertDatabaseHas('attendances', [
            'user_id' => $user -> id,
            'date' => '2025-04-01',
            'clock_in_at' => '2025-04-01 09:00:00',
            'clock_out_at' => '2025-04-01 18:00:00',
        ]);

        $attendance = Attendance::where('user_id', $user -> id)
            -> where('date', '2025-04-01')
            -> where('clock_in_at', '2025-04-01-09:00:00')
            -> where('clock_out_at', '2025-04-01-18:00:00')
            -> first();

        $this -> assertDatabaseHas('intervals', [
            'attendance_id' => $attendance -> id,
            'interval_in_at' => '2025-04-01 12:00:00',
            'interval_out_at' => '2025-04-01 13:00:00',
        ]);

        $response = $this -> get('/attendance');
        $response -> assertSee('勤務外');
        $response -> assertSee('勤務一覧');

        $response = $this -> get('/attendance/list?year=2025&month=4');

        $response -> assertStatus(200);

        $detailDate = Carbon::create(2025, 4, 1) -> toDateString();

        $response -> assertSee('┃ 勤務一覧');
        $response -> assertSee('04/01');

        $response -> assertSee('詳細ページへ');

        $response = $this -> get("/attendance/{$user -> id}?date={$detailDate}");

        $response -> assertStatus(200);

        $response -> assertSee('┃ 勤務詳細');
        $response -> assertSee('日付');
        $response -> assertSee('2025年');
        $response -> assertSee('4月 1日');
    }

    /** @test */
    public function test_clock_in_and_out_times_match_logged_in_user_records()
    {
        $this -> seed(AttendanceIndexTestSeeder::class);

        $response = $this -> get('/login');

        $response -> assertStatus(200);

        $response = $this -> post('/login/certification', [
            'email' => 'user@example.com',
            'password' => '1234abcd',
        ]);

        $user = Auth::user();

        $this -> assertDatabaseHas('attendances', [
            'user_id' => $user -> id,
            'date' => '2025-04-01',
            'clock_in_at' => '2025-04-01 09:00:00',
            'clock_out_at' => '2025-04-01 18:00:00',
        ]);

        $attendance = Attendance::where('user_id', $user -> id)
            -> where('date', '2025-04-01')
            -> where('clock_in_at', '2025-04-01-09:00:00')
            -> where('clock_out_at', '2025-04-01-18:00:00')
            -> first();

        $this -> assertDatabaseHas('intervals', [
            'attendance_id' => $attendance -> id,
            'interval_in_at' => '2025-04-01 12:00:00',
            'interval_out_at' => '2025-04-01 13:00:00',
        ]);

        $response = $this -> get('/attendance');
        $response -> assertSee('勤務外');
        $response -> assertSee('勤務一覧');

        $response = $this -> get('/attendance/list?year=2025&month=4');

        $response -> assertStatus(200);

        $detailDate = Carbon::create(2025, 4, 1) -> toDateString();

        $response -> assertSee('┃ 勤務一覧');
        $response -> assertSee('04/01');
        $response -> assertSee('出勤時間');
        $response -> assertSee('09:00');
        $response -> assertSee('退勤時間');
        $response -> assertSee('18:00');

        $response -> assertSee('詳細ページへ');

        $response = $this -> get("/attendance/{$user -> id}?date={$detailDate}");

        $response -> assertStatus(200);

        $response -> assertSee('┃ 勤務詳細');
        $response -> assertSee('日付');
        $response -> assertSee('2025年');
        $response -> assertSee('4月 1日');
        $response -> assertSee('出勤・退勤');
        $response -> assertSee('09:00');
        $response -> assertSee('18:00');
    }

    /** @test */
    public function test_interval_time_match_logged_in_user_records()
    {
        $this -> seed(AttendanceIndexTestSeeder::class);

        $response = $this -> get('/login');

        $response -> assertStatus(200);

        $response = $this -> post('/login/certification', [
            'email' => 'user@example.com',
            'password' => '1234abcd',
        ]);

        $user = Auth::user();

        $this -> assertDatabaseHas('attendances', [
            'user_id' => $user -> id,
            'date' => '2025-04-01',
            'clock_in_at' => '2025-04-01 09:00:00',
            'clock_out_at' => '2025-04-01 18:00:00',
        ]);

        $attendance = Attendance::where('user_id', $user -> id)
            -> where('date', '2025-04-01')
            -> where('clock_in_at', '2025-04-01-09:00:00')
            -> where('clock_out_at', '2025-04-01-18:00:00')
            -> first();

        $this -> assertDatabaseHas('intervals', [
            'attendance_id' => $attendance -> id,
            'interval_in_at' => '2025-04-01 12:00:00',
            'interval_out_at' => '2025-04-01 13:00:00',
        ]);

        $response = $this -> get('/attendance');
        $response -> assertSee('勤務外');
        $response -> assertSee('勤務一覧');

        $response = $this -> get('/attendance/list?year=2025&month=4');

        $response -> assertStatus(200);

        $detailDate = Carbon::create(2025, 4, 1) -> toDateString();

        $response -> assertSee('┃ 勤務一覧');
        $response -> assertSee('04/01');
        $response -> assertSee('休憩時間');
        $response -> assertSee('1:00');

        $response -> assertSee('詳細ページへ');

        $response = $this -> get("/attendance/{$user -> id}?date={$detailDate}");

        $response -> assertStatus(200);

        $response -> assertSee('┃ 勤務詳細');
        $response -> assertSee('日付');
        $response -> assertSee('2025年');
        $response -> assertSee('4月 1日');
        $response -> assertSee('休憩1');
        $response -> assertSee('12:00');
        $response -> assertSee('13:00');
    }
}

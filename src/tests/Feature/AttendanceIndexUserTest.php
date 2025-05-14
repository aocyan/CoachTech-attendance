<?php

namespace Tests\Feature;

use Database\Seeders\AttendanceIndexTestSeeder;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AttendanceIndexUserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_all_my_attendance_records_displayed()
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

        $firstAttendance = Attendance::where('user_id', $user -> id)
            -> where('date', '2025-04-01')
            -> where('clock_in_at', '2025-04-01 09:00:00')
            -> where('clock_out_at', '2025-04-01 18:00:00')
            -> first();

        $this -> assertDatabaseHas('intervals', [
            'attendance_id' => $firstAttendance -> id,
            'interval_in_at' => '2025-04-01 12:00:00',
            'interval_out_at' => '2025-04-01 13:00:00',
        ]);

        $this -> assertDatabaseHas('attendances', [
            'user_id' => $user -> id,
            'date' => '2025-04-10',
            'clock_in_at' => '2025-04-10 09:00:00',
            'clock_out_at' => '2025-04-10 18:00:00',
        ]);

        $secondAttendance = Attendance::where('user_id', $user -> id)
            -> where('date', '2025-04-10')
            -> where('clock_in_at', '2025-04-10 09:00:00')
            -> where('clock_out_at', '2025-04-10 18:00:00')
            -> first();

        $this -> assertDatabaseHas('intervals', [
            'attendance_id' => $secondAttendance -> id,
            'interval_in_at' => '2025-04-10 12:00:00',
            'interval_out_at' => '2025-04-10 13:00:00',
        ]);

        $this -> assertDatabaseHas('attendances', [
            'user_id' => $user -> id,
            'date' => '2025-04-20',
            'clock_in_at' => '2025-04-20 09:00:00',
            'clock_out_at' => '2025-04-20 18:00:00',
        ]);

        $thirdAttendance = Attendance::where('user_id', $user -> id)
            -> where('date','2025-04-20')
            -> where('clock_in_at','2025-04-20 09:00:00')
            -> where('clock_out_at','2025-04-20 18:00:00')
            -> first();

        $this -> assertDatabaseHas('intervals', [
            'attendance_id' => $thirdAttendance -> id,
            'interval_in_at' => '2025-04-20 12:00:00',
            'interval_out_at' => '2025-04-20 13:00:00',
        ]);

        $this -> assertDatabaseHas('attendances', [
            'user_id' => $user -> id,
            'date' => '2025-04-30',
            'clock_in_at' => '2025-04-30 09:00:00',
            'clock_out_at' => '2025-04-30 18:00:00',
        ]);

        $fourthAttendance = Attendance::where('user_id', $user -> id)
            -> where('date', '2025-04-30')
            -> where('clock_in_at', '2025-04-30 09:00:00')
            -> where('clock_out_at', '2025-04-30 18:00:00')
            -> first();

        $this -> assertDatabaseHas('intervals', [
            'attendance_id' => $fourthAttendance -> id,
            'interval_in_at' => '2025-04-30 12:00:00',
            'interval_out_at' => '2025-04-30 13:00:00',
        ]);

        $response = $this -> get('/attendance');
        $response -> assertSee('勤務外');
        $response -> assertSee('勤務一覧');

        $response = $this -> get('/attendance/list?year=2025&month=4');

        $response -> assertStatus(200);

        $response -> assertSee('2025年4月');
        $response -> assertSee('┃ 勤務一覧');

        $response -> assertSee('04/01');
        $response -> assertSee('出勤時間');
        $response -> assertSee('09:00');
        $response -> assertSee('退勤時間');
        $response -> assertSee('18:00');
        $response -> assertSee('休憩時間');
        $response -> assertSee('1:00');
        $response -> assertSee('実働時間');
        $response -> assertSee('9:00');

        $response -> assertSee('04/10');
        $response -> assertSee('出勤時間');
        $response -> assertSee('09:00');
        $response -> assertSee('退勤時間');
        $response -> assertSee('18:00');
        $response -> assertSee('休憩時間');
        $response -> assertSee('1:00');
        $response -> assertSee('実働時間');
        $response -> assertSee('9:00');

        $response -> assertSee('04/20');
        $response -> assertSee('出勤時間');
        $response -> assertSee('09:00');
        $response -> assertSee('退勤時間');
        $response -> assertSee('18:00');
        $response -> assertSee('休憩時間');
        $response -> assertSee('1:00');
        $response -> assertSee('実働時間');
        $response -> assertSee('9:00');

        $response -> assertSee('04/30');
        $response -> assertSee('出勤時間');
        $response -> assertSee('09:00');
        $response -> assertSee('退勤時間');
        $response -> assertSee('18:00');
        $response -> assertSee('休憩時間');
        $response -> assertSee('1:00');
        $response -> assertSee('実働時間');
        $response -> assertSee('9:00');
    }

    /** @test */
    public function test_display_current_month_on_attendance_list_screen()
    {
        $this -> seed(AttendanceIndexTestSeeder::class);

        $response = $this -> get('/login');

        $response -> assertStatus(200);

        $response = $this -> post('/login/certification', [
            'email' => 'user@example.com',
            'password' => '1234abcd',
        ]);

        $user = Auth::user();

        $nowDateTime = now();
        Carbon::setTestNow($nowDateTime);

        $response = $this -> get('/attendance');
        $response -> assertSee('勤務外');
        $response -> assertSee('勤務一覧');

        $response = $this -> get('/attendance/list');

        $response -> assertStatus(200);

        $response -> assertSee('┃ 勤務一覧');
        $response -> assertSee($nowDateTime -> format('Y年n月'));
    }

    /** @test */
    public function test_previous_month_button_displays_previous_month_data()
    {
        $this -> seed(AttendanceIndexTestSeeder::class);

        $response = $this -> get('/login');

        $response -> assertStatus(200);

        $response = $this -> post('/login/certification', [
            'email' => 'user@example.com',
            'password' => '1234abcd',
        ]);

        $user = Auth::user();

        $nowDate = Carbon::create(2025, 5, 11);
        Carbon::setTestNow($nowDate);

        $response = $this -> get('/attendance');
        $response -> assertSee('勤務外');
        $response -> assertSee('勤務一覧');

        $response = $this -> get('/attendance/list');

        $response -> assertStatus(200);

        $response -> assertSee('┃ 勤務一覧');
        $response -> assertSee('2025年5月');
        $response -> assertSee('⬅前月');

        $response = $this -> get('/attendance/list?year=2025&month=4');

        $response -> assertStatus(200);

        $response -> assertSee('┃ 勤務一覧');
        $response -> assertSee('2025年4月');
    }

    /** @test */
    public function test_next_month_button_displays_next_month_data()
    {
        $this -> seed(AttendanceIndexTestSeeder::class);

        $response = $this -> get('/login');

        $response -> assertStatus(200);

        $response = $this -> post('/login/certification', [
            'email' => 'user@example.com',
            'password' => '1234abcd',
        ]);

        $user = Auth::user();

        $nowDate = Carbon::create(2025, 5, 11);
        Carbon::setTestNow($nowDate);

        $response = $this -> get('/attendance');
        $response -> assertSee('勤務外');
        $response -> assertSee('勤務一覧');

        $response = $this -> get('/attendance/list');

        $response -> assertStatus(200);

        $response -> assertSee('┃ 勤務一覧');
        $response -> assertSee('2025年5月');
        $response -> assertSee('来月➡');

        $response = $this -> get('/attendance/list?year=2025&month=6');

        $response -> assertStatus(200);

        $response -> assertSee('┃ 勤務一覧');
        $response -> assertSee('2025年6月');
    }

    /** @test */
    public function test_click_detail_button_navigate_to_daily_attendance_detail_page()
    {
        $this -> seed(AttendanceIndexTestSeeder::class);

        $response = $this -> get('/login');

        $response -> assertStatus(200);

        $response = $this -> post('/login/certification', [
            'email' => 'user@example.com',
            'password' => '1234abcd',
        ]);

        $user = Auth::user();

        $response = $this -> get('/attendance');
        $response -> assertSee('勤務外');
        $response -> assertSee('勤務一覧');

        $response = $this -> get('/attendance/list');

        $response -> assertStatus(200);

        $response -> assertSee('┃ 勤務一覧');
        $response -> assertSee('詳細ページへ');

        $detailDate = Carbon::create(2025, 4, 1);
        $response = $this -> get("/attendance/{$user -> id}?$detailDate");

        $response -> assertStatus(200);

        $response -> assertSee('┃ 勤務詳細');
    }
}

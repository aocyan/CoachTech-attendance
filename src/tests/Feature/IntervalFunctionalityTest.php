<?php

namespace Tests\Feature;

use Database\Seeders\WorkingTestSeeder;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class IntervalFunctionalityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_interval_in_button_works_correctly()
    {
        $this -> seed(WorkingTestSeeder::class);

        $response = $this -> get('/login');

        $response -> assertStatus(200);

        $response = $this -> post('/login/certification', [
            'email' => 'user@example.com',
            'password' => '1234abcd',
        ]);

        $user = Auth::user();

        $clockInTime = Carbon::now() -> setTime(9, 0, 0);
        $nowDateTime = now() -> setTime(12, 0, 0);
        Carbon::setTestNow($nowDateTime);
        $date = $clockInTime -> toDateString();

        $response = $this -> get('/attendance');
        $response -> assertSee('勤務中');
        $response -> assertSee('休憩する');

        $response = $this -> post('/attendance/interval/in');

        $this -> assertDatabaseHas('users', [
            'name' => '山田　太郎',
            'status' => 'intervalIn',
        ]);

        $attendance = Attendance::where('user_id', $user -> id)
            -> where('date', $date)
            -> where('clock_in_at', $clockInTime)
            -> whereNull('clock_out_at')
            -> first();

        $this -> assertDatabaseHas('intervals', [
            'attendance_id' => $attendance -> id,
            'interval_in_at' => $nowDateTime,
            'interval_out_at' => null,
        ]);

        $response -> assertRedirect('/attendance');
        $response = $this -> get('/attendance');

        $response -> assertSee('休憩中');

        $response -> assertDontSee('勤務外');
        $response -> assertDontSee('勤務中');
        $response -> assertDontSee('退勤済');
    }

    /** @test */
    public function test_interval_button_allows_multiple_entries_per_day()
    {
        $this -> seed(WorkingTestSeeder::class);

        $response = $this -> get('/login');

        $response -> assertStatus(200);

        $response = $this -> post('/login/certification', [
            'email' => 'user@example.com',
            'password' => '1234abcd',
        ]);

        $user = Auth::user();

        $this -> assertDatabaseHas('users', [
            'name' => '山田　太郎',
            'status' => 'working',
        ]);

        $clockInTime = Carbon::now() -> setTime(9, 0, 0);
        $intervalInTime = now() -> setTime(12, 0, 0);
        Carbon::setTestNow($intervalInTime);
        $date = $clockInTime -> toDateString();

        $response = $this -> get('/attendance');
        $response -> assertSee('勤務中');
        $response -> assertSee('休憩する');

        $response = $this -> post('/attendance/interval/in');

        $this -> assertDatabaseHas('users', [
            'name' => '山田　太郎',
            'status' => 'intervalIn',
        ]);

        $attendance = Attendance::where('user_id', $user -> id)
            -> where('date', $date)
            -> where('clock_in_at', $clockInTime)
            -> whereNull('clock_out_at')
            -> first();

        $this -> assertDatabaseHas('intervals', [
            'attendance_id' => $attendance -> id,
            'interval_in_at' => $intervalInTime,
            'interval_out_at' => null,
        ]);

        $intervalOutTime = now() -> setTime(12, 30, 0);
        Carbon::setTestNow($intervalOutTime);

        $response = $this -> get('/attendance');
        $response -> assertSee('休憩中');
        $response -> assertSee('休憩終わり');

        $response = $this -> post('/attendance/interval/out');

        $this -> assertDatabaseHas('users', [
            'name' => '山田　太郎',
            'status' => 'working',
        ]);

        $attendance = Attendance::where('user_id', $user -> id)
            -> where('date', $date)
            -> where('clock_in_at', $clockInTime)
            -> whereNull('clock_out_at')
            -> first();

        $this -> assertDatabaseHas('intervals', [
            'attendance_id' => $attendance -> id,
            'interval_in_at' => $intervalInTime,
            'interval_out_at' => $intervalOutTime,
        ]);

        $response = $this -> get('/attendance'); 
        $response -> assertSee('休憩する');
    }

    /** @test */
    public function test_interval_out_button_works_correctly()
    {
        $this -> seed(WorkingTestSeeder::class);

        $response = $this -> get('/login');

        $response -> assertStatus(200);

        $response = $this -> post('/login/certification', [
            'email' => 'user@example.com',
            'password' => '1234abcd',
        ]);

        $user = Auth::user();

        $this -> assertDatabaseHas('users', [
            'name' => '山田　太郎',
            'status' => 'working',
        ]);

        $clockInTime = Carbon::now() -> setTime(9, 0, 0);
        $intervalInTime = now() -> setTime(12, 0, 0);
        Carbon::setTestNow($intervalInTime);
        $date = $clockInTime -> toDateString();

        $response = $this -> get('/attendance');
        $response -> assertSee('勤務中');
        $response -> assertSee('休憩する');

        $response = $this -> post('/attendance/interval/in');

        $this -> assertDatabaseHas('users', [
            'name' => '山田　太郎',
            'status' => 'intervalIn',
        ]);

        $attendance = Attendance::where('user_id', $user -> id)
            -> where('date', $date)
            -> where('clock_in_at', $clockInTime)
            -> whereNull('clock_out_at')
            -> first();

        $this -> assertDatabaseHas('intervals', [
            'attendance_id' => $attendance -> id,
            'interval_in_at' => $intervalInTime,
            'interval_out_at' => null,
        ]);

        $intervalOutTime = now() -> setTime(12, 30, 0);
        Carbon::setTestNow($intervalOutTime);

        $response = $this -> get('/attendance');
        $response -> assertSee('休憩中');
        $response -> assertSee('休憩終わり');

        $response = $this -> post('/attendance/interval/out');

        $this -> assertDatabaseHas('users', [
            'name' => '山田　太郎',
            'status' => 'working',
        ]);

        $attendance = Attendance::where('user_id', $user -> id)
            -> where('date', $date)
            -> where('clock_in_at', $clockInTime)
            -> whereNull('clock_out_at')
            -> first();

        $this -> assertDatabaseHas('intervals', [
            'attendance_id' => $attendance -> id,
            'interval_in_at' => $intervalInTime,
            'interval_out_at' => $intervalOutTime,
        ]);

        $response = $this -> get('/attendance'); 
        $response -> assertSee('勤務中');
    }

    /** @test */
    public function test_interval_out_button_allows_multiple_entries_per_day()
    {
        $this -> seed(WorkingTestSeeder::class);

        $response = $this -> get('/login');

        $response -> assertStatus(200);

        $response = $this -> post('/login/certification', [
            'email' => 'user@example.com',
            'password' => '1234abcd',
        ]);

        $user = Auth::user();

        $this -> assertDatabaseHas('users', [
            'name' => '山田　太郎',
            'status' => 'working',
        ]);

        $clockInTime = Carbon::now() -> setTime(9, 0, 0);
        $intervalInFirstTime = now() -> setTime(12, 0, 0);
        Carbon::setTestNow($intervalInFirstTime);
        $date = $clockInTime -> toDateString();

        $response = $this -> get('/attendance');
        $response -> assertSee('勤務中');
        $response -> assertSee('休憩する');

        $response = $this -> post('/attendance/interval/in');

        $this -> assertDatabaseHas('users', [
            'name' => '山田　太郎',
            'status' => 'intervalIn',
        ]);

        $attendance = Attendance::where('user_id', $user -> id)
            -> where('date', $date)
            -> where('clock_in_at', $clockInTime)
            -> whereNull('clock_out_at')
            -> first();

        $this -> assertDatabaseHas('intervals', [
            'attendance_id' => $attendance -> id,
            'interval_in_at' => $intervalInFirstTime,
            'interval_out_at' => null,
        ]);

        $intervalOutFirstTime = now() -> setTime(12, 30, 0);
        Carbon::setTestNow($intervalOutFirstTime);

        $response = $this -> get('/attendance');
        $response -> assertSee('休憩中');
        $response -> assertSee('休憩終わり');

        $response = $this -> post('/attendance/interval/out');

        $this -> assertDatabaseHas('users', [
            'name' => '山田　太郎',
            'status' => 'working',
        ]);

        $attendance = Attendance::where('user_id', $user -> id)
            -> where('date', $date)
            -> where('clock_in_at', $clockInTime)
            -> whereNull('clock_out_at')
            -> first();

        $this -> assertDatabaseHas('intervals', [
            'attendance_id' => $attendance -> id,
            'interval_in_at' => $intervalInFirstTime,
            'interval_out_at' => $intervalOutFirstTime,
        ]);

        $response = $this -> get('/attendance');
        $response -> assertSee('勤務中');
        $response -> assertSee('休憩する');

        $intervalInSecondTime = now() -> setTime(15, 0, 0);
        Carbon::setTestNow($intervalInSecondTime);

        $response = $this -> post('/attendance/interval/in');

        $this -> assertDatabaseHas('users', [
            'name' => '山田　太郎',
            'status' => 'intervalIn',
        ]);

        $attendance = Attendance::where('user_id', $user -> id)
            -> where('date', $date)
            -> where('clock_in_at', $clockInTime)
            -> whereNull('clock_out_at')
            -> first();

        $this -> assertDatabaseHas('intervals', [
            'attendance_id' => $attendance -> id,
            'interval_in_at' => $intervalInSecondTime,
            'interval_out_at' => null,
        ]);

        $response = $this -> get('/attendance');
        $response -> assertSee('休憩中');
        $response -> assertSee('休憩終わり');
    }

    /** @test */
    public function test_interval_times_are_visible_on_index_screen()
    {
        $this -> seed(WorkingTestSeeder::class);

        $response = $this -> get('/login');

        $response -> assertStatus(200);

        $response = $this -> post('/login/certification', [
            'email' => 'user@example.com',
            'password' => '1234abcd',
        ]);

        $user = Auth::user();

        $this -> assertDatabaseHas('users', [
            'name' => '山田　太郎',
            'status' => 'working',
        ]);

        $clockInTime = Carbon::now() -> setTime(9, 0, 0);
        $intervalInTime = now() -> setTime(12, 0, 0);
        Carbon::setTestNow($intervalInTime);
        $date = $clockInTime -> toDateString();

        $response = $this -> get('/attendance');
        $response -> assertSee('勤務中');
        $response -> assertSee('休憩する');

        $response = $this -> post('/attendance/interval/in');

        $this -> assertDatabaseHas('users', [
            'name' => '山田　太郎',
            'status' => 'intervalIn',
        ]);

        $attendance = Attendance::where('user_id', $user -> id)
            -> where('date', $date)
            -> where('clock_in_at', $clockInTime)
            -> whereNull('clock_out_at')
            -> first();

        $this -> assertDatabaseHas('intervals', [
            'attendance_id' => $attendance -> id,
            'interval_in_at' => $intervalInTime,
            'interval_out_at' => null,
        ]);

        $intervalOutTime = now() -> setTime(12, 30, 0);
        Carbon::setTestNow($intervalOutTime);

        $response = $this -> get('/attendance');
        $response -> assertSee('休憩中');
        $response -> assertSee('休憩終わり');

        $response = $this -> post('/attendance/interval/out');

        $this -> assertDatabaseHas('users', [
            'name' => '山田　太郎',
            'status' => 'working',
        ]);

        $attendance = Attendance::where('user_id', $user -> id)
            -> where('date', $date)
            -> where('clock_in_at', $clockInTime)
            -> whereNull('clock_out_at')
            -> first();

        $this -> assertDatabaseHas('intervals', [
            'attendance_id' => $attendance -> id,
            'interval_in_at' => $intervalInTime,
            'interval_out_at' => $intervalOutTime,
        ]);

        $response = $this -> get('/attendance/list');

        $response -> assertSee($clockInTime -> format('Y年n月'));
        $response -> assertSee($clockInTime -> format('n/d'));
        $response -> assertSee('休憩時間');
        $response -> assertSee($intervalInTime -> format('0:30'));
    }
}

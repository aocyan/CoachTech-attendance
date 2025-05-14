<?php

namespace Tests\Feature;

use App\Models\Admin;
use Database\Seeders\AdminSeeder;
use Database\Seeders\ClockInTestSeeder;
use Database\Seeders\WorkingTestSeeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Carbon\Carbon;
use Tests\TestCase;

class ClockOutFunctionalityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_clock_out_button_works_correctly()
    {
        $this -> seed(WorkingTestSeeder::class);

        $response = $this -> get('/login');

        $response -> assertStatus(200);

        $response = $this -> post('/login/certification', [
            'email' => 'user@example.com',
            'password' => '1234abcd',
        ]);

        $user = Auth::user();

        $response -> assertRedirect('/attendance');
        $response = $this -> get('/attendance');

        $response -> assertSee('勤務中');

        $clockOutTime = now() -> setTime(18, 0, 0);
        Carbon::setTestNow($clockOutTime);
        $date = $clockOutTime -> toDateString();

        $response = $this -> get('/attendance');
        $response -> assertSee('勤務中');
        $response -> assertSee('退勤する');

        $response = $this -> post('/attendance/clock/out');

        $this -> assertDatabaseHas('users', [
            'name' => '山田　太郎',
            'status' => 'clockOut',
        ]);

        $this -> assertDatabaseHas('attendances', [
            'user_id' => $user -> id,
            'date' => $date,
            'clock_out_at' => $clockOutTime,
        ]);

        $response -> assertRedirect('/attendance');
        $response = $this -> get('/attendance');

        $response -> assertSee('退勤済');

        $response -> assertDontSee('勤務外');
        $response -> assertDontSee('勤務中');
        $response -> assertDontSee('休憩中');
    }

    /** @test */
    public function test_clock_out_time_is_visible_on_admin_index_screen()
    {
        $this -> seed(ClockInTestSeeder::class);

        $response = $this -> get('/login');

        $response -> assertStatus(200);

        $response = $this -> post('/login/certification', [
            'email' => 'user@example.com',
            'password' => '1234abcd',
        ]);

        $user = Auth::user();

        $this -> assertDatabaseHas('users', [
            'name' => '山田　太郎',
            'status' => 'clockIn',
        ]);

        $this -> assertDatabaseHas('attendances', [
            'user_id' => $user -> id,
            'date' => null,
            'clock_in_at' => null,
            'clock_out_at' => null,
        ]);

        $response -> assertRedirect('/attendance');
        $response = $this -> get('/attendance');

        $clockInTime = Carbon::now()->setTime(9, 0, 0);
        Carbon::setTestNow($clockInTime); 
        $date = $clockInTime -> toDateString();

        $response -> assertSee('勤務外');
        $response -> assertSee('出勤する');

        $response = $this -> post('/attendance/clock/in');

        $this -> assertDatabaseHas('users', [
            'name' => '山田　太郎',
            'status' => 'working',
        ]);

        $this -> assertDatabaseHas('attendances', [
            'user_id' => $user -> id,
            'date' => $date,
            'clock_in_at' => $clockInTime,
            'clock_out_at' => null,
        ]);

        $response = $this -> get('/attendance');

        $response -> assertSee('勤務中');
        $response -> assertSee('退勤する');

        $clockOutTime = Carbon::now()->setTime(18, 0, 0);
        Carbon::setTestNow($clockOutTime);

        $response = $this -> post('/attendance/clock/out');

        $this -> assertDatabaseHas('users', [
            'name' => '山田　太郎',
            'status' => 'clockOut',
        ]);

        $this -> assertDatabaseHas('attendances', [
            'user_id' => $user -> id,
            'date' => $date,
            'clock_in_at' => $clockInTime,
            'clock_out_at' => $clockOutTime,
        ]);

        $response = $this -> get('/attendance');

        $response -> assertSee('退勤済');

        $response = $this -> post('/logout');

        $this -> seed(AdminSeeder::class);

        $adminUser = Admin::where('email', 'admin@example.com') -> first();

        $response = $this -> get('/admin/login');

        $response -> assertStatus(200);

        $response = $this -> post('/admin/login/certification', [
            'email' => 'admin@example.com',
            'password' => '1234abcd',
        ]);

        $this -> actingAs($adminUser, 'admin');

        $response -> assertRedirect('/admin/attendance/list');
        $response = $this -> get('/admin/attendance/list');

        $response -> assertSee($clockInTime -> format('Y年n月j日の勤務一覧'));
        $response -> assertSee('名前');
        $response -> assertSee('山田　太郎');
        $response -> assertSee('退勤時間');
        $response -> assertSee($clockOutTime -> format('H:i'));
    }
}
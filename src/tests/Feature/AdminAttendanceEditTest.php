<?php

namespace Tests\Feature;

use Database\Seeders\AdminSeeder;
use Database\Seeders\AttendanceIndexTestSeeder;
use App\Models\User;
use App\Models\Admin;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminAttendanceEditTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_selected_attendance_data_is_displayed_correctly()
    {
        $this -> seed(AttendanceIndexTestSeeder::class);

        $user = User::where('name', '山田　太郎') -> first();

        $this -> assertDatabaseHas('attendances', [
            'user_id' => $user -> id,
            'date' => '2025-04-01',
            'clock_in_at' => '2025-04-01 09:00:00',
            'clock_out_at' => '2025-04-01 18:00:00',
        ]);

        $attendance = Attendance::where('user_id', $user -> id)
            -> where('date', '2025-04-01')
            -> where('clock_in_at', '2025-04-01 09:00:00')
            -> where('clock_out_at', '2025-04-01 18:00:00')
            -> first();

        $this -> assertDatabaseHas('intervals', [
            'attendance_id' => $attendance -> id,
            'interval_in_at' => '2025-04-01 12:00:00',
            'interval_out_at' => '2025-04-01 13:00:00',
        ]);

        $this -> seed(AdminSeeder::class);

        $adminUser = Admin::where('email', 'admin@example.com') -> first();

        $response = $this -> get('/admin/login');

        $response -> assertStatus(200);

        $response = $this -> post('/admin/login/certification', [
            'email' => 'admin@example.com',
            'password' => '1234abcd',
        ]);

        $this -> actingAs($adminUser, 'admin');

        $response = $this -> get('/admin/attendance/list?year=2025&month=4&day=1');

        $response -> assertStatus(200);

        $response -> assertSee('┃ 2025年4月1日の勤務一覧');

        $response -> assertSee('名前');
        $response -> assertSee('山田　太郎');
        $response -> assertSee('出勤時間');
        $response -> assertSee('09:00');
        $response -> assertSee('退勤時間');
        $response -> assertSee('18:00');
        $response -> assertSee('休憩時間');
        $response -> assertSee('1:00');
        $response -> assertSee('実働時間');
        $response -> assertSee('8:00');
        $response -> assertSee('詳細');
        $response -> assertSee('詳細ページへ');

        $response = $this -> get("/attendance/{$user -> id}?date=2025-04-01&attendance_id={$attendance -> id}");

        $response -> assertStatus(200);
             
        $response -> assertSee('┃ 勤務詳細');
        $response -> assertSee('名前');
        $response -> assertSee('山田　太郎');
        $response -> assertSee('日付');
        $response -> assertSee('2025年');
        $response -> assertSee('4月1日');
        $response -> assertSee('出勤・退勤');
        $response -> assertSee('08:00');
        $response -> assertSee('18:00');
        $response -> assertSee('休憩1');
        $response -> assertSee('12:00');
        $response -> assertSee('13:00');
        $response -> assertSee('備考');
        $response -> assertSee('');
        $response -> assertSee('修正する');
    }

    /** @test */
    public function test_admin_error_message_is_shown_when_clock_in_is_after_clock_out()
    {
        $this -> seed(AttendanceIndexTestSeeder::class);

        $user = User::where('name', '山田　太郎') -> first();

        $this -> assertDatabaseHas('attendances', [
            'user_id' => $user -> id,
            'date' => '2025-04-01',
            'clock_in_at' => '2025-04-01 09:00:00',
            'clock_out_at' => '2025-04-01 18:00:00',
        ]);

        $attendance = Attendance::where('user_id', $user -> id)
            -> where('date', '2025-04-01')
            -> where('clock_in_at', '2025-04-01 09:00:00')
            -> where('clock_out_at', '2025-04-01 18:00:00')
            -> first();

        $this -> seed(AdminSeeder::class);

        $adminUser = Admin::where('email', 'admin@example.com') -> first();

        $response = $this -> get('/admin/login');

        $response -> assertStatus(200);

        $response = $this -> post('/admin/login/certification', [
            'email' => 'admin@example.com',
            'password' => '1234abcd',
        ]);

        $this -> actingAs($adminUser, 'admin');

        $response = $this -> get('/admin/attendance/list?year=2025&month=4&day=1');

        $response -> assertStatus(200);

        $response -> assertSee('詳細ページへ');

        $response = $this -> get("/attendance/{$user -> id}?date=2025-04-01&attendance_id={$attendance -> id}");

        $response -> assertSee('┃ 勤務詳細');
        $response -> assertSee('名前');
        $response -> assertSee('山田　太郎');
        $response -> assertSee('日付');
        $response -> assertSee('2025年');
        $response -> assertSee('4月1日');
        $response -> assertSee('出勤・退勤');
        $response -> assertSee('08:00');
        $response -> assertSee('18:00');
    
        $response -> assertSee('修正する');

        $detailDate = Carbon::create(2025, 4, 1) -> toDateString();

        $response = $this -> post("/attendance/admin/correction/{$user -> id}", [
            'name' => $user -> name,
            'date_data' => $detailDate,
            'clock_in' => '18:00',
            'clock_out' => '09:00',
            'interval_in' => ['12:00'],
            'interval_out' => ['13:00'],
            'comment' => 'テスト',
        ]);

        $response -> assertStatus(302);

        $response -> assertSessionHasErrors(['clock_in']);

        $response = $this -> get("/attendance/{$user -> id}?date=2025-04-01&attendance_id={$attendance -> id}");

        $response -> assertSeeText('出勤時間もしくは退勤時間が不適切な値です');
    }

    /** @test */
    public function test_admin_error_displayed_when_break_start_after_clock_out()
    {
        $this -> seed(AttendanceIndexTestSeeder::class);

        $user = User::where('name', '山田　太郎') -> first();

        $this -> assertDatabaseHas('attendances', [
            'user_id' => $user -> id,
            'date' => '2025-04-01',
            'clock_in_at' => '2025-04-01 09:00:00',
            'clock_out_at' => '2025-04-01 18:00:00',
        ]);

        $attendance = Attendance::where('user_id', $user -> id)
            -> where('date', '2025-04-01')
            -> where('clock_in_at', '2025-04-01 09:00:00')
            -> where('clock_out_at', '2025-04-01 18:00:00')
            -> first();

        $this -> assertDatabaseHas('intervals', [
            'attendance_id' => $attendance -> id,
            'interval_in_at' => '2025-04-01 12:00:00',
            'interval_out_at' => '2025-04-01 13:00:00',
        ]);

        $this -> seed(AdminSeeder::class);

        $adminUser = Admin::where('email', 'admin@example.com') -> first();

        $response = $this -> get('/admin/login');

        $response -> assertStatus(200);

        $response = $this -> post('/admin/login/certification', [
            'email' => 'admin@example.com',
            'password' => '1234abcd',
        ]);

        $this -> actingAs($adminUser, 'admin');

        $response = $this -> get('/admin/attendance/list?year=2025&month=4&day=1');

        $response -> assertStatus(200);

        $response -> assertSee('詳細ページへ');

        $response = $this -> get("/attendance/{$user -> id}?date=2025-04-01&attendance_id={$attendance -> id}");

        $response -> assertSee('┃ 勤務詳細');
        $response -> assertSee('名前');
        $response -> assertSee('山田　太郎');
        $response -> assertSee('日付');
        $response -> assertSee('2025年');
        $response -> assertSee('4月1日');
        $response -> assertSee('出勤・退勤');
        $response -> assertSee('09:00');
        $response -> assertSee('18:00');
        $response -> assertSee('休憩1');
        $response -> assertSee('12:00');
        $response -> assertSee('13:00');
    
        $response -> assertSee('修正する');

        $detailDate = Carbon::create(2025, 4, 1) -> toDateString();

        $response = $this -> post("/attendance/admin/correction/{$user -> id}", [
            'name' => $user -> name,
            'date_data' => $detailDate,
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'interval_in' => ['19:00'],
            'interval_out' => ['20:00'],
            'comment' => 'テスト',
        ]);

        $response -> assertStatus(302);

        $response -> assertSessionHasErrors(['interval_out.0']);

        $response = $this -> get("/attendance/{$user -> id}?date=2025-04-01&attendance_id={$attendance -> id}");

        $response -> assertSeeText('休憩時間が勤務時間外です');
    }

    /** @test */
    public function test_admin_interval_out_is_after_clock_out()
    {
        $this -> seed(AttendanceIndexTestSeeder::class);

        $user = User::where('name', '山田　太郎') -> first();

        $this -> assertDatabaseHas('attendances', [
            'user_id' => $user -> id,
            'date' => '2025-04-01',
            'clock_in_at' => '2025-04-01 09:00:00',
            'clock_out_at' => '2025-04-01 18:00:00',
        ]);

        $attendance = Attendance::where('user_id', $user -> id)
            -> where('date', '2025-04-01')
            -> where('clock_in_at', '2025-04-01 09:00:00')
            -> where('clock_out_at', '2025-04-01 18:00:00')
            -> first();

        $this -> assertDatabaseHas('intervals', [
            'attendance_id' => $attendance -> id,
            'interval_in_at' => '2025-04-01 12:00:00',
            'interval_out_at' => '2025-04-01 13:00:00',
        ]);

        $this -> seed(AdminSeeder::class);

        $adminUser = Admin::where('email', 'admin@example.com') -> first();

        $response = $this -> get('/admin/login');

        $response -> assertStatus(200);

        $response = $this -> post('/admin/login/certification', [
            'email' => 'admin@example.com',
            'password' => '1234abcd',
        ]);

        $this -> actingAs($adminUser, 'admin');

        $response = $this -> get('/admin/attendance/list?year=2025&month=4&day=1');

        $response -> assertStatus(200);

        $response -> assertSee('詳細ページへ');

        $response = $this -> get("/attendance/{$user -> id}?date=2025-04-01&attendance_id={$attendance -> id}");

        $response -> assertSee('┃ 勤務詳細');
        $response -> assertSee('名前');
        $response -> assertSee('山田　太郎');
        $response -> assertSee('日付');
        $response -> assertSee('2025年');
        $response -> assertSee('4月1日');
        $response -> assertSee('出勤・退勤');
        $response -> assertSee('09:00');
        $response -> assertSee('18:00');
        $response -> assertSee('休憩1');
        $response -> assertSee('12:00');
        $response -> assertSee('13:00');
    
        $response -> assertSee('修正する');

        $detailDate = Carbon::create(2025, 4, 1) -> toDateString();

        $response = $this -> post("/attendance/admin/correction/{$user -> id}", [
            'name' => $user -> name,
            'date_data' => $detailDate,
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'interval_in' => ['17:30'],
            'interval_out' => ['18:30'],
            'comment' => 'テスト',
        ]);

        $response -> assertStatus(302);

        $response -> assertSessionHasErrors(['interval_out.0']);

        $response = $this -> get("/attendance/{$user -> id}?date=2025-04-01&attendance_id={$attendance -> id}");

        $response -> assertSeeText('休憩時間が勤務時間外です');
    }

    /** @test */
    public function test_admin_error_message_is_displayed_when_remark_field_is_empty()
    {
        $this -> seed(AttendanceIndexTestSeeder::class);

        $user = User::where('name', '山田　太郎') -> first();

        $this -> assertDatabaseHas('attendances', [
            'user_id' => $user -> id,
            'date' => '2025-04-01',
            'clock_in_at' => '2025-04-01 09:00:00',
            'clock_out_at' => '2025-04-01 18:00:00',
        ]);

        $attendance = Attendance::where('user_id', $user -> id)
            -> where('date', '2025-04-01')
            -> where('clock_in_at', '2025-04-01 09:00:00')
            -> where('clock_out_at', '2025-04-01 18:00:00')
            -> first();

        $this -> assertDatabaseHas('intervals', [
            'attendance_id' => $attendance -> id,
            'interval_in_at' => '2025-04-01 12:00:00',
            'interval_out_at' => '2025-04-01 13:00:00',
        ]);

        $this -> seed(AdminSeeder::class);

        $adminUser = Admin::where('email', 'admin@example.com') -> first();

        $response = $this -> get('/admin/login');

        $response -> assertStatus(200);

        $response = $this -> post('/admin/login/certification', [
            'email' => 'admin@example.com',
            'password' => '1234abcd',
        ]);

        $this -> actingAs($adminUser, 'admin');

        $response = $this -> get('/admin/attendance/list?year=2025&month=4&day=1');

        $response -> assertStatus(200);

        $response -> assertSee('詳細ページへ');

        $response = $this -> get("/attendance/{$user -> id}?date=2025-04-01&attendance_id={$attendance -> id}");

        $response -> assertSee('┃ 勤務詳細');
        $response -> assertSee('名前');
        $response -> assertSee('山田　太郎');
        $response -> assertSee('日付');
        $response -> assertSee('2025年');
        $response -> assertSee('4月1日');
        $response -> assertSee('出勤・退勤');
        $response -> assertSee('09:00');
        $response -> assertSee('18:00');
        $response -> assertSee('休憩1');
        $response -> assertSee('12:00');
        $response -> assertSee('13:00');
    
        $response -> assertSee('修正する');

        $detailDate = Carbon::create(2025, 4, 1) -> toDateString();

        $response = $this -> post("/attendance/admin/correction/{$user -> id}", [
            'name' => $user -> name,
            'date_data' => $detailDate,
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'interval_in' => ['17:30'],
            'interval_out' => ['18:30'],
            'comment' => '',
        ]);

        $response -> assertStatus(302);

        $response -> assertSessionHasErrors('comment');

        $response = $this -> get("/attendance/{$user -> id}?date=2025-04-01&attendance_id={$attendance -> id}");

        $response -> assertSeeText('備考を記入してください');
    }
}

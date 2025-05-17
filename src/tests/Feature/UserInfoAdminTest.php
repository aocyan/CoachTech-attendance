<?php

namespace Tests\Feature;

use Database\Seeders\AdminSeeder;
use Database\Seeders\AttendanceIndexTestSeeder;
use Database\Seeders\AttendancesAdminTestSeeder;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserInfoAdminTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_admin_can_view_all_users_name_and_email()
    {
        $this -> seed(AttendancesAdminTestSeeder::class);

        $this -> assertDatabaseHas('users', [
            'name' => '山田　太郎',
            'email' => 'user1@example.com'
        ]);

        $this -> assertDatabaseHas('users', [
            'name' => '森　花子',
            'email' => 'user2@example.com'
        ]);

        $this -> assertDatabaseHas('users', [
            'name' => '伊藤　三郎',
            'email' => 'user3@example.com'
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

        $response = $this -> get('/admin/staff/list');

        $response -> assertStatus(200);

        $response -> assertSee('名前');
        $response -> assertSee('メールアドレス');
        $response -> assertSee('山田　太郎');
        $response -> assertSee('user1@example.com');
        $response -> assertSee('森　花子');
        $response -> assertSee('user1@example.com');
        $response -> assertSee('伊藤　三郎');
        $response -> assertSee('user1@example.com');
    }

    /** @test */
    public function test_user_attendance_info_is_displayed_correctly()
    {
        $this -> seed(AttendanceIndexTestSeeder::class);

        $user = User::where('name', '山田　太郎') -> first();

        $this -> assertDatabaseHas('users', [
            'name' => '山田　太郎',
            'email' => 'user@example.com'
        ]);

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

        $this -> seed(AdminSeeder::class);

        $adminUser = Admin::where('email', 'admin@example.com') -> first();

        $response = $this -> get('/admin/login');

        $response -> assertStatus(200);

        $response = $this -> post('/admin/login/certification', [
            'email' => 'admin@example.com',
            'password' => '1234abcd',
        ]);

        $this -> actingAs($adminUser, 'admin');

        $response = $this -> get('/admin/staff/list');

        $response -> assertStatus(200);

        $response -> assertSee('名前');
        $response -> assertSee('メールアドレス');
        $response -> assertSee('山田　太郎');
        $response -> assertSee('user@example.com');
        $response -> assertSee('月次勤務一覧');

        $response -> assertSee('月次勤務ページへ');

        $response = $this -> get("/admin/attendance/staff/{$user -> id}?year=2025&month=4");

        $response -> assertStatus(200);

        $response -> assertSee('2025年4月');
        $response -> assertSee('┃ 山田　太郎さんの月次勤務');

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
    public function test_previous_month_button_displays_previous_month_info()
    {
        $this -> seed(AttendanceIndexTestSeeder::class);

        $user = User::where('name', '山田　太郎') -> first();

        $this -> assertDatabaseHas('users', [
            'name' => '山田　太郎',
            'email' => 'user@example.com'
        ]);

        $nowDate = Carbon::create(2025, 5, 11);
        Carbon::setTestNow($nowDate);

        $this -> seed(AdminSeeder::class);

        $adminUser = Admin::where('email', 'admin@example.com') -> first();

        $response = $this -> get('/admin/login');

        $response -> assertStatus(200);

        $response = $this -> post('/admin/login/certification', [
            'email' => 'admin@example.com',
            'password' => '1234abcd',
        ]);

        $this -> actingAs($adminUser, 'admin');

        $response = $this -> get("/admin/attendance/staff/{$user -> id}");

        $response -> assertStatus(200);

        $response -> assertSee('2025年5月');
        $response -> assertSee('┃ 山田　太郎さんの月次勤務');

        $response -> assertSee('⬅前月');
        $response = $this -> get("/admin/attendance/staff/{$user -> id}?year=2025&month=4");

        $response -> assertStatus(200);

        $response -> assertSee('┃ 山田　太郎さんの月次勤務');
        $response -> assertSee('2025年4月');
    }

    /** @test */
    public function test_next_month_button_displays_next_month_info()
    {
        $this -> seed(AttendanceIndexTestSeeder::class);

        $user = User::where('name', '山田　太郎') -> first();

        $this -> assertDatabaseHas('users', [
            'name' => '山田　太郎',
            'email' => 'user@example.com'
        ]);

        $nowDate = Carbon::create(2025, 5, 11);
        Carbon::setTestNow($nowDate);

        $this -> seed(AdminSeeder::class);

        $adminUser = Admin::where('email', 'admin@example.com') -> first();

        $response = $this -> get('/admin/login');

        $response -> assertStatus(200);

        $response = $this -> post('/admin/login/certification', [
            'email' => 'admin@example.com',
            'password' => '1234abcd',
        ]);

        $this -> actingAs($adminUser, 'admin');

        $response = $this -> get("/admin/attendance/staff/{$user -> id}");

        $response -> assertStatus(200);

        $response -> assertSee('2025年5月');
        $response -> assertSee('┃ 山田　太郎さんの月次勤務');

        $response -> assertSee('翌月➡');
        $response = $this -> get("/admin/attendance/staff/{$user -> id}?year=2025&month=6");

        $response -> assertStatus(200);

        $response -> assertSee('┃ 山田　太郎さんの月次勤務');
        $response -> assertSee('2025年6月');
    }

    /** @test */
    public function test_clicking_details_button_redirects_to_attendance_detail_page()
    {
        $this -> seed(AttendanceIndexTestSeeder::class);

        $user = User::where('name', '山田　太郎') -> first();

        $this -> assertDatabaseHas('users', [
            'name' => '山田　太郎',
            'email' => 'user@example.com'
        ]);

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

        $response = $this -> get("/admin/attendance/staff/{$user -> id}?year=2025&month=4");

        $response -> assertStatus(200);

        $response -> assertSee('2025年4月');
        $response -> assertSee('┃ 山田　太郎さんの月次勤務');

        $response -> assertSee('04/01');
        $response -> assertSee('出勤時間');
        $response -> assertSee('09:00');
        $response -> assertSee('退勤時間');
        $response -> assertSee('18:00');
        $response -> assertSee('休憩時間');
        $response -> assertSee('1:00');
        $response -> assertSee('実働時間');
        $response -> assertSee('9:00');

        $response = $this -> get("/attendance/{$user -> id}?date=2025-04-01");

        $response -> assertStatus(200);

        $response -> assertSee('┃ 勤務詳細');
        $response -> assertSee('2025年');
        $response -> assertSee('4月1日');
        $response -> assertSee('出勤・退勤');
        $response -> assertSee('09:00');
        $response -> assertSee('18:00');
        $response -> assertSee('休憩1');
        $response -> assertSee('12:00');
        $response -> assertSee('13:00');
    }
}

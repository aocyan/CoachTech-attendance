<?php

namespace App\Http\Controllers;

use App\Models\Interval;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function store(Request $request)
	{
        $user = User::store();

		Auth::login($user);

		return redirect()->route('user.attend');
	}

    public function attend()
	{
        $dateTime = Attendance::nowDateTime();
		$attendanceData = Attendance::defaultSettingAttend();
		$intervalData = Interval::defaultSettingInterval();
		$userStatus = $attendanceData['userStatus'];

		return view('user.attend', compact('dateTime', 'attendanceData','intervalData','userStatus'));
	}

	public function statusAttend()
	{
		$dateTime = Attendance::nowDateTime();
		$attendanceData = Attendance::statusAttend();
		$intervalData = Interval::statusAttend();
		$userStatus = session('userStatus');

		return view('user.attend', compact('dateTime', 'attendanceData','intervalData','userStatus'));
	}

	public function clockIn()
	{
        Attendance::clockInTime();

		return redirect()->route('user.status');
	}

	public function clockOut()
	{
		$attendanceData = Attendance::clockOutTime();

		$userStatus = $attendanceData['userStatus'];

		return redirect()->route('user.status');
	}

	public function intervalIn()
	{
		$intervalData = Interval::intervalInTime();

		session(['userStatus' => $intervalData['userStatus']]);

		return redirect()->route('user.status');
	}

	public function intervalOut()
	{
		$intervalData = Interval::intervalOutTime();

		session(['userStatus' => $intervalData['userStatus']]);

		return redirect()->route('user.status');
	}

    public function index()
	{
		return view('user.index');
	}

    public function detail()
	{
		return view('user.detail');
	}

    public function apply()
	{
		return view('user.apply');
	}
}

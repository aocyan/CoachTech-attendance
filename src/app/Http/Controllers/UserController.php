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

		return redirect()->route('user.default');
	}

    public function defaultAttend()
	{
		$attendanceData = Attendance::defaultSettingAttend();
		$intervalData = Interval::defaultSettingInterval();

		return redirect()->route('user.attend');
	}

	public function attend()
	{
		$dateTime = Attendance::nowDateTime();
		$attendanceData = Attendance::statusAttend();
		$intervalData = Interval::statusInterval();

		if ($attendanceData->updated_at
        	->toDateString() !== now()
        	->toDateString()) {
				Attendance::dateChanges();
		}

		return view('user.attend', compact('dateTime', 'attendanceData','intervalData'));
	}

	public function loginAttend()
	{
		Attendance::userLoginAttend();

		return redirect()->route('user.attend');
	}

	public function clockIn()
	{
        Attendance::clockInTime();

		return redirect()->route('user.attend');
	}

	public function clockOut()
	{
		$attendanceData = Attendance::clockOutTime();
		
		return redirect()->route('user.attend');
	}

	public function intervalIn()
	{
		$intervalData = Interval::intervalInTime();

		return redirect()->route('user.attend');
	}

	public function intervalOut()
	{
		$intervalData = Interval::intervalOutTime();

		return redirect()->route('user.attend');
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

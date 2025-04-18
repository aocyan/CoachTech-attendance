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

		return redirect() -> route('user.default');
	}

    public function defaultAttend()
	{
		Attendance::defaultSettingAttend();
		Interval::defaultSettingInterval();

		return redirect() -> route('user.attend');
	}

	public function attend()
	{
		$dateTime = Attendance::nowDateTime();
		$attendanceData = Attendance::statusAttend();
		$intervalData = Interval::statusInterval();

		if ($attendanceData->updated_at -> toDateString() !== now()
        								-> toDateString()) {
			Attendance::dateChanges();
		}

		return view('user.attend', compact('dateTime', 'attendanceData','intervalData'));
	}

	public function clockIn()
	{
        Attendance::clockInTime();

		return redirect() -> route('user.attend');
	}

	public function clockOut()
	{
		Attendance::clockOutTime();
		
		return redirect() -> route('user.attend');
	}

	public function intervalIn()
	{
		Interval::intervalInTime();

		return redirect() -> route('user.attend');
	}

	public function intervalOut()
	{
		Interval::intervalOutTime();

		return redirect() -> route('user.attend');
	}

    public function index(Request $request)
	{
    	$year = $request->query('year', now() -> year);
    	$month = $request->query('month', now() -> month);
		$indexTime = Attendance::indexTime($year, $month);

		$MonthClockInTime = Attendance::getMonthClockTime(Auth::user(), $year, $month);
		$intervalTotalTime = Interval::getMonthIntervalTotalTime(Auth::user(), $year, $month);
		$workingTotalTime = Attendance::workingTotalTime(Auth::user(), $year, $month);

		return view('user.index', array_merge($indexTime, $MonthClockInTime,$intervalTotalTime,$workingTotalTime));
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

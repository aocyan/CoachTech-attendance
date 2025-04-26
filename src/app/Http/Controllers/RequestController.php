<?php

namespace App\Http\Controllers;

use App\Models\Interval;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function apply()
	{
		return view('request.apply');
	}

    public function correction()
	{
		return view('request.correction');
	}
}

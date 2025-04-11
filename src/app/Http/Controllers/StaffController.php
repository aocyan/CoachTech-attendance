<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
	{
		return view('staff.index');
	}

    public function attendList()
	{
		return view('staff.attend_index');
	}
}

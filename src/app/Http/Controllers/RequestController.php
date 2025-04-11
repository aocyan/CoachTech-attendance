<?php

namespace App\Http\Controllers;

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

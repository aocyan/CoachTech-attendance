<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;

    protected $fillable = [
        'correction_id',
        'interval_in_at',
        'interval_out_at',
    ];

    protected $casts = [
        'interval_in_at' => 'datetime',
        'interval_out_at' => 'datetime',
    ];

    public function correction()
    {
        return $this -> belongsTo(Correction::class, 'correction_id');
    }

    public static function store($request, $correction)
    {
        $intervalIns = $request -> input('interval_in', []);
        $intervalOuts = $request -> input('interval_out', []);
        
        $date = $correction -> date;

        $count = count($intervalIns);
   
        for ($i = 0; $i < $count; $i++) {
            
            $in = $intervalIns[$i];
            $out = $intervalOuts[$i];

            if (empty($in) && empty($out)) {
                continue;
            }

            $leave = new Leave();
            $leave -> correction_id = $correction->id;
            $leave -> interval_in_at = $in ? Carbon::parse($date . ' ' . $in) : null;
            $leave -> interval_out_at = $out ? Carbon::parse($date . ' ' . $out) : null;
            $leave -> save();
        }
    }
}

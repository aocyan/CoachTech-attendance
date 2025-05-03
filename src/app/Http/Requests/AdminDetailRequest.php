<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminDetailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'clock_in' => ['required', 'date_format:H:i'],
            'clock_out' => ['required', 'date_format:H:i'],
            'interval_in.*' => ['nullable', 'date_format:H:i'],
            'interval_out.*' => ['nullable', 'date_format:H:i'],
            'comment' => ['required','string'],
        ];      
    }

    public function messages(): array
    {
        return [
            'clock_in.required' => '出勤時間を入力してください。',
            'clock_out.required' => '退勤時間を入力してください。',
            'clock_in.date_format' => '出勤時間の形式が正しくありません。',
            'clock_out.date_format' => '退勤時間の形式が正しくありません。',
            'interval_in.*.date_format' => '休憩開始時間の形式が正しくありません。',
            'interval_out.*.date_format' => '休憩終了時間の形式が正しくありません。',
            'comment.required' => '備考を記入してください。',
        ];
    }

    public function withValidator($validator)
    {
        $validator -> after(function ($validator) {
            $clockIn = $this -> input('clock_in');
            $clockOut = $this -> input('clock_out');

            if ($clockIn >= $clockOut) {
                $validator
                    -> errors()
                    -> add('clock_in', '出勤時間もしくは退勤時間が不適切な値です');
            }

            $intervalIns = $this -> input('interval_in', []);
            $intervalOuts = $this -> input('interval_out', []);

            foreach ($intervalIns as $i => $in) {
                $out = $intervalOuts[$i] ?? null;

                if (($in && !$out) || (!$in && $out)) {
                    $validator
                        -> errors()
                        -> add('interval_in.0', '休憩開始時間と休憩終了時間の両方を入力してください');
                }

                if ($in && $in < $clockIn) {
                    $validator
                        -> errors()
                        -> add("interval_in.$i", '休憩時間が勤務時間外です');
                }


                if ($out && $out > $clockOut) {
                    $validator
                        -> errors()
                        -> add("interval_out.$i", '休憩時間が勤務時間外です');
                }

                if ($in && $out && $in >= $out) {
                    $validator
                        -> errors()
                        -> add("interval_in.$i", '休憩開始時間は休憩終了時間より前の値を入力してください');
                }
            }
        });
    }
}

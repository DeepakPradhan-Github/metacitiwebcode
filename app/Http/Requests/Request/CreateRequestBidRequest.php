<?php

namespace App\Http\Requests\Request;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequestBidRequest extends FormRequest
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
            'request_id'=>'required|exists:requests,id',
            'user_id'=>'required|integer',
            'driver_id'=>'required|integer',
            'default_price'=>'required|double',
            'bid_price'=>'required|double'
        ];
    }
}

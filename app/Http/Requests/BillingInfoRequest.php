<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class BillingInfoRequest extends Request
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
            'billing-address-first-name'    => 'required',
            'billing-address-last-name'     => 'required',
            'billing-address-address1'      => 'required',
            'billing-address-city'          => 'required',
            'billing-address-zip'           => 'required',
            'billing-address-country'       => 'required',
            'billing-address-state'         => 'required',
            'billing-address-phone'         => 'required',
            'billing-address-email'         => 'required|email',
        ];
    }
}

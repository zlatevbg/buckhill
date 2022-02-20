<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return (bool) Auth::user();
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['errors' => $validator->errors()], 422));
    }

    protected function getValidatorInstance()
    {
        $this->query->set('details', json_decode((string) $this->query->get('details'), true));

        return parent::getValidatorInstance();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'type' => 'required|string|in:credit_card,cash_on_delivery,bank_transfer',
        ];

        if ($this->type == 'credit_card') {
            $rules['details'] = 'required|array:ccv,number,expire_date,holder_name';
            $rules['details.ccv'] = 'required|digits:3';
            $rules['details.number'] = 'required|digits:16';
            $rules['details.expire_date'] = 'required|date_format:y/m';
            $rules['details.holder_name'] = 'required|string';
        } elseif ($this->type == 'cash_on_delivery') {
            $rules['details'] = 'required|array:address,last_name,first_name';
            $rules['details.address'] = 'required|string';
            $rules['details.last_name'] = 'required|string';
            $rules['details.first_name'] = 'required|string';
        } elseif ($this->type == 'bank_transfer') {
            $rules['details'] = 'required|array:iban,name,swift';
            $rules['details.iban'] = 'required|alpha_num';
            $rules['details.name'] = 'required|string';
            $rules['details.swift'] = 'required|alpha_num';
        }

        return $rules;
    }
}

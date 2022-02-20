<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateOrderRequest extends FormRequest
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
        $this->query->set('products', json_decode((string) $this->query->get('products'), true));
        $this->query->set('address', json_decode((string) $this->query->get('address'), true));

        return parent::getValidatorInstance();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'order_status_uuid' => 'required|uuid|exists:order_statuses,uuid',
            'payment_uuid' => 'required|uuid|exists:payments,uuid',
            'products' => 'required|array',
            'products.*.product' => 'required|uuid|exists:products,uuid',
            'products.*.quantity' => 'required|integer',
            'address' => 'required|array:billing,shipping',
        ];
    }
}

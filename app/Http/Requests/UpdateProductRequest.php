<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProductRequest extends FormRequest
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
        $this->query->set('metadata', json_decode((string) $this->query->get('metadata'), true));

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
            'category_uuid' => 'required|uuid|exists:categories,uuid',
            'title' => 'required|string',
            'price' => 'required|numeric',
            'description' => 'required',
            'metadata' => 'required|array:brand,image',
            'metadata.brand' => 'required|uuid|exists:brands,uuid',
            'metadata.image' => 'required|uuid|exists:files,uuid',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;

class StoreProductRequest extends FormRequest
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
            "title"                  => "required|string",
            "description"            => "string|nullable",
            "sku"                    => "string|nullable",
            "product_image"          => "array|nullable",
            "product_variant"        => "required|array",
            "product_variant_prices" => "required|array"
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = Response::json([
            "success" => false,
            "message" => $validator->errors()->first()
        ]);
        throw new ValidationException($validator, $response);
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplierCreateRequest extends FormRequest
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
            "name" => "required|string",
            "contact_person" => "nullable|string",
            "phone" => "nullable|string",
            "email" => "nullable|string",
            "address" => "nullable|string",
            "note" => "nullable|string",
            "is_active" => "required|boolean",
            "restaurant_id" => "required|numeric",
        ];
    }
}

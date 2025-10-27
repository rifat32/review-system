<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoicePaymentUpdateRequest extends FormRequest
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
            "id"=>"required|numeric|exists:expenses,id",
            "amount"=>"required|numeric",
            "payment_method"=>"required|string",
            "payment_date"=>"required|date",
            "note"=>"nullable|string",
            "paid_by" => "nullable|string",
            // "customer_id" => "required|numeric|exists:users,id",
            "is_active" => "required|boolean",
            "description" => "nullable|string",
            "expense_type" => "required|string",
            "supplier_id" => "required|numeric|exists:suppliers,id",
            "restaurant_id" => "required|numeric",
            "reciepts" => "present|array",
        ];
    }
}

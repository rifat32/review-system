<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoicePaymentCreateRequest extends FormRequest
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
            "amount"=>"required|numeric",
            "payment_method"=>"required|string",
            "payment_date"=>"required|date",
            "note"=>"nullable|string",
            // "customer_id" => "required|numeric|exists:users,id",
            "is_active" => "required|boolean",
            "paid_by" => "nullable|string",
            "description" => "nullable|string",
            "expense_type" => "required|string",
            "supplier_id" => "required|numeric|exists:suppliers,id",

            "reciepts" => "present|array",
            "restaurant_id" => "required|numeric",



        ];


    }
}

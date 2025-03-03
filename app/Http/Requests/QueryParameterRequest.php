<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QueryParameterRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'startDate' => 'required|date|date_format:d-m-Y|before_or_equal:endDate',
            'endDate' => 'required|date|date_format:d-m-Y|after_or_equal:startDate',
            'supplier_id' => 'nullable|integer|exists:suppliers,id',
            'customer_id' => 'nullable|integer|exists:customers,id',
        ];
    }
}

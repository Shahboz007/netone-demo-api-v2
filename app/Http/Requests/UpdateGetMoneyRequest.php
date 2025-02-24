<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGetMoneyRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'parent_id' => 'nullable|exists:get_money,id',
            'name' => 'nullable|string|min:1|max:255|unique:get_money,name',
            'amount' => 'nullable|numeric|min:0',
            'comment' => 'nullable|string|min:6|max:255',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'name' => [
                'nullable',
                'string',
                'min:1',
                'max:255',
                Rule::unique('get_money', 'name')->ignore($this->route('get_money')),
            ],
            'amount' => 'nullable|numeric|min:0',
            'comment' => 'nullable|string|min:6|max:255',
        ];
    }
}

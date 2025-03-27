<?php

namespace App\Http\Requests;

use App\Models\Polka;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePolkaRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'name' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('polkas', 'name')->ignore($this->route('polka'))
            ],
            'parent_id' => [
                'nullable',
                'numeric',
                'exists:polkas,id',
                Rule::notIn($this->getDescendants($this->route('polka')))
            ],
        ];
    }
    private function getDescendants(string|null $polkaId): array
    {
        if(!$polkaId) return [];

        $descendants = [];
        $this->findChildrenPolkas($polkaId, $descendants);
        return $descendants;
    }

    private function findChildrenPolkas($polkaId, &$descendants): void
    {
        $children = Polka::where('parent_id', $polkaId)->get()->pluck('id')->toArray();;
        foreach ($children as $childId) {
            $descendants[] = $childId;
            $this->findChildrenPolkas($childId, $descendants);
        }
    }

    public function messages(): array
    {
        return [
            "name.unique" => "Bu nomli polka allaqachon mavjud",
            "parent_id.exists" => "Polkani noto'g'ri tanladingiz",
        ];
    }
}

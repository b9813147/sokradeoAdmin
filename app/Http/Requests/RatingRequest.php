<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RatingRequest extends FormRequest
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
//            'groupId' => 'required|integer',
            'name'    => [
                'required',
                'string',
//                Rule::unique('ratings')->where(function ($q) {
//                    return $q->where('groups_id', $this->groupId);
//                })
            ],
            'type'    => [
                'required', 'integer',
//                Rule::unique('ratings')->where(function ($q) {
//                    return $q->where('groups_id', $this->groupId);
//                })
            ],
        ];
    }
}

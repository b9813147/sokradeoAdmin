<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\Rule;

class SubjectRequest extends FormRequest
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
            'groups_id' => 'required|integer',
            'subject' => [
                'required',
                'string',
                Rule::unique('group_subject_fields')->where(function ($q) {
                    return $q->where('groups_id', $this->groups_id);
                })
            ],
            'alias'   => 'required|string',
            'subject_fields_id'    => 'required|integer',
        ];
    }
}

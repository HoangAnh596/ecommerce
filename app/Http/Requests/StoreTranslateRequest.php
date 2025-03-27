<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTranslateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'translate_name' => 'required',
            'translate_canonical' => 'required|unique:routers,canonical, '.$this->id.',module_id'
        ];
    }

    public function messages(): array
    {
        return [
            'translate_name.required' => 'Tên tiêu đề không được để trống.',
            'translate_canonical.required' => 'Đường dẫn không được để trống.',
            'translate_canonical.unique' => 'Đường dẫn đã tồn tại. Hãy chọn đường dẫn khác.',
        ];
    }
}

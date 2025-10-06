<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StorePermissionRequest extends FormRequest
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
            'name' => 'required',
            'canonical' => 'required|unique:permissions'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên tiêu đề không được để trống.',
            'canonical.required' => 'Bạn chưa nhập từ khóa của quyền.',
            'canonical.unique' => 'Từ khóa của quyền đã tồn tại. Chọn từ khóa khác.'
        ];
    }
}

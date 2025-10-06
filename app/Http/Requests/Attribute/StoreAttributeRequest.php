<?php

namespace App\Http\Requests\Attribute;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttributeRequest extends FormRequest
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
            'canonical' => 'required|unique:routers|regex:/^[a-zA-Z0-9\-]+$/',
            'attribute_catalogue_id' => 'gt:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tiêu đề bài viết không được để trống.',
            'canonical.required' => 'Đường dẫn không được để trống.',
            'canonical.unique' => 'Đường dẫn đã tồn tại. Hãy chọn đường dẫn khác.',
            'attribute_catalogue_id.gt' => 'Danh mục cha không được để trống.',
            'canonical.regex' => 'Đường dẫn không chứa ký tự đặc biệt. Vui lòng nhập lại.',
        ];
    }
}

<?php

namespace App\Http\Requests\Promotion;

use Illuminate\Foundation\Http\FormRequest;

class StorePromotionRequest extends FormRequest
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
        $rules = [
            'name' => 'required',
            // 'code' => 'required|unique:promotions',
            'startDate' => 'required|custom_date_format',
        ];

        if(!$this->input('neverEndDate')) {
            $rules['endDate'] = 'required|custom_date_format|custom_after:startDate';
        }

        return $rules;
    }

    public function messages(): array
    {
        $messages = [
            'name.required' => 'Bạn chưa nhập tên chương trình khuyến mại.',
            // 'code.required' => 'Bạn chưa nhập mã khuyến mại.',
            // 'code.unique' => 'Mã khuyến mại đã tồn tại. Hãy chọn mã khác.',
            'startDate.required' => 'Bạn chưa nhập vào ngày bắt đầu của khuyến mại.',
            'startDate.custom_date_format' => 'Ngày bắt đầu khuyến mại không đúng định dạng.',
            'endDate.required' => 'Bạn chưa nhập vào ngày kết thúc của khuyến mại.',
            'endDate.custom_date_format' => 'Ngày kết thúc khuyến mại không đúng định dạng.',
        ];

        if(!$this->input('neverEndDate')) {
            $messages['endDate.required'] = 'Bạn chưa nhập vào ngày kết thúc của khuyến mại.';
            $messages['endDate.custom_after'] = 'Ngày kết thúc phải lớn hơn ngày bắt đầu của khuyến mại.';
        }

        return $messages;
    }
}

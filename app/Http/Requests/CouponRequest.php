<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CouponRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSeller());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'code' => 'required|string|max:50|unique:coupons,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:1',
            'min_order_value' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date|after:today',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'code.required' => 'Vui lòng nhập mã giảm giá.',
            'code.unique' => 'Mã giảm giá này đã tồn tại trên hệ thống, vui lòng chọn mã khác.',
            'name.required' => 'Vui lòng nhập tên chương trình ưu đãi.',
            'discount_type.required' => 'Vui lòng chọn loại giảm giá.',
            'discount_value.required' => 'Vui lòng nhập mức giảm giá.',
            'expires_at.after' => 'Ngày hết hạn phải là một ngày trong tương lai.',
        ];
    }
}

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
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $cleanNumber = function ($value) {
            if ($value === null || $value === '') {
                return null;
            }
            if (is_numeric($value)) {
                return $value;
            }
            // Strip dots and commas if submitted with thousands separators
            $cleaned = str_replace(['.', ','], '', (string) $value);

            return is_numeric($cleaned) ? $cleaned : $value;
        };

        $this->merge([
            'discount_value' => $cleanNumber($this->input('discount_value')),
            'min_order_value' => $cleanNumber($this->input('min_order_value')),
            'max_discount_amount' => $cleanNumber($this->input('max_discount_amount')),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $couponId = $this->route('id');
        $isPercent = $this->input('discount_type') === 'percent';

        return [
            'code' => 'required|string|max:50|unique:coupons,code,'.($couponId ?? 'NULL').',id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => [
                'required',
                'numeric',
                'min:1',
                $isPercent ? 'max:100' : 'nullable',
            ],
            'min_order_value' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
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
            'discount_value.max' => 'Mức giảm giá theo phần trăm không được vượt quá 100%.',
            'expires_at.after' => 'Ngày hết hạn phải là một ngày trong tương lai.',
        ];
    }
}

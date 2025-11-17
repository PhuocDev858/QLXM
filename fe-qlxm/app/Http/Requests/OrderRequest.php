<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[a-zA-ZÀ-ỹ\s]+$/' // Chỉ chữ cái và khoảng trắng
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'
            ],
            'phone' => [
                'required',
                'string',
                'min:10',
                'max:11',
                'regex:/^(0[3|5|7|8|9])+([0-9]{8})$/' // Format SĐT Việt Nam
            ],
            'address' => [
                'required',
                'string',
                'min:10',
                'max:500'
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'name.min' => 'Họ tên phải có ít nhất 2 ký tự.',
            'name.max' => 'Họ tên không được quá 255 ký tự.',
            'name.regex' => 'Họ tên chỉ được chứa chữ cái và khoảng trắng.',

            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
            'email.max' => 'Email không được quá 255 ký tự.',
            'email.regex' => 'Định dạng email không đúng.',

            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.min' => 'Số điện thoại phải có ít nhất 10 số.',
            'phone.max' => 'Số điện thoại không được quá 11 số.',
            'phone.regex' => 'Số điện thoại không đúng định dạng (VD: 0912345678).',

            'address.required' => 'Vui lòng nhập địa chỉ.',
            'address.min' => 'Địa chỉ phải có ít nhất 10 ký tự.',
            'address.max' => 'Địa chỉ không được quá 500 ký tự.',

            'notes.max' => 'Ghi chú không được quá 1000 ký tự.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Kiểm tra giỏ hàng có sản phẩm không
            $cart = session()->get('cart', []);
            if (empty($cart)) {
                $validator->errors()->add('cart', 'Giỏ hàng của bạn đang trống. Vui lòng thêm sản phẩm trước khi đặt hàng.');
            }
        });
    }
}

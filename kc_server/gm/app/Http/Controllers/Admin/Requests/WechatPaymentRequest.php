<?php

namespace App\Http\Controllers\Admin\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class WechatPaymentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [];
    }

    protected function failedValidation(Validator $validator)
    {
        exit(json_encode($validator->errors(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

//    public function rules()
//    {
//        return [
//            'h5_id' => 'required |exists:h5_template,h5_id',
//            'font_color' => 'string|nullable',
//            'select_color' => 'string|nullable',
//            'background_color' => 'string|nullable',
//            'position' => 'int|required| between:0,1'
//        ];
//    }
//
//    public function messages()
//    {
//        return [
//            'h5_id.required' => 'h5_id必须填写',
//            'h5_id.exists' => 'h5_id不存在!',
//            'position.required' => '请选择位置!'
//        ];
//    }
}

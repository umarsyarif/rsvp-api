<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
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
            'email' => 'required|email',
            'password' => 'required|min:6|max:16',
        ];
    }

    // public function messages()
    // {
    //     return [
    //         'email.required' => 'O campo de email é obrigatório',
    //         'password.required' => 'O campo de password é obrigatório',
    //     ];
    // }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'name' => 'required|min:3|max:60',
            'email' => 'required|email|min:3|max:60|unique:users',
            'password' => 'required|confirmed|min:6|max:16',
        ];
    }

    public function response(array $errors)
    {
        // Always return JSON.
        return response()->json($errors, 422);
    }
}

<?php
namespace App\Http\Controllers\Requests;
use Illuminate\Foundation\Http\FormRequest;
class RegisterUserAdminOpRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
            'name' => 'required|min:3|regex:/^[A-Za-záàâãéèêíóôõúçÁÀÂÃÉÈÍÓÔÕÚÇ ]+$/',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|min:3',
            'photo' => 'nullable|image|max:2048',
            'type' => 'required|in:a,o'
        ];
    }
}

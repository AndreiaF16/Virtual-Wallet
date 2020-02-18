<?php
namespace App\Http\Controllers\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        $user = Auth::user();
        return [
            'name' => 'string|min:3|regex:/^[A-Za-záàâãéèêíóôõúçÁÀÂÃÉÈÍÓÔÕÚÇ ]+$/',
            'email' => 'string|email|max:255|unique:users,email,'.$user->id,
            'nif' => ['nullable','sometimes',Rule::requiredIf($this->user()->type == 'u'),'numeric','digits:9'],
            'file' => 'nullable|image|max:2048'
        ];
    }
} 
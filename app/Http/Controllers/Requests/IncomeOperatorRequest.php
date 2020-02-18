<?php
namespace App\Http\Controllers\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IncomeOperatorRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
            'value' => 'required|numeric|min:0.01|max:5000',
            'email' => 'required|string|email|max:255|exists:wallets,email',
            'type_payment' => 'required|in:c,bt',
            'iban' => 'nullable|required_if:type_payment,bt|regex:/^[A-Za-z]{2}[0-9]{23}/',
            'source_description' => 'nullable|required_if:type_payment,bt|max:255'
        ];
    }
}
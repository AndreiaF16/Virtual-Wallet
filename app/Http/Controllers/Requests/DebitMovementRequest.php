<?php
namespace App\Http\Controllers\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DebitMovementRequest extends FormRequest
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
            'type_payment' => 'required|in:mb,bt',
            'iban' => 'nullable|required_if:type_payment,bt|regex:/^[A-Za-z]{2}[0-9]{23}/',
            'category_id' => 'required|exists:categories,id',
            'mb_entity_code'=> 'nullable|required_if:type_payment,mb|max:5|min:5',
            'mb_payment_reference'=>'nullable|required_if:type_payment,mb|max:9|min:9',
            'source_description' => 'nullable|required_if:transfer,1|max:255',
            'transfer' => 'required|in:0,1',
            'destination_email' => 'nullable|required_if:transfer,1|string|email|max:255|exists:wallets,email',
            'description' => 'nullable|required_if:type_payment,mb|max:255',
        ];
    }
}

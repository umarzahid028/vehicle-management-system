<?php 


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->hasRole('Sales Team');
    }

    public function rules()
    {
        return [
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'amount' => ['required', 'numeric', 'min:0'],
        ];
    }
} 
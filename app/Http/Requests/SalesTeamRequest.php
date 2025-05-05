<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SalesTeamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();
        
        // Check if user has the Sales Manager role
        if ($user->hasRole(['Super Admin', 'Admin', 'Sales Manager'])) {
            return true;
        }

        // For create/store requests
        if ($this->isMethod('POST')) {
            return $user->hasPermissionTo('create sales team');
        }
        
        // For update/put requests
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return $user->hasPermissionTo('edit sales team');
        }
        
        // For delete requests
        if ($this->isMethod('DELETE')) {
            return $user->hasPermissionTo('delete sales team');
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'position' => ['required', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'max:2048'], // 2MB max
            'is_active' => ['boolean'],
            'manager_id' => ['nullable', 'exists:users,id'],
        ];

        // For create requests, require password
        if ($this->isMethod('POST')) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
            $rules['password_confirmation'] = ['required', 'string'];
        }

        // For update requests, make password optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];
            $rules['password_confirmation'] = ['nullable', 'string', 'required_with:password'];
        }

        // Make email unique unless it's the current record being updated
        if ($this->route('sales_team')) {
            $rules['email'][] = Rule::unique('sales_teams')->ignore($this->route('sales_team'));
        } else {
            $rules['email'][] = Rule::unique('sales_teams');
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The team member name is required.',
            'email.required' => 'The email address is required.',
            'email.unique' => 'This email address is already in use.',
            'position.required' => 'The position is required.',
            'photo.image' => 'The uploaded file must be an image.',
            'photo.max' => 'The photo may not be larger than 2MB.',
            'password.required' => 'The password is required for new team members.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password_confirmation.required' => 'Please confirm the password.',
            'password_confirmation.required_with' => 'Please confirm the new password.',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];

        // Non-admins always order for themselves, so the controller derives
        // customer_id from the session rather than accepting it from the client.
        if ($this->attributes->get('authUser')?->isAdmin()) {
            $rules['customer_id'] = ['required', 'integer', 'exists:customers,id'];
        }

        return $rules;
    }
}

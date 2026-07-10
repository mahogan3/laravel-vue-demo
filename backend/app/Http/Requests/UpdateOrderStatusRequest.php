<?php

namespace App\Http\Requests;

use App\Enums\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderStatusRequest extends FormRequest
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
        $current = $this->route('order')->status;

        $allowed = array_map(fn (OrderStatus $s) => $s->value, $current->availableTransitions());

        return [
            'status' => ['required', Rule::enum(OrderStatus::class), Rule::in($allowed)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status.in' => 'Order status can only move forward (or be cancelled).',
        ];
    }
}

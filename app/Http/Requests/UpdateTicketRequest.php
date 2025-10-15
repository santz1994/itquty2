<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware/policy
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'subject' => ['sometimes', 'required', 'string', 'max:255', 'min:5'],
            'body' => ['sometimes', 'required', 'string', 'min:10'],
            'priority_id' => ['sometimes', 'required', 'integer', 'exists:tickets_priorities,id'],
            'type_id' => ['sometimes', 'required', 'integer', 'exists:tickets_types,id'],
            'status_id' => ['sometimes', 'required', 'integer', 'exists:tickets_statuses,id'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
            'location_id' => ['nullable', 'integer', 'exists:locations,id'],
            'asset_id' => ['nullable', 'integer', 'exists:assets,id'],
            'due_date' => ['nullable', 'date'],
            'resolved_at' => ['nullable', 'date'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'subject.required' => 'The ticket subject is required.',
            'subject.min' => 'The subject must be at least 5 characters.',
            'subject.max' => 'The subject may not be greater than 255 characters.',
            'body.required' => 'The ticket description is required.',
            'body.min' => 'The description must be at least 10 characters.',
            'priority_id.required' => 'Please select a priority.',
            'priority_id.exists' => 'The selected priority is invalid.',
            'type_id.required' => 'Please select a ticket type.',
            'type_id.exists' => 'The selected ticket type is invalid.',
            'status_id.required' => 'Please select a status.',
            'status_id.exists' => 'The selected status is invalid.',
            'assigned_to.exists' => 'The selected user does not exist.',
            'location_id.exists' => 'The selected location does not exist.',
            'asset_id.exists' => 'The selected asset does not exist.',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'subject' => 'ticket subject',
            'body' => 'description',
            'priority_id' => 'priority',
            'type_id' => 'ticket type',
            'status_id' => 'status',
            'assigned_to' => 'assigned user',
            'location_id' => 'location',
            'asset_id' => 'asset',
            'due_date' => 'due date',
            'resolved_at' => 'resolution date',
        ];
    }
}

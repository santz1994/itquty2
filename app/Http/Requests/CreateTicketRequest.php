<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTicketRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Will be handled by middleware
    }

    public function rules()
    {
        return [
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'ticket_priority_id' => 'required|exists:tickets_priorities,id',
            'ticket_type_id' => 'required|exists:tickets_types,id',
            'location_id' => 'required|exists:locations,id',
            'asset_id' => 'nullable|exists:assets,id',
            'asset_ids' => 'nullable|array',
            'asset_ids.*' => 'exists:assets,id',
            'user_id' => 'required|exists:users,id'
        ];
    }

    public function messages()
    {
        return [
            'subject.required' => 'Subjek tiket harus diisi',
            'description.required' => 'Deskripsi masalah harus diisi',
            'ticket_priority_id.required' => 'Prioritas tiket harus dipilih',
            'ticket_type_id.required' => 'Jenis tiket harus dipilih',
            'location_id.required' => 'Lokasi harus dipilih',
            'asset_id.exists' => 'Asset yang dipilih tidak valid',
            'user_id.required' => 'User ID diperlukan'
        ];
    }

    protected function prepareForValidation()
    {
        // Set the authenticated user's ID if not provided
        $this->merge([
            'user_id' => auth()->id() ?? $this->user_id
        ]);
    }

    /**
     * Configure the validator instance with cross-field validation.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Description should have reasonable length
            if ($this->filled('description') && strlen($this->description) < 10) {
                $validator->errors()->add('description', 'Description should be at least 10 characters to properly describe the issue.');
            }

            // If assets are selected, verify they're not already in "In Repair" status
            if ($this->filled('asset_ids') && is_array($this->asset_ids)) {
                $assetsInRepair = \App\Asset::whereIn('id', $this->asset_ids)
                    ->whereIn('status_id', [3, 4]) // Out for Repairs, Waiting for Repairs
                    ->exists();
                
                if ($assetsInRepair) {
                    $validator->errors()->add('asset_ids', 'One or more selected assets are already marked as "In Repair". Please check the asset status.');
                }
            }

            // Subject should not be too short
            if ($this->filled('subject') && strlen($this->subject) < 5) {
                $validator->errors()->add('subject', 'Subject should be at least 5 characters.');
            }

            // Verify ticket_status_id is valid if provided
            if ($this->filled('ticket_status_id')) {
                $status = \App\TicketsStatus::find($this->ticket_status_id);
                if (!$status) {
                    $validator->errors()->add('ticket_status_id', 'Invalid ticket status selected.');
                }
            }
        });
    }
}
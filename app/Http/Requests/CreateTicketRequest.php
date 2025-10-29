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
}
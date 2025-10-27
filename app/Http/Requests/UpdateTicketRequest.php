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
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'ticket_priority_id' => 'required|exists:tickets_priorities,id',
            'ticket_type_id' => 'required|exists:tickets_types,id',
            'ticket_status_id' => 'required|exists:tickets_statuses,id',
            'location_id' => 'nullable|exists:locations,id',
            'asset_id' => 'nullable|exists:assets,id',
            'assigned_to' => 'nullable|exists:users,id',
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
            'subject.required' => 'Subjek tiket harus diisi',
            'subject.string' => 'Subjek tiket harus berupa teks',
            'subject.max' => 'Subjek tiket maksimal 255 karakter',
            'description.required' => 'Deskripsi masalah harus diisi',
            'description.string' => 'Deskripsi harus berupa teks',
            'ticket_priority_id.required' => 'Prioritas tiket harus dipilih',
            'ticket_priority_id.exists' => 'Prioritas tiket yang dipilih tidak valid',
            'ticket_type_id.required' => 'Jenis tiket harus dipilih',
            'ticket_type_id.exists' => 'Jenis tiket yang dipilih tidak valid',
            'ticket_status_id.required' => 'Status tiket harus dipilih',
            'ticket_status_id.exists' => 'Status tiket yang dipilih tidak valid',
            'location_id.exists' => 'Lokasi yang dipilih tidak valid',
            'asset_id.exists' => 'Asset yang dipilih tidak valid',
            'assigned_to.exists' => 'User yang dipilih tidak valid',
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
            'subject' => 'subjek tiket',
            'description' => 'deskripsi',
            'ticket_priority_id' => 'prioritas',
            'ticket_type_id' => 'jenis tiket',
            'ticket_status_id' => 'status',
            'assigned_to' => 'user yang ditugaskan',
            'location_id' => 'lokasi',
            'asset_id' => 'asset',
        ];
    }
}

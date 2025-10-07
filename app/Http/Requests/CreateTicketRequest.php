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
        // Auto-generate ticket code and set initial status
        $this->merge([
            'ticket_code' => $this->generateTicketCode(),
            'ticket_status_id' => 1, // Assuming 1 = Open
            'user_id' => auth()->id() ?? $this->user_id
        ]);
    }

    private function generateTicketCode()
    {
        $prefix = 'TKT';
        $date = now()->format('Ymd');
        
        $lastTicket = \App\Ticket::whereDate('created_at', today())
                                ->orderBy('id', 'desc')
                                ->first();
        
        $sequence = $lastTicket ? 
                    (int)substr($lastTicket->ticket_code, -3) + 1 : 1;
        
        return $prefix . '-' . $date . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }
}
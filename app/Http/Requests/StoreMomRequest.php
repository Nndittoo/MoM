<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMomRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Ganti ini menjadi true jika user sudah login
        return true; 
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Informasi Rapat
            'title' => ['required', 'string', 'max:255'],
            'meeting_date' => ['required', 'date_format:Y-m-d'],
            'location' => ['required', 'string', 'max:255'],
            'start_time' => ['required', 'date_format:H:i'], 
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            
            // Pimpinan & Notulen 
            'pimpinan_rapat' => ['required', 'string', 'max:255'], // DIREVISI
            'notulen' => ['required', 'string', 'max:255'], // DIREVISI
            
            // Pembahasan
            'pembahasan' => ['required', 'string'],
            
            // Peserta Rapat
            'attendees_manual' => ['required', 'array', 'min:1'], // Array of names (strings)
            'attendees_manual.*' => ['required', 'string', 'max:255'],
            
            // Peserta Mitra (JSON string dari frontend)
            'partner_attendees_json' => ['nullable', 'string'], // Diubah ke nullable (opsional)
            
            // Agenda (Array of strings)
            'agendas' => ['required', 'array', 'min:1'], 
            'agendas.*' => ['required', 'string', 'max:500'],
            
            // Tindak Lanjut (Action Items) - Array of objects (nullable karena tidak ada di form)
            'action_items' => ['nullable', 'array'],
            'action_items.*.item' => ['required_with:action_items', 'string', 'max:500'],
            'action_items.*.due' => ['required_with:action_items', 'date_format:Y-m-d'],
            
            // Lampiran (File Upload)
            'attachments' => ['nullable', 'array'], // Diubah ke nullable (opsional)
            'attachments.*' => ['file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'], // Max 10MB
        ];
    }


}
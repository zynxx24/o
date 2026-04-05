<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_date' => ['required', 'date', 'after:today'],
            'event_type' => ['required', 'string', 'max:100'],
            'guest_count' => ['required', 'integer', 'min:1'],
            'delivery_address' => ['required', 'string', 'max:500'],
            'delivery_city' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'string', 'in:transfer_bca,transfer_mandiri,transfer_bri,ewallet_gopay,ewallet_ovo,cod'],
            'promo_code' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'event_date.required' => 'Tanggal acara wajib diisi.',
            'event_date.after' => 'Tanggal acara harus setelah hari ini.',
            'event_type.required' => 'Jenis acara wajib diisi.',
            'guest_count.required' => 'Jumlah tamu wajib diisi.',
            'guest_count.min' => 'Jumlah tamu minimal 1.',
            'delivery_address.required' => 'Alamat pengiriman wajib diisi.',
            'delivery_city.required' => 'Kota pengiriman wajib diisi.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
        ];
    }
}

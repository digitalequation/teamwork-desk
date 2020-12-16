<?php

namespace DigitalEquation\TeamworkDesk\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject' => 'required',
            'message' => 'required',
        ];
    }
}
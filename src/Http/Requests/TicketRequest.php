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
            'id'       => 'sometimes',
            'subject'  => 'required',
            'message'  => 'required',
            'priority' => 'sometimes',
            'user'     => 'sometimes',
        ];
    }
}
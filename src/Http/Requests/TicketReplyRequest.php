<?php

namespace DigitalEquation\TeamworkDesk\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketReplyRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ticketId'   => 'required',
            'customerId' => 'required',
            'body'       => 'required',
        ];
    }
}
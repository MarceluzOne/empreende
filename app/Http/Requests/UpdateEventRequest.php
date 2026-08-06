<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Capacidade nunca pode cair abaixo do número de inscritos já existentes.
        $minCapacity = $this->route('event')->participants()->count();

        return [
            'title'            => 'required|string|max:255',
            'type'             => 'required|in:single,consecutive,alternated',
            'start_time'       => 'required',
            'end_time'         => 'required|after:start_time',
            'max_capacity'     => 'required|integer|min:'.$minCapacity,
            'speaker_id'       => 'required|exists:speakers,id',
            'visibility'       => 'required|in:public,private',
            'whatsapp_group_link' => 'nullable|url|max:255',
            'single_date'      => 'required_if:type,single|nullable|date',
            'start_date'       => 'required_if:type,consecutive|nullable|date',
            'end_date_period'  => 'required_if:type,consecutive|nullable|date|after_or_equal:start_date',
            'selected_dates'   => 'required_if:type,alternated|nullable|array',
            'selected_dates.*' => 'date',
            'image'            => 'nullable|image|max:5120',
        ];
    }
}

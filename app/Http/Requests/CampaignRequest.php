<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CampaignRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'sending_identity_id' => ['required', 'exists:sending_identities,id'],
            'contact_list_id' => ['required', 'exists:contact_lists,id'],
            'template_id' => ['nullable', 'exists:templates,id'],
            'subject' => ['required', 'string', 'max:255'],
            'html_content' => ['required', 'string'],
            'track_opens' => ['sometimes', 'boolean'],
            'track_clicks' => ['sometimes', 'boolean'],
            'enable_unsubscribe' => ['sometimes', 'boolean'],
            'send_interval_seconds' => ['required', 'integer', 'min:1'],
            'status' => ['nullable', Rule::in(['draft', 'scheduled', 'sending', 'sent', 'failed'])],
            'scheduled_at' => ['nullable', 'date'],
            'sending_window_enabled' => ['sometimes', 'boolean'],
            'sending_window_start' => ['nullable', 'date_format:H:i'],
            'sending_window_end' => ['nullable', 'date_format:H:i'],
            'sending_window_schedule' => ['sometimes', 'array'],
            'sending_window_schedule.*.enabled' => ['nullable', 'boolean'],
            'sending_window_schedule.*.start' => ['nullable', 'date_format:H:i'],
            'sending_window_schedule.*.end' => ['nullable', 'date_format:H:i'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'sending_window_enabled' => $this->boolean('sending_window_enabled'),
        ]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $schedule = $this->input('sending_window_schedule', []);
            $scheduleEnabled = false;

            foreach ($schedule as $day => $config) {
                $enabled = filter_var(data_get($config, 'enabled'), FILTER_VALIDATE_BOOL);
                $start = data_get($config, 'start');
                $end = data_get($config, 'end');

                if ($enabled) {
                    $scheduleEnabled = true;

                    if (!$start || !$end) {
                        $validator->errors()->add("sending_window_schedule.$day.start", 'Podaj godziny dla każdego włączonego dnia.');
                    } elseif ($start >= $end) {
                        $validator->errors()->add("sending_window_schedule.$day.end", 'Koniec dnia musi być po starcie.');
                    }
                }
            }

            if ($this->boolean('sending_window_enabled') && ! $scheduleEnabled) {
                if (!$this->filled('sending_window_start') || !$this->filled('sending_window_end')) {
                    $validator->errors()->add('sending_window_start', 'Podaj godzinę startu i końca okna wysyłki.');
                }

                if ($this->filled('sending_window_start') && $this->filled('sending_window_end')) {
                    if ($this->input('sending_window_start') >= $this->input('sending_window_end')) {
                        $validator->errors()->add('sending_window_end', 'Godzina zakończenia musi być po godzinie startu.');
                    }
                }
            }
        });
    }
}

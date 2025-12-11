@csrf
<input type="hidden" name="action" value="draft" id="campaign-action">

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="block text-sm font-medium text-slate-700">Nazwa kampanii</label>
        <input type="text" name="name" value="{{ old('name', $campaign->name ?? '') }}" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500" required>
    </div>
    @php
        $extraSubjects = old('extra_subjects', $campaign->extra_subjects ?? []);
        $extraContents = old('extra_contents', $campaign->extra_contents ?? []);
    @endphp
    <div>
        <div class="flex items-center justify-between">
            <label class="block text-sm font-medium text-slate-700">Temat e-mail</label>
            <button type="button" id="open-subject-modal" class="flex items-center text-xs text-blue-700 hover:underline">
                + Dodaj więcej tematów
            </button>
        </div>
        <input type="text" name="subject" value="{{ old('subject', $campaign->subject ?? '') }}" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500" required>
        <p class="text-xs text-slate-500 mt-1">Możesz dodać wiele tematów dla zwiększenia dostarczalności. Losujemy temat dla każdego odbiorcy.</p>
        @if(!empty($extraSubjects))
            <p class="mt-1 text-xs text-blue-700 font-semibold">Dodatkowe tematy: {{ count($extraSubjects) }} (edytuj w oknie zarządzania)</p>
        @endif
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Tożsamość nadawcy</label>
        <select name="sending_identity_id" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500" required>
            <option value="">Wybierz...</option>
            @foreach ($sendingIdentities as $identity)
                <option value="{{ $identity->id }}" @selected(old('sending_identity_id', $campaign->sending_identity_id ?? '') == $identity->id)>
                    {{ $identity->name }} ({{ $identity->from_email }})
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Lista kontaktów</label>
        <select name="contact_list_id" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500" required>
            <option value="">Wybierz...</option>
            @foreach ($contactLists as $list)
                <option value="{{ $list->id }}" @selected(old('contact_list_id', $campaign->contact_list_id ?? '') == $list->id)>
                    {{ $list->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Szablon (opcjonalnie)</label>
        <select name="template_id" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500">
            <option value="">Brak</option>
            @foreach ($templates as $template)
                <option value="{{ $template->id }}" @selected(old('template_id', $campaign->template_id ?? '') == $template->id)>
                    {{ $template->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-sm font-medium text-slate-700">Odstęp między wysyłkami (sekundy)</label>
            <div class="mt-1 grid grid-cols-2 gap-2">
                <input type="number" min="1" name="send_interval_seconds" value="{{ old('send_interval_seconds', $campaign->send_interval_seconds ?? 1) }}" class="w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500" required placeholder="Od">
                <input type="number" min="1" name="send_interval_max_seconds" value="{{ old('send_interval_max_seconds', $campaign->send_interval_max_seconds ?? ($campaign->send_interval_seconds ?? 1)) }}" class="w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500" placeholder="Do">
            </div>
            <p class="text-xs text-slate-500 mt-1">Losowy odstęp w zadanym przedziale dla każdej wiadomości.</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Data zaplanowania</label>
            <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at', optional($campaign->scheduled_at)->format('Y-m-d\TH:i')) }}" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500">
        </div>
    </div>
</div>

<div class="mt-4 flex flex-wrap gap-4">
    <label class="inline-flex items-center space-x-2 text-sm text-slate-700">
        <input type="hidden" name="track_opens" value="0">
        <input type="checkbox" name="track_opens" value="1" class="rounded border-slate-300 text-blue-700 focus:ring-blue-500" @checked(old('track_opens', $campaign->track_opens ?? true))>
        <span>Śledź otwarcia</span>
    </label>
    <label class="inline-flex items-center space-x-2 text-sm text-slate-700">
        <input type="hidden" name="track_clicks" value="0">
        <input type="checkbox" name="track_clicks" value="1" class="rounded border-slate-300 text-blue-700 focus:ring-blue-500" @checked(old('track_clicks', $campaign->track_clicks ?? true))>
        <span>Śledź kliknięcia</span>
    </label>
    <label class="inline-flex items-center space-x-2 text-sm text-slate-700">
        <input type="hidden" name="enable_unsubscribe" value="0">
        <input type="checkbox" name="enable_unsubscribe" value="1" class="rounded border-slate-300 text-blue-700 focus:ring-blue-500" @checked(old('enable_unsubscribe', $campaign->enable_unsubscribe ?? true))>
        <span>Link wypisu</span>
    </label>
</div>

<div class="mt-6 rounded-lg border border-slate-200 bg-slate-50 p-4 space-y-3">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-semibold text-slate-900">Okno wysyłki w ciągu dnia</p>
            <p class="text-xs text-slate-600">Ustal w jakich godzinach kampania może być wysyłana. Pozostaw wyłączone, aby wysyłać całą dobę.</p>
        </div>
        <label class="inline-flex items-center space-x-2 text-sm text-slate-700">
            <input type="hidden" name="sending_window_enabled" value="0">
            <input type="checkbox" name="sending_window_enabled" value="1" class="rounded border-slate-300 text-blue-700 focus:ring-blue-500" @checked(old('sending_window_enabled', $campaign->sending_window_enabled ?? false))>
            <span>Włącz okno</span>
        </label>
    </div>
    @php
        $days = [
            'monday' => 'Poniedziałek',
            'tuesday' => 'Wtorek',
            'wednesday' => 'Środa',
            'thursday' => 'Czwartek',
            'friday' => 'Piątek',
            'saturday' => 'Sobota',
            'sunday' => 'Niedziela',
        ];
        $schedule = old('sending_window_schedule', $campaign->sending_window_schedule ?? []);
    @endphp
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-slate-600">
                    <th class="pb-2">Dzień</th>
                    <th class="pb-2">Włącz</th>
                    <th class="pb-2">Start</th>
                    <th class="pb-2">Koniec</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($days as $key => $label)
                    @php
                        $conf = $schedule[$key] ?? [];
                    @endphp
                    <tr>
                        <td class="py-2 font-semibold text-slate-900">{{ $label }}</td>
                        <td class="py-2">
                            <input type="hidden" name="sending_window_schedule[{{ $key }}][enabled]" value="0">
                            <input type="checkbox" name="sending_window_schedule[{{ $key }}][enabled]" value="1" class="rounded border-slate-300 text-blue-700 focus:ring-blue-500" @checked(data_get($conf, 'enabled', false))>
                        </td>
                        <td class="py-2">
                            <input type="time" name="sending_window_schedule[{{ $key }}][start]" value="{{ data_get($conf, 'start') }}" class="w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500">
                        </td>
                        <td class="py-2">
                            <input type="time" name="sending_window_schedule[{{ $key }}][end]" value="{{ data_get($conf, 'end') }}" class="w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="flex flex-wrap gap-3 pt-2">
        <button type="button" id="campaign-window-all-day" class="rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Całą dobę</button>
        <button type="button" id="campaign-window-disable" class="rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Wyłącz okna</button>
    </div>
</div>

<div class="mt-4">
    <div class="flex items-center justify-between">
        <label class="block text-sm font-medium text-slate-700">Treść kampanii</label>
        <button type="button" id="open-content-modal" class="flex items-center text-xs text-blue-700 hover:underline">
            + Dodaj kolejne treści
        </button>
    </div>
    <textarea name="html_content" class="tinymce-editor mt-1 w-full rounded-md border-slate-300 shadow-sm" rows="14">{{ old('html_content', $campaign->html_content ?? '') }}</textarea>
    <p class="text-xs text-slate-500 mt-1">Wiele wariantów treści zwiększa dostarczalność – losujemy wariant per odbiorca.</p>
    @if(!empty($extraContents))
        <p class="mt-1 text-xs text-blue-700 font-semibold">Dodatkowe treści: {{ count($extraContents) }} (edytuj w oknie zarządzania)</p>
    @endif
</div>

{{-- Modale zarządzania wariantami --}}
<div id="subject-modal" class="fixed inset-0 bg-black/40 z-40 hidden">
    <div class="mx-auto mt-16 max-w-2xl rounded-xl bg-white p-6 shadow-lg">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-slate-900">Dodatkowe tematy</h3>
            <button type="button" data-close-subject class="text-slate-500 hover:text-slate-700 text-sm">Zamknij</button>
        </div>
        <p class="text-sm text-slate-600 mt-1">Dodaj kilka tematów — system wylosuje jeden dla każdego maila.</p>
        <div id="subject-variants" class="mt-4 space-y-3">
            @foreach($extraSubjects as $idx => $subject)
                <input type="text" name="extra_subjects[]" value="{{ $subject }}" class="w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500" placeholder="Temat #{{ $idx + 1 }}">
            @endforeach
        </div>
        <div class="mt-4 flex items-center justify-between">
            <button type="button" id="add-subject-variant" class="rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">+ Dodaj temat</button>
            <button type="button" data-close-subject class="rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-800">Gotowe</button>
        </div>
    </div>
</div>

<div id="content-modal" class="fixed inset-0 bg-black/40 z-40 hidden">
    <div class="mx-auto mt-16 max-w-3xl rounded-xl bg-white p-6 shadow-lg">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-slate-900">Dodatkowe treści</h3>
            <button type="button" data-close-content class="text-slate-500 hover:text-slate-700 text-sm">Zamknij</button>
        </div>
        <p class="text-sm text-slate-600 mt-1">Możesz wprowadzić prosty HTML lub tekst. Losujemy jedną treść dla każdego odbiorcy.</p>
        <div id="content-variants" class="mt-4 space-y-3">
            @foreach($extraContents as $idx => $content)
                <textarea name="extra_contents[]" rows="3" class="w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500" placeholder="Treść #{{ $idx + 1 }}">{{ $content }}</textarea>
            @endforeach
        </div>
        <div class="mt-4 flex items-center justify-between">
            <button type="button" id="add-content-variant" class="rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">+ Dodaj treść</button>
            <button type="button" data-close-content class="rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-800">Gotowe</button>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const modalToggle = (id, show) => {
                    const el = document.getElementById(id);
                    if (!el) return;
                    el.classList.toggle('hidden', !show);
                };

                // Tematy
                const openSubject = document.getElementById('open-subject-modal');
                const subjectModal = document.getElementById('subject-modal');
                const addSubject = document.getElementById('add-subject-variant');
                const subjectWrap = document.getElementById('subject-variants');
                openSubject?.addEventListener('click', () => modalToggle('subject-modal', true));
                subjectModal?.querySelectorAll('[data-close-subject]')?.forEach((btn) => {
                    btn.addEventListener('click', () => modalToggle('subject-modal', false));
                });
                addSubject?.addEventListener('click', () => {
                    const input = document.createElement('input');
                    input.type = 'text';
                    input.name = 'extra_subjects[]';
                    input.placeholder = `Temat #${(subjectWrap?.children.length || 0) + 1}`;
                    input.className = 'w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500';
                    subjectWrap?.appendChild(input);
                });

                // Treści
                const openContent = document.getElementById('open-content-modal');
                const contentModal = document.getElementById('content-modal');
                const addContent = document.getElementById('add-content-variant');
                const contentWrap = document.getElementById('content-variants');
                openContent?.addEventListener('click', () => modalToggle('content-modal', true));
                contentModal?.querySelectorAll('[data-close-content]')?.forEach((btn) => {
                    btn.addEventListener('click', () => modalToggle('content-modal', false));
                });
                addContent?.addEventListener('click', () => {
                    const area = document.createElement('textarea');
                    area.name = 'extra_contents[]';
                    area.rows = 3;
                    area.placeholder = `Treść #${(contentWrap?.children.length || 0) + 1}`;
                    area.className = 'w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500';
                    contentWrap?.appendChild(area);
                });
            });
        </script>
    @endpush
@endonce

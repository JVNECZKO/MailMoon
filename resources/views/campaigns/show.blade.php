<x-app-layout>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <p class="text-sm text-slate-500">Kampania</p>
            <h1 class="text-2xl font-semibold text-slate-900">{{ $campaign->name }}</h1>
            <p class="text-sm text-slate-600">{{ $campaign->subject }}</p>
        </div>
        <div class="flex items-center space-x-3 mt-3 sm:mt-0">
            <a href="{{ route('campaigns.edit', $campaign) }}" class="rounded-md border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Edytuj</a>
            <form action="{{ route('campaigns.send-now', $campaign) }}" method="POST">
                @csrf
                <button type="submit" class="rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-800">Wyślij ponownie</button>
            </form>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Podsumowanie</h2>
                    <p class="text-sm text-slate-500">Status: <span class="font-semibold text-slate-900">{{ ucfirst($campaign->status) }}</span></p>
                </div>
                <div class="text-sm text-slate-600 text-right">
                    <p>Lista: <span class="font-semibold">{{ $campaign->contactList?->name ?? '—' }}</span></p>
                    <p>Tożsamość: <span class="font-semibold">{{ $campaign->sendingIdentity?->name ?? '—' }}</span></p>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div class="rounded-lg border border-slate-100 bg-slate-50 p-4">
                    <p class="text-sm text-slate-500">Wiadomości</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $stats['total'] }}</p>
                    <p class="text-xs text-slate-500">wysłane: {{ $stats['sent'] }}</p>
                </div>
                <div class="rounded-lg border border-slate-100 bg-slate-50 p-4">
                    <p class="text-sm text-slate-500">Open rate</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $stats['open_rate'] }}%</p>
                    <p class="text-xs text-slate-500">unikalne: {{ $stats['unique_opens'] }}</p>
                </div>
                <div class="rounded-lg border border-slate-100 bg-slate-50 p-4">
                    <p class="text-sm text-slate-500">Click rate</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $stats['click_rate'] }}%</p>
                    <p class="text-xs text-slate-500">unikalne: {{ $stats['unique_clicks'] }}</p>
                </div>
                <div class="rounded-lg border border-slate-100 bg-slate-50 p-4">
                    <p class="text-sm text-slate-500">Kliknięcia łącznie</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $stats['total_clicks'] }}</p>
                </div>
                <div class="rounded-lg border border-slate-100 bg-slate-50 p-4">
                    <p class="text-sm text-slate-500">Otwarcia łącznie</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $stats['total_opens'] }}</p>
                </div>
                <div class="rounded-lg border border-slate-100 bg-slate-50 p-4">
                    <p class="text-sm text-slate-500">Wypisania</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $stats['unsubscribes'] }}</p>
                    <p class="text-xs text-slate-500">Rate: {{ $stats['unsubscribe_rate'] }}%</p>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-slate-700">
                <div class="rounded-lg border border-slate-100 bg-slate-50 p-4">
                    <p class="font-semibold text-slate-900 mb-1">Ustawienia śledzenia</p>
                    <ul class="list-disc list-inside space-y-1">
                        <li>Otwarcia: {{ $campaign->track_opens ? 'włączone' : 'wyłączone' }}</li>
                        <li>Kliknięcia: {{ $campaign->track_clicks ? 'włączone' : 'wyłączone' }}</li>
                        <li>Link wypisu: {{ $campaign->enable_unsubscribe ? 'włączony' : 'wyłączony' }}</li>
                    </ul>
                </div>
                <div class="rounded-lg border border-slate-100 bg-slate-50 p-4">
                    <p class="font-semibold text-slate-900 mb-1">Harmonogram</p>
                    <p>Odstęp: <span class="font-semibold">{{ $campaign->send_interval_seconds }} s</span></p>
                    <p>Zaplanowano: <span class="font-semibold">{{ $campaign->scheduled_at?->format('Y-m-d H:i') ?? '—' }}</span></p>
                    <p class="mt-2 text-sm text-slate-700">
                        Okno wysyłki:
                        @php
                            $schedule = $campaign->sending_window_schedule ?? [];
                        @endphp
                        @if($schedule)
                            <span class="font-semibold">wg harmonogramu tygodniowego</span>
                        @elseif($campaign->sending_window_enabled && $campaign->sending_window_start && $campaign->sending_window_end)
                            <span class="font-semibold">{{ $campaign->sending_window_start }} - {{ $campaign->sending_window_end }}</span>
                        @else
                            <span class="font-semibold">całą dobę</span>
                        @endif
                    </p>
                    <p class="mt-2 text-slate-600 text-sm">Utworzono: {{ $campaign->created_at?->format('Y-m-d H:i') }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900 mb-3">Podgląd treści</h2>
            <div class="rounded-lg border border-slate-100 bg-slate-50 p-4 text-sm text-slate-800 space-y-2">
                {!! $campaign->html_content !!}
            </div>
        </div>
    </div>
</x-app-layout>

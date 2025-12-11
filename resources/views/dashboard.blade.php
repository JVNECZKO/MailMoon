<x-app-layout>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <p class="text-sm text-slate-500">Szybki podgląd</p>
            <h1 class="text-2xl font-semibold text-slate-900">Pulpit MailMoon</h1>
        </div>
        <div class="mt-3 sm:mt-0 flex items-center space-x-3">
            <a href="{{ route('templates.create') }}" class="inline-flex items-center rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Nowy szablon</a>
            <a href="{{ route('campaigns.create') }}" class="inline-flex items-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-800">Nowa kampania</a>
        </div>
    </div>

    @if (!$hasActiveIdentity)
        <div class="mb-6 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            Brak aktywnej tożsamości nadawcy. Dodaj ją w sekcji <a href="{{ route('sending-identities.index') }}" class="font-semibold text-amber-800 underline">Tożsamości</a>, aby móc wysyłać kampanie.
        </div>
    @endif

    <div class="grid gap-4 md:grid-cols-3 mb-8">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-sm text-slate-500">Kampanie</p>
            <div class="mt-2 flex items-end justify-between">
                <div class="text-3xl font-semibold text-slate-900">{{ $campaignCount }}</div>
            </div>
            <p class="mt-2 text-xs text-slate-500">aktywne, szkice i wysłane</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-sm text-slate-500">Kontakty</p>
            <div class="mt-2 text-3xl font-semibold text-slate-900">{{ $contactsCount }}</div>
            <p class="mt-2 text-xs text-slate-500">łącznie we wszystkich listach</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-sm text-slate-500">Szablony</p>
            <div class="mt-2 text-3xl font-semibold text-slate-900">{{ $templatesCount }}</div>
            <p class="mt-2 text-xs text-slate-500">gotowe do użycia w kampaniach</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Ostatnia kampania</h2>
                    <p class="text-sm text-slate-500">Podsumowanie wyników</p>
                </div>
            </div>
            @if ($lastCampaign)
                @php
                    $sent = $lastCampaignStats['sent'] ?? 0;
                    $openRate = $sent ? round(($lastCampaignStats['unique_opens'] / $sent) * 100, 1) : 0;
                    $clickRate = $sent ? round(($lastCampaignStats['unique_clicks'] / $sent) * 100, 1) : 0;
                    $unsubscribeRate = $sent ? round(($lastCampaignStats['unsubscribes'] / $sent) * 100, 1) : 0;
                @endphp
                <div class="rounded-lg border border-slate-100 bg-slate-50 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-500">Nazwa</p>
                            <p class="font-semibold text-slate-900">{{ $lastCampaign->name }}</p>
                        </div>
                        <span class="rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-700">{{ ucfirst($lastCampaign->status) }}</span>
                    </div>
                    <dl class="mt-4 grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <dt class="text-slate-500">Wysłane</dt>
                            <dd class="font-semibold text-slate-900">{{ $sent }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Otwarcia unikalne</dt>
                            <dd class="font-semibold text-slate-900">{{ $lastCampaignStats['unique_opens'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Kliknięcia unikalne</dt>
                            <dd class="font-semibold text-slate-900">{{ $lastCampaignStats['unique_clicks'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Wypisania</dt>
                            <dd class="font-semibold text-slate-900">{{ $lastCampaignStats['unsubscribes'] }}</dd>
                        </div>
                    </dl>
                    <div class="mt-4 grid grid-cols-3 gap-3 text-sm">
                        <div>
                            <p class="text-slate-500">Open rate</p>
                            <p class="font-semibold text-slate-900">{{ $openRate }}%</p>
                        </div>
                        <div>
                            <p class="text-slate-500">Click rate</p>
                            <p class="font-semibold text-slate-900">{{ $clickRate }}%</p>
                        </div>
                        <div>
                            <p class="text-slate-500">Unsubscribe</p>
                            <p class="font-semibold text-slate-900">{{ $unsubscribeRate }}%</p>
                        </div>
                    </div>
                    <div class="mt-4 text-sm text-slate-600 flex items-center justify-between">
                        <span>Lista: {{ $lastCampaign->contactList?->name ?? 'Brak' }}</span>
                        <span>Tożsamość: {{ $lastCampaign->sendingIdentity?->name ?? 'Brak' }}</span>
                    </div>
                </div>
            @else
                <p class="text-slate-600">Brak kampanii do wyświetlenia. Utwórz pierwszą kampanię, aby śledzić wyniki.</p>
            @endif
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Szybkie działania</h2>
                    <p class="text-sm text-slate-500">Najczęstsze kroki w MailMoon</p>
                </div>
            </div>
            <div class="space-y-3">
                <a href="{{ route('sending-identities.index') }}" class="flex items-center justify-between rounded-lg border border-slate-100 bg-slate-50 px-4 py-3 hover:border-blue-200">
                    <div>
                        <p class="font-semibold text-slate-900">Dodaj tożsamość nadawcy</p>
                        <p class="text-sm text-slate-600">Wymagane przed wysyłką kampanii</p>
                    </div>
                    <span class="text-blue-700 text-sm font-semibold">Przejdź</span>
                </a>
                <a href="{{ route('contact-lists.index') }}" class="flex items-center justify-between rounded-lg border border-slate-100 bg-slate-50 px-4 py-3 hover:border-blue-200">
                    <div>
                        <p class="font-semibold text-slate-900">Zarządzaj listami i kontaktami</p>
                        <p class="text-sm text-slate-600">Importuj lub edytuj kontakty</p>
                    </div>
                    <span class="text-blue-700 text-sm font-semibold">Przejdź</span>
                </a>
                <a href="{{ route('campaigns.index') }}" class="flex items-center justify-between rounded-lg border border-slate-100 bg-slate-50 px-4 py-3 hover:border-blue-200">
                    <div>
                        <p class="font-semibold text-slate-900">Zaplanowane wysyłki</p>
                        <p class="text-sm text-slate-600">Podgląd statusów i wyników</p>
                    </div>
                    <span class="text-blue-700 text-sm font-semibold">Przejdź</span>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>

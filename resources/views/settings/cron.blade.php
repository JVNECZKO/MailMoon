<x-app-layout>
    <div class="mb-6">
        <p class="text-sm text-slate-500">Automatyzacja</p>
        <h1 class="text-2xl font-semibold text-slate-900">Cron kampanii</h1>
        <p class="text-sm text-slate-600 mt-1">Użyj poniższego linku w cronie, aby uruchamiać zaplanowane kampanie.</p>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
        <div>
            <p class="text-sm font-semibold text-slate-900">Adres wywołania</p>
            <div class="mt-2 rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm break-all">{{ $cronUrl }}</div>
        </div>
        <div>
            <p class="text-sm font-semibold text-slate-900">Polecenie cURL</p>
            <div class="mt-2 rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm break-all">{{ $curlSnippet }}</div>
        </div>
        <div>
            <p class="text-sm font-semibold text-slate-900">Linia do crontaba</p>
            <div class="mt-2 rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm break-all">{{ $cronLine }}</div>
            <p class="text-xs text-slate-500 mt-2">Uruchamiaj co minutę, aby wysyłać zaplanowane kampanie.</p>
        </div>
        <div class="pt-2">
            <form action="{{ route('settings.cron.regenerate') }}" method="POST" class="inline-flex items-center gap-2">
                @csrf
                <button type="submit" class="rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-800">Wygeneruj nowy link</button>
                <span class="text-xs text-slate-500">Aktualny pozostaje ważny do jego wygaśnięcia lub ręcznego resetu.</span>
            </form>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <p class="text-sm text-slate-500">Warming dla tożsamości</p>
            <h1 class="text-2xl font-semibold text-slate-900">{{ $sendingIdentity->name }}</h1>
            <p class="text-sm text-slate-600">E-mail: {{ $sendingIdentity->from_email }}</p>
        </div>
        <div class="flex flex-wrap gap-2 mt-3 md:mt-0">
            <form action="{{ route('warming.pause', $sendingIdentity) }}" method="POST">
                @csrf
                <button type="submit" class="rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Pauzuj</button>
            </form>
            <form action="{{ route('warming.resume', $sendingIdentity) }}" method="POST">
                @csrf
                <button type="submit" class="rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Wznów</button>
            </form>
            <form action="{{ route('warming.finish', $sendingIdentity) }}" method="POST">
                @csrf
                <button type="submit" class="rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-100">Zakończ</button>
            </form>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-sm text-slate-500">Status</p>
            <p class="text-2xl font-semibold text-slate-900">{{ $warming?->status ?? 'inactive' }}</p>
            @if($warming)
                <p class="text-xs text-slate-500 mt-1">Plan: {{ ucfirst($warming->plan) }}</p>
            @endif
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-sm text-slate-500">Dzień</p>
            <p class="text-2xl font-semibold text-slate-900">{{ $progress['current'] }} / {{ $progress['total'] }}</p>
            <p class="text-xs text-slate-500 mt-1">Postęp: {{ $progress['percent'] }}%</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-sm text-slate-500">Wysłane</p>
            <p class="text-2xl font-semibold text-slate-900">{{ $warming?->sent_today ?? 0 }} / {{ $warming?->daily_target ?? 0 }} dzisiaj</p>
            <p class="text-xs text-slate-500 mt-1">Łącznie: {{ $warming?->total_sent ?? 0 }}</p>
        </div>
    </div>

    <div class="mt-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-900 mb-3">Kreator warmingu</h2>
        <form action="{{ route('warming.start', $sendingIdentity) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700">Lista warmupowa</label>
                <select name="contact_list_id" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500" required>
                    <option value="">Wybierz listę</option>
                    @foreach($contactLists as $list)
                        <option value="{{ $list->id }}" @selected($warming?->contact_list_id === $list->id)>{{ $list->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Plan warmingu</label>
                <select name="plan" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500">
                    <option value="slow" @selected($warming?->plan === 'slow')>Slow Warm-up (bezpieczny)</option>
                    <option value="standard" @selected($warming?->plan === 'standard' || !$warming)>Standard (zalecany)</option>
                    <option value="fast" @selected($warming?->plan === 'fast')>Fast Warm-up (dobra reputacja)</option>
                </select>
                <p class="text-xs text-slate-500 mt-1">Wybierz tempo zwiększania liczby maili dziennie.</p>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Temat</label>
                    <input type="text" name="subject" value="{{ old('subject', $warming->subject ?? 'Test MailMoon') }}" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Interwał (sekundy)</label>
                    <input type="number" name="send_interval_seconds" value="{{ old('send_interval_seconds', $warming->send_interval_seconds ?? 30) }}" min="5" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500" required>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Treść (prosty tekst)</label>
                <textarea name="body" rows="4" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500" required>{{ old('body', $warming->body ?? "Cześć,\nTo testowy mail w ramach warmingu skrzynki. Dzięki!") }}</textarea>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-800">Start Warmingu</button>
            </div>
        </form>
    </div>

    <div class="mt-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-900 mb-3">Opis działania</h2>
        <p class="text-sm text-slate-700 leading-relaxed">
            Warming to stopniowe budowanie reputacji nadawcy. MailMoon wysyła codziennie niewielką liczbę maili na wybraną listę i zwiększa wolumen zgodnie z planem.
            Najlepiej użyć własnych, zaangażowanych adresów (np. Twoje skrzynki testowe, znajomi). Treść powinna być naturalna i prosta.
        </p>
    </div>
</x-app-layout>

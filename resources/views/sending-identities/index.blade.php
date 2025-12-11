<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-sm text-slate-500">Połączenia SMTP</p>
            <h1 class="text-2xl font-semibold text-slate-900">Tożsamości nadawcy</h1>
        </div>
        <a href="{{ route('sending-identities.create') }}" class="inline-flex items-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-800">Dodaj tożsamość</a>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3">Nazwa</th>
                    <th class="px-4 py-3">Adres</th>
                    <th class="px-4 py-3">Host</th>
                    <th class="px-4 py-3">Test</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Akcje</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-sm">
                @forelse ($sendingIdentities as $identity)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-semibold text-slate-900">{{ $identity->name }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $identity->from_email }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $identity->smtp_host }}:{{ $identity->smtp_port }}</td>
                        <td class="px-4 py-3">
                            <form action="{{ route('sending-identities.test', $identity) }}" method="POST" class="flex items-center space-x-2">
                                @csrf
                                <input type="hidden" name="recipient" value="{{ auth()->user()->email }}">
                                <button type="submit" class="text-blue-700 hover:underline">Test SMTP/IMAP</button>
                            </form>
                        </td>
                        <td class="px-4 py-3">
                            @if($identity->is_active)
                                <span class="inline-flex rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-700">Aktywna</span>
                            @else
                                <span class="inline-flex rounded-full bg-slate-200 px-2 py-1 text-xs font-semibold text-slate-700">Wyłączona</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <a href="{{ route('sending-identities.edit', $identity) }}" class="text-blue-700 hover:underline">Edytuj</a>
                            <a href="{{ route('warming.show', $identity) }}" class="text-slate-700 hover:underline">Warming</a>
                            <form action="{{ route('sending-identities.destroy', $identity) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Usunąć tę tożsamość?')">Usuń</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-slate-600">Brak zapisanych tożsamości. Dodaj pierwszą, aby wysyłać e-maile.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>

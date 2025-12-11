<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-sm text-slate-500">Mailingi</p>
            <h1 class="text-2xl font-semibold text-slate-900">Kampanie</h1>
        </div>
        <a href="{{ route('campaigns.create') }}" class="inline-flex items-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-800">Nowa kampania</a>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3">Nazwa</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Wysłane</th>
                    <th class="px-4 py-3">Otwarcia</th>
                    <th class="px-4 py-3">Kliknięcia</th>
                    <th class="px-4 py-3">Utworzono</th>
                    <th class="px-4 py-3 text-right">Akcje</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-sm">
                @forelse ($campaigns as $campaign)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-semibold text-slate-900">{{ $campaign->name }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ ucfirst($campaign->status) }}</span>
                        </td>
                        <td class="px-4 py-3 text-slate-700">{{ $campaign->sent_messages_count }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $campaign->unique_opens_count }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $campaign->unique_clicks_count }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $campaign->created_at?->format('Y-m-d') }}</td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <a href="{{ route('campaigns.show', $campaign) }}" class="text-blue-700 hover:underline">Podgląd</a>
                            <a href="{{ route('campaigns.edit', $campaign) }}" class="text-slate-700 hover:underline">Edytuj</a>
                            <form action="{{ route('campaigns.send-now', $campaign) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-green-700 hover:underline">Wyślij teraz</button>
                            </form>
                            <form action="{{ route('campaigns.destroy', $campaign) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Usunąć kampanię?')" class="text-red-600 hover:underline">Usuń</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-slate-600">Brak kampanii.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3">
            {{ $campaigns->links() }}
        </div>
    </div>
</x-app-layout>

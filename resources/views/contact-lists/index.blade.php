<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-sm text-slate-500">Bazy odbiorców</p>
            <h1 class="text-2xl font-semibold text-slate-900">Listy kontaktów</h1>
        </div>
        <a href="{{ route('contact-lists.create') }}" class="inline-flex items-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-800">Nowa lista</a>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3">Nazwa</th>
                    <th class="px-4 py-3">Kontakty</th>
                    <th class="px-4 py-3">Opis</th>
                    <th class="px-4 py-3 text-right">Akcje</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-sm">
                @forelse ($contactLists as $list)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-semibold text-slate-900">{{ $list->name }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $list->contacts_count }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $list->description ?? '—' }}</td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <a href="{{ route('contact-lists.show', $list) }}" class="text-blue-700 hover:underline">Podgląd</a>
                            <a href="{{ route('contact-lists.edit', $list) }}" class="text-slate-700 hover:underline">Edytuj</a>
                            <form action="{{ route('contact-lists.destroy', $list) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Usunąć listę?')" class="text-red-600 hover:underline">Usuń</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-slate-600">Brak list kontaktów. Dodaj pierwszą, aby importować odbiorców.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>

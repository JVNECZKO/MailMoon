<x-app-layout>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <p class="text-sm text-slate-500">Lista kontaktów</p>
            <h1 class="text-2xl font-semibold text-slate-900">{{ $contactList->name }}</h1>
            <p class="text-sm text-slate-500">{{ $contactList->description }}</p>
        </div>
        <div class="flex items-center space-x-3 mt-3 sm:mt-0">
            <a href="{{ route('contact-lists.index') }}" class="text-sm text-blue-700 hover:underline">Wszystkie listy</a>
            <a href="{{ route('contact-lists.contacts.create', $contactList) }}" class="inline-flex items-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-800">Dodaj kontakt</a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                <h2 class="text-sm font-semibold text-slate-900">Kontakty ({{ $contacts->total() }})</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left font-semibold text-slate-600">
                        <tr>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Imię</th>
                            <th class="px-4 py-3">Nazwisko</th>
                            <th class="px-4 py-3 text-right">Akcje</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($contacts as $contact)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-semibold text-slate-900">{{ $contact->email }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $contact->first_name ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $contact->last_name ?? '—' }}</td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    <a href="{{ route('contact-lists.contacts.edit', [$contactList, $contact]) }}" class="text-blue-700 hover:underline">Edytuj</a>
                                    <form action="{{ route('contact-lists.contacts.destroy', [$contactList, $contact]) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Usunąć kontakt?')" class="text-red-600 hover:underline">Usuń</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-slate-600">Brak kontaktów na tej liście.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3">
                {{ $contacts->links() }}
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-semibold text-slate-900 mb-3">Import kontaktów</h2>
            <p class="text-sm text-slate-600 mb-4">Obsługiwane formaty: CSV, XLSX, XLS, TXT lub wklejenie listy (email, imię, nazwisko).</p>
            <form action="{{ route('contact-lists.import', $contactList) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700">Plik z kontaktami</label>
                    <input type="file" name="file" class="mt-1 block w-full text-sm text-slate-700">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Wklej listę</label>
                    <textarea name="manual_input" rows="4" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500" placeholder="email@example.com,Jan,Kowalski&#10;drugi@example.com"></textarea>
                </div>
                <button type="submit" class="w-full rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-800">Importuj</button>
            </form>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-sm text-slate-500">Baza</p>
            <h1 class="text-2xl font-semibold text-slate-900">Edycja listy</h1>
        </div>
        <a href="{{ route('contact-lists.index') }}" class="text-sm text-blue-700 hover:underline">Powrót</a>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <form action="{{ route('contact-lists.update', $contactList) }}" method="POST" class="space-y-6">
            @method('PUT')
            @include('contact-lists.form', ['contactList' => $contactList])

            <div class="flex justify-end space-x-3">
                <a href="{{ route('contact-lists.index') }}" class="rounded-md border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Anuluj</a>
                <button type="submit" class="rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-800">Zapisz zmiany</button>
            </div>
        </form>
    </div>
</x-app-layout>

<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-sm text-slate-500">Wzorce wiadomości</p>
            <h1 class="text-2xl font-semibold text-slate-900">Szablony</h1>
        </div>
        <a href="{{ route('templates.create') }}" class="inline-flex items-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-800">Nowy szablon</a>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3">Nazwa</th>
                    <th class="px-4 py-3">Temat</th>
                    <th class="px-4 py-3">Utworzono</th>
                    <th class="px-4 py-3 text-right">Akcje</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-sm">
                @forelse ($templates as $template)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-semibold text-slate-900">{{ $template->name }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $template->subject }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $template->created_at?->format('Y-m-d') }}</td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <a href="{{ route('templates.edit', $template) }}" class="text-blue-700 hover:underline">Edytuj</a>
                            <form action="{{ route('templates.destroy', $template) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Usunąć szablon?')" class="text-red-600 hover:underline">Usuń</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-slate-600">Brak szablonów.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>

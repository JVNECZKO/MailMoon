<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-sm text-slate-500">SMTP</p>
            <h1 class="text-2xl font-semibold text-slate-900">Nowa tożsamość</h1>
        </div>
        <a href="{{ route('sending-identities.index') }}" class="text-sm text-blue-700 hover:underline">Powrót</a>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <form action="{{ route('sending-identities.store') }}" method="POST" class="space-y-6">
            @include('sending-identities.form', ['sendingIdentity' => new \App\Models\SendingIdentity()])

            <div class="flex justify-end space-x-3">
                <a href="{{ route('sending-identities.index') }}" class="rounded-md border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Anuluj</a>
                <button type="submit" class="rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-800">Zapisz</button>
            </div>
        </form>
    </div>
</x-app-layout>

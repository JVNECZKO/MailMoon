<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-sm text-slate-500">SMTP</p>
            <h1 class="text-2xl font-semibold text-slate-900">Edycja tożsamości</h1>
        </div>
        <div class="flex items-center space-x-3">
            <form action="{{ route('sending-identities.test', $sendingIdentity) }}" method="POST" class="flex items-center space-x-2">
                @csrf
                <input type="email" name="recipient" value="{{ auth()->user()->email }}" class="rounded-md border border-slate-300 px-2 py-1 text-sm focus:border-blue-600 focus:ring-blue-500" title="Adres do testu SMTP">
                <button type="submit" class="rounded-md border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-800 hover:bg-blue-100">Test SMTP/IMAP</button>
            </form>
            <a href="{{ route('sending-identities.index') }}" class="text-sm text-blue-700 hover:underline">Powrót</a>
        </div>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <form action="{{ route('sending-identities.update', $sendingIdentity) }}" method="POST" class="space-y-6">
            @method('PUT')
            @include('sending-identities.form', ['sendingIdentity' => $sendingIdentity])

            <div class="flex justify-end space-x-3">
                <a href="{{ route('sending-identities.index') }}" class="rounded-md border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Anuluj</a>
                <button type="submit" class="rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-800">Zapisz zmiany</button>
            </div>
        </form>
    </div>
</x-app-layout>

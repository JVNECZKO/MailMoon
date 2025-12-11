@csrf
<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-slate-700">Email</label>
        <input type="email" name="email" value="{{ old('email', $contact->email ?? '') }}" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500" required>
    </div>
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-slate-700">Imię</label>
            <input type="text" name="first_name" value="{{ old('first_name', $contact->first_name ?? '') }}" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Nazwisko</label>
            <input type="text" name="last_name" value="{{ old('last_name', $contact->last_name ?? '') }}" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500">
        </div>
    </div>
</div>

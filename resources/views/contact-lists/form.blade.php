@csrf
<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-slate-700">Nazwa listy</label>
        <input type="text" name="name" value="{{ old('name', $contactList->name ?? '') }}" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500" required>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Opis (opcjonalnie)</label>
        <textarea name="description" rows="3" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500">{{ old('description', $contactList->description ?? '') }}</textarea>
    </div>
</div>

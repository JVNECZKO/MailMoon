@csrf
<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="block text-sm font-medium text-slate-700">Nazwa</label>
        <input type="text" name="name" value="{{ old('name', $sendingIdentity->name ?? '') }}" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500" required>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Adres nadawcy</label>
        <input type="email" name="from_email" value="{{ old('from_email', $sendingIdentity->from_email ?? '') }}" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500" required>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">SMTP host</label>
        <input type="text" name="smtp_host" value="{{ old('smtp_host', $sendingIdentity->smtp_host ?? '') }}" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500" required>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">SMTP port</label>
        <input type="number" name="smtp_port" value="{{ old('smtp_port', $sendingIdentity->smtp_port ?? 587) }}" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500" required>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">SMTP użytkownik</label>
        <input type="text" name="smtp_username" value="{{ old('smtp_username', $sendingIdentity->smtp_username ?? '') }}" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500" required>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">SMTP hasło @if(isset($sendingIdentity))<span class="text-slate-500">(pozostaw puste aby nie zmieniać)</span>@endif</label>
        <input type="password" name="smtp_password" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500" @if(!isset($sendingIdentity)) required @endif>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Szyfrowanie</label>
        <select name="smtp_encryption" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500">
            <option value="" @selected(old('smtp_encryption', $sendingIdentity->smtp_encryption ?? '') === '')>Brak</option>
            <option value="tls" @selected(old('smtp_encryption', $sendingIdentity->smtp_encryption ?? '') === 'tls')>TLS</option>
            <option value="ssl" @selected(old('smtp_encryption', $sendingIdentity->smtp_encryption ?? '') === 'ssl')>SSL</option>
        </select>
    </div>
    <div class="flex items-center space-x-2 pt-6">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-blue-700 focus:ring-blue-500" @checked(old('is_active', $sendingIdentity->is_active ?? true))>
        <span class="text-sm text-slate-700">Aktywna</span>
    </div>
</div>

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
        <label class="block text-sm font-medium text-slate-700">Tryb wysyłki</label>
        <select name="send_mode" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500">
            <option value="smtp" @selected(old('send_mode', $sendingIdentity->send_mode ?? 'smtp') === 'smtp')>SMTP (bez zapisu w Sent)</option>
            <option value="imap" @selected(old('send_mode', $sendingIdentity->send_mode ?? 'smtp') === 'imap')>SMTP + IMAP (zapis do Sent)</option>
        </select>
    </div>

    <div class="md:col-span-2">
        <div class="mt-4 rounded-lg border border-slate-200 p-4 bg-slate-50">
            <p class="text-sm font-semibold text-slate-900 mb-3">SMTP</p>
            <div class="grid gap-4 md:grid-cols-2">
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
            </div>
        </div>
    </div>

    <div class="md:col-span-2">
        <div class="mt-4 rounded-lg border border-slate-200 p-4 bg-slate-50">
            <p class="text-sm font-semibold text-slate-900 mb-3">IMAP (do zapisu w Sent – wymagane gdy tryb SMTP + IMAP)</p>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700">IMAP host</label>
                    <input type="text" name="imap_host" value="{{ old('imap_host', $sendingIdentity->imap_host ?? '') }}" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">IMAP port</label>
                    <input type="number" name="imap_port" value="{{ old('imap_port', $sendingIdentity->imap_port ?? 993) }}" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">IMAP użytkownik</label>
                    <input type="text" name="imap_username" value="{{ old('imap_username', $sendingIdentity->imap_username ?? '') }}" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">IMAP hasło @if(isset($sendingIdentity))<span class="text-slate-500">(pozostaw puste aby nie zmieniać)</span>@endif</label>
                    <input type="password" name="imap_password" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Szyfrowanie IMAP</label>
                    <select name="imap_encryption" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500">
                        <option value="" @selected(old('imap_encryption', $sendingIdentity->imap_encryption ?? '') === '')>Brak</option>
                        <option value="tls" @selected(old('imap_encryption', $sendingIdentity->imap_encryption ?? '') === 'tls')>TLS</option>
                        <option value="ssl" @selected(old('imap_encryption', $sendingIdentity->imap_encryption ?? '') === 'ssl')>SSL</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="flex items-center space-x-2 pt-6">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-blue-700 focus:ring-blue-500" @checked(old('is_active', $sendingIdentity->is_active ?? true))>
        <span class="text-sm text-slate-700">Aktywna</span>
    </div>
</div>

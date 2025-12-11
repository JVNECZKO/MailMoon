@csrf
<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-slate-700">Nazwa szablonu</label>
        <input type="text" name="name" value="{{ old('name', $template->name ?? '') }}" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500" required>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Temat</label>
        <input type="text" name="subject" value="{{ old('subject', $template->subject ?? '') }}" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-500" required>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Treść HTML</label>
        <textarea name="html_content" class="tinymce-editor mt-1 w-full rounded-md border-slate-300 shadow-sm" rows="12">{{ old('html_content', $template->html_content ?? '') }}</textarea>
    </div>
</div>

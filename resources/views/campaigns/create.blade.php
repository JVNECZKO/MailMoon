<x-app-layout>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <p class="text-sm text-slate-500">Kampanie</p>
            <h1 class="text-2xl font-semibold text-slate-900">Nowa kampania</h1>
        </div>
        <a href="{{ route('campaigns.index') }}" class="text-sm text-blue-700 hover:underline">Powrót do listy</a>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-500">Wybierz szablon, aby wstępnie uzupełnić temat i treść.</p>
            </div>
            <button type="button" id="load-template" class="rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Wczytaj wybrany szablon
            </button>
        </div>
        <form action="{{ route('campaigns.store') }}" method="POST" class="space-y-6">
            @include('campaigns.form')

            <div class="flex flex-wrap justify-end gap-3">
                <a href="{{ route('campaigns.index') }}" class="rounded-md border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Anuluj</a>
                <button type="submit" data-action="draft" class="campaign-action rounded-md border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Zapisz jako szkic</button>
                <button type="submit" data-action="schedule" class="campaign-action rounded-md border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-800 hover:bg-blue-100">Zaplanuj</button>
                <button type="submit" data-action="send_now" class="campaign-action rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-800">Wyślij teraz</button>
            </div>
        </form>
    </div>

    @include('partials.tinymce')

    @push('scripts')
        <script>
            document.querySelectorAll('.campaign-action').forEach((btn) => {
                btn.addEventListener('click', () => {
                    document.getElementById('campaign-action').value = btn.dataset.action;
                });
            });

            const loadTemplateBtn = document.getElementById('load-template');
            if (loadTemplateBtn) {
                loadTemplateBtn.addEventListener('click', () => {
                    const templateId = document.querySelector('select[name="template_id"]').value;
                    if (templateId) {
                        window.location = "{{ route('campaigns.create') }}?template_id=" + templateId;
                    }
                });
            }

            const allDayBtn = document.getElementById('campaign-window-all-day');
            const disableBtn = document.getElementById('campaign-window-disable');
            if (allDayBtn) {
                allDayBtn.addEventListener('click', () => {
                    document.querySelectorAll('input[name^="sending_window_schedule"]').forEach((input) => {
                        if (input.type === 'checkbox') {
                            input.checked = true;
                        }
                        if (input.name.endsWith('[start]')) {
                            input.value = '00:00';
                        }
                        if (input.name.endsWith('[end]')) {
                            input.value = '23:59';
                        }
                    });
                });
            }
            if (disableBtn) {
                disableBtn.addEventListener('click', () => {
                    document.querySelectorAll('input[name^="sending_window_schedule"]').forEach((input) => {
                        if (input.type === 'checkbox') {
                            input.checked = false;
                        } else {
                            input.value = '';
                        }
                    });
                });
            }
        </script>
    @endpush
</x-app-layout>

@once
    @push('scripts')
        <script src="https://cdn.tiny.cloud/1/4tym1ky7pr5smrb2uqs9s5j01frqzz2qwyfe2fn9ijjtilbo/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (typeof tinymce === 'undefined') {
                    return;
                }

                window.initTinyEditors = function (root = document, options = {}) {
                    const force = !!options.force;
                    root.querySelectorAll('textarea.tinymce-editor').forEach((el) => {
                        if (el.dataset.tinyInit === '1') {
                            return;
                        }
                        if (!force && el.dataset.deferTiny === '1') {
                            return;
                        }
                        const configuredHeight = parseInt(el.dataset.tinyHeight || '420', 10);
                        tinymce.init({
                            target: el,
                            height: Number.isNaN(configuredHeight) ? 420 : configuredHeight,
                            menubar: false,
                            plugins: 'link lists table code autoresize',
                            toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright | bullist numlist | link table | code',
                            branding: false,
                            setup: () => {
                                el.dataset.tinyInit = '1';
                            }
                        });
                    });
                };

                initTinyEditors();

                document.querySelectorAll('form').forEach((form) => {
                    form.addEventListener('submit', () => {
                        if (typeof tinymce !== 'undefined') {
                            tinymce.triggerSave();
                        }
                    });
                });
            });
        </script>
    @endpush
@endonce

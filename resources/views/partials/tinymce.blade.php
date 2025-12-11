@once
    @push('scripts')
        <script src="https://cdn.tiny.cloud/1/4tym1ky7pr5smrb2uqs9s5j01frqzz2qwyfe2fn9ijjtilbo/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (typeof tinymce === 'undefined') {
                    return;
                }

                tinymce.init({
                    selector: '.tinymce-editor',
                    height: 420,
                    menubar: false,
                    plugins: 'link lists table code autoresize',
                    toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright | bullist numlist | link table | code',
                    branding: false,
                });

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

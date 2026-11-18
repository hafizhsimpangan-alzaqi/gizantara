document.addEventListener('DOMContentLoaded', function () {
    const editorElement = document.getElementById('article-editor');
    const form = document.getElementById('article-form');
    const htmlInput = document.getElementById('article-html');
    const editorError = document.getElementById('article-editor-error');
    if (!editorElement || !form || !htmlInput) return;

    function aturErrorEditor(pesan) {
        if (pesan) {
            editorElement.setAttribute('aria-invalid', 'true');
            if (editorError) {
                editorError.textContent = pesan;
                editorError.hidden = false;
            }
        } else {
            editorElement.removeAttribute('aria-invalid');
            if (editorError) {
                editorError.textContent = '';
                editorError.hidden = true;
            }
        }
    }

    if (typeof Quill === 'undefined') {
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            const status = form.querySelector('.form-status');
            const pesan = 'Editor belum siap. Muat ulang halaman lalu coba lagi.';
            aturErrorEditor(pesan);
            if (status) {
                status.classList.add('is-error');
                status.textContent = pesan;
            }
        });
        return;
    }

    const quill = new Quill(editorElement, {
        theme: 'snow',
        formats: ['header', 'bold', 'italic', 'underline', 'list', 'link'],
        modules: {
            toolbar: [
                [{ header: [2, 3, false] }],
                ['bold', 'italic', 'underline'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['link']
            ]
        }
    });
    quill.root.setAttribute('role', 'textbox');
    quill.root.setAttribute('aria-labelledby', 'article-editor-label');
    quill.root.setAttribute('aria-describedby', 'article-editor-error');
    quill.root.setAttribute('aria-multiline', 'true');
    const headingPicker = form.querySelector('.ql-picker-label');
    if (headingPicker) headingPicker.setAttribute('aria-label', 'Gaya paragraf');
    const initialData = document.getElementById('article-initial-html');
    if (initialData) {
        const htmlAwal = JSON.parse(initialData.textContent);
        quill.setContents(quill.clipboard.convert({ html: htmlAwal }));
    }

    quill.on('text-change', function () {
        if (quill.getText().trim()) {
            aturErrorEditor('');
        }
    });

    form.addEventListener('submit', function (event) {
        const status = form.querySelector('.form-status');
        const text = quill.getText().trim();
        if (!text) {
            event.preventDefault();
            const pesan = 'Isi artikel perlu diisi sebelum disimpan.';
            aturErrorEditor(pesan);
            if (status) {
                status.classList.add('is-error');
                status.textContent = pesan;
            }
            quill.focus();
            return;
        }
        aturErrorEditor('');
        if (status) {
            status.classList.remove('is-error');
            status.textContent = '';
        }
        htmlInput.value = quill.getSemanticHTML();
    });
});

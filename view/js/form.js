document.addEventListener('DOMContentLoaded', function () {
    function aturErrorField(input, pesan) {
        if (!input) return;
        const field = input.closest('.form-field');
        const errorEl = field ? field.querySelector('.field-error') : null;
        if (pesan) {
            input.setAttribute('aria-invalid', 'true');
            if (errorEl) {
                errorEl.textContent = pesan;
                errorEl.hidden = false;
            }
        } else {
            input.removeAttribute('aria-invalid');
            if (errorEl) {
                errorEl.textContent = '';
                errorEl.hidden = true;
            }
        }
    }

    let confirmTrigger = null;
    document.querySelectorAll('[data-open-confirm]').forEach(function (button) {
        button.addEventListener('click', function () {
            const dialog = document.getElementById(button.dataset.openConfirm);
            if (dialog && typeof dialog.showModal === 'function') {
                const targetId = dialog.querySelector('[data-delete-id]');
                const targetName = dialog.querySelector('[data-delete-name]');
                if (targetId && targetName) {
                    targetId.value = button.dataset.deleteId || button.dataset.userId || '';
                    targetName.textContent = button.dataset.deleteName || button.dataset.userName || '';
                }
                confirmTrigger = button;
                dialog.showModal();
            }
        });
    });
    document.querySelectorAll('[data-close-confirm]').forEach(function (button) {
        button.addEventListener('click', function () {
            const dialog = button.closest('dialog');
            if (dialog) dialog.close();
        });
    });
    document.querySelectorAll('dialog.confirm-dialog').forEach(function (dialog) {
        dialog.addEventListener('close', function () {
            if (confirmTrigger) confirmTrigger.focus();
        });
    });

    document.querySelectorAll('form.form-card').forEach(function (form) {
        form.addEventListener('invalid', function (event) {
            const target = event.target;
            if (target && target.validationMessage) {
                aturErrorField(target, target.validationMessage);
            }
        }, true);

        form.addEventListener('input', function (event) {
            const target = event.target;
            if (!target) return;
            target.setCustomValidity('');
            if (target.checkValidity()) {
                aturErrorField(target, '');
            }
        });

        form.addEventListener('change', function (event) {
            const target = event.target;
            if (!target || target.type === 'file') return;
            target.setCustomValidity('');
            if (target.checkValidity()) {
                aturErrorField(target, '');
            }
        });
    });

    [document.getElementById('form-user'), document.getElementById('register-form')].forEach(function (userForm) {
        if (!userForm) return;
        const password = userForm.querySelector('[name="password"]');
        const confirmation = userForm.querySelector('[name="confirm_password"]');
        if (password && confirmation) {
            userForm.addEventListener('submit', function (event) {
                if (password.value !== confirmation.value) {
                    event.preventDefault();
                    const pesanCocok = 'Konfirmasi password belum sama.';
                    confirmation.setCustomValidity(pesanCocok);
                    aturErrorField(confirmation, pesanCocok);
                    confirmation.reportValidity();
                    confirmation.focus();
                }
            });
            password.addEventListener('input', function () {
                confirmation.setCustomValidity('');
                if (password.value === confirmation.value) {
                    aturErrorField(confirmation, '');
                }
            });
            confirmation.addEventListener('input', function () {
                confirmation.setCustomValidity('');
                if (password.value === confirmation.value && confirmation.checkValidity()) {
                    aturErrorField(confirmation, '');
                }
            });
        }
    });

    document.querySelectorAll('[data-image-preview]').forEach(function (input) {
        input.addEventListener('change', function () {
            const file = input.files[0];
            const fieldWrap = input.closest('.form-field') || input.parentElement;
            const wrap = fieldWrap ? fieldWrap.querySelector('[data-preview-wrap]') : null;
            if (!file) {
                aturErrorField(input, '');
                return;
            }
            const form = input.closest('form');
            const status = form ? form.querySelector('.form-status') : null;
            if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > 2 * 1024 * 1024) {
                const pesanFile = 'Pilih gambar JPEG, PNG, atau WebP dengan ukuran maksimal 2 MB.';
                input.value = '';
                if (wrap) wrap.hidden = true;
                aturErrorField(input, pesanFile);
                if (status) {
                    status.classList.add('is-error');
                    status.textContent = pesanFile;
                }
                return;
            }
            aturErrorField(input, '');
            if (wrap) {
                const image = wrap.querySelector('[data-preview-image]');
                const caption = wrap.querySelector('[data-preview-caption]');
                image.src = URL.createObjectURL(file);
                image.onload = function () { URL.revokeObjectURL(image.src); };
                caption.textContent = file.name + ' · Pratinjau';
                wrap.hidden = false;
            }
            if (status) {
                status.classList.remove('is-error');
                status.textContent = 'Gambar baru akan diunggah saat formulir disimpan.';
            }
        });
    });
});

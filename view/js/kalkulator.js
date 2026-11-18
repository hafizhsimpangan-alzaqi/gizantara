document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('calculator-form');
    if (!form) return;
    const food = document.getElementById('calculator-food');
    const weight = document.getElementById('calculator-weight');
    const foodError = document.getElementById('calculator-food-error');
    const weightError = document.getElementById('calculator-weight-error');
    const status = document.getElementById('calculator-status');
    const title = document.getElementById('calculator-result-title');
    const sourceEl = document.getElementById('calculator-source');
    const fields = ['energy', 'protein', 'fat', 'carbohydrate'];

    function aturError(input, errorEl, pesan) {
        if (!input) return;
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

    function resetResult() {
        fields.forEach(function (field) {
            const el = document.querySelector('[data-result="' + field + '"]');
            if (el) el.textContent = '—';
        });
        title.textContent = 'Pilih bahan dan berat';
        if (sourceEl) sourceEl.textContent = 'Sumber data akan ditampilkan setelah perhitungan.';
        status.classList.remove('is-error');
        status.textContent = '';
    }

    food.addEventListener('change', function () {
        aturError(food, foodError, '');
        resetResult();
    });
    weight.addEventListener('input', function () {
        aturError(weight, weightError, '');
        resetResult();
    });
    form.addEventListener('reset', function () {
        aturError(food, foodError, '');
        aturError(weight, weightError, '');
        resetResult();
    });

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        aturError(food, foodError, '');
        aturError(weight, weightError, '');

        if (!food.value) {
            const pesanBahan = 'Pilih satu bahan pangan dari katalog terlebih dahulu.';
            aturError(food, foodError, pesanBahan);
            status.classList.add('is-error');
            status.textContent = pesanBahan;
            food.focus();
            return;
        }

        const grams = Number(weight.value);
        if (!weight.value.trim() || !Number.isFinite(grams) || grams <= 0 || grams > 10000) {
            const pesanBerat = 'Masukkan berat lebih dari 0 sampai 10.000 gram.';
            aturError(weight, weightError, pesanBerat);
            status.classList.add('is-error');
            status.textContent = pesanBerat;
            weight.focus();
            return;
        }

        const option = food.selectedOptions[0];
        if (!option || fields.some(function (field) { return !Number.isFinite(Number(option.dataset[field])) || option.dataset[field] === undefined; })) {
            status.classList.add('is-error');
            status.textContent = 'Data gizi bahan ini belum lengkap.';
            return;
        }

        title.textContent = option.textContent + ' · ' + grams.toLocaleString('id-ID') + ' g';
        fields.forEach(function (field) {
            const valuePer100 = Number(option.dataset[field]);
            const target = document.querySelector('[data-result="' + field + '"]');
            if (target) {
                target.textContent = ((grams / 100) * valuePer100).toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }
        });
        if (sourceEl) sourceEl.textContent = 'Sumber: ' + option.dataset.source;
        status.classList.remove('is-error');
        status.textContent = 'Estimasi dihitung dari nilai gizi katalog per 100 gram.';
    });
});

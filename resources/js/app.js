
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import deLocale from '@fullcalendar/core/locales/de';

document.addEventListener('DOMContentLoaded', () => {
    const calendarEl = document.getElementById('events-calendar');
    const calendarFeedUrl = calendarEl?.dataset.feedUrl || '/events/feed';

    if (!calendarEl) {
        return;
    }

    const form = document.getElementById('event-form');
    const formMethodInput = document.getElementById('event-form-method');
    const eventIdInput = document.getElementById('event_id');
    const startsAtInput = document.getElementById('starts_at');
    const endsAtInput = document.getElementById('ends_at');
    const titleInput = document.getElementById('title');
    const typeInput = document.getElementById('type');
    const locationInput = document.getElementById('location');
    const descriptionInput = document.getElementById('description');
    const allDayInput = document.querySelector('input[name="all_day"]');
    const formCard = document.getElementById('event-form-card');
    const submitButton = document.getElementById('event-submit-button');
    const resetButton = document.getElementById('event-form-reset');
    const editHint = document.getElementById('event-edit-hint');
    const foodModalOpenOff = document.getElementById('foodModalOpenOff');
    const eventEditHint = document.getElementById('event-edit-hint');
    const eventInsuranceBadge = document.getElementById('event-insurance-badge');
    const eventInsuranceLink = document.getElementById('event-insurance-link');
    const eventInsuranceNote = document.getElementById('event-insurance-note');

    function applyInsuranceReminderState(eventData) {
    const extendedProps = eventData?.extendedProps ?? {};
    const insuranceUrl = extendedProps.insurance_url || null;
    const isInsuranceReminder = Boolean(extendedProps.is_insurance_reminder || insuranceUrl);

    if (isInsuranceReminder && insuranceUrl) {
        eventInsuranceBadge?.classList.remove('hidden');
        eventInsuranceLink?.classList.remove('hidden');
        eventInsuranceNote?.classList.remove('hidden');
        eventInsuranceLink.href = insuranceUrl;
    } else {
        eventInsuranceBadge?.classList.add('hidden');
        eventInsuranceLink?.classList.add('hidden');
        eventInsuranceNote?.classList.add('hidden');

        if (eventInsuranceLink) {
            eventInsuranceLink.href = '#';
        }
    }
}

    if (!form) {
        return;
    }

    const storeUrl = form.dataset.storeUrl;
    const updateUrlTemplate = form.dataset.updateUrlTemplate;

    const pad = (value) => String(value).padStart(2, '0');

    const formatForDateTimeLocal = (date) => {
        const year = date.getFullYear();
        const month = pad(date.getMonth() + 1);
        const day = pad(date.getDate());
        const hours = pad(date.getHours());
        const minutes = pad(date.getMinutes());

        return `${year}-${month}-${day}T${hours}:${minutes}`;
    };

    const scrollToForm = () => {
        if (!formCard) return;

        const top = formCard.getBoundingClientRect().top + window.scrollY - 20;

        window.scrollTo({
            top,
            behavior: 'smooth',
        });
    };

    const switchToCreateMode = () => {
        form.action = storeUrl;

        if (formMethodInput) {
            formMethodInput.value = '';
        }

        if (eventIdInput) {
            eventIdInput.value = '';
        }

        if (submitButton) {
            submitButton.textContent = 'Termin speichern';
        }

        if (editHint) {
            editHint.classList.add('hidden');
        }
    };

    const switchToEditMode = (eventId) => {
        form.action = updateUrlTemplate.replace('__ID__', eventId);

        if (formMethodInput) {
            formMethodInput.value = 'PATCH';
        }

        if (eventIdInput) {
            eventIdInput.value = eventId;
        }

        if (submitButton) {
            submitButton.textContent = 'Termin aktualisieren';
        }

        if (editHint) {
            editHint.classList.remove('hidden');
        }
    };

    const clearForm = () => {
    switchToCreateMode();

    if (titleInput) titleInput.value = '';
    if (startsAtInput) startsAtInput.value = '';
    if (endsAtInput) endsAtInput.value = '';
    if (locationInput) locationInput.value = '';
    if (descriptionInput) descriptionInput.value = '';
    if (typeInput) typeInput.value = 'Sonstiges';
    if (allDayInput) allDayInput.checked = false;

    eventInsuranceBadge?.classList.add('hidden');
    eventInsuranceLink?.classList.add('hidden');
    eventInsuranceNote?.classList.add('hidden');

    if (eventInsuranceLink) {
        eventInsuranceLink.href = '#';
    }
};

    if (resetButton) {
        resetButton.addEventListener('click', () => {
            clearForm();
            scrollToForm();
            if (titleInput) {
                setTimeout(() => titleInput.focus(), 250);
            }
        });
    }

    switchToCreateMode();

    const calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, interactionPlugin],
        initialView: 'dayGridMonth',
        locale: deLocale,
        firstDay: 1,
        height: 'auto',
        fixedWeekCount: false,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: '',
        },
        buttonText: {
            today: 'Heute',
        },
        events: {
    url: calendarFeedUrl,
    method: 'GET',
    failure() {
        console.error('Termine konnten nicht geladen werden. Feed:', calendarFeedUrl);
    },
},
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false,
        },

        dateClick(info) {
            clearForm();

            if (!startsAtInput) {
                return;
            }

            const clickedDate = new Date(info.date);
            clickedDate.setHours(9, 0, 0, 0);

            startsAtInput.value = formatForDateTimeLocal(clickedDate);

            scrollToForm();

            if (titleInput) {
                setTimeout(() => titleInput.focus(), 350);
            }
        },

        eventClick(info) {
            const event = info.event;

            switchToEditMode(event.id);

            if (titleInput) {
                titleInput.value = event.title ?? '';
            }

            if (startsAtInput && event.start) {
                startsAtInput.value = formatForDateTimeLocal(event.start);
            }

            if (endsAtInput) {
    if (event.end) {
        let endDate = new Date(event.end);

        if (event.allDay) {
            endDate.setDate(endDate.getDate() - 1);
            endDate.setHours(0, 0, 0, 0);
        }

        endsAtInput.value = formatForDateTimeLocal(endDate);
    } else {
        endsAtInput.value = '';
    }
}

            if (allDayInput) {
                allDayInput.checked = !!event.allDay;
            }

            if (typeInput) {
                typeInput.value = event.extendedProps?.type ?? 'Sonstiges';
            }

            if (locationInput) {
                locationInput.value = event.extendedProps?.location ?? '';
            }

            if (descriptionInput) {
                descriptionInput.value = event.extendedProps?.description ?? '';
            }

            scrollToForm();

            if (titleInput) {
                setTimeout(() => titleInput.focus(), 350);
            }
            applyInsuranceReminderState(info.event);
        },
    });

    calendar.render();
});

   const foodCheckRoot = document.getElementById('food-check');

if (foodCheckRoot) {
    const FOOD_CHECK_HISTORY_KEY = 'food_check_recent_products_v1';

    const foodCheckOriginalInfoBtn = document.getElementById('foodCheckOriginalInfoBtn');
const foodCheckAlternativeInfoBtn = document.getElementById('foodCheckAlternativeInfoBtn');

const foodCheckGradeImage = document.getElementById('foodCheckGradeImage');
const foodCheckGradeFallback = document.getElementById('foodCheckGradeFallback');

const foodCheckAlternativeGradeImage = document.getElementById('foodCheckAlternativeGradeImage');
const foodCheckAlternativeGradeFallback = document.getElementById('foodCheckAlternativeGradeFallback');

const foodModalGradeImage = document.getElementById('foodModalGradeImage');
const foodModalGradeFallback = document.getElementById('foodModalGradeFallback');

const foodProductModal = document.getElementById('foodProductModal');
const foodModalCloseBtn = document.getElementById('foodModalCloseBtn');
const foodModalTitle = document.getElementById('foodModalTitle');
const foodModalImage = document.getElementById('foodModalImage');
const foodModalNoImage = document.getElementById('foodModalNoImage');
const foodModalBrand = document.getElementById('foodModalBrand');
const foodModalGrade = document.getElementById('foodModalGrade');
const foodModalBarcode = document.getElementById('foodModalBarcode');
const foodModalSugar = document.getElementById('foodModalSugar');
const foodModalFat = document.getElementById('foodModalFat');
const foodModalProtein = document.getElementById('foodModalProtein');
const foodModalKcal = document.getElementById('foodModalKcal');
const foodModalCategories = document.getElementById('foodModalCategories');

    const foodModeBarcodeBtn = document.getElementById('foodModeBarcodeBtn');
    const foodModeSearchBtn = document.getElementById('foodModeSearchBtn');

    const foodBarcodeMode = document.getElementById('foodBarcodeMode');
    const foodSearchMode = document.getElementById('foodSearchMode');

    const foodCheckBarcode = document.getElementById('foodCheckBarcode');
    const foodCheckGoal = document.getElementById('foodCheckGoal');
    const foodCheckBtn = document.getElementById('foodCheckBtn');

    const foodSearchInput = document.getElementById('foodSearchInput');
    const foodSearchGoal = document.getElementById('foodSearchGoal');
    const foodSearchBtn = document.getElementById('foodSearchBtn');
    const foodSearchResultsWrap = document.getElementById('foodSearchResultsWrap');
    const foodSearchResults = document.getElementById('foodSearchResults');
    const foodSearchNoResults = document.getElementById('foodSearchNoResults');
    const foodSearchCount = document.getElementById('foodSearchCount');

    const foodCheckLoading = document.getElementById('foodCheckLoading');
    const foodCheckError = document.getElementById('foodCheckError');
    const foodCheckSuccess = document.getElementById('foodCheckSuccess');
    const foodCheckResult = document.getElementById('foodCheckResult');

    const foodCheckSource = document.getElementById('foodCheckSource');
    const foodCheckGrade = document.getElementById('foodCheckGrade');
    const foodCheckGoalBadge = document.getElementById('foodCheckGoalBadge');

    const foodCheckImage = document.getElementById('foodCheckImage');
    const foodCheckNoImage = document.getElementById('foodCheckNoImage');
    const foodCheckName = document.getElementById('foodCheckName');
    const foodCheckBrand = document.getElementById('foodCheckBrand');
    const foodCheckBarcodeText = document.getElementById('foodCheckBarcodeText');

    const foodCheckSugar = document.getElementById('foodCheckSugar');
    const foodCheckFat = document.getElementById('foodCheckFat');
    const foodCheckProtein = document.getElementById('foodCheckProtein');

    const foodCheckAddOriginalBtn = document.getElementById('foodCheckAddOriginalBtn');

    const foodCheckAlternativeCard = document.getElementById('foodCheckAlternativeCard');
    const foodCheckAlternativeImage = document.getElementById('foodCheckAlternativeImage');
    const foodCheckAlternativeNoImage = document.getElementById('foodCheckAlternativeNoImage');
    const foodCheckAlternativeName = document.getElementById('foodCheckAlternativeName');
    const foodCheckAlternativeBrand = document.getElementById('foodCheckAlternativeBrand');
    const foodCheckAlternativeBarcode = document.getElementById('foodCheckAlternativeBarcode');
    const foodCheckAlternativeGrade = document.getElementById('foodCheckAlternativeGrade');
    const foodCheckAlternativeSugar = document.getElementById('foodCheckAlternativeSugar');
    const foodCheckAlternativeFat = document.getElementById('foodCheckAlternativeFat');
    const foodCheckAlternativeProtein = document.getElementById('foodCheckAlternativeProtein');

    const foodCheckSuggestionReason = document.getElementById('foodCheckSuggestionReason');
    const foodCheckSuggestionRule = document.getElementById('foodCheckSuggestionRule');
    const foodCheckAddToShoppingBtn = document.getElementById('foodCheckAddToShoppingBtn');

    const foodCheckNoSuggestionWrap = document.getElementById('foodCheckNoSuggestionWrap');
    const foodCheckHistory = document.getElementById('foodCheckHistory');
    const foodCheckHistoryEmpty = document.getElementById('foodCheckHistoryEmpty');

    let latestFoodCheckPayload = null;

    function setFoodMode(mode) {
        if (mode === 'search') {
            foodSearchMode.classList.remove('hidden');
            foodBarcodeMode.classList.add('hidden');

            foodModeSearchBtn.classList.remove('bg-slate-100', 'text-slate-700', 'ring-1', 'ring-slate-200');
            foodModeSearchBtn.classList.add('bg-slate-900', 'text-white');

            foodModeBarcodeBtn.classList.remove('bg-slate-900', 'text-white');
            foodModeBarcodeBtn.classList.add('bg-slate-100', 'text-slate-700', 'ring-1', 'ring-slate-200');
        } else {
            foodBarcodeMode.classList.remove('hidden');
            foodSearchMode.classList.add('hidden');

            foodModeBarcodeBtn.classList.remove('bg-slate-100', 'text-slate-700', 'ring-1', 'ring-slate-200');
            foodModeBarcodeBtn.classList.add('bg-slate-900', 'text-white');

            foodModeSearchBtn.classList.remove('bg-slate-900', 'text-white');
            foodModeSearchBtn.classList.add('bg-slate-100', 'text-slate-700', 'ring-1', 'ring-slate-200');
        }
    }

    function formatCategories(categories) {
    if (!Array.isArray(categories) || !categories.length) {
        return 'Keine Kategorien vorhanden';
    }

    return categories
        .slice(0, 6)
        .map(cat => String(cat).replace(/^en:/, '').replace(/^de:/, '').replace(/^fr:/, ''))
        .join(', ');
}

function getNutriScoreImageUrl(grade) {
    const value = String(grade || '').trim().toLowerCase();

    if (!['a', 'b', 'c', 'd', 'e'].includes(value)) {
        return null;
    }

    return `https://static.openfoodfacts.org/images/attributes/dist/nutriscore-${value}-new-en.svg`;
}

function setNutriScoreImage(grade, imageEl, fallbackEl) {
    if (!imageEl || !fallbackEl) {
        return;
    }

    const value = String(grade || '').trim().toLowerCase();
    const imageUrl = getNutriScoreImageUrl(value);

    if (!imageUrl) {
        imageEl.src = '';
        imageEl.classList.add('hidden');
        fallbackEl.textContent = `Nutri-Grade: ${String(grade || '-').toUpperCase()}`;
        fallbackEl.classList.remove('hidden');
        return;
    }

    imageEl.src = imageUrl;
    imageEl.alt = `Nutri-Score ${value.toUpperCase()}`;
    imageEl.classList.remove('hidden');
    fallbackEl.classList.add('hidden');

    imageEl.onerror = function () {
        imageEl.classList.add('hidden');
        fallbackEl.textContent = `Nutri-Grade: ${value.toUpperCase()}`;
        fallbackEl.classList.remove('hidden');
    };
}

function openFoodModal(product) {
    if (!foodProductModal || !product) {
        return;
    }
    if (foodModalOpenOff) {
    const barcode = product.barcode || '';
    foodModalOpenOff.href = barcode
        ? `https://world.openfoodfacts.org/product/${encodeURIComponent(barcode)}`
        : '#';

    if (!barcode) {
        foodModalOpenOff.classList.add('pointer-events-none', 'opacity-50');
    } else {
        foodModalOpenOff.classList.remove('pointer-events-none', 'opacity-50');
    }
}

    const nutriments = product.nutriments || {};

    foodModalTitle.textContent = product.product_name || 'Unbekanntes Produkt';
    foodModalBrand.textContent = product.brand || 'Keine Marke vorhanden';
    setNutriScoreImage(
    product.nutrition_grade,
    foodModalGradeImage,
    foodModalGradeFallback
);
    foodModalBarcode.textContent = `Barcode: ${product.barcode || '-'}`;

    foodModalSugar.textContent = foodCheckFormatGram(nutriments.sugars_100g);
    foodModalFat.textContent = foodCheckFormatGram(nutriments.fat_100g);
    foodModalProtein.textContent = foodCheckFormatGram(nutriments.proteins_100g);

    const kcal = nutriments['energy-kcal_100g'];
    foodModalKcal.textContent = kcal !== undefined && kcal !== null && kcal !== ''
        ? `${Number(kcal).toFixed(0)}`
        : '-';

    foodModalCategories.textContent = formatCategories(product.categories || []);

    if (product.image_url) {
        foodModalImage.src = product.image_url;
        foodModalImage.classList.remove('hidden');
        foodModalNoImage.classList.add('hidden');
    } else {
        foodModalImage.src = '';
        foodModalImage.classList.add('hidden');
        foodModalNoImage.classList.remove('hidden');
    }

    foodProductModal.classList.remove('hidden');
    foodProductModal.classList.add('flex');
}

function closeFoodModal() {
    if (!foodProductModal) {
        return;
    }

    foodProductModal.classList.add('hidden');
    foodProductModal.classList.remove('flex');
}

    function foodCheckShowLoading() {
        foodCheckLoading.classList.remove('hidden');
        foodCheckError.classList.add('hidden');
        foodCheckSuccess.classList.add('hidden');
    }

    function foodCheckHideLoading() {
        foodCheckLoading.classList.add('hidden');
    }

    function foodCheckShowError(message) {
        foodCheckError.textContent = message;
        foodCheckError.classList.remove('hidden');
    }

    function foodCheckShowSuccess(message) {
        foodCheckSuccess.textContent = message;
        foodCheckSuccess.classList.remove('hidden');

        setTimeout(() => {
            foodCheckSuccess.classList.add('hidden');
        }, 2500);
    }

    function foodCheckFormatGram(value) {
        if (value === null || value === undefined || value === '') {
            return '-';
        }

        const num = Number(value);

        if (Number.isNaN(num)) {
            return '-';
        }

        return `${num.toFixed(1)} g`;
    }

    function foodCheckGoalLabel(goal) {
        return goal === 'abnehmen' ? 'Abnehmen' : 'Allgemein';
    }

    function getFoodCheckHistory() {
        try {
            const raw = localStorage.getItem(FOOD_CHECK_HISTORY_KEY);
            const parsed = raw ? JSON.parse(raw) : [];
            return Array.isArray(parsed) ? parsed : [];
        } catch (e) {
            return [];
        }
    }

    function setFoodCheckHistory(items) {
        localStorage.setItem(FOOD_CHECK_HISTORY_KEY, JSON.stringify(items));
    }

    function saveFoodCheckHistory(entry) {
        const items = getFoodCheckHistory().filter(item => item.barcode !== entry.barcode);
        items.unshift(entry);
        setFoodCheckHistory(items.slice(0, 8));
        renderFoodCheckHistory();
    }

    function renderFoodCheckHistory() {
        const items = getFoodCheckHistory();
        foodCheckHistory.innerHTML = '';

        if (!items.length) {
            foodCheckHistoryEmpty.classList.remove('hidden');
            return;
        }

        foodCheckHistoryEmpty.classList.add('hidden');

        items.forEach(item => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'w-full rounded-[20px] bg-white p-3 text-left ring-1 ring-slate-200';
            button.innerHTML = `
                <p class="font-bold text-slate-900">${escapeHtml(item.product_name || 'Unbekanntes Produkt')}</p>
                <p class="mt-1 text-xs text-slate-500">
                    ${escapeHtml(item.brand || 'Keine Marke')} · ${escapeHtml(item.barcode || '-')}
                </p>
            `;

            button.addEventListener('click', function () {
                foodCheckBarcode.value = item.barcode || '';
                foodCheckGoal.value = item.goal || 'abnehmen';
                setFoodMode('barcode');
                loadFoodCheck();
            });

            foodCheckHistory.appendChild(button);
        });
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function setShoppingFormValue(fieldId, value) {
        const element = document.getElementById(fieldId);
        if (element) {
            element.value = value;
        }
    }

    function applyToShoppingForm(title, note = '') {
        setShoppingFormValue('title', title || '');
        setShoppingFormValue('quantity', '1');
        if (note) {
            setShoppingFormValue('note', note);
        }

        document.getElementById('title')?.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });

        document.getElementById('title')?.focus();
    }

    async function loadFoodCheck() {
    const barcode = foodCheckBarcode.value.trim();
    const goal = foodCheckGoal.value;

    if (!barcode) {
        foodCheckShowError('Bitte einen Barcode eingeben.');
        return;
    }

    foodCheckShowLoading();

    try {
        const response = await fetch(`/api/food/product/${encodeURIComponent(barcode)}?goal=${encodeURIComponent(goal)}`);

        let data = null;
        const contentType = response.headers.get('content-type') || '';

        if (contentType.includes('application/json')) {
            data = await response.json();
        } else {
            const text = await response.text();
            console.error('Keine JSON-Antwort vom Server:', text);
            throw new Error('Server hat keine gültige JSON-Antwort zurückgegeben.');
        }

        foodCheckHideLoading();

        if (!response.ok || !data.ok) {
            foodCheckShowError(data.message || 'Fehler beim Laden des Produkts.');
            return;
        }

        latestFoodCheckPayload = data;

        const product = data.product || {};
        const nutriments = product.nutriments || {};
        const suggestion = data.suggestion || null;
        const alternativeProduct = data.alternative_product || null;
        const alternativeNutriments = alternativeProduct?.nutriments || {};

        foodCheckSource.textContent = `Quelle: ${data.source || '-'}`;
        setNutriScoreImage(
    product.nutrition_grade,
    foodCheckGradeImage,
    foodCheckGradeFallback
);
        foodCheckGoalBadge.textContent = `Ziel: ${foodCheckGoalLabel(goal)}`;

        foodCheckName.textContent = product.product_name || 'Unbekanntes Produkt';
        foodCheckBrand.textContent = product.brand || 'Keine Marke vorhanden';
        foodCheckBarcodeText.textContent = product.barcode || barcode;

        if (product.image_url) {
            foodCheckImage.src = product.image_url;
            foodCheckImage.classList.remove('hidden');
            foodCheckNoImage.classList.add('hidden');
        } else {
            foodCheckImage.src = '';
            foodCheckImage.classList.add('hidden');
            foodCheckNoImage.classList.remove('hidden');
        }

        foodCheckSugar.textContent = foodCheckFormatGram(nutriments.sugars_100g);
        foodCheckFat.textContent = foodCheckFormatGram(nutriments.fat_100g);
        foodCheckProtein.textContent = foodCheckFormatGram(nutriments.proteins_100g);

        if (alternativeProduct) {

            const diffSugar =
    (alternativeNutriments.sugars_100g ?? 0) -
    (nutriments.sugars_100g ?? 0);

const diffProtein =
    (alternativeNutriments.proteins_100g ?? 0) -
    (nutriments.proteins_100g ?? 0);

const diffSugarText =
    diffSugar < 0
        ? `${Math.abs(diffSugar).toFixed(1)} g weniger Zucker`
        : `${diffSugar.toFixed(1)} g mehr Zucker`;

const diffProteinText =
    diffProtein > 0
        ? `${diffProtein.toFixed(1)} g mehr Eiweiß`
        : `${Math.abs(diffProtein).toFixed(1)} g weniger Eiweiß`;

document.getElementById('foodCheckDiffSugar').textContent =
    diffSugarText;

document.getElementById('foodCheckDiffProtein').textContent =
    diffProteinText;

document.getElementById('foodCheckDifferenceBox')
    .classList.remove('hidden');

    foodCheckOriginalInfoBtn?.addEventListener('click', function () {
    if (!latestFoodCheckPayload?.product) {
        return;
    }

    openFoodModal(latestFoodCheckPayload.product);
});

foodCheckAlternativeInfoBtn?.addEventListener('click', function () {
    if (!latestFoodCheckPayload?.alternative_product) {
        return;
    }

    openFoodModal(latestFoodCheckPayload.alternative_product);
});

foodModalCloseBtn?.addEventListener('click', closeFoodModal);

foodProductModal?.addEventListener('click', function (event) {
    if (event.target === foodProductModal) {
        closeFoodModal();
    }
});
            foodCheckAlternativeName.textContent =
                alternativeProduct.product_name || suggestion?.alternative_label || suggestion?.alternative || 'Alternative';

            foodCheckAlternativeBrand.textContent =
                alternativeProduct.brand || 'Keine Marke vorhanden';

            foodCheckAlternativeBarcode.textContent =
                alternativeProduct.barcode || '-';

            setNutriScoreImage(
    alternativeProduct.nutrition_grade,
    foodCheckAlternativeGradeImage,
    foodCheckAlternativeGradeFallback
);
            foodCheckAlternativeSugar.textContent =
                foodCheckFormatGram(alternativeNutriments.sugars_100g);

            foodCheckAlternativeFat.textContent =
                foodCheckFormatGram(alternativeNutriments.fat_100g);

            foodCheckAlternativeProtein.textContent =
                foodCheckFormatGram(alternativeNutriments.proteins_100g);

            foodCheckSuggestionReason.textContent =
                suggestion?.reason || '-';

            foodCheckSuggestionRule.textContent =
                suggestion?.matched_term || '-';

            if (alternativeProduct.image_url) {
                foodCheckAlternativeImage.src = alternativeProduct.image_url;
                foodCheckAlternativeImage.classList.remove('hidden');
                foodCheckAlternativeNoImage.classList.add('hidden');
            } else {
                foodCheckAlternativeImage.src = '';
                foodCheckAlternativeImage.classList.add('hidden');
                foodCheckAlternativeNoImage.classList.remove('hidden');
            }

            foodCheckAlternativeCard.classList.remove('hidden');
            foodCheckNoSuggestionWrap.classList.add('hidden');
        } else {
            foodCheckAlternativeCard.classList.add('hidden');
            foodCheckNoSuggestionWrap.classList.remove('hidden');
        }

        saveFoodCheckHistory({
            barcode: product.barcode || barcode,
            product_name: product.product_name || 'Unbekanntes Produkt',
            brand: product.brand || 'Keine Marke',
            goal: goal,
        });

        foodCheckResult.classList.remove('hidden');
        foodCheckError.classList.add('hidden');
    } catch (error) {
        foodCheckHideLoading();
        foodCheckShowError('Die Anfrage konnte nicht verarbeitet werden.');
        console.error(error);
    }
}

    async function searchFoodProducts() {
        const query = foodSearchInput.value.trim();
        const goal = foodSearchGoal.value;

        if (query.length < 2) {
            foodCheckShowError('Bitte mindestens 2 Zeichen eingeben.');
            return;
        }

        foodCheckShowLoading();
        foodSearchResultsWrap.classList.add('hidden');
        foodSearchResults.innerHTML = '';
        foodSearchNoResults.classList.add('hidden');

        try {
            const response = await fetch(`/api/food/search?q=${encodeURIComponent(query)}&goal=${encodeURIComponent(goal)}`);
            const data = await response.json();

            foodCheckHideLoading();

            if (!response.ok || !data.ok) {
                foodCheckShowError(data.message || 'Fehler bei der Produktsuche.');
                return;
            }

            const products = data.products || [];
            foodSearchCount.textContent = `${products.length} Treffer`;
            foodSearchResultsWrap.classList.remove('hidden');

            if (!products.length) {
                foodSearchNoResults.classList.remove('hidden');
                return;
            }

            if (products.length === 1) {
                const onlyProduct = products[0];
                foodCheckBarcode.value = onlyProduct.barcode || '';
                foodCheckGoal.value = goal;
                setFoodMode('barcode');
                await loadFoodCheck();
                return;
            }

            products.forEach(product => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'flex w-full items-start gap-3 rounded-[20px] bg-white p-3 text-left ring-1 ring-slate-200';

                const imageHtml = product.image_url
                    ? `<img src="${escapeHtml(product.image_url)}" alt="" class="h-14 w-14 shrink-0 rounded-[16px] object-contain bg-slate-50 p-1 ring-1 ring-slate-200">`
                    : `<div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-[16px] bg-slate-50 text-xl ring-1 ring-slate-200">🥣</div>`;

                button.innerHTML = `
                    ${imageHtml}
                    <div class="min-w-0">
                        <p class="font-bold text-slate-900">${escapeHtml(product.product_name || 'Unbekanntes Produkt')}</p>
                        <p class="mt-1 text-xs text-slate-500">${escapeHtml(product.brand || 'Keine Marke')}</p>
                    </div>
                `;

                button.addEventListener('click', async function () {
                    foodCheckBarcode.value = product.barcode || '';
                    foodCheckGoal.value = goal;
                    setFoodMode('barcode');
                    await loadFoodCheck();
                });

                foodSearchResults.appendChild(button);
            });
        } catch (error) {
            foodCheckHideLoading();
            foodCheckShowError('Die Suche konnte nicht verarbeitet werden.');
            console.error(error);
        }
    }

    foodModeBarcodeBtn?.addEventListener('click', function () {
        setFoodMode('barcode');
    });

    foodModeSearchBtn?.addEventListener('click', function () {
        setFoodMode('search');
    });

    foodCheckBtn?.addEventListener('click', loadFoodCheck);

    foodCheckBarcode?.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            loadFoodCheck();
        }
    });

    foodSearchBtn?.addEventListener('click', searchFoodProducts);

    foodSearchInput?.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            searchFoodProducts();
        }
    });

    foodCheckAddOriginalBtn?.addEventListener('click', function () {
        if (!latestFoodCheckPayload || !latestFoodCheckPayload.product) {
            foodCheckShowError('Es liegt aktuell kein Originalprodukt vor.');
            return;
        }

        const product = latestFoodCheckPayload.product || {};

        applyToShoppingForm(
            product.product_name || '',
            `Originalprodukt aus Food Check${product.brand ? ' · Marke: ' + product.brand : ''}`
        );

        foodCheckShowSuccess(`„${product.product_name || 'Produkt'}“ wurde ins Formular übernommen.`);
    });

    foodCheckAddToShoppingBtn?.addEventListener('click', function () {
    if (!latestFoodCheckPayload) {
        foodCheckShowError('Es liegt aktuell keine Alternative vor.');
        return;
    }

    const product = latestFoodCheckPayload.product || {};
    const suggestion = latestFoodCheckPayload.suggestion || {};
    const alternativeProduct = latestFoodCheckPayload.alternative_product || null;

    const title =
        alternativeProduct?.product_name ||
        suggestion.alternative_label ||
        suggestion.alternative ||
        '';

    if (!title) {
        foodCheckShowError('Es liegt aktuell keine Alternative vor.');
        return;
    }

    applyToShoppingForm(
        title,
        `Alternative zu: ${product.product_name || 'Produkt'} · Vorschlag aus Food Check`
    );

    foodCheckShowSuccess(`„${title}“ wurde ins Formular übernommen.`);
});

    renderFoodCheckHistory();
}
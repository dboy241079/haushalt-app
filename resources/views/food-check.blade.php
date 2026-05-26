<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Check</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background:
                radial-gradient(circle at top right, rgba(59, 130, 246, 0.12), transparent 30%),
                radial-gradient(circle at bottom left, rgba(16, 185, 129, 0.10), transparent 25%),
                #f8fafc;
            min-height: 100vh;
        }

        .page-wrap {
            max-width: 1180px;
            margin: 28px auto;
            padding: 0 16px;
        }

        .app-shell {
            display: grid;
            grid-template-columns: 1.7fr 1fr;
            gap: 24px;
        }

        .card-ui {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 24px;
            box-shadow: 0 14px 40px rgba(15, 23, 42, 0.08);
            overflow: hidden;
        }

        .main-header {
            padding: 28px 28px 20px;
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: #fff;
        }

        .main-header h1 {
            margin: 0 0 8px;
            font-size: 2rem;
            font-weight: 700;
        }

        .main-header p {
            margin: 0;
            color: rgba(255, 255, 255, 0.75);
        }

        .card-body-ui {
            padding: 24px 28px 28px;
        }

        .badge-source,
        .badge-grade,
        .badge-soft {
            display: inline-flex;
            align-items: center;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
        }

        .badge-source {
            background: #e2e8f0;
            color: #0f172a;
        }

        .badge-soft {
            background: #ecfeff;
            color: #0f766e;
        }

        .grade-a { background: #16a34a; color: white; }
        .grade-b { background: #65a30d; color: white; }
        .grade-c { background: #eab308; color: #111827; }
        .grade-d { background: #f97316; color: white; }
        .grade-e { background: #dc2626; color: white; }
        .grade-default { background: #cbd5e1; color: #0f172a; }

        .product-image {
            width: 100%;
            max-width: 250px;
            max-height: 250px;
            object-fit: contain;
            border-radius: 18px;
            background: #fff;
            border: 1px solid #e2e8f0;
            padding: 12px;
        }

        .metric-box {
            border-radius: 18px;
            padding: 18px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            height: 100%;
            text-align: center;
        }

        .metric-value {
            font-size: 1.7rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.1;
        }

        .metric-label {
            margin-top: 6px;
            font-size: 0.92rem;
            color: #64748b;
        }

        .suggestion-box {
            border-radius: 20px;
            padding: 20px;
            background: linear-gradient(135deg, #fff7ed, #fffbeb);
            border: 1px solid #fdba74;
        }

        .suggestion-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: #9a3412;
            margin-bottom: 6px;
        }

        .suggestion-text {
            color: #7c2d12;
            margin-bottom: 10px;
        }

        .neutral-box {
            border-radius: 20px;
            padding: 20px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .sidebar-card {
            padding: 22px;
        }

        .sidebar-title {
            font-size: 1.1rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 14px;
        }

        .history-list,
        .shopping-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .history-item,
        .shopping-item {
            padding: 14px 14px;
            border-radius: 16px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .history-item button,
        .shopping-item button {
            border: none;
            background: none;
            padding: 0;
            text-align: left;
            width: 100%;
        }

        .history-name,
        .shopping-name {
            font-weight: 700;
            color: #0f172a;
        }

        .history-meta,
        .shopping-meta {
            font-size: 0.88rem;
            color: #64748b;
            margin-top: 4px;
        }

        .mini-btn {
            border: none;
            border-radius: 12px;
            padding: 8px 12px;
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
        }

        .mini-btn-dark {
            background: #0f172a;
            color: #fff;
        }

        .mini-btn-light {
            background: #e2e8f0;
            color: #0f172a;
        }

        .top-info {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 18px;
        }

        .loading-box,
        .error-box,
        .success-box {
            border-radius: 18px;
            padding: 16px;
            margin-bottom: 18px;
        }

        .loading-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
        }

        .error-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .success-box {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }

        .hidden {
            display: none !important;
        }

        .small-muted {
            font-size: 0.88rem;
            color: #64748b;
        }

        @media (max-width: 991px) {
            .app-shell {
                grid-template-columns: 1fr;
            }

            .main-header,
            .card-body-ui,
            .sidebar-card {
                padding-left: 18px;
                padding-right: 18px;
            }
        }
    </style>
</head>
<body>
    <div class="page-wrap">
        <div class="app-shell">
            <div class="card-ui">
                <div class="main-header">
                    <h1>Food Check</h1>
                    <p>Produkte prüfen, Nährwerte bewerten und bessere Alternativen direkt in die Einkaufsliste übernehmen.</p>
                </div>

                <div class="card-body-ui">
                    <div class="row g-3 align-items-end mb-4">
                        <div class="col-12 col-md-6">
                            <label for="barcode" class="form-label fw-semibold">Barcode</label>
                            <input
                                type="text"
                                id="barcode"
                                class="form-control form-control-lg"
                                placeholder="z. B. 3017624010701"
                                value="3017624010701"
                            >
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="goal" class="form-label fw-semibold">Ziel</label>
                            <select id="goal" class="form-select form-select-lg">
                                <option value="abnehmen" selected>Abnehmen</option>
                                <option value="allgemein">Allgemein</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-2 d-grid">
                            <button id="checkBtn" class="btn btn-dark btn-lg">Prüfen</button>
                        </div>
                    </div>

                    <div id="loadingBox" class="loading-box hidden">Produkt wird geladen...</div>
                    <div id="errorBox" class="error-box hidden"></div>
                    <div id="successBox" class="success-box hidden"></div>

                    <div id="resultBox" class="hidden">
                        <div class="top-info">
                            <span id="sourceBadge" class="badge-source">Quelle</span>
                            <span id="gradeBadge" class="badge-grade grade-default">Nutri-Grade</span>
                            <span id="productGoalBadge" class="badge-soft">Ziel: -</span>
                        </div>

                        <div class="row g-4 align-items-start">
                            <div class="col-12 col-lg-4 text-center">
                                <img id="productImage" class="product-image hidden" alt="Produktbild">
                                <div id="noImageText" class="text-muted">Kein Bild vorhanden</div>
                            </div>

                            <div class="col-12 col-lg-8">
                                <h2 id="productName" class="h3 mb-2">-</h2>
                                <p id="productBrand" class="text-muted mb-3">-</p>
                                <p class="mb-2"><strong>Barcode:</strong> <span id="productBarcode">-</span></p>
                                <p class="small-muted mb-0">
                                    Diese Ansicht nutzt deinen lokalen Cache und eure interne Bewertungslogik.
                                </p>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-4">
                                <div class="metric-box">
                                    <div id="sugarValue" class="metric-value">-</div>
                                    <div class="metric-label">Zucker / 100 g</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="metric-box">
                                    <div id="fatValue" class="metric-value">-</div>
                                    <div class="metric-label">Fett / 100 g</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="metric-box">
                                    <div id="proteinValue" class="metric-value">-</div>
                                    <div class="metric-label">Eiweiß / 100 g</div>
                                </div>
                            </div>
                        </div>

                        <div id="suggestionBox" class="suggestion-box hidden">
                            <div class="suggestion-title" id="suggestionAlternative">-</div>
                            <p class="suggestion-text" id="suggestionReason">-</p>
                            <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
                                <small class="text-muted">
                                    Regel:
                                    <span id="suggestionMatchedTerm">-</span>
                                </small>

                                <button id="addSuggestionBtn" class="mini-btn mini-btn-dark" type="button">
                                    Alternative in Einkaufsliste übernehmen
                                </button>
                            </div>
                        </div>

                        <div id="noSuggestionBox" class="neutral-box hidden">
                            <div class="fw-bold text-dark mb-2">Keine spezielle Empfehlung gefunden</div>
                            <p class="mb-0 text-secondary">
                                Für dieses Produkt liegt aktuell keine passende Regel vor.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column gap-4">
                <div class="card-ui sidebar-card">
                    <div class="sidebar-title">Zuletzt geprüfte Produkte</div>
                    <div id="historyList" class="history-list"></div>
                    <div id="emptyHistory" class="small-muted">Noch keine Produkte geprüft.</div>
                </div>

                <div class="card-ui sidebar-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="sidebar-title mb-0">Einkaufsliste</div>
                        <button id="clearShoppingBtn" class="mini-btn mini-btn-light" type="button">
                            Liste leeren
                        </button>
                    </div>

                    <div id="shoppingList" class="shopping-list"></div>
                    <div id="emptyShopping" class="small-muted">Noch keine Produkte übernommen.</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const STORAGE_HISTORY_KEY = 'food_check_recent_products_v1';
        const STORAGE_SHOPPING_KEY = 'shopping_list_items_v1';

        const barcodeInput = document.getElementById('barcode');
        const goalSelect = document.getElementById('goal');
        const checkBtn = document.getElementById('checkBtn');

        const loadingBox = document.getElementById('loadingBox');
        const errorBox = document.getElementById('errorBox');
        const successBox = document.getElementById('successBox');
        const resultBox = document.getElementById('resultBox');

        const sourceBadge = document.getElementById('sourceBadge');
        const gradeBadge = document.getElementById('gradeBadge');
        const productGoalBadge = document.getElementById('productGoalBadge');

        const productImage = document.getElementById('productImage');
        const noImageText = document.getElementById('noImageText');
        const productName = document.getElementById('productName');
        const productBrand = document.getElementById('productBrand');
        const productBarcode = document.getElementById('productBarcode');

        const sugarValue = document.getElementById('sugarValue');
        const fatValue = document.getElementById('fatValue');
        const proteinValue = document.getElementById('proteinValue');

        const suggestionBox = document.getElementById('suggestionBox');
        const noSuggestionBox = document.getElementById('noSuggestionBox');
        const suggestionAlternative = document.getElementById('suggestionAlternative');
        const suggestionReason = document.getElementById('suggestionReason');
        const suggestionMatchedTerm = document.getElementById('suggestionMatchedTerm');
        const addSuggestionBtn = document.getElementById('addSuggestionBtn');

        const historyList = document.getElementById('historyList');
        const emptyHistory = document.getElementById('emptyHistory');

        const shoppingList = document.getElementById('shoppingList');
        const emptyShopping = document.getElementById('emptyShopping');
        const clearShoppingBtn = document.getElementById('clearShoppingBtn');

        let latestPayload = null;

        function getStorageArray(key) {
            try {
                const raw = localStorage.getItem(key);
                const parsed = raw ? JSON.parse(raw) : [];
                return Array.isArray(parsed) ? parsed : [];
            } catch (error) {
                return [];
            }
        }

        function setStorageArray(key, value) {
            localStorage.setItem(key, JSON.stringify(value));
        }

        function showLoading() {
            loadingBox.classList.remove('hidden');
            errorBox.classList.add('hidden');
            successBox.classList.add('hidden');
            resultBox.classList.add('hidden');
        }

        function hideLoading() {
            loadingBox.classList.add('hidden');
        }

        function showError(message) {
            errorBox.textContent = message;
            errorBox.classList.remove('hidden');
            successBox.classList.add('hidden');
            resultBox.classList.add('hidden');
        }

        function showSuccess(message) {
            successBox.textContent = message;
            successBox.classList.remove('hidden');
            setTimeout(() => {
                successBox.classList.add('hidden');
            }, 2500);
        }

        function formatGram(value) {
            if (value === null || value === undefined || value === '') {
                return '-';
            }

            const num = Number(value);

            if (Number.isNaN(num)) {
                return '-';
            }

            return `${num.toFixed(1)} g`;
        }

        function getGradeClass(grade) {
            const value = String(grade || '').toLowerCase();

            if (['a', 'b', 'c', 'd', 'e'].includes(value)) {
                return `grade-${value}`;
            }

            return 'grade-default';
        }

        function normalizeGoal(goal) {
            if (goal === 'abnehmen') {
                return 'Abnehmen';
            }

            return 'Allgemein';
        }

        function saveToHistory(entry) {
            const items = getStorageArray(STORAGE_HISTORY_KEY)
                .filter(item => item.barcode !== entry.barcode);

            items.unshift(entry);

            setStorageArray(STORAGE_HISTORY_KEY, items.slice(0, 8));
            renderHistory();
        }

        function renderHistory() {
            const items = getStorageArray(STORAGE_HISTORY_KEY);
            historyList.innerHTML = '';

            if (!items.length) {
                emptyHistory.classList.remove('hidden');
                return;
            }

            emptyHistory.classList.add('hidden');

            items.forEach(item => {
                const wrapper = document.createElement('div');
                wrapper.className = 'history-item';

                const button = document.createElement('button');
                button.type = 'button';
                button.innerHTML = `
                    <div class="history-name">${escapeHtml(item.product_name || 'Unbekannt')}</div>
                    <div class="history-meta">
                        ${escapeHtml(item.brand || 'Keine Marke')} · ${escapeHtml(item.barcode || '-')}
                    </div>
                `;

                button.addEventListener('click', () => {
                    barcodeInput.value = item.barcode || '';
                    if (item.goal) {
                        goalSelect.value = item.goal;
                    }
                    loadProduct();
                });

                wrapper.appendChild(button);
                historyList.appendChild(wrapper);
            });
        }

        function addToShoppingList(name, meta = '') {
            const items = getStorageArray(STORAGE_SHOPPING_KEY);

            const exists = items.some(item => item.name.toLowerCase() === String(name).toLowerCase());

            if (exists) {
                showSuccess('Dieses Produkt ist schon in der Einkaufsliste.');
                return;
            }

            items.unshift({
                name,
                meta,
                created_at: new Date().toISOString(),
            });

            setStorageArray(STORAGE_SHOPPING_KEY, items.slice(0, 30));
            renderShoppingList();
            showSuccess(`„${name}“ wurde zur Einkaufsliste hinzugefügt.`);
        }

        function removeFromShoppingList(index) {
            const items = getStorageArray(STORAGE_SHOPPING_KEY);
            items.splice(index, 1);
            setStorageArray(STORAGE_SHOPPING_KEY, items);
            renderShoppingList();
        }

        function renderShoppingList() {
            const items = getStorageArray(STORAGE_SHOPPING_KEY);
            shoppingList.innerHTML = '';

            if (!items.length) {
                emptyShopping.classList.remove('hidden');
                return;
            }

            emptyShopping.classList.add('hidden');

            items.forEach((item, index) => {
                const wrapper = document.createElement('div');
                wrapper.className = 'shopping-item';

                const top = document.createElement('div');
                top.className = 'd-flex justify-content-between align-items-start gap-2';

                const left = document.createElement('div');
                left.innerHTML = `
                    <div class="shopping-name">${escapeHtml(item.name)}</div>
                    <div class="shopping-meta">${escapeHtml(item.meta || '')}</div>
                `;

                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'mini-btn mini-btn-light';
                removeBtn.textContent = 'Entfernen';
                removeBtn.addEventListener('click', () => removeFromShoppingList(index));

                top.appendChild(left);
                top.appendChild(removeBtn);
                wrapper.appendChild(top);
                shoppingList.appendChild(wrapper);
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

        async function loadProduct() {
            const barcode = barcodeInput.value.trim();
            const goal = goalSelect.value;

            if (!barcode) {
                showError('Bitte einen Barcode eingeben.');
                return;
            }

            showLoading();

            try {
                const response = await fetch(`/api/food/product/${encodeURIComponent(barcode)}?goal=${encodeURIComponent(goal)}`);
                const data = await response.json();

                hideLoading();

                if (!response.ok || !data.ok) {
                    showError(data.message || 'Fehler beim Laden des Produkts.');
                    return;
                }

                latestPayload = data;

                const product = data.product || {};
                const nutriments = product.nutriments || {};
                const suggestion = data.suggestion || null;

                sourceBadge.textContent = `Quelle: ${data.source || '-'}`;

                const grade = (product.nutrition_grade || '-').toUpperCase();
                gradeBadge.textContent = `Nutri-Grade: ${grade}`;
                gradeBadge.className = `badge-grade ${getGradeClass(product.nutrition_grade)}`;

                productGoalBadge.textContent = `Ziel: ${normalizeGoal(goal)}`;

                productName.textContent = product.product_name || 'Unbekanntes Produkt';
                productBrand.textContent = product.brand || 'Keine Marke vorhanden';
                productBarcode.textContent = product.barcode || barcode;

                if (product.image_url) {
                    productImage.src = product.image_url;
                    productImage.classList.remove('hidden');
                    noImageText.classList.add('hidden');
                } else {
                    productImage.src = '';
                    productImage.classList.add('hidden');
                    noImageText.classList.remove('hidden');
                }

                sugarValue.textContent = formatGram(nutriments.sugars_100g);
                fatValue.textContent = formatGram(nutriments.fat_100g);
                proteinValue.textContent = formatGram(nutriments.proteins_100g);

                if (suggestion) {
                    suggestionAlternative.textContent = suggestion.alternative || '-';
                    suggestionReason.textContent = suggestion.reason || '-';
                    suggestionMatchedTerm.textContent = suggestion.matched_term || '-';
                    suggestionBox.classList.remove('hidden');
                    noSuggestionBox.classList.add('hidden');
                } else {
                    suggestionBox.classList.add('hidden');
                    noSuggestionBox.classList.remove('hidden');
                }

                resultBox.classList.remove('hidden');
                errorBox.classList.add('hidden');

                saveToHistory({
                    barcode: product.barcode || barcode,
                    product_name: product.product_name || 'Unbekanntes Produkt',
                    brand: product.brand || 'Keine Marke',
                    goal: goal,
                });
            } catch (error) {
                hideLoading();
                showError('Die Anfrage konnte nicht verarbeitet werden.');
                console.error(error);
            }
        }

        addSuggestionBtn.addEventListener('click', function () {
            if (!latestPayload || !latestPayload.suggestion) {
                showError('Es liegt aktuell keine Alternative vor.');
                return;
            }

            const product = latestPayload.product || {};
            const suggestion = latestPayload.suggestion || {};

            addToShoppingList(
                suggestion.alternative || 'Alternative',
                `Für ${product.product_name || 'dieses Produkt'}`
            );
        });

        checkBtn.addEventListener('click', loadProduct);

        barcodeInput.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                loadProduct();
            }
        });

        clearShoppingBtn.addEventListener('click', function () {
            localStorage.removeItem(STORAGE_SHOPPING_KEY);
            renderShoppingList();
            showSuccess('Die Einkaufsliste wurde geleert.');
        });

        renderHistory();
        renderShoppingList();
        window.addEventListener('load', loadProduct);
    </script>
</body>
</html>
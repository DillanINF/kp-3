import './bootstrap';

const openModal = (id) => {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.documentElement.classList.add('overflow-hidden');
};

const toDateFromIdString = (value) => {
    const raw = String(value ?? '').trim();
    if (!raw) return null;

    const iso = /^\d{4}-\d{2}-\d{2}$/;
    if (iso.test(raw)) {
        const d = new Date(`${raw}T00:00:00`);
        return Number.isFinite(d.getTime()) ? d : null;
    }

    const m = raw.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
    if (m) {
        const [, dd, mm, yyyy] = m;
        const d = new Date(`${yyyy}-${mm}-${dd}T00:00:00`);
        return Number.isFinite(d.getTime()) ? d : null;
    }

    const d = new Date(raw);
    return Number.isFinite(d.getTime()) ? d : null;
};

const closeModal = (id) => {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');

    const anyOpen = document.querySelector('[data-modal]:not(.hidden)');
    if (!anyOpen) {
        document.documentElement.classList.remove('overflow-hidden');
    }
};

document.addEventListener('click', (e) => {
    const openTrigger = e.target.closest('[data-open-modal]');
    if (openTrigger) {
        const modalId = openTrigger.getAttribute('data-open-modal');
        const itemType = openTrigger.getAttribute('data-item-type');
        const supplierId = openTrigger.getAttribute('data-supplier-id');
        if (itemType && modalId === 'modal-tambah-item') {
            const itemTypeEl = document.querySelector('#modal-tambah-item [data-add-item-type]');
            if (itemTypeEl) itemTypeEl.value = itemType;

            const supplierEl = document.querySelector('#modal-tambah-item [data-add-item-supplier]');
            if (supplierEl && supplierId) supplierEl.value = supplierId;

            const openSupplierEl = document.querySelector('#modal-tambah-item [data-open-supplier-id]');
            if (openSupplierEl && supplierId) openSupplierEl.value = supplierId;

            const nameEl = document.querySelector('#modal-tambah-item [name="name"]');
            const unitEl = document.querySelector('#modal-tambah-item [name="unit"]');
            const priceEl = document.querySelector('#modal-tambah-item [name="price"]');
            if (nameEl) nameEl.value = '';
            if (unitEl) unitEl.value = 'pcs';
            if (priceEl) priceEl.value = '0';
        }

        openModal(modalId);
        return;
    }

    const closeTrigger = e.target.closest('[data-close-modal]');
    if (closeTrigger) {
        closeModal(closeTrigger.getAttribute('data-close-modal'));
        return;
    }

    const overlay = e.target.closest('[data-modal]');
    if (overlay && e.target === overlay) {
        const id = overlay.getAttribute('id');
        if (id) closeModal(id);
    }
});

document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;
    document.querySelectorAll('[data-modal]:not(.hidden)').forEach((modal) => {
        const id = modal.getAttribute('id');
        if (id) closeModal(id);
    });
});

const INVOICE_ROWS_STORAGE_KEY = 'invoice_rows_v1';
const INVOICE_NEXT_STORAGE_KEY = 'invoice_next_v1';

const readInvoiceRows = () => {
    try {
        const raw = window.localStorage.getItem(INVOICE_ROWS_STORAGE_KEY);
        if (!raw) return [];
        const parsed = JSON.parse(raw);
        return Array.isArray(parsed) ? parsed : [];
    } catch {
        return [];
    }
};

const writeInvoiceRows = (rows) => {
    window.localStorage.setItem(INVOICE_ROWS_STORAGE_KEY, JSON.stringify(rows));
};

const getDefaultNextInvoiceNo = (rows) => {
    const max = rows.reduce((acc, row) => {
        const n = Number.parseInt(row?.invoiceNo, 10);
        if (!Number.isFinite(n)) return acc;
        return Math.max(acc, n);
    }, 0);
    return max + 1;
};

const readNextInvoiceNo = (rows) => {
    if (!rows || rows.length === 0) return 1;
    try {
        const raw = window.localStorage.getItem(INVOICE_NEXT_STORAGE_KEY);
        const n = Number.parseInt(raw || '', 10);
        if (Number.isFinite(n) && n > 0) return n;
        return getDefaultNextInvoiceNo(rows);
    } catch {
        return getDefaultNextInvoiceNo(rows);
    }
};

const writeNextInvoiceNo = (n) => {
    window.localStorage.setItem(INVOICE_NEXT_STORAGE_KEY, String(n));
};

const findSmallestMissingInvoiceNo = (rows, startFrom = 1) => {
    const used = new Set(
        rows
            .map((r) => Number.parseInt(r?.invoiceNo, 10))
            .filter((n) => Number.isFinite(n) && n > 0),
    );
    let i = startFrom;
    while (used.has(i)) i += 1;
    return i;
};

const formatDateId = (date) => {
    try {
        return new Intl.DateTimeFormat('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
        }).format(date);
    } catch {
        const dd = String(date.getDate()).padStart(2, '0');
        const mm = String(date.getMonth() + 1).padStart(2, '0');
        const yyyy = String(date.getFullYear());
        return `${dd}/${mm}/${yyyy}`;
    }
};

const formatCellValue = (value) => {
    const str = String(value ?? '').trim();
    return str.length > 0 && str !== 'null' && str !== 'undefined' ? str : '-';
};

const toIntOrNull = (value) => {
    const raw = String(value ?? '').trim();
    if (raw.length === 0) return null;
    const n = Number.parseInt(raw, 10);
    return Number.isFinite(n) ? n : null;
};

const toIntOrZero = (value) => {
    const n = toIntOrNull(value);
    return Number.isFinite(n) ? n : 0;
};

const getInvoiceByNo = (invoiceNo) => {
    const rows = readInvoiceRows();
    return rows.find((r) => Number.parseInt(r?.invoiceNo, 10) === invoiceNo) || null;
};

const getInputPoBaseUrl = () => {
    const tbody = document.querySelector('[data-invoice-rows]');
    const url = tbody?.dataset?.inputPoUrl;
    return url && String(url).trim().length > 0 ? String(url) : null;
};

const navigateToInputPo = (invoiceNo) => {
    if (!Number.isFinite(invoiceNo)) return;
    const base = getInputPoBaseUrl();
    if (!base) {
        window.alert('URL Input PO belum tersedia.');
        return;
    }

    const url = new URL(base, window.location.origin);
    url.searchParams.set('invoiceNo', String(invoiceNo));
    window.location.href = url.toString();
};

const hydratePoPageFromQuery = () => {
    const poPage = document.querySelector('[data-po-page]');
    if (!poPage) return;

    const qs = new URLSearchParams(window.location.search);
    const invoiceNo = Number.parseInt(qs.get('invoiceNo') || '', 10);
    if (!Number.isFinite(invoiceNo)) {
        window.alert('Invoice belum dipilih.');
        const backUrl = poPage?.dataset?.backUrl;
        if (backUrl) window.location.href = String(backUrl);
        return;
    }

    const row = getInvoiceByNo(invoiceNo);
    if (!row) {
        window.alert('Data invoice tidak ditemukan.');
        const backUrl = poPage?.dataset?.backUrl;
        if (backUrl) window.location.href = String(backUrl);
        return;
    }

    poPage.dataset.invoiceNo = String(invoiceNo);

    const customerEl = document.querySelector('[data-po-customer-name]');
    const noPoEl = document.querySelector('[data-po-no-po]');
    const alamatEl = document.querySelector('[data-po-alamat]');

    if (customerEl) customerEl.value = String(row.customerName ?? '');
    if (noPoEl) noPoEl.value = row.noPo ? String(row.noPo) : '';

    const details = row?.poDetails || {};
    if (alamatEl) {
        const alamat = String(details.alamat ?? '').trim();
        if (alamat.length > 0) {
            alamatEl.value = alamat;
        }
 else {
            const legacyAlamat1 = String(details.alamat1 ?? '').trim();
            const legacyAlamat2 = String(details.alamat2 ?? '').trim();
            alamatEl.value = [legacyAlamat1, legacyAlamat2].filter(Boolean).join(' ').trim();
        }
    }

    const items = Array.isArray(details.items) ? details.items : [];
    renderPoItems(items);

    if (noPoEl) noPoEl.focus();
};

const buildPoItemRow = (item = {}) => {
    const tr = document.createElement('tr');
    tr.setAttribute('data-po-item-row', '');
    tr.innerHTML = `
        <td class="px-4 py-3">
            <input data-po-item-product type="text" placeholder="Pilih Produk" class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" />
        </td>
        <td class="px-4 py-3">
            <div class="grid grid-cols-[1fr_84px] gap-2">
                <input data-po-item-qty type="number" min="0" step="1" placeholder="0" class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" />
                <input data-po-item-unit type="text" value="pcs" class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm text-slate-700 outline-none" />
            </div>
        </td>
        <td class="px-4 py-3">
            <input data-po-item-price type="number" min="0" step="1" placeholder="0" class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" />
        </td>
        <td class="px-4 py-3">
            <input data-po-item-total type="text" disabled value="0" class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm text-slate-700 outline-none" />
        </td>
        <td class="px-4 py-3 text-center">
            <button type="button" data-po-remove-item class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100" aria-label="Delete">×</button>
        </td>
    `;

    const productEl = tr.querySelector('[data-po-item-product]');
    const qtyEl = tr.querySelector('[data-po-item-qty]');
    const unitEl = tr.querySelector('[data-po-item-unit]');
    const priceEl = tr.querySelector('[data-po-item-price]');

    if (productEl) productEl.value = String(item.product ?? '');
    if (qtyEl) qtyEl.value = String(item.qty ?? '');
    if (unitEl) unitEl.value = String(item.unit ?? 'pcs');
    if (priceEl) priceEl.value = String(item.price ?? '');

    return tr;
};

const renderPoItems = (items) => {
    const tbody = document.querySelector('[data-po-items]');
    if (!tbody) return;

    tbody.innerHTML = '';

    const normalized = Array.isArray(items) && items.length > 0 ? items : [{}];
    normalized.forEach((item) => {
        tbody.appendChild(buildPoItemRow(item));
    });
    recalcPoTotals();
};

const recalcPoTotals = () => {
    const poPage = document.querySelector('[data-po-page]');
    if (!poPage) return;

    let grandTotal = 0;
    const rows = document.querySelectorAll('[data-po-items] [data-po-item-row]');
    rows.forEach((row) => {
        const qty = toIntOrZero(row.querySelector('[data-po-item-qty]')?.value);
        const price = toIntOrZero(row.querySelector('[data-po-item-price]')?.value);
        const total = qty * price;
        grandTotal += total;
        const totalEl = row.querySelector('[data-po-item-total]');
        if (totalEl) totalEl.value = String(total);
    });

    const gt = document.querySelector('[data-po-grand-total]');
    if (gt) gt.value = String(grandTotal);
};

const collectPoItems = () => {
    const rows = Array.from(document.querySelectorAll('[data-po-items] [data-po-item-row]'));
    return rows
        .map((row) => {
            const product = (row.querySelector('[data-po-item-product]')?.value || '').trim();
            const qty = toIntOrNull(row.querySelector('[data-po-item-qty]')?.value);
            const unit = (row.querySelector('[data-po-item-unit]')?.value || 'pcs').trim() || 'pcs';
            const price = toIntOrNull(row.querySelector('[data-po-item-price]')?.value);
            const total = (Number.isFinite(qty) ? qty : 0) * (Number.isFinite(price) ? price : 0);

            if (product.length === 0 && !Number.isFinite(qty) && !Number.isFinite(price)) return null;

            return {
                product: product.length > 0 ? product : null,
                qty: Number.isFinite(qty) ? qty : null,
                unit,
                price: Number.isFinite(price) ? price : null,
                total,
            };
        })
        .filter(Boolean);
};

const savePoPage = () => {
    const poPage = document.querySelector('[data-po-page]');
    if (!poPage) return;

    const invoiceNo = Number.parseInt(poPage.dataset.invoiceNo || '', 10);
    if (!Number.isFinite(invoiceNo)) {
        window.alert('Invoice belum dipilih.');
        return;
    }

    const noPoEl = document.querySelector('[data-po-no-po]');
    const alamatEl = document.querySelector('[data-po-alamat]');

    const noPo = (noPoEl?.value || '').trim();

    const items = collectPoItems();
    const qtySum = items.reduce((acc, it) => acc + (Number.isFinite(it.qty) ? it.qty : 0), 0);
    const grandTotal = items.reduce((acc, it) => acc + (Number.isFinite(it.total) ? it.total : 0), 0);

    const poDetails = {
        alamat: (alamatEl?.value || '').trim() || null,
        items,
        grandTotal,
        qtySum,
    };

    const rows = readInvoiceRows();
    const updated = rows.map((r) => {
        const n = Number.parseInt(r?.invoiceNo, 10);
        if (n !== invoiceNo) return r;
        return {
            ...r,
            noPo: noPo.length > 0 ? noPo : null,
            totalPo: grandTotal > 0 ? String(grandTotal) : null,
            qty: qtySum > 0 ? String(qtySum) : null,
            dateStr: r.dateStr || null,
            poDetails,
        };
    });

    writeInvoiceRows(updated);
    const backUrl = poPage?.dataset?.backUrl;
    if (backUrl) {
        window.location.href = String(backUrl);
    }
};

const buildInvoiceRow = ({ invoiceNo, customerName, noPo, totalPo, qty, dateStr }) => {
    const tr = document.createElement('tr');
    tr.className = 'cursor-pointer transition-colors hover:bg-indigo-50 active:bg-indigo-100';
    tr.setAttribute('data-invoice-no', String(invoiceNo));

    const safeDate = formatCellValue(dateStr || formatDateId(new Date()));
    const safeCustomer = formatCellValue(customerName);

    tr.innerHTML = `
        <td class="px-4 py-3 text-slate-700">${safeDate}</td>
        <td class="px-4 py-3">
            <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-indigo-100 text-xs font-semibold text-indigo-700">${invoiceNo}</span>
        </td>
        <td class="px-4 py-3 text-slate-700">${safeCustomer}</td>
        <td class="px-4 py-3 text-slate-500">${formatCellValue(noPo)}</td>
        <td class="px-4 py-3 text-slate-500">${formatCellValue(totalPo)}</td>
        <td class="px-4 py-3 text-slate-500">${formatCellValue(qty)}</td>
        <td class="px-4 py-3">
            <div class="flex flex-col items-center justify-center gap-2">
                <div class="flex items-center justify-center gap-2">
                    <button type="button" data-action="edit-invoice-row" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 hover:bg-slate-50" aria-label="Edit">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4">
                            <path d="M12 20H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M16.5 3.5C17.3284 2.67157 18.6716 2.67157 19.5 3.5C20.3284 4.32843 20.3284 5.67157 19.5 6.5L8 18L3 19L4 14L16.5 3.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <button type="button" data-action="delete-invoice-row" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100" aria-label="Delete">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4">
                            <path d="M3 6H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M8 6V4H16V6" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                            <path d="M19 6L18 20H6L5 6" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                            <path d="M10 11V17" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M14 11V17" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </button>
                </div>
            </div>
        </td>
    `;

    return tr;
};

const updateInvoiceCountUI = () => {
    const rows = readInvoiceRows();
    const count = rows.length;
    const totalEl = document.querySelector('[data-invoice-total-count]');
    if (totalEl) totalEl.textContent = String(count);

    const badgeEl = document.querySelector('[data-invoice-count-badge]');
    if (badgeEl) badgeEl.textContent = `${count} data`;
};

const applyInvoiceSearchFilter = () => {
    const input = document.querySelector('[data-invoice-search]');
    const value = (input?.value || '').trim().toLowerCase();
    const rows = document.querySelectorAll('[data-invoice-rows] tr');
    rows.forEach((row) => {
        const invoiceNo = (row.getAttribute('data-invoice-no') || '').toLowerCase();
        row.classList.toggle('hidden', value.length > 0 && !invoiceNo.includes(value));
    });
};

const updateDashboardKpis = () => {
    const root = document.querySelector('[data-dashboard]');
    if (!root) return;

    const rows = readInvoiceRows();

    const now = new Date();
    const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);
    const startOfNextMonth = new Date(now.getFullYear(), now.getMonth() + 1, 1);

    const invoiceThisMonth = rows.filter((r) => {
        const d = toDateFromIdString(r?.dateStr);
        if (!d) return false;
        return d >= startOfMonth && d < startOfNextMonth;
    }).length;

    const poPending = rows.filter((r) => {
        const hasPo = String(r?.noPo ?? '').trim().length > 0;
        return !hasPo;
    }).length;

    const invEl = root.querySelector('[data-kpi="invoice-this-month"]');
    if (invEl) invEl.textContent = String(invoiceThisMonth);

    const poEl = root.querySelector('[data-kpi="po-pending"]');
    if (poEl) poEl.textContent = String(poPending);
};

const renderInvoiceTable = () => {
    const tbody = document.querySelector('[data-invoice-rows]');
    if (!tbody) return;

    const rows = readInvoiceRows()
        .slice()
        .sort((a, b) => Number.parseInt(a.invoiceNo, 10) - Number.parseInt(b.invoiceNo, 10));

    tbody.innerHTML = '';

    if (rows.length === 0) {
        const empty = document.createElement('tr');
        empty.innerHTML =
            '<td colspan="7" class="px-4 py-6 text-center text-sm text-slate-500">Belum ada data invoice.</td>';
        tbody.appendChild(empty);
        updateInvoiceCountUI();
        return;
    }

    rows.forEach((row) => {
        tbody.appendChild(
            buildInvoiceRow({
                invoiceNo: row.invoiceNo,
                customerName: row.customerName,
                noPo: row.noPo,
                totalPo: row.totalPo,
                qty: row.qty,
                dateStr: row.dateStr,
            }),
        );
    });

    applyInvoiceSearchFilter();
    updateInvoiceCountUI();
};

const hydrateInvoiceStorageFromDOMIfNeeded = () => {
    const existing = readInvoiceRows();
    if (existing.length > 0) return;

    const domRows = Array.from(document.querySelectorAll('[data-invoice-rows] tr'));
    const hydrated = domRows
        .map((tr) => {
            const invoiceNoRaw = tr.getAttribute('data-invoice-no');
            const invoiceNo = Number.parseInt(invoiceNoRaw || '', 10);
            if (!Number.isFinite(invoiceNo)) return null;

            const tds = tr.querySelectorAll('td');
            const dateStr = (tds[0]?.textContent || '').trim();
            const customerName = (tds[2]?.textContent || '').trim();
            const noPo = (tds[3]?.textContent || '').trim();
            const totalPo = (tds[4]?.textContent || '').trim();
            const qty = (tds[5]?.textContent || '').trim();

            return {
                invoiceNo,
                customerName: customerName.length ? customerName : null,
                noPo: noPo && noPo !== '-' ? noPo : null,
                totalPo: totalPo && totalPo !== '-' ? totalPo : null,
                qty: qty && qty !== '-' ? qty : null,
                dateStr: dateStr.length ? dateStr : null,
            };
        })
        .filter(Boolean);

    if (hydrated.length > 0) {
        writeInvoiceRows(hydrated);
        writeNextInvoiceNo(getDefaultNextInvoiceNo(hydrated));
    }
};

const getSelectedCustomer = (selectEl) => {
    if (!selectEl) return null;
    const customerId = selectEl.value;
    const option = selectEl.selectedOptions && selectEl.selectedOptions[0];
    const name = option?.dataset?.name;
    if (!customerId || !name) return null;
    return { customerId, name };
};

const handleTambahInvoice = () => {
    const selectEl = document.querySelector('[data-tambah-invoice-customer]');
    const selected = getSelectedCustomer(selectEl);
    if (!selected) {
        window.alert('Pilih customer terlebih dahulu.');
        return;
    }

    const rows = readInvoiceRows();
    const nextStored = readNextInvoiceNo(rows);
    const gap = findSmallestMissingInvoiceNo(rows, 1);

    let invoiceNo;
    if (rows.length === 0) {
        invoiceNo = 1;
    } else {
        invoiceNo = gap < nextStored ? gap : nextStored;
    }

    const newRows = rows.concat([
        {
            invoiceNo,
            customerId: selected.customerId,
            customerName: selected.name,
            noPo: null,
            totalPo: null,
            qty: null,
            dateStr: formatDateId(new Date()),
        },
    ]);
    writeInvoiceRows(newRows);

    if (rows.length === 0) {
        writeNextInvoiceNo(2);
    } else if (invoiceNo === nextStored) {
        writeNextInvoiceNo(nextStored + 1);
    }

    renderInvoiceTable();
    closeModal('modal-tambah-invoice');
};

const handleAturInvoice = () => {
    const selectEl = document.querySelector('[data-atur-invoice-customer]');
    const nextEl = document.querySelector('[data-atur-invoice-next]');
    const selected = getSelectedCustomer(selectEl);
    if (!selected) {
        window.alert('Pilih customer terlebih dahulu.');
        return;
    }

    const nextValue = Number.parseInt(nextEl?.value || '', 10);
    if (!Number.isFinite(nextValue) || nextValue <= 0) {
        window.alert('No Invoice Berikutnya harus berupa angka.');
        return;
    }

    const rows = readInvoiceRows();
    const used = new Set(rows.map((r) => Number.parseInt(r?.invoiceNo, 10)).filter((n) => Number.isFinite(n)));
    if (used.has(nextValue)) {
        window.alert('No Invoice tersebut sudah dipakai.');
        return;
    }

    writeNextInvoiceNo(nextValue);

    const newRows = rows.concat([
        {
            invoiceNo: nextValue,
            customerId: selected.customerId,
            customerName: selected.name,
            noPo: null,
            totalPo: null,
            qty: null,
            dateStr: formatDateId(new Date()),
        },
    ]);
    writeInvoiceRows(newRows);
    writeNextInvoiceNo(nextValue + 1);
    renderInvoiceTable();
    closeModal('modal-atur-invoice');
};

document.addEventListener('click', (e) => {
    const addItemBtn = e.target.closest('[data-po-add-item]');
    if (addItemBtn) {
        const tbody = document.querySelector('[data-po-items]');
        if (tbody) {
            tbody.appendChild(buildPoItemRow({}));
            recalcPoTotals();
        }
        return;
    }

    const removeItemBtn = e.target.closest('[data-po-remove-item]');
    if (removeItemBtn) {
        const row = removeItemBtn.closest('[data-po-item-row]');
        const tbody = document.querySelector('[data-po-items]');
        if (row && tbody) {
            row.remove();
            if (tbody.querySelectorAll('[data-po-item-row]').length === 0) {
                tbody.appendChild(buildPoItemRow({}));
            }
            recalcPoTotals();
        }
        return;
    }

    const editCustomerBtn = e.target.closest('[data-action="edit-customer"]');
    if (editCustomerBtn) {
        const id = editCustomerBtn.getAttribute('data-customer-id');
        const name = editCustomerBtn.getAttribute('data-customer-name') || '';
        const email = editCustomerBtn.getAttribute('data-customer-email') || '';
        const phone = editCustomerBtn.getAttribute('data-customer-phone') || '';
        const address = editCustomerBtn.getAttribute('data-customer-address') || '';
        const isActive = editCustomerBtn.getAttribute('data-customer-active') || '0';

        const form = document.querySelector('[data-edit-customer-form]');
        if (form) {
            const template = form.getAttribute('data-action-template') || '';
            if (template.includes('__ID__') && id) {
                form.setAttribute('action', template.replace('__ID__', id));
            }
        }

        const nameEl = document.querySelector('[data-edit-customer-name]');
        const emailEl = document.querySelector('[data-edit-customer-email]');
        const phoneEl = document.querySelector('[data-edit-customer-phone]');
        const addressEl = document.querySelector('[data-edit-customer-address]');
        const activeEl = document.querySelector('[data-edit-customer-active]');

        if (nameEl) nameEl.value = name;
        if (emailEl) emailEl.value = email;
        if (phoneEl) phoneEl.value = phone;
        if (addressEl) addressEl.value = address;
        if (activeEl) activeEl.checked = isActive === '1';

        openModal('modal-edit-customer');
        return;
    }

    const editSupplierBtn = e.target.closest('[data-action="edit-supplier"]');
    if (editSupplierBtn) {
        const id = editSupplierBtn.getAttribute('data-supplier-id');
        const name = editSupplierBtn.getAttribute('data-supplier-name') || '';
        const contact = editSupplierBtn.getAttribute('data-supplier-contact') || '';
        const phone = editSupplierBtn.getAttribute('data-supplier-phone') || '';
        const email = editSupplierBtn.getAttribute('data-supplier-email') || '';
        const address = editSupplierBtn.getAttribute('data-supplier-address') || '';
        const isActive = editSupplierBtn.getAttribute('data-supplier-active') || '0';

        const form = document.querySelector('[data-edit-supplier-form]');
        if (form) {
            const template = form.getAttribute('data-action-template') || '';
            if (template.includes('__ID__') && id) {
                form.setAttribute('action', template.replace('__ID__', id));
            }
        }

        const nameEl = document.querySelector('[data-edit-supplier-name]');
        const contactEl = document.querySelector('[data-edit-supplier-contact]');
        const phoneEl = document.querySelector('[data-edit-supplier-phone]');
        const emailEl = document.querySelector('[data-edit-supplier-email]');
        const addressEl = document.querySelector('[data-edit-supplier-address]');
        const activeEl = document.querySelector('[data-edit-supplier-active]');

        if (nameEl) nameEl.value = name;
        if (contactEl) contactEl.value = contact;
        if (phoneEl) phoneEl.value = phone;
        if (emailEl) emailEl.value = email;
        if (addressEl) addressEl.value = address;
        if (activeEl) activeEl.checked = isActive === '1';

        openModal('modal-edit-supplier');
        return;
    }

    const editItemBtn = e.target.closest('[data-action="edit-item"]');
    if (editItemBtn) {
        const id = editItemBtn.getAttribute('data-item-id');
        const sku = editItemBtn.getAttribute('data-item-sku') || '';
        const itemType = editItemBtn.getAttribute('data-item-type') || '';
        const supplierId = editItemBtn.getAttribute('data-item-supplier-id') || '';
        const name = editItemBtn.getAttribute('data-item-name') || '';
        const unit = editItemBtn.getAttribute('data-item-unit') || '';
        const price = editItemBtn.getAttribute('data-item-price') || '0';
        const isActive = editItemBtn.getAttribute('data-item-active') || '0';

        const form = document.querySelector('[data-edit-item-form]');
        if (form) {
            const template = form.getAttribute('data-action-template') || '';
            if (template.includes('__ID__') && id) {
                form.setAttribute('action', template.replace('__ID__', id));
            }
        }

        const skuEl = document.querySelector('[data-edit-item-sku]');
        const itemTypeEl = document.querySelector('[data-edit-item-type]');
        const supplierEl = document.querySelector('[data-edit-item-supplier]');
        const nameEl = document.querySelector('[data-edit-item-name]');
        const unitEl = document.querySelector('[data-edit-item-unit]');
        const priceEl = document.querySelector('[data-edit-item-price]');
        const activeEl = document.querySelector('[data-edit-item-active]');

        if (skuEl) skuEl.value = sku;
        if (itemTypeEl) itemTypeEl.value = itemType;
        if (supplierEl) supplierEl.value = supplierId;
        if (nameEl) nameEl.value = name;
        if (unitEl) unitEl.value = unit;
        if (priceEl) priceEl.value = price;
        if (activeEl) activeEl.checked = isActive === '1';

        const openSupplierEl = document.querySelector('#modal-edit-item [data-open-supplier-id]');
        if (openSupplierEl) openSupplierEl.value = supplierId;

        openModal('modal-edit-item');
        return;
    }

    const editItemInBtn = e.target.closest('[data-action="edit-item-in"]');
    if (editItemInBtn) {
        const id = editItemInBtn.getAttribute('data-item-in-id');
        const supplierId = editItemInBtn.getAttribute('data-item-in-supplier-id') || '';
        const itemId = editItemInBtn.getAttribute('data-item-in-item-id') || '';
        const qty = editItemInBtn.getAttribute('data-item-in-qty') || '1';
        const date = editItemInBtn.getAttribute('data-item-in-date') || '';

        const form = document.querySelector('[data-edit-item-in-form]');
        if (form) {
            const template = form.getAttribute('data-action-template') || '';
            if (template.includes('__ID__') && id) {
                form.setAttribute('action', template.replace('__ID__', id));
            }
        }

        const supplierEl = document.querySelector('[data-edit-item-in-supplier]');
        const itemEl = document.querySelector('[data-edit-item-in-item]');
        const qtyEl = document.querySelector('[data-edit-item-in-qty]');
        const dateEl = document.querySelector('[data-edit-item-in-date]');

        if (supplierEl) supplierEl.value = supplierId;
        if (itemEl) itemEl.value = itemId;
        if (qtyEl) qtyEl.value = qty;
        if (dateEl) dateEl.value = date;

        openModal('modal-edit-item-in');
        return;
    }

    const editItemOutBtn = e.target.closest('[data-action="edit-item-out"]');
    if (editItemOutBtn) {
        const id = editItemOutBtn.getAttribute('data-item-out-id');
        const customerId = editItemOutBtn.getAttribute('data-item-out-customer-id') || '';
        const itemId = editItemOutBtn.getAttribute('data-item-out-item-id') || '';
        const qty = editItemOutBtn.getAttribute('data-item-out-qty') || '1';
        const date = editItemOutBtn.getAttribute('data-item-out-date') || '';

        const form = document.querySelector('[data-edit-item-out-form]');
        if (form) {
            const template = form.getAttribute('data-action-template') || '';
            if (template.includes('__ID__') && id) {
                form.setAttribute('action', template.replace('__ID__', id));
            }
        }

        const customerEl = document.querySelector('[data-edit-item-out-customer]');
        const itemEl = document.querySelector('[data-edit-item-out-item]');
        const qtyEl = document.querySelector('[data-edit-item-out-qty]');
        const dateEl = document.querySelector('[data-edit-item-out-date]');

        if (customerEl) customerEl.value = customerId;
        if (itemEl) itemEl.value = itemId;
        if (qtyEl) qtyEl.value = qty;
        if (dateEl) dateEl.value = date;

        openModal('modal-edit-item-out');
        return;
    }

    const tambahBtn = e.target.closest('#btn-tambah-invoice-lanjut');
    if (tambahBtn) {
        handleTambahInvoice();
        return;
    }

    const aturBtn = e.target.closest('#btn-atur-invoice-lanjut');
    if (aturBtn) {
        handleAturInvoice();
        return;
    }

    const deleteBtn = e.target.closest('[data-action="delete-invoice-row"]');
    if (deleteBtn) {
        const row = deleteBtn.closest('tr');
        const invoiceNo = Number.parseInt(row?.getAttribute('data-invoice-no') || '', 10);
        if (Number.isFinite(invoiceNo)) {
            const rows = readInvoiceRows();
            const filtered = rows.filter((r) => Number.parseInt(r?.invoiceNo, 10) !== invoiceNo);
            writeInvoiceRows(filtered);
            if (filtered.length === 0) {
                writeNextInvoiceNo(1);
            }
            renderInvoiceTable();
        }
        return;
    }

    const editBtn = e.target.closest('[data-action="edit-invoice-row"]');
    if (editBtn) {
        const row = editBtn.closest('tr');
        const invoiceNo = Number.parseInt(row?.getAttribute('data-invoice-no') || '', 10);
        if (Number.isFinite(invoiceNo)) {
            navigateToInputPo(invoiceNo);
        }
        return;
    }

    const saveBtn = e.target.closest('[data-po-save]');
    if (saveBtn) {
        savePoPage();
        return;
    }
});

document.addEventListener('dblclick', (e) => {
    const actionBtn = e.target.closest('[data-action]');
    if (actionBtn) return;

    const row = e.target.closest('[data-invoice-rows] tr');
    if (!row) return;
    const invoiceNo = Number.parseInt(row.getAttribute('data-invoice-no') || '', 10);
    if (!Number.isFinite(invoiceNo)) return;
    navigateToInputPo(invoiceNo);
});

document.addEventListener('input', (e) => {
    const search = e.target.closest('[data-invoice-search]');
    if (!search) return;
    applyInvoiceSearchFilter();
});

document.addEventListener('input', (e) => {
    const inPoItems = e.target.closest('[data-po-items]');
    if (!inPoItems) return;
    if (e.target.closest('[data-po-item-qty]') || e.target.closest('[data-po-item-price]')) {
        recalcPoTotals();
    }
});

hydrateInvoiceStorageFromDOMIfNeeded();
renderInvoiceTable();

hydratePoPageFromQuery();

updateDashboardKpis();

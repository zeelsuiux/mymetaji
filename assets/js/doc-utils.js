/* ============================================
   MY METAJI - SHARED DOCUMENT UTILITIES
   Used by the document format pages:
   invoice-format, quotation-format, receipt-format,
   bill-book-format, estimate-format, credit-note,
   debit-note, delivery-challan, balance-sheet, work-order
   ============================================ */
(function (global) {
    'use strict';

    var RUPEE = '\u20B9';

    var STATES = [
        'Andhra Pradesh', 'Arunachal Pradesh', 'Assam', 'Bihar', 'Chhattisgarh', 'Goa', 'Gujarat',
        'Haryana', 'Himachal Pradesh', 'Jharkhand', 'Karnataka', 'Kerala', 'Madhya Pradesh',
        'Maharashtra', 'Manipur', 'Meghalaya', 'Mizoram', 'Nagaland', 'Odisha', 'Punjab',
        'Rajasthan', 'Sikkim', 'Tamil Nadu', 'Telangana', 'Tripura', 'Uttar Pradesh', 'Uttarakhand',
        'West Bengal', 'Andaman & Nicobar Islands', 'Chandigarh', 'Dadra & Nagar Haveli and Daman & Diu',
        'Delhi', 'Jammu & Kashmir', 'Ladakh', 'Lakshadweep', 'Puducherry'
    ];

    var STATE_CODES = {
        'Andhra Pradesh': '37', 'Arunachal Pradesh': '12', 'Assam': '18', 'Bihar': '10',
        'Chhattisgarh': '27', 'Goa': '07', 'Gujarat': '24', 'Haryana': '06', 'Himachal Pradesh': '02',
        'Jharkhand': '20', 'Karnataka': '29', 'Kerala': '32', 'Madhya Pradesh': '23',
        'Maharashtra': '27', 'Manipur': '14', 'Meghalaya': '01', 'Mizoram': '15', 'Nagaland': '13',
        'Odisha': '21', 'Puducherry': '34', 'Punjab': '03', 'Rajasthan': '08', 'Sikkim': '11',
        'Tamil Nadu': '33', 'Telangana': '36', 'Tripura': '14', 'Uttar Pradesh': '09',
        'Uttarakhand': '05', 'West Bengal': '19', 'Delhi': '07', 'Jammu & Kashmir': '01',
        'Ladakh': '38', 'Chandigarh': '04', 'Lakshadweep': '31', 'Andaman & Nicobar Islands': '35',
        'Dadra & Nagar Haveli and Daman & Diu': '26'
    };

    var GST_RATES = ['0', '0.25', '3', '5', '12', '18', '28'];

    var ACCENTS = {
        blue: '#2563EB', indigo: '#4F46E5', green: '#059669', teal: '#0D9488',
        amber: '#D97706', red: '#DC2626', slate: '#334155', violet: '#7C3AED'
    };

    /* ---------- number helpers ---------- */

    function numToWords(n) {
        n = Math.abs(Number(n) || 0);
        if (n === 0) return 'Zero';
        var o = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten',
            'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        var t = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
        function c(x) {
            if (x < 20) return o[x];
            if (x < 100) return t[Math.floor(x / 10)] + (x % 10 ? ' ' + o[x % 10] : '');
            if (x < 1000) return o[Math.floor(x / 100)] + ' Hundred' + (x % 100 ? ' and ' + c(x % 100) : '');
            if (x < 100000) return c(Math.floor(x / 1000)) + ' Thousand' + (x % 1000 ? ' ' + c(x % 1000) : '');
            if (x < 10000000) return c(Math.floor(x / 100000)) + ' Lakh' + (x % 100000 ? ' ' + c(x % 100000) : '');
            return c(Math.floor(x / 10000000)) + ' Crore' + (x % 10000000 ? ' ' + c(x % 10000000) : '');
        }
        var i = Math.floor(n);
        var d = Math.round((n - i) * 100);
        var r = c(i);
        if (d > 0) r += ' and ' + c(d) + ' Paise';
        return r;
    }

    function inWords(n) { return numToWords(n) + ' Only'; }

    function money(n, dp) {
        var v = Number(n) || 0;
        return RUPEE + v.toFixed(dp === undefined ? 2 : dp);
    }

    function qty(n) {
        var v = Number(n) || 0;
        return Number.isInteger(v) ? String(v) : v.toFixed(2).replace(/0+$/, '').replace(/\.$/, '');
    }

    function round2(n) { return Math.round((Number(n) || 0) * 100) / 100; }

    /* ---------- date helpers ---------- */

    function todayISO() { return new Date().toISOString().slice(0, 10); }

    function addDaysISO(days) {
        var d = new Date();
        d.setDate(d.getDate() + (Number(days) || 0));
        return d.toISOString().slice(0, 10);
    }

    function fmtDate(iso) {
        if (!iso) return '';
        var m = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        var p = String(iso).split('-');
        if (p.length !== 3) return iso;
        var d = parseInt(p[2], 10);
        if (!d) return iso;
        return d + ' ' + m[parseInt(p[1], 10) - 1] + ' ' + p[0];
    }

    /* ---------- DOM helpers ---------- */

    function el(id) { return document.getElementById(id); }

    function val(id) {
        var e = el(id);
        if (!e) return '';
        return e.type === 'checkbox' ? e.checked : e.value;
    }

    function num(id, dflt) {
        var v = parseFloat(val(id));
        return isNaN(v) ? (dflt === undefined ? 0 : dflt) : v;
    }

    function text(id, v) {
        var e = el(id);
        if (e) e.textContent = v === undefined || v === null || v === '' ? '\u2014' : v;
        return e;
    }

    function html(id, v) { var e = el(id); if (e) e.innerHTML = v || ''; return e; }

    /** Show element only when value is non-empty. */
    function showIf(id, cond) {
        var e = el(id);
        if (e) e.style.display = cond ? '' : 'none';
    }

    function setToggle(elId, on) { var e = el(elId); if (e) e.checked = !!on; }

    /** Re-render on any input/change inside the given field ids. */
    function bind(ids, fn) {
        ids.forEach(function (id) {
            var e = el(id);
            if (!e) return;
            e.addEventListener('input', fn);
            e.addEventListener('change', fn);
        });
    }

    function optionsHTML(list, placeholder) {
        var h = '<option value="">' + (placeholder || 'Select') + '</option>';
        list.forEach(function (s) { h += '<option>' + s + '</option>'; });
        return h;
    }

    /** Populate a <select> with all Indian states. */
    function fillStates(id, placeholder) {
        var e = el(id);
        if (e && !e.dataset.filled) { e.innerHTML = optionsHTML(STATES, placeholder); e.dataset.filled = '1'; }
    }

    /** Populate a GST-rate <select>. */
    function fillRates(id) {
        var e = el(id);
        if (!e || e.dataset.filled) return;
        var h = '';
        GST_RATES.forEach(function (r) { h += '<option value="' + r + '"' + (r === '18' ? ' selected' : '') + '>' + r + '%</option>'; });
        e.innerHTML = h;
        e.dataset.filled = '1';
    }

    /* ---------- accent theme (applies --fmt-accent to :root) ---------- */

    function setAccent(key) {
        var hex = ACCENTS[key] || ACCENTS.blue;
        document.documentElement.style.setProperty('--fmt-accent', hex);
    }

    function bindSwatches(btnAttr, onPick) {
        var btns = document.querySelectorAll('[' + btnAttr + ']');
        Array.prototype.forEach.call(btns, function (b) {
            b.style.setProperty('--sw', ACCENTS[b.getAttribute(btnAttr)] || ACCENTS.blue);
            b.addEventListener('click', function () {
                Array.prototype.forEach.call(btns, function (x) { x.classList.remove('active'); });
                b.classList.add('active');
                setAccent(b.getAttribute(btnAttr));
                if (onPick) onPick();
            });
        });
    }

    /** ISO state code for GST place-of-supply. */
    function stateCode(name) { return STATE_CODES[name] || ''; }

    /** true when seller and buyer states differ -> IGST applies */
    function isInterState(sellerStateId, buyerStateId) {
        var s = val(sellerStateId), b = val(buyerStateId);
        if (!s || !b) return false;
        return s !== b;
    }

    /* ---------- PDF helpers (require jsPDF + autoTable on window) ---------- */

    function newDoc() {
        var Ctor = global.jspdf && global.jspdf.jsPDF;
        if (!Ctor) { alert('PDF library is still loading. Please try again in a moment.'); return null; }
        return new Ctor('p', 'mm', 'a4');
    }

    /**
     * Draws the standard branded header band.
     * opts: { title, accent:[r,g,b], left:[{label,value}...], right:[{label,value}...] }
     * Returns the y cursor after the band.
     */
    function pdfHeader(doc, opts) {
        opts = opts || {};
        var w = doc.internal.pageSize.getWidth();
        var mg = opts.margin === undefined ? 14 : opts.margin;
        var accent = opts.accent || [37, 99, 235];
        var h = opts.height || 40;

        doc.setFillColor(accent[0], accent[1], accent[2]);
        doc.rect(0, 0, w, h, 'F');
        doc.setTextColor(255, 255, 255);

        var y = 15;
        doc.setFontSize(opts.orgSize || 17);
        doc.setFont('helvetica', 'bold');
        doc.text(opts.org || 'Your Business', mg, y);

        doc.setFont('helvetica', 'normal');
        doc.setFontSize(8.5);
        (opts.left || []).forEach(function (row) {
            y += 5.2;
            if (y > h - 2) return;
            doc.text(row.value || '', mg, y);
        });

        doc.setFont('helvetica', 'bold');
        doc.setFontSize(opts.titleSize || 13.5);
        doc.text(opts.title || 'DOCUMENT', w - mg, 15, { align: 'right' });

        doc.setFont('helvetica', 'normal');
        doc.setFontSize(8.5);
        var ry = 15;
        (opts.right || []).forEach(function (row) {
            ry += 5.2;
            if (ry > h - 2) return;
            doc.text(row.label + ': ' + (row.value || '\u2014'), w - mg, ry, { align: 'right' });
        });

        return h + 7;
    }

    /** Grey divider + "generated by" footer. */
    function pdfFooter(doc, y, note) {
        var w = doc.internal.pageSize.getWidth();
        var h = doc.internal.pageSize.getHeight();
        if (y > h - 18) { doc.addPage(); y = 18; }
        doc.setDrawColor(208, 213, 221);
        doc.setLineWidth(0.3);
        doc.line(14, y, w - 14, y);
        doc.setFontSize(7);
        doc.setTextColor(130, 138, 145);
        doc.setFont('helvetica', 'normal');
        var t = (note ? note + '  \u00B7  ' : '') + 'Generated by My Metaji \u2014 Free GST Billing Software';
        doc.text(t, w / 2, y + 5, { align: 'center' });
        return y + 5;
    }

    /** Right-aligned label/value list used for totals blocks. */
    function pdfTotals(doc, xRight, y, rows, opts) {
        opts = opts || {};
        var w = doc.internal.pageSize.getWidth();
        var labelX = opts.labelX || (w - 14 - 52);
        doc.setFontSize(opts.size || 8.5);
        rows.forEach(function (r) {
            if (!r) return;
            if (r.rule) {
                doc.setDrawColor(203, 213, 225);
                doc.setLineWidth(0.3);
                doc.line(labelX, y - 1.5, w - 14, y - 1.5);
                y += r.gap || 2;
            }
            if (r.label === undefined || r.value === undefined) return;
            if (r.bold) doc.setFont('helvetica', 'bold'); else doc.setFont('helvetica', 'normal');
            doc.setFontSize(r.size || opts.size || 8.5);
            doc.setTextColor.apply(doc, r.color || [51, 65, 85]);
            doc.text(String(r.label), labelX, y);
            doc.text(String(r.value), w - 14, y, { align: 'right' });
            y += r.gap || 5;
        });
        doc.setFont('helvetica', 'normal');
        doc.setTextColor(51, 65, 85);
        return y;
    }

    /** AutoTable wrapper with consistent branding. */
    function pdfTable(doc, head, body, opts) {
        opts = opts || {};
        var accent = opts.accent || [37, 99, 235];
        doc.autoTable(Object.assign({
            startY: opts.startY,
            head: [head],
            body: body,
            theme: opts.theme || 'grid',
            styles: { fontSize: 7.6, cellPadding: 2, lineColor: [226, 232, 240], lineWidth: 0.2 },
            headStyles: { fillColor: accent, textColor: 255, fontStyle: 'bold', fontSize: 7.4 },
            alternateRowStyles: { fillColor: [248, 250, 252] },
            margin: { left: 14, right: 14, top: opts.top === undefined ? 14 : opts.top }
        }, opts.extra || {}));
        return doc.lastAutoTable.finalY;
    }

    /** Write a small section label. */
    function pdfSection(doc, x, y, label, width) {
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(8.5);
        doc.setTextColor(30, 41, 59);
        doc.text(label, x, y);
        var tw = doc.getTextWidth(label);
        doc.setDrawColor(226, 232, 240);
        doc.setLineWidth(0.3);
        doc.line(x, y + 1.6, x + (width || 60), y + 1.6);
        return y + 5;
    }

    function rgb(hex) {
        var h = String(hex || '').replace('#', '');
        if (h.length === 3) h = h[0] + h[0] + h[1] + h[1] + h[2] + h[2];
        var n = parseInt(h, 16);
        if (isNaN(n)) return [37, 99, 235];
        return [(n >> 16) & 255, (n >> 8) & 255, n & 255];
    }

    function currentAccentRGB() {
        return rgb(getComputedStyle(document.documentElement).getPropertyValue('--fmt-accent').trim() || '#2563EB');
    }

    /* ---------- misc UI ---------- */

    function scrollToPreview(id) {
        var e = el(id);
        if (e) e.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    /** Generic dynamic item-row manager used by the item-based documents. */
    function ItemList(cfg) {
        this.wrap = el(cfg.wrap);
        this.rowClass = cfg.rowClass || 'doc-item';
        this.tpl = cfg.tpl;
        this.read = cfg.read;
        this.onChange = cfg.onChange || function () { };
        this.index = 0;
    }
    ItemList.prototype.add = function (seed) {
        if (!this.wrap) return null;
        var id = this.index++;
        this.wrap.insertAdjacentHTML('beforeend', this.tpl(id, seed || {}));
        this.onChange();
        return id;
    };
    ItemList.prototype.remove = function (id) {
        var e = document.querySelector('[data-row="' + id + '"]');
        if (e) e.remove();
        this.onChange();
    };
    ItemList.prototype.each = function (fn) {
        if (!this.wrap) return;
        Array.prototype.forEach.call(this.wrap.querySelectorAll('.' + this.rowClass), fn);
    };

    global.DOC = {
        RUPEE: RUPEE,
        STATES: STATES,
        STATE_CODES: STATE_CODES,
        GST_RATES: GST_RATES,
        ACCENTS: ACCENTS,
        numToWords: numToWords,
        inWords: inWords,
        money: money,
        qty: qty,
        round2: round2,
        todayISO: todayISO,
        addDaysISO: addDaysISO,
        fmtDate: fmtDate,
        el: el,
        val: val,
        num: num,
        text: text,
        html: html,
        showIf: showIf,
        setToggle: setToggle,
        bind: bind,
        optionsHTML: optionsHTML,
        fillStates: fillStates,
        fillRates: fillRates,
        setAccent: setAccent,
        bindSwatches: bindSwatches,
        stateCode: stateCode,
        isInterState: isInterState,
        newDoc: newDoc,
        pdfHeader: pdfHeader,
        pdfFooter: pdfFooter,
        pdfTotals: pdfTotals,
        pdfTable: pdfTable,
        pdfSection: pdfSection,
        rgb: rgb,
        currentAccentRGB: currentAccentRGB,
        scrollToPreview: scrollToPreview,
        ItemList: ItemList
    };
})(window);

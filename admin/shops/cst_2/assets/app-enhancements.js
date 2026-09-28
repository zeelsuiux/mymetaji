/**
 * Mymetaji - App Enhancements
 * Quick Invoice, Autocomplete, Keyboard Shortcuts, Notifications, Bulk Operations
 */

(function () {
  'use strict';

  /* ============================================================
   * 1. AUTOCOMPLETE SYSTEM
   * ============================================================ */
  window.MMAutocomplete = function (input, options) {
    this.input = input;
    this.options = Object.assign({
      data: [],
      key: 'name',
      subKey: '',
      onSelect: null,
      placeholder: 'Type to search...',
      minChars: 1,
      maxResults: 8,
    }, options || {});
    this.isOpen = false;
    this.selectedIndex = -1;
    this.filtered = [];
    this.dropdown = null;
    this.init();
  };

  MMAutocomplete.prototype.init = function () {
    this.input.setAttribute('autocomplete', 'off');
    this.input.setAttribute('role', 'combobox');
    this.input.setAttribute('aria-autocomplete', 'list');

    // Create dropdown
    this.dropdown = document.createElement('div');
    this.dropdown.className = 'mm-autocomplete-dropdown';
    this.dropdown.style.display = 'none';
    this.input.parentNode.style.position = 'relative';
    this.input.parentNode.appendChild(this.dropdown);

    // Events
    this.input.addEventListener('input', this.onInput.bind(this));
    this.input.addEventListener('keydown', this.onKeydown.bind(this));
    this.input.addEventListener('focus', this.onInput.bind(this));
    document.addEventListener('click', (e) => {
      if (!this.input.parentNode.contains(e.target)) this.close();
    });
  };

  MMAutocomplete.prototype.onInput = function () {
    var query = this.input.value.trim().toLowerCase();
    if (query.length < this.options.minChars) { this.close(); return; }

    this.filtered = this.options.data.filter(function (item) {
      var name = (item[this.options.key] || '').toLowerCase();
      var sub = this.options.subKey ? (item[this.options.subKey] || '').toLowerCase() : '';
      return name.indexOf(query) !== -1 || sub.indexOf(query) !== -1;
    }.bind(this)).slice(0, this.options.maxResults);

    this.selectedIndex = -1;
    this.render();
  };

  MMAutocomplete.prototype.onKeydown = function (e) {
    if (!this.isOpen) return;
    var items = this.dropdown.querySelectorAll('.mm-ac-item');
    if (e.key === 'ArrowDown') {
      e.preventDefault();
      this.selectedIndex = Math.min(this.selectedIndex + 1, items.length - 1);
      this.highlight();
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      this.selectedIndex = Math.max(this.selectedIndex - 1, 0);
      this.highlight();
    } else if (e.key === 'Enter') {
      e.preventDefault();
      if (this.selectedIndex >= 0 && items[this.selectedIndex]) {
        this.selectItem(this.filtered[this.selectedIndex]);
      }
    } else if (e.key === 'Escape') {
      this.close();
    }
  };

  MMAutocomplete.prototype.highlight = function () {
    var items = this.dropdown.querySelectorAll('.mm-ac-item');
    items.forEach(function (item, i) {
      item.classList.toggle('active', i === this.selectedIndex);
    }.bind(this));
  };

  MMAutocomplete.prototype.selectItem = function (item) {
    this.input.value = item[this.options.key] || '';
    this.close();
    if (this.options.onSelect) this.options.onSelect(item, this.input);
  };

  MMAutocomplete.prototype.render = function () {
    if (this.filtered.length === 0) { this.close(); return; }
    var html = '';
    this.filtered.forEach(function (item, i) {
      var label = item[this.options.key] || '';
      var sub = this.options.subKey ? item[this.options.subKey] : '';
      html += '<div class="mm-ac-item" data-index="' + i + '">';
      html += '<span class="mm-ac-label">' + this.escapeHtml(label) + '</span>';
      if (sub) html += '<span class="mm-ac-sub">' + this.escapeHtml(sub) + '</span>';
      html += '</div>';
    }.bind(this));
    this.dropdown.innerHTML = html;
    this.dropdown.style.display = 'block';
    this.isOpen = true;

    // Click handlers
    this.dropdown.querySelectorAll('.mm-ac-item').forEach(function (el) {
      el.addEventListener('click', function () {
        var idx = parseInt(el.dataset.index);
        this.selectItem(this.filtered[idx]);
      }.bind(this));
    }.bind(this));
  };

  MMAutocomplete.prototype.close = function () {
    this.dropdown.style.display = 'none';
    this.isOpen = false;
    this.selectedIndex = -1;
  };

  MMAutocomplete.prototype.escapeHtml = function (str) {
    var div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
  };

  /* ============================================================
   * 2. NOTIFICATION TOAST SYSTEM
   * ============================================================ */
  window.MMToast = {
    container: null,
    init: function () {
      if (this.container) return;
      this.container = document.createElement('div');
      this.container.className = 'mm-toast-container';
      document.body.appendChild(this.container);
    },
    show: function (message, type, duration) {
      this.init();
      type = type || 'info';
      duration = duration || 3000;
      var toast = document.createElement('div');
      toast.className = 'mm-toast mm-toast-' + type;
      var icon = type === 'success' ? '✓' : type === 'error' ? '✕' : type === 'warning' ? '⚠' : 'ℹ';
      toast.innerHTML = '<span class="mm-toast-icon">' + icon + '</span><span class="mm-toast-msg">' + message + '</span>';
      this.container.appendChild(toast);
      setTimeout(function () { toast.classList.add('show'); }, 10);
      setTimeout(function () {
        toast.classList.remove('show');
        setTimeout(function () { toast.remove(); }, 300);
      }, duration);
    },
    success: function (msg, dur) { this.show(msg, 'success', dur); },
    error: function (msg, dur) { this.show(msg, 'error', dur); },
    warning: function (msg, dur) { this.show(msg, 'warning', dur); },
    info: function (msg, dur) { this.show(msg, 'info', dur); },
  };

  /* ============================================================
   * 3. KEYBOARD SHORTCUTS SYSTEM
   * ============================================================ */
  window.MMShortcuts = {
    shortcuts: {},
    helpVisible: false,
    register: function (key, callback, label) {
      this.shortcuts[key.toLowerCase()] = { callback: callback, label: label };
    },
    init: function () {
      document.addEventListener('keydown', function (e) {
        // Don't trigger in input fields unless it's Ctrl combo
        var tag = e.target.tagName;
        var isInput = tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT' || e.target.isContentEditable;

        var key = '';
        if (e.ctrlKey || e.metaKey) key += 'ctrl+';
        if (e.shiftKey) key += 'shift+';
        if (e.altKey) key += 'alt+';
        key += e.key.toLowerCase();

        var shortcut = this.shortcuts[key];
        if (shortcut) {
          // Allow Ctrl shortcuts in inputs
          if (isInput && !e.ctrlKey && !e.metaKey) return;
          e.preventDefault();
          shortcut.callback();
        }

        // ? for help (only when not in input)
        if (e.key === '?' && !isInput) {
          e.preventDefault();
          this.toggleHelp();
        }
      }.bind(this));
    },
    toggleHelp: function () {
      var existing = document.getElementById('mm-shortcut-help');
      if (existing) { existing.remove(); return; }

      var modal = document.createElement('div');
      modal.id = 'mm-shortcut-help';
      modal.className = 'mm-shortcut-modal';
      var html = '<div class="mm-shortcut-dialog"><div class="mm-shortcut-header"><h3>⌨️ Keyboard Shortcuts</h3><button class="mm-shortcut-close" onclick="this.closest(\'.mm-shortcut-modal\').remove()">✕</button></div><div class="mm-shortcut-list">';
      for (var key in this.shortcuts) {
        var display = key.replace('ctrl+', 'Ctrl+').replace('shift+', 'Shift+').replace('alt+', 'Alt+');
        html += '<div class="mm-shortcut-row"><kbd>' + display + '</kbd><span>' + this.shortcuts[key].label + '</span></div>';
      }
      html += '<div class="mm-shortcut-row"><kbd>?</kbd><span>Show this help</span></div>';
      html += '</div></div>';
      modal.innerHTML = html;
      modal.addEventListener('click', function (e) { if (e.target === modal) modal.remove(); });
      document.body.appendChild(modal);
    }
  };

  /* ============================================================
   * 4. BULK SELECTION SYSTEM
   * ============================================================ */
  window.MMBulk = {
    selected: new Set(),
    toolbar: null,
    init: function () {
      var table = document.querySelector('.data-table');
      if (!table) return;

      // Add select-all checkbox to header
      var thead = table.querySelector('thead tr');
      if (thead) {
        var th = document.createElement('th');
        th.className = 'bulk-th';
        th.innerHTML = '<input type="checkbox" id="mm-select-all" class="mm-bulk-checkbox">';
        thead.insertBefore(th, thead.firstChild);

        // Add checkboxes to rows
        var tbody = table.querySelector('tbody');
        if (tbody) {
          tbody.querySelectorAll('tr').forEach(function (row) {
            var id = row.dataset.id || row.querySelector('[data-id]')?.dataset.id || '';
            var td = document.createElement('td');
            td.className = 'bulk-td';
            td.innerHTML = '<input type="checkbox" class="mm-bulk-checkbox" data-id="' + id + '">';
            row.insertBefore(td, row.firstChild);
          });
        }

        // Select all handler
        document.getElementById('mm-select-all')?.addEventListener('change', function (e) {
          var checked = e.target.checked;
          table.querySelectorAll('.mm-bulk-checkbox[data-id]').forEach(function (cb) {
            cb.checked = checked;
            var id = cb.dataset.id;
            if (checked) MMBulk.selected.add(id); else MMBulk.selected.delete(id);
          });
          MMBulk.updateToolbar();
        });

        // Individual checkbox handlers
        table.querySelectorAll('.mm-bulk-checkbox[data-id]').forEach(function (cb) {
          cb.addEventListener('change', function () {
            if (cb.checked) MMBulk.selected.add(cb.dataset.id); else MMBulk.selected.delete(cb.dataset.id);
            MMBulk.updateToolbar();
          });
        });
      }

      // Create toolbar
      this.toolbar = document.createElement('div');
      this.toolbar.className = 'mm-bulk-toolbar';
      this.toolbar.style.display = 'none';
      this.toolbar.innerHTML = '<span class="mm-bulk-count">0 selected</span>' +
        '<button class="btn btn-outline btn-sm" id="mm-bulk-print">🖨️ Print</button>' +
        '<button class="btn btn-outline btn-sm" id="mm-bulk-export">📥 Export</button>' +
        '<button class="btn btn-danger btn-sm" id="mm-bulk-delete">🗑️ Delete</button>';
      document.querySelector('.content')?.prepend(this.toolbar);

      // Bulk actions
      document.getElementById('mm-bulk-print')?.addEventListener('click', function () { MMBulk.printSelected(); });
      document.getElementById('mm-bulk-export')?.addEventListener('click', function () { MMBulk.exportSelected(); });
      document.getElementById('mm-bulk-delete')?.addEventListener('click', function () { MMBulk.deleteSelected(); });
    },
    updateToolbar: function () {
      if (!this.toolbar) return;
      var count = this.selected.size;
      if (count > 0) {
        this.toolbar.style.display = 'flex';
        this.toolbar.querySelector('.mm-bulk-count').textContent = count + ' selected';
      } else {
        this.toolbar.style.display = 'none';
      }
    },
    printSelected: function () {
      var ids = Array.from(this.selected);
      if (ids.length === 0) { MMToast.warning('No records selected'); return; }
      // Get current module from URL
      var params = new URLSearchParams(window.location.search);
      var module = params.get('m') || 'invoices';
      if (module === 'invoices') {
        ids.forEach(function (id) { window.open('invoice_print.php?id=' + id, '_blank'); });
      } else if (module === 'quotations') {
        ids.forEach(function (id) { window.open('quotation_print.php?id=' + id, '_blank'); });
      } else {
        MMToast.info('Print available for invoices and quotations');
      }
    },
    exportSelected: function () {
      MMToast.info('Exporting selected records...');
      // Trigger export with selected IDs
      var params = new URLSearchParams(window.location.search);
      var module = params.get('m') || '';
      window.location.href = 'export.php?module=' + module + '&download=1&ids=' + Array.from(this.selected).join(',');
    },
    deleteSelected: function () {
      var ids = Array.from(this.selected);
      if (ids.length === 0) { MMToast.warning('No records selected'); return; }
      if (!confirm('Delete ' + ids.length + ' selected records? This cannot be undone.')) return;
      var params = new URLSearchParams(window.location.search);
      var module = params.get('m') || '';
      var deleted = 0;
      ids.forEach(function (id) {
        fetch('module.php?m=' + module + '&action=delete&id=' + id, { method: 'GET' })
          .then(function () { deleted++; if (deleted === ids.length) { MMToast.success(deleted + ' records deleted'); setTimeout(function () { location.reload(); }, 500); } });
      });
    }
  };

  /* ============================================================
   * 5. QUICK INVOICE
   * ============================================================ */
  window.QuickInvoice = {
    init: function () {
      // Register keyboard shortcut
      MMShortcuts.register('ctrl+n', function () { QuickInvoice.open(); }, 'New Invoice (Quick)');
      MMShortcuts.register('ctrl+shift+n', function () { QuickInvoice.openQuotation(); }, 'New Quotation (Quick)');
      MMShortcuts.register('ctrl+p', function () { QuickInvoice.printLast(); }, 'Print Last Invoice');
    },
    open: function () {
      window.location.href = 'module.php?m=invoices&action=new&quick=1';
    },
    openQuotation: function () {
      window.location.href = 'module.php?m=quotations&action=new&quick=1';
    },
    printLast: function () {
      // Try to get last invoice from page
      var lastRow = document.querySelector('.data-table tbody tr:first-child');
      if (lastRow) {
        var id = lastRow.dataset?.id || lastRow.querySelector('[data-id]')?.dataset?.id;
        if (id) { window.open('invoice_print.php?id=' + id, '_blank'); return; }
      }
      MMToast.info('No invoice found to print');
    }
  };

  /* ============================================================
   * 6. WHATSAPP INTEGRATION
   * ============================================================ */
  window.MMWhatsApp = {
    sendBill: function (phone, message) {
      phone = phone.replace(/[^0-9]/g, '');
      if (phone.length < 10) { MMToast.error('Invalid phone number'); return; }
      var url = 'https://wa.me/91' + phone + '?text=' + encodeURIComponent(message);
      window.open(url, '_blank');
    },
    sendPaymentReminder: function (phone, customerName, amount, invoiceNo) {
      var msg = 'Dear ' + customerName + ',\n\n';
      msg += 'This is a friendly reminder for pending payment.\n\n';
      msg += 'Invoice: #' + invoiceNo + '\n';
      msg += 'Amount Due: ₹' + amount + '\n\n';
      msg += 'Please make the payment at your earliest convenience.\n\n';
      msg += 'Thank you!\n';
      msg += '— My Metaji';
      this.sendBill(phone, msg);
    },
    shareInvoice: function (phone, invoiceNo, amount) {
      var msg = 'Invoice #' + invoiceNo + '\n';
      msg += 'Amount: ₹' + amount + '\n';
      msg += 'View and pay: [link]\n\n';
      msg += 'Powered by My Metaji';
      this.sendBill(phone, msg);
    }
  };

  /* ============================================================
   * 7. CHARTS SYSTEM (Simple Canvas Charts)
   * ============================================================ */
  window.MMChart = {
    bar: function (canvasId, labels, data, colors) {
      var canvas = document.getElementById(canvasId);
      if (!canvas) return;
      var ctx = canvas.getContext('2d');
      var w = canvas.width = canvas.parentElement.offsetWidth;
      var h = canvas.height = 250;
      var padding = { top: 20, right: 20, bottom: 40, left: 60 };
      var chartW = w - padding.left - padding.right;
      var chartH = h - padding.top - padding.bottom;
      var maxVal = Math.max.apply(null, data) * 1.1 || 1;
      var barWidth = chartW / data.length * 0.6;
      var gap = chartW / data.length * 0.4;

      ctx.clearRect(0, 0, w, h);

      // Grid lines
      ctx.strokeStyle = '#e0e0e0';
      ctx.lineWidth = 0.5;
      for (var i = 0; i <= 5; i++) {
        var y = padding.top + chartH - (chartH * i / 5);
        ctx.beginPath();
        ctx.moveTo(padding.left, y);
        ctx.lineTo(w - padding.right, y);
        ctx.stroke();
        ctx.fillStyle = '#999';
        ctx.font = '11px Poppins';
        ctx.textAlign = 'right';
        ctx.fillText('₹' + (maxVal * i / 5).toFixed(0), padding.left - 5, y + 4);
      }

      // Bars
      colors = colors || ['#0D47A1', '#2196F3', '#4CAF50', '#FF9800', '#F44336', '#9C27B0'];
      data.forEach(function (val, i) {
        var x = padding.left + i * (chartW / data.length) + gap / 2;
        var barH = (val / maxVal) * chartH;
        var y = padding.top + chartH - barH;

        // Bar gradient
        var grad = ctx.createLinearGradient(x, y, x, y + barH);
        grad.addColorStop(0, colors[i % colors.length]);
        grad.addColorStop(1, colors[i % colors.length] + '88');
        ctx.fillStyle = grad;

        // Rounded top
        var radius = Math.min(4, barWidth / 2);
        ctx.beginPath();
        ctx.moveTo(x + radius, y);
        ctx.lineTo(x + barWidth - radius, y);
        ctx.quadraticCurveTo(x + barWidth, y, x + barWidth, y + radius);
        ctx.lineTo(x + barWidth, y + barH);
        ctx.lineTo(x, y + barH);
        ctx.lineTo(x, y + radius);
        ctx.quadraticCurveTo(x, y, x + radius, y);
        ctx.fill();

        // Value on top
        ctx.fillStyle = '#333';
        ctx.font = '11px Poppins';
        ctx.textAlign = 'center';
        ctx.fillText('₹' + val.toFixed(0), x + barWidth / 2, y - 5);

        // Label
        ctx.fillStyle = '#666';
        ctx.font = '11px Poppins';
        ctx.fillText(labels[i] || '', x + barWidth / 2, h - padding.bottom + 18);
      });
    },
    donut: function (canvasId, labels, data, colors) {
      var canvas = document.getElementById(canvasId);
      if (!canvas) return;
      var ctx = canvas.getContext('2d');
      var w = canvas.width = canvas.parentElement.offsetWidth;
      var h = canvas.height = 200;
      var cx = w / 2 - 60;
      var cy = h / 2;
      var r = Math.min(cx, cy) - 15;
      var inner = r * 0.6;
      var total = data.reduce(function (a, b) { return a + b; }, 0) || 1;
      var startAngle = -Math.PI / 2;

      colors = colors || ['#0D47A1', '#2196F3', '#4CAF50', '#FF9800', '#F44336'];
      ctx.clearRect(0, 0, w, h);

      data.forEach(function (val, i) {
        var slice = (val / total) * Math.PI * 2;
        ctx.beginPath();
        ctx.arc(cx, cy, r, startAngle, startAngle + slice);
        ctx.arc(cx, cy, inner, startAngle + slice, startAngle, true);
        ctx.closePath();
        ctx.fillStyle = colors[i % colors.length];
        ctx.fill();
        startAngle += slice;
      });

      // Center text
      ctx.fillStyle = '#333';
      ctx.font = 'bold 18px Poppins';
      ctx.textAlign = 'center';
      ctx.fillText('₹' + total.toFixed(0), cx, cy + 5);
      ctx.font = '11px Poppins';
      ctx.fillStyle = '#999';
      ctx.fillText('Total', cx, cy + 22);

      // Legend
      var lx = w - 110;
      var ly = 20;
      labels.forEach(function (label, i) {
        ctx.fillStyle = colors[i % colors.length];
        ctx.fillRect(lx, ly + i * 22, 12, 12);
        ctx.fillStyle = '#333';
        ctx.font = '12px Poppins';
        ctx.textAlign = 'left';
        ctx.fillText(label + ' (₹' + data[i].toFixed(0) + ')', lx + 18, ly + i * 22 + 10);
      });
    },
    line: function (canvasId, labels, data, color) {
      var canvas = document.getElementById(canvasId);
      if (!canvas) return;
      var ctx = canvas.getContext('2d');
      var w = canvas.width = canvas.parentElement.offsetWidth;
      var h = canvas.height = 200;
      var padding = { top: 20, right: 20, bottom: 40, left: 60 };
      var chartW = w - padding.left - padding.right;
      var chartH = h - padding.top - padding.bottom;
      var maxVal = Math.max.apply(null, data) * 1.1 || 1;
      color = color || '#0D47A1';

      ctx.clearRect(0, 0, w, h);

      // Grid
      ctx.strokeStyle = '#e0e0e0';
      ctx.lineWidth = 0.5;
      for (var i = 0; i <= 4; i++) {
        var y = padding.top + chartH - (chartH * i / 4);
        ctx.beginPath();
        ctx.moveTo(padding.left, y);
        ctx.lineTo(w - padding.right, y);
        ctx.stroke();
        ctx.fillStyle = '#999';
        ctx.font = '11px Poppins';
        ctx.textAlign = 'right';
        ctx.fillText('₹' + (maxVal * i / 4).toFixed(0), padding.left - 5, y + 4);
      }

      // Line
      var points = [];
      data.forEach(function (val, i) {
        var x = padding.left + (i / (data.length - 1 || 1)) * chartW;
        var y = padding.top + chartH - (val / maxVal) * chartH;
        points.push({ x: x, y: y });
      });

      // Area fill
      ctx.beginPath();
      ctx.moveTo(points[0].x, padding.top + chartH);
      points.forEach(function (p) { ctx.lineTo(p.x, p.y); });
      ctx.lineTo(points[points.length - 1].x, padding.top + chartH);
      ctx.closePath();
      var grad = ctx.createLinearGradient(0, padding.top, 0, padding.top + chartH);
      grad.addColorStop(0, color + '33');
      grad.addColorStop(1, color + '05');
      ctx.fillStyle = grad;
      ctx.fill();

      // Line path
      ctx.beginPath();
      ctx.moveTo(points[0].x, points[0].y);
      points.forEach(function (p) { ctx.lineTo(p.x, p.y); });
      ctx.strokeStyle = color;
      ctx.lineWidth = 2.5;
      ctx.stroke();

      // Dots
      points.forEach(function (p) {
        ctx.beginPath();
        ctx.arc(p.x, p.y, 4, 0, Math.PI * 2);
        ctx.fillStyle = '#fff';
        ctx.fill();
        ctx.strokeStyle = color;
        ctx.lineWidth = 2;
        ctx.stroke();
      });

      // Labels
      labels.forEach(function (label, i) {
        var x = padding.left + (i / (labels.length - 1 || 1)) * chartW;
        ctx.fillStyle = '#666';
        ctx.font = '11px Poppins';
        ctx.textAlign = 'center';
        ctx.fillText(label, x, h - padding.bottom + 18);
      });
    }
  };

  /* ============================================================
   * 8. DAILY SUMMARY WIDGET
   * ============================================================ */
  window.MMSummary = {
    refresh: function () {
      var el = document.getElementById('mm-daily-summary');
      if (!el) return;
      fetch('api_summary.php')
        .then(function (r) { return r.json(); })
        .then(function (data) {
          if (data.error) return;
          el.querySelector('.mm-sum-invoices').textContent = data.todayInvoices || 0;
          el.querySelector('.mm-sum-amount').textContent = '₹' + (data.todayAmount || 0).toFixed(0);
          el.querySelector('.mm-sum-collection').textContent = '₹' + (data.todayCollection || 0).toFixed(0);
          el.querySelector('.mm-sum-pending').textContent = '₹' + (data.totalPending || 0).toFixed(0);
        })
        .catch(function () { });
    }
  };

  /* ============================================================
   * INIT ON DOM READY
   * ============================================================ */
  document.addEventListener('DOMContentLoaded', function () {
    // Init shortcuts
    MMShortcuts.init();
    MMShortcuts.register('ctrl+n', function () { QuickInvoice.open(); }, 'New Invoice');
    MMShortcuts.register('ctrl+shift+n', function () { QuickInvoice.openQuotation(); }, 'New Quotation');
    MMShortcuts.register('ctrl+f', function () {
      var searchInput = document.querySelector('.search-box input, .data-table input[type="search"]');
      if (searchInput) searchInput.focus();
    }, 'Search');
    MMShortcuts.register('escape', function () {
      document.querySelectorAll('.mm-shortcut-modal, .form-modal.is-open').forEach(function (el) { el.remove(); });
    }, 'Close');

    // Init Quick Invoice
    QuickInvoice.init();

    // Init Bulk Selection
    // MMBulk.init(); // Removed - no table checkboxes needed

    // Init autocomplete on customer fields
    document.querySelectorAll('input[name="customer"][type="text"], input[data-autocomplete="customer"]').forEach(function (input) {
      fetchCustomers(function (customers) {
        new MMAutocomplete(input, {
          data: customers,
          key: 'name',
          subKey: 'phone',
          onSelect: function (item) {
            // Auto-fill phone if available
            var phoneField = document.querySelector('input[name="phone"]');
            if (phoneField && item.phone) phoneField.value = item.phone;
          }
        });
      });
    });

    // Init autocomplete on product fields
    document.querySelectorAll('input[data-autocomplete="product"]').forEach(function (input) {
      fetchProducts(function (products) {
        new MMAutocomplete(input, {
          data: products,
          key: 'name',
          subKey: 'selling_price',
          onSelect: function (item, el) {
            // Find price field and auto-fill
            var row = el.closest('tr, .line-item, .product-row');
            if (row) {
              var priceField = row.querySelector('input[name*="price"], input[name*="amount"]');
              if (priceField && item.selling_price) priceField.value = item.selling_price;
              var qtyField = row.querySelector('input[name*="qty"]');
              if (qtyField && !qtyField.value) qtyField.value = '1';
            }
          }
        });
      });
    });

    // Add Quick Invoice button to dashboard if present
    var dashModules = document.querySelector('.dashboard-modules');
    if (dashModules) {
      var quickBtn = document.createElement('a');
      quickBtn.className = 'module-tile mm-quick-invoice-tile';
      quickBtn.href = 'module.php?m=invoices&action=new&quick=1';
      quickBtn.innerHTML = '<div class="icon">⚡</div><div class="name">Quick Invoice</div><div class="count">Ctrl+N</div>';
      dashModules.insertBefore(quickBtn, dashModules.firstChild);
    }

    // Add notification badge updates
    updateNotificationBadges();
  });

  /* ============================================================
   * HELPER: Fetch Customers
   * ============================================================ */
  function fetchCustomers(callback) {
    // Try to get from embedded data or fetch
    if (window._mmCustomers) { callback(window._mmCustomers); return; }
    fetch('module.php?m=customers&action=list&json=1')
      .then(function (r) { return r.json(); })
      .then(function (data) {
        window._mmCustomers = data.rows || data || [];
        callback(window._mmCustomers);
      })
      .catch(function () {
        // Fallback: try to read from existing select options
        var select = document.querySelector('select[name="customer"]');
        if (select) {
          var customers = [];
          select.querySelectorAll('option').forEach(function (opt) {
            if (opt.value) customers.push({ name: opt.textContent.trim(), id: opt.value });
          });
          callback(customers);
        } else {
          callback([]);
        }
      });
  }

  /* ============================================================
   * HELPER: Fetch Products
   * ============================================================ */
  function fetchProducts(callback) {
    if (window._mmProducts) { callback(window._mmProducts); return; }
    fetch('module.php?m=products&action=list&json=1')
      .then(function (r) { return r.json(); })
      .then(function (data) {
        window._mmProducts = data.rows || data || [];
        callback(window._mmProducts);
      })
      .catch(function () { callback([]); });
  }

  /* ============================================================
   * HELPER: Update Notification Badges
   * ============================================================ */
  function updateNotificationBadges() {
    // This will be called periodically to update sidebar badges
    // For now, it's handled server-side in layout_top.php
  }

})();

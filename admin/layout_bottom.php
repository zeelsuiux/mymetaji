    <!-- Floating Action Button (Mobile Quick Actions) -->
    <?php if (is_admin()): ?>
    <div class="mm-fab" id="mm-fab">
      <div class="mm-fab-menu" id="mm-fab-menu">
        <a href="module.php?m=invoices&action=new" class="mm-fab-item">📄 New Invoice</a>
        <a href="module.php?m=quotations&action=new" class="mm-fab-item">📋 New Quotation</a>
        <a href="module.php?m=payments&action=new" class="mm-fab-item">💰 Add Payment</a>
        <a href="module.php?m=customers&action=new" class="mm-fab-item">👤 Add Customer</a>
      </div>
      <button class="mm-fab-btn" id="mm-fab-btn" title="Quick Actions">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      </button>
    </div>
    <?php endif; ?>

    </div>
  </div>
</div>
<script src="assets/app-enhancements.js?v=<?= (int)@filemtime(__DIR__ . '/assets/app-enhancements.js') ?>"></script>
<script>
// FAB Toggle
document.addEventListener('DOMContentLoaded', function() {
  // Sidebar backdrop close
  var backdrop = document.getElementById('sidebar-backdrop');
  var sidebar = document.getElementById('sidebar');
  if (backdrop && sidebar) {
    backdrop.addEventListener('click', function() {
      sidebar.classList.remove('open');
      backdrop.classList.remove('visible');
    });
  }

  var fabBtn = document.getElementById('mm-fab-btn');
  var fabMenu = document.getElementById('mm-fab-menu');
  if (fabBtn && fabMenu) {
    fabBtn.addEventListener('click', function() {
      fabMenu.classList.toggle('open');
    });
    document.addEventListener('click', function(e) {
      if (!document.getElementById('mm-fab').contains(e.target)) {
        fabMenu.classList.remove('open');
      }
    });
  }

  // Global Search
  var searchInput = document.getElementById('mm-global-search');
  var resultsDiv = document.getElementById('mm-global-results');
  if (searchInput && resultsDiv) {
    var searchTimeout;
    searchInput.addEventListener('input', function() {
      clearTimeout(searchTimeout);
      var q = searchInput.value.trim();
      if (q.length < 2) { resultsDiv.style.display = 'none'; return; }
      searchTimeout = setTimeout(function() {
        var queries = [
          { module: 'invoices', label: 'Invoice', icon: '📄', url: 'module.php?m=invoices&action=view&id=' },
          { module: 'customers', label: 'Customer', icon: '👤', url: 'module.php?m=customers&action=view&id=' },
          { module: 'products', label: 'Product', icon: '📦', url: 'module.php?m=products&action=view&id=' }
        ];
        var allResults = [];
        var done = 0;
        queries.forEach(function(qry) {
          fetch('module.php?m=' + qry.module + '&action=list&json=1&search=' + encodeURIComponent(q))
            .then(function(r) { return r.json(); })
            .then(function(data) {
              var rows = data.rows || data || [];
              rows.slice(0, 3).forEach(function(row) {
                var label = '';
                if (qry.module === 'invoices') label = '#' + (row.invoice_no || '') + ' — ' + (row.customer || '');
                else if (qry.module === 'customers') label = (row.name || '') + (row.phone ? ' · ' + row.phone : '');
                else label = (row.name || '') + (row.price ? ' · ₹' + row.price : '');
                allResults.push({ label: label, sub: qry.label, icon: qry.icon, url: qry.url + row.id });
              });
              done++;
              if (done === queries.length) renderSearchResults(allResults, resultsDiv);
            }).catch(function() { done++; if (done === queries.length) renderSearchResults(allResults, resultsDiv); });
        });
      }, 300);
    });
    function renderSearchResults(allResults, resultsDiv) {
      var html = '';
      allResults.slice(0, 8).forEach(function(r) {
        html += '<div class="mm-ac-item" onclick="window.location=\'' + r.url + '\'">';
        html += '<span class="mm-ac-icon">' + r.icon + '</span>';
        html += '<span class="mm-ac-label">' + r.label + '</span>';
        html += '<span class="mm-ac-sub">' + r.sub + '</span>';
        html += '</div>';
      });
      if (allResults.length === 0) html = '<div class="mm-ac-item"><span class="mm-ac-label">No results found</span></div>';
      resultsDiv.innerHTML = html;
      resultsDiv.style.display = 'block';
    }
    document.addEventListener('click', function(e) {
      if (!searchInput.parentNode.contains(e.target)) resultsDiv.style.display = 'none';
    });
  }
});
</script>
<script>
function resetSidebarPosition() {
  const sidebarNav = document.querySelector('.sidebar nav');
  if (sidebarNav) sidebarNav.scrollTop = 0;
}

/* ═══ STYLED CONFIRM MODAL ═══ */
function mmConfirm(title, message, onConfirm, type) {
  type = type || 'danger';
  var existing = document.querySelector('.mm-confirm-overlay');
  if (existing) existing.remove();
  var iconEmoji = type === 'danger' ? '⚠️' : '❓';
  var html = '<div class="mm-confirm-overlay" id="mm-confirm">' +
    '<div class="mm-confirm-box">' +
    '<div class="mm-confirm-icon ' + type + '">' + iconEmoji + '</div>' +
    '<h3 class="mm-confirm-title">' + title + '</h3>' +
    '<p class="mm-confirm-message">' + message + '</p>' +
    '<div class="mm-confirm-actions">' +
    '<button class="mm-confirm-cancel" onclick="mmConfirmClose()">Cancel</button>' +
    '<button class="mm-confirm-ok" id="mm-confirm-ok">Delete</button>' +
    '</div></div></div>';
  document.body.insertAdjacentHTML('beforeend', html);
  var overlay = document.getElementById('mm-confirm');
  var okBtn = document.getElementById('mm-confirm-ok');
  requestAnimationFrame(function() { overlay.classList.add('visible'); });
  okBtn.onclick = function() { mmConfirmClose(); if (onConfirm) onConfirm(); };
  overlay.addEventListener('click', function(e) { if (e.target === overlay) mmConfirmClose(); });
  document.addEventListener('keydown', function handler(e) {
    if (e.key === 'Escape') { mmConfirmClose(); document.removeEventListener('keydown', handler); }
  });
}
function mmConfirmClose() {
  var overlay = document.querySelector('.mm-confirm-overlay');
  if (overlay) { overlay.classList.remove('visible'); setTimeout(function() { overlay.remove(); }, 200); }
}

/* ═══ REPLACE NATIVE CONFIRM() ═══ */
document.addEventListener('DOMContentLoaded', function() {
  document.addEventListener('click', function(e) {
    var link = e.target.closest('a[onclick*="confirm("]');
    if (!link) return;
    var onclick = link.getAttribute('onclick');
    var match = onclick.match(/confirm\(['"](.+?)['"]\)/);
    if (!match) return;
    e.preventDefault();
    var msg = match[1];
    var href = link.getAttribute('href');
    mmConfirm('Are you sure?', msg, function() { window.location.href = href; }, 'danger');
  });
});

document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('table.data-table thead').forEach(function (thead) {
    const table = thead.closest('table');
    const body = table ? table.querySelector('tbody') : null;
    if (!table || !body) return;

    thead.querySelectorAll('th').forEach(function (header, columnIndex) {
      if (header.textContent.trim().toLowerCase() === 'actions') return;
      header.classList.add('sortable-header');
      header.addEventListener('click', function () {
        const ascending = header.dataset.sortDirection !== 'asc';
        thead.querySelectorAll('th').forEach(function (item) {
          delete item.dataset.sortDirection;
        });
        header.dataset.sortDirection = ascending ? 'asc' : 'desc';

        const rows = Array.from(body.querySelectorAll('tr'));
        rows.sort(function (first, second) {
          const firstText = first.cells[columnIndex]?.textContent.trim() || '';
          const secondText = second.cells[columnIndex]?.textContent.trim() || '';
          const firstNumber = Number(firstText.replace(/[₹,]/g, ''));
          const secondNumber = Number(secondText.replace(/[₹,]/g, ''));
          let comparison;

          if (firstText !== '-' && secondText !== '-' && Number.isFinite(firstNumber) && Number.isFinite(secondNumber)) {
            comparison = firstNumber - secondNumber;
          } else if (/^\d{4}-\d{2}-\d{2}$/.test(firstText) && /^\d{4}-\d{2}-\d{2}$/.test(secondText)) {
            comparison = firstText.localeCompare(secondText);
          } else {
            comparison = firstText.localeCompare(secondText, undefined, { numeric: true, sensitivity: 'base' });
          }

          return ascending ? comparison : -comparison;
        });
        rows.forEach(function (row) { body.appendChild(row); });
      });
    });
  });

  resetSidebarPosition();
});

window.addEventListener('pageshow', resetSidebarPosition);
</script>
</body>
</html>

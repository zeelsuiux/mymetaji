    </div>
  </div>
</div>
<script>
function resetSidebarPosition() {
  const sidebarNav = document.querySelector('.sidebar nav');
  if (sidebarNav) sidebarNav.scrollTop = 0;
}

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

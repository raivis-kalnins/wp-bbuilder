document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.wpbb-table-block[data-datatable="1"] table').forEach(function (table) {
    if (!window.DataTable) return;
    var wrap = table.closest('.wpbb-table-block');
    try {
      new window.DataTable(table, {
        searching: wrap && wrap.dataset.searching === '1',
        paging: wrap && wrap.dataset.paging === '1',
        ordering: wrap && wrap.dataset.ordering === '1',
        info: wrap && wrap.dataset.info === '1',
        lengthChange: wrap && wrap.dataset.lengthchange === '1',
        autoWidth: false
      });
    } catch (e) {}
  });
});

document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.wpbb-table-block[data-datatable="1"] table').forEach(function (table) {
    var wrap = table.closest('.wpbb-table-block');
    if (!wrap || table.dataset.wpbbDatatableInit === '1') return;
    if (!window.DataTable) return;
    try {
      if (table.closest('.dataTable-wrapper')) return;
      new window.DataTable(table, {
        searching: wrap.dataset.searching === '1',
        paging: wrap.dataset.paging === '1',
        ordering: wrap.dataset.ordering === '1',
        info: wrap.dataset.info === '1',
        lengthChange: wrap.dataset.lengthchange === '1',
        autoWidth: false,
        responsive: true
      });
      table.dataset.wpbbDatatableInit = '1';
    } catch (e) {}
  });
});
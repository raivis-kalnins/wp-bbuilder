(function () {
  function initTables() {
    var tables = document.querySelectorAll('.wpbb-table-block[data-datatable="1"] table');
    tables.forEach(function (table) {
      var wrap = table.closest('.wpbb-table-block');
      if (!wrap || table.dataset.wpbbDatatableInit === '1') return;

      try {
        if (table.closest('.dataTables_wrapper, .dt-container, .dataTable-wrapper')) return;

        var opts = {
          searching: wrap.dataset.searching === '1',
          paging: wrap.dataset.paging === '1',
          ordering: wrap.dataset.ordering === '1',
          info: wrap.dataset.info === '1',
          lengthChange: wrap.dataset.lengthchange === '1',
          autoWidth: false,
          responsive: true
        };

        if (window.DataTable) {
          new window.DataTable(table, opts);
          table.dataset.wpbbDatatableInit = '1';
          return;
        }

        if (window.jQuery && window.jQuery.fn && window.jQuery.fn.DataTable) {
          window.jQuery(table).DataTable(opts);
          table.dataset.wpbbDatatableInit = '1';
          return;
        }
      } catch (e) {
        console && console.warn && console.warn('WPBB DataTable init failed', e);
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initTables);
  } else {
    initTables();
  }

  if (typeof MutationObserver !== 'undefined') {
    var observer = new MutationObserver(function () {
      initTables();
    });
    observer.observe(document.documentElement, { childList: true, subtree: true });
  }
})();
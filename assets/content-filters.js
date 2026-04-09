document.addEventListener('DOMContentLoaded', function () {
  function post(data) {
    return fetch((window.wpbbContentFilters && wpbbContentFilters.ajaxUrl) || '/wp-admin/admin-ajax.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
      credentials: 'same-origin',
      body: new URLSearchParams(data).toString()
    }).then(function (r) { return r.json(); });
  }

  document.querySelectorAll('[data-wpbb-load-more-btn]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var page = parseInt(btn.dataset.page || '1', 10) + 1;
      post({
        action: 'wpbb_load_more',
        page: page,
        perPage: btn.dataset.perPage || '3',
        postType: btn.dataset.postType || 'post',
        category: btn.dataset.category || '',
        itemClass: btn.dataset.itemClass || 'col-md-4'
      }).then(function (res) {
        if (!res || !res.success) return;
        var wrap = btn.closest('.wpbb-load-more');
        var results = wrap ? wrap.querySelector('[data-wpbb-load-more-results]') : null;
        if (results) results.insertAdjacentHTML('beforeend', (res.data && res.data.html) || '');
        btn.dataset.page = String(page);
        if (page >= parseInt(btn.dataset.max || '1', 10) || !(res.data && res.data.html)) btn.remove();
      });
    });
  });

  document.querySelectorAll('.wpbb-blog-filter').forEach(function (block) {
    var submit = block.querySelector('[data-wpbb-blog-submit]');
    var results = block.querySelector('[data-wpbb-blog-results]');
    if (!submit || !results) return;
    function run() {
      submit.disabled = true;
      post({
        action: 'wpbb_blog_filter',
        postType: results.dataset.postType || 'post',
        taxonomy: results.dataset.taxonomy || 'category',
        perPage: results.dataset.perPage || '6',
        search: (block.querySelector('[data-wpbb-blog-search]') || {}).value || '',
        category: (block.querySelector('[data-wpbb-blog-category]') || {}).value || '',
        year: (block.querySelector('[data-wpbb-blog-year]') || {}).value || '',
        sort: (block.querySelector('[data-wpbb-blog-sort]') || {}).value || 'date_desc'
      }).then(function (res) {
        if (res && res.success && res.data) results.innerHTML = res.data.html || '';
      }).finally(function () { submit.disabled = false; });
    }
    submit.addEventListener('click', run);
    var search = block.querySelector('[data-wpbb-blog-search]');
    if (search) search.addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); run(); } });
  });
});

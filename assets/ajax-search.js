document.addEventListener('input', function (e) {
  var input = e.target.closest('.wpbb-ajax-search-input');
  if (!input) return;
  var wrap = input.closest('.wpbb-ajax-search');
  if (!wrap) return;
  var results = wrap.querySelector('.wpbb-ajax-search-results');
  var btn = wrap.querySelector('.wpbb-ajax-search-page-btn');
  var term = input.value.trim();
  if (btn) btn.href = (wrap.dataset.searchUrl || '/?s=') + encodeURIComponent(term);
  if (term.length < 2) { results.innerHTML = ''; return; }
  var url = '/wp-admin/admin-ajax.php?action=wpbb_ajax_search&term=' + encodeURIComponent(term) + '&limit=' + encodeURIComponent(wrap.dataset.limit || '10') + '&mode=' + encodeURIComponent(wrap.dataset.mode || 'title') + '&sort=' + encodeURIComponent(wrap.dataset.sort || 'relevance');
  fetch(url).then(function(r){ return r.json(); }).then(function(data){
    if (!data || !data.success) { results.innerHTML = ''; return; }
    var showExcerpt = wrap.dataset.showExcerpt === '1';
    var showPrice = wrap.dataset.showPrice === '1';
    results.innerHTML = data.data.items.map(function(item){
      var img = item.image ? '<img src="' + item.image + '" alt="" class="wpbb-ajax-search-thumb">' : '';
      var excerpt = showExcerpt && item.excerpt ? '<small class="wpbb-ajax-search-excerpt">' + item.excerpt + '</small>' : '';
      var price = showPrice && item.price ? '<small class="wpbb-ajax-search-price">' + item.price + '</small>' : '';
      return '<a class="wpbb-ajax-search-item" href="' + item.url + '">' + img + '<span><strong>' + item.title + '</strong><small>' + item.type + '</small>' + price + excerpt + '</span></a>';
    }).join('');
  }).catch(function(){ results.innerHTML = ''; });
});
(function($){
  function showNotice(message, type){
    var id='wpbb-ordering-notice';
    var $notice=$('#'+id);
    if(!$notice.length){
      $notice=$('<div id="'+id+'" class="notice is-dismissible wpbb-ordering-notice"><p></p></div>');
      $('.wrap h1').first().after($notice);
    }
    $notice.removeClass('notice-success notice-error notice-info').addClass(type || 'notice-info');
    $notice.find('p').text(message);
  }

  $(function(){
    var $list=$('#the-list');
    if(!$list.length || typeof wpbbOrdering==='undefined') return;

    $list.addClass('wpbb-sortable-list');
    $list.sortable({
      items:'tr',
      axis:'y',
      cursor:'move',
      handle:'.wpbb-order-drag-handle',
      helper:function(e, ui){
        ui.children().each(function(){ $(this).width($(this).width()); });
        return ui;
      },
      placeholder:'wpbb-order-placeholder',
      start:function(e, ui){ ui.placeholder.height(ui.item.height()); },
      update:function(){
        var ids=[];
        $list.children('tr').each(function(){
          var id=(this.id || '').replace('post-','');
          if(id) ids.push(id);
        });

        showNotice(wpbbOrdering.saving, 'notice-info');

        $.post(wpbbOrdering.ajaxUrl, {
          action:'wpbb_save_sort_order',
          nonce:wpbbOrdering.nonce,
          postType:wpbbOrdering.postType,
          ids:ids
        }).done(function(response){
          if(response && response.success){
            showNotice(wpbbOrdering.saved, 'notice-success');
          } else {
            showNotice((response && response.data && response.data.message) || wpbbOrdering.error, 'notice-error');
          }
        }).fail(function(){
          showNotice(wpbbOrdering.error, 'notice-error');
        });
      }
    });
  });
})(jQuery);

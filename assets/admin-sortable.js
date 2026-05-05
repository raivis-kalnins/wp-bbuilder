
(function(){
  function init(){
    var grid = document.querySelector('.wpbb-admin-grid');
    if(!grid) return;
    var cards = Array.prototype.slice.call(grid.querySelectorAll('.wpbb-card'));
    if(!cards.length) return;
    var key = 'wpbb-admin-card-order';
    cards.forEach(function(card, index){
      if(!card.id) card.id = 'wpbb-card-' + index;
      card.setAttribute('draggable','true');
    });

    function saveOrder(){
      var ids = Array.prototype.map.call(grid.querySelectorAll('.wpbb-card'), function(c){ return c.id; });
      try { localStorage.setItem(key, JSON.stringify(ids)); } catch(e){}
    }

    function restoreOrder(){
      try {
        var ids = JSON.parse(localStorage.getItem(key) || '[]');
        ids.forEach(function(id){
          var el = document.getElementById(id);
          if(el) grid.appendChild(el);
        });
      } catch(e){}
    }

    var dragged = null;
    grid.addEventListener('dragstart', function(e){
      var card = e.target.closest('.wpbb-card');
      if(!card) return;
      dragged = card;
      card.classList.add('is-dragging');
      e.dataTransfer.effectAllowed = 'move';
    });
    grid.addEventListener('dragend', function(e){
      var card = e.target.closest('.wpbb-card');
      if(card) card.classList.remove('is-dragging');
      dragged = null;
      Array.prototype.forEach.call(grid.querySelectorAll('.wpbb-card'), function(c){ c.classList.remove('drop-before','drop-after'); });
      saveOrder();
    });
    grid.addEventListener('dragover', function(e){
      if(!dragged) return;
      e.preventDefault();
      var target = e.target.closest('.wpbb-card');
      if(!target || target === dragged) return;
      Array.prototype.forEach.call(grid.querySelectorAll('.wpbb-card'), function(c){ c.classList.remove('drop-before','drop-after'); });
      var rect = target.getBoundingClientRect();
      var before = e.clientY < rect.top + rect.height / 2;
      target.classList.add(before ? 'drop-before' : 'drop-after');
    });
    grid.addEventListener('drop', function(e){
      if(!dragged) return;
      e.preventDefault();
      var target = e.target.closest('.wpbb-card');
      if(!target || target === dragged) return;
      var rect = target.getBoundingClientRect();
      var before = e.clientY < rect.top + rect.height / 2;
      target.classList.remove('drop-before','drop-after');
      if(before){
        grid.insertBefore(dragged, target);
      } else {
        grid.insertBefore(dragged, target.nextSibling);
      }
      saveOrder();
    });

    restoreOrder();
  }
  if(document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();

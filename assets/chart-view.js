document.addEventListener('DOMContentLoaded', function(){
  document.querySelectorAll('.wpbb-chart').forEach(function(el){
    var box = el.querySelector('.wpbb-chart__canvas');
    if (!box) return;
    var type = el.dataset.chartType || 'bar';
    var json = el.dataset.chartJson || '';
    if (window.Chart && json) {
      try {
        var data = JSON.parse(json);
        box.innerHTML = '<canvas></canvas>';
        var ctx = box.querySelector('canvas');
        new Chart(ctx, { type: type, data: data, options: { responsive: true, maintainAspectRatio: false, plugins:{legend:{display:true}} } });
        return;
      } catch(e) {}
    }
    box.textContent = 'Chart: ' + type;
  });
  document.querySelectorAll('.wpbb-countdown-timer').forEach(function(el){
    var target = new Date(el.dataset.targetDate || '');
    var out = el.querySelector('.wpbb-countdown-timer__value');
    function tick(){
      if (!out || isNaN(target.getTime())) return;
      var diff = Math.max(0, target.getTime() - Date.now());
      var s = Math.floor(diff / 1000);
      var d = Math.floor(s / 86400); s -= d*86400;
      var h = Math.floor(s / 3600); s -= h*3600;
      var m = Math.floor(s / 60); s -= m*60;
      out.textContent = d + 'd ' + h + 'h ' + m + 'm ' + s + 's';
    }
    tick(); setInterval(tick, 1000);
  });
  document.querySelectorAll('.wpbb-weather').forEach(function(el){
    var key = el.dataset.apiKey || '';
    var location = el.dataset.location || 'Riga';
    var lang = el.dataset.lang || 'lv';
    var tempEl = el.querySelector('.wpbb-weather-temp');
    var noteEl = el.querySelector('.wpbb-weather-note');
    if (!key) { if (noteEl) noteEl.textContent = 'Add API key in settings/block.'; return; }
    fetch('https://api.openweathermap.org/data/2.5/weather?q=' + encodeURIComponent(location) + '&appid=' + encodeURIComponent(key) + '&units=metric&lang=' + encodeURIComponent(lang))
      .then(function(r){ return r.json(); })
      .then(function(data){
        if (data && data.main && tempEl) tempEl.textContent = Math.round(data.main.temp) + '°C';
        if (noteEl) noteEl.textContent = data && data.weather && data.weather[0] ? data.weather[0].description : '';
      })
      .catch(function(){ if (noteEl) noteEl.textContent = 'Weather unavailable'; });
  });
});
/* TechSallus — blog.js — Category filter */
(function () {
  'use strict';

  const pills = document.querySelectorAll('.cat-pill');
  const cards = document.querySelectorAll('.article-card[data-cat]');
  const noResults = document.getElementById('no-results');

  if (!pills.length) return;

  pills.forEach(pill => {
    pill.addEventListener('click', function () {
      pills.forEach(p => p.classList.remove('active'));
      this.classList.add('active');

      const cat = this.dataset.cat;
      let visible = 0;

      cards.forEach(card => {
        const show = cat === 'todos' || card.dataset.cat === cat;
        card.style.display = show ? '' : 'none';
        if (show) visible++;
      });

      if (noResults) noResults.style.display = visible === 0 ? '' : 'none';
    });
  });

})();

(() => {
  // smooth anchors
  document.addEventListener('click', (e) => {
    const a = e.target.closest('a[href^="#"]');
    if (!a) return;

    const id = a.getAttribute('href');
    const el = document.querySelector(id);
    if (!el) return;

    e.preventDefault();
    el.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });

  // scroll to error message if present
  const err = document.querySelector('.fh-alert, div[style*="border:1px solid #f00"]');
  if (err) {
    err.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  // rating: highlight clicked number for instant feedback
  const rating = document.getElementById('rating');
  if (rating) {
    rating.addEventListener('click', (e) => {
      const a = e.target.closest('a');
      if (!a) return;
      // quick UI feedback (navigation still happens)
      a.style.transform = 'translateY(-1px)';
      setTimeout(() => { a.style.transform = ''; }, 120);
    });
  }
})();

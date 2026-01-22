(() => {
  const adminRoot = document.querySelector('.fh-admin');
  const toggleBtn = document.getElementById('fhSidebarToggle');

  if (adminRoot) {
    const backdrop = adminRoot.querySelector('.fh-sidebar-backdrop');

    const closeSidebar = () => adminRoot.classList.remove('fh-sidebar-open');

    toggleBtn?.addEventListener('click', () => {
      adminRoot.classList.toggle('fh-sidebar-open');
    });

    backdrop?.addEventListener('click', closeSidebar);

    window.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeSidebar();
    });
  }

  document.addEventListener('click', (e) => {
    const a = e.target.closest('a[data-confirm="delete"]');
    if (!a) return;

    e.preventDefault();

    const href  = a.getAttribute('href');
    const title = a.getAttribute('data-title') || 'Удалить?';
    const text  = a.getAttribute('data-text')  || 'Действие нельзя отменить.';

    if (!window.Swal) {
      if (confirm(title + '\n' + text)) window.location.href = href;
      return;
    }

    Swal.fire({
      title,
      text,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Да, удалить',
      cancelButtonText: 'Отмена',
      confirmButtonColor: '#ef4444'
    }).then((result) => {
      if (result.isConfirmed) window.location.href = href;
    });
  });

  /**
   * Live poster preview (movie add/edit forms)
   * Requires:
   *  - input#posterInput
   *  - img#posterPreview
   *  - optional #posterPlaceholder
   *  - form#movieEditForm or #movieAddForm with data-project-base="/filmhub"
   */
  const posterInput = document.getElementById('posterInput');
  const posterImg = document.getElementById('posterPreview');
  const posterPlaceholder = document.getElementById('posterPlaceholder');

  if (posterInput && posterImg) {
    const form = posterInput.closest('form');
    const projectBase = (form && form.dataset && form.dataset.projectBase) ? form.dataset.projectBase : '';

    const buildPosterUrl = (value) => {
      let v = String(value || '').trim();
      if (!v) return '';

      if (/^https?:\/\//i.test(v)) return v;
      if (v.startsWith('/')) return projectBase + v;
      if (v.startsWith('img/')) return projectBase + '/' + v;

      if (!/\.[a-z0-9]{2,5}$/i.test(v)) v += '.jpg';
      return projectBase + '/img/movies/' + v;
    };

    const applyPreview = () => {
      const url = buildPosterUrl(posterInput.value);
      if (url) {
        posterImg.src = url;
        posterImg.style.display = 'block';
        if (posterPlaceholder) posterPlaceholder.style.display = 'none';
      } else {
        posterImg.style.display = 'none';
        if (posterPlaceholder) posterPlaceholder.style.display = 'block';
      }
    };

    posterInput.addEventListener('input', applyPreview);
  }
})();

(() => {
  // Mobile sidebar toggle
  const adminRoot = document.querySelector('.fh-admin');
  const toggleBtn = document.getElementById('fhSidebarToggle');

  if (adminRoot) {
    // create backdrop for mobile
    const backdrop = document.createElement('div');
    backdrop.className = 'fh-sidebar-backdrop';
    adminRoot.prepend(backdrop);

    const closeSidebar = () => adminRoot.classList.remove('fh-sidebar-open');
    const openSidebar = () => adminRoot.classList.add('fh-sidebar-open');

    toggleBtn?.addEventListener('click', () => {
      adminRoot.classList.toggle('fh-sidebar-open');
    });

    backdrop.addEventListener('click', closeSidebar);

    // close on ESC
    window.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeSidebar();
    });
  }

  // SweetAlert2 confirm for deletes
  document.addEventListener('click', (e) => {
    const a = e.target.closest('a[data-confirm="delete"]');
    if (!a) return;

    e.preventDefault();

    const href = a.getAttribute('href');
    const title = a.getAttribute('data-title') || 'Удалить?';
    const text  = a.getAttribute('data-text')  || 'Действие нельзя отменить.';

    if (!window.Swal) {
      // fallback
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

  // Poster preview in forms (input name="poster")
  const posterInput = document.querySelector('input[name="poster"]');
  const posterImg = document.getElementById('fhPosterPreview');

  const computeSiteBase = () => {
    // from /filmhub/admin/... -> /filmhub
    const path = window.location.pathname.replace(/\\/g, '/');
    const idx = path.lastIndexOf('/admin');
    if (idx !== -1) return path.slice(0, idx);
    return '';
  };

  const normalizePosterUrl = (val) => {
    const v = (val || '').trim();
    if (!v) return '';
    // already absolute?
    if (/^https?:\/\//i.test(v)) return v;
    if (v.startsWith('/')) return window.location.origin + v;
    // relative path like img/movies/...
    return window.location.origin + computeSiteBase() + '/' + v;
  };

  const setPosterPreview = () => {
    if (!posterInput || !posterImg) return;
    const url = normalizePosterUrl(posterInput.value);
    if (!url) {
      posterImg.classList.add('d-none');
      posterImg.removeAttribute('src');
      return;
    }
    posterImg.src = url;
    posterImg.classList.remove('d-none');
  };

  if (posterInput && posterImg) {
    posterInput.addEventListener('input', setPosterPreview);
    setPosterPreview();
  }
})();

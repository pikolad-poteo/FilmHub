(() => {
  // Mobile sidebar toggle
  const btn = document.getElementById('fhSidebarToggle');
  const sidebar = document.querySelector('.fh-sidebar');
  if (btn && sidebar) {
    btn.addEventListener('click', () => sidebar.classList.toggle('open'));
    document.addEventListener('click', (e) => {
      if (!sidebar.classList.contains('open')) return;
      const isClickInside = sidebar.contains(e.target) || btn.contains(e.target);
      if (!isClickInside) sidebar.classList.remove('open');
    });
  }

  // SweetAlert confirm for delete links
  document.addEventListener('click', async (e) => {
    const a = e.target.closest('a[data-confirm="delete"]');
    if (!a) return;

    e.preventDefault();

    const title = a.getAttribute('data-title') || 'Удалить?';
    const text  = a.getAttribute('data-text')  || 'Действие нельзя отменить.';
    const href  = a.getAttribute('href');

    const res = await Swal.fire({
      icon: 'warning',
      title,
      text,
      showCancelButton: true,
      confirmButtonText: 'Удалить',
      cancelButtonText: 'Отмена',
      confirmButtonColor: '#dc3545'
    });

    if (res.isConfirmed) window.location.href = href;
  });
})();

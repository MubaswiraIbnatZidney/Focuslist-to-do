document.addEventListener('click', (e) => {
  const editBtn = e.target.closest('.edit-btn');
  if (editBtn) {
    const id = editBtn.dataset.id;
    const row = document.getElementById('edit-' + id);
    if (row) {
      row.style.display = row.style.display === 'block' ? 'none' : 'block';
      const input = row.querySelector('input[name="title"]');
      if (row.style.display === 'block' && input) {
        input.focus(); input.setSelectionRange(input.value.length, input.value.length);
      }
    }
  }
  const cancelBtn = e.target.closest('.cancel-btn');
  if (cancelBtn) {
    const id = cancelBtn.dataset.id;
    const row = document.getElementById('edit-' + id);
    if (row) row.style.display = 'none';
  }
});

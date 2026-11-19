document.addEventListener('click', (event) => {
    const row = event.target.closest('[data-row-link]');
    if (!row || event.target.closest('a,button,input,select,textarea,label')) return;
    window.location.assign(row.dataset.rowLink);
});
document.addEventListener('keydown', (event) => {
    const row = event.target.closest('[data-row-link]');
    if (row && (event.key === 'Enter' || event.key === ' ')) { event.preventDefault(); window.location.assign(row.dataset.rowLink); }
});

(() => {
  const trigger = document.querySelector('[data-brisa-search]');
  const panel = document.getElementById('brisa-search-panel');
  if (!trigger || !panel) return;
  const toggle = (open) => {
    panel.hidden = !open;
    trigger.setAttribute('aria-expanded', String(open));
    if (open) panel.querySelector('input').focus();
  };
  trigger.addEventListener('click', () => toggle(panel.hidden));
  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && !panel.hidden) { toggle(false); trigger.focus(); }
  });
})();

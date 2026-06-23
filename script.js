// cargar las vistas previas (iframes) solo cuando son visibles
  const frames = document.querySelectorAll('.preview-frame');
  const frameObs = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const frame = entry.target;
        if (!frame.src) frame.src = frame.dataset.src;
        frameObs.unobserve(frame);
      }
    });
  }, { threshold: .1, rootMargin: '200px' });
  frames.forEach(f => frameObs.observe(f));

  // scroll reveal
  const els = document.querySelectorAll('.reveal');
  const obs = new IntersectionObserver((entries)=>{
    entries.forEach(e=>{ if(e.isIntersecting){ e.target.classList.add('in'); obs.unobserve(e.target); } });
  },{threshold:.15});
  els.forEach(el=>obs.observe(el));

  // hero terminal reveal immediately
  document.getElementById('term').classList.add('in');

  // copy email
  const copyBtn = document.getElementById('copy-email');
  copyBtn.addEventListener('click', ()=>{
    navigator.clipboard.writeText('rafaelrh.dev@gmail.com').then(()=>{
      const original = copyBtn.textContent;
      copyBtn.textContent = '¡Copiado!';
      setTimeout(()=>{ copyBtn.textContent = original; }, 1800);
    });
  });

  // copy phone
  const copyPhoneBtn = document.getElementById('copy-phone');
  if (copyPhoneBtn) {
    copyPhoneBtn.addEventListener('click', ()=>{
      navigator.clipboard.writeText('4271278213').then(()=>{
        const original = copyPhoneBtn.textContent;
        copyPhoneBtn.textContent = '¡Copiado!';
        setTimeout(()=>{ copyPhoneBtn.textContent = original; }, 1800);
      });
    });
  }

  // ===== modal de vista previa interactiva =====
  const modal = document.getElementById('preview-modal');
  const modalIframe = document.getElementById('modal-iframe');
  const modalCat = document.getElementById('modal-cat');
  const modalName = document.getElementById('modal-name');
  const modalOpenTab = document.getElementById('modal-open-tab');

  function openPreview(url, name, cat){
    modalIframe.src = url;
    modalName.textContent = name || '';
    modalCat.textContent = cat || '';
    modalOpenTab.href = url;
    modal.classList.add('open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }

  function closePreview(){
    modal.classList.remove('open');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    setTimeout(()=>{ modalIframe.src = ''; }, 250);
  }

  document.querySelectorAll('.cat-card-clickable').forEach(card => {
    card.addEventListener('click', (e) => {
      // si el clic fue directamente en el link "Ver invitación", deja que navegue normal
      if (e.target.closest('.cat-link')) return;
      const url = card.dataset.url;
      const name = card.dataset.name;
      const cat = card.dataset.cat;
      openPreview(url, name, cat);
    });
  });

  modal.querySelectorAll('[data-close]').forEach(el => el.addEventListener('click', closePreview));
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && modal.classList.contains('open')) closePreview();
  });
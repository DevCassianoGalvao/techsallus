/* ─────────────────────────────────────────────────────────────
   TechSallus — admin.js
   Kanban (SortableJS) · Lead modal · Notes AJAX · Histórico AJAX
   ───────────────────────────────────────────────────────────── */
(function () {
  'use strict';

  /* ── Helpers ─────────────────────────────────────────────── */
  function escHtml(str) {
    var d = document.createElement('div');
    d.textContent = str == null ? '' : String(str);
    return d.innerHTML;
  }

  function postJSON(url, data) {
    var csrf = document.querySelector('meta[name="csrf-token"]');
    return fetch(appUrl(url), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': csrf ? csrf.content : '',
      },
      body: JSON.stringify(data),
    }).then(function (r) { return r.json(); });
  }

  function appUrl(path) {
    if (/^https?:\/\//.test(path)) return path;
    var script = document.querySelector('script[src*="/assets/js/admin.js"]');
    var src = script ? script.getAttribute('src') : '';
    var base = src ? src.replace(/\/assets\/js\/admin\.js.*$/, '') : '';
    return base + '/' + String(path).replace(/^\/+/, '');
  }

  /* ═══════════════════════════════════════════════════════════
     MODAL
     ═══════════════════════════════════════════════════════════ */
  var modalOverlay   = document.getElementById('lead-modal');
  var modalClose     = document.getElementById('modal-close');
  var currentLeadId  = null;

  function openModal(cardEl) {
    if (!modalOverlay) return;
    currentLeadId = cardEl.dataset.id;

    /* Fill detail fields */
    setText('modal-title-text',     cardEl.dataset.nome);
    setText('modal-sub-text',       cardEl.dataset.inst);
    setText('modal-perfil',         cardEl.dataset.perfil);
    setText('modal-desafio',        cardEl.dataset.desafio);
    setText('modal-cargo',          cardEl.dataset.cargo);
    setText('modal-tipo',           cardEl.dataset.tipo);
    setText('modal-cidade-estado',
      (cardEl.dataset.cidade || '—') + ' / ' + (cardEl.dataset.estado || '—'));
    setText('modal-porte',          cardEl.dataset.porte);
    setText('modal-email',          cardEl.dataset.email);
    setText('modal-whatsapp',       cardEl.dataset.wa);
    setText('modal-status',         cardEl.dataset.status);
    setText('modal-data',           cardEl.dataset.data);

    var mensagemRow = document.getElementById('modal-mensagem-row');
    var mensagem     = cardEl.dataset.mensagem || '';
    if (mensagemRow) {
      mensagemRow.style.display = mensagem ? '' : 'none';
      setText('modal-mensagem', mensagem);
    }

    var utmRow = document.getElementById('modal-utm-row');
    var utm    = cardEl.dataset.utm || '';
    if (utmRow) {
      utmRow.style.display = utm ? '' : 'none';
      setText('modal-utm', utm);
    }

    /* Reset to detalhes tab, clear lists */
    switchTab('detalhes');
    var notesList = document.getElementById('notes-list');
    if (notesList) notesList.innerHTML = '';
    var histList = document.getElementById('historico-list');
    if (histList) histList.innerHTML = '';

    modalOverlay.classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function closeModal() {
    if (!modalOverlay) return;
    modalOverlay.classList.remove('open');
    document.body.style.overflow = '';
    currentLeadId = null;
  }

  function setText(id, value) {
    var el = document.getElementById(id);
    if (el) el.textContent = value || '—';
  }

  /* Close triggers */
  if (modalOverlay) {
    modalOverlay.addEventListener('click', function (e) {
      if (e.target === modalOverlay) closeModal();
    });
  }
  if (modalClose) modalClose.addEventListener('click', closeModal);
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeModal();
  });

  /* ── Tabs ─────────────────────────────────────────────────── */
  function switchTab(name) {
    document.querySelectorAll('.modal-tab').forEach(function (t) {
      t.classList.toggle('active', t.dataset.tab === name);
    });
    document.querySelectorAll('.modal-tab-pane').forEach(function (p) {
      p.classList.toggle('active', p.dataset.tab === name);
    });
  }

  document.querySelectorAll('.modal-tab').forEach(function (tab) {
    tab.addEventListener('click', function () {
      var name = this.dataset.tab;
      switchTab(name);
      if (name === 'notas'     && currentLeadId) loadNotas(currentLeadId);
      if (name === 'historico' && currentLeadId) loadHistorico(currentLeadId);
    });
  });

  /* ── "Ver detalhes" button → open modal ─────────────────── */
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('.kanban-card-btn');
    if (btn) {
      if (btn.classList.contains('card-btn-archive')) return;
      var card = btn.closest('.kanban-card');
      if (card) openModal(card);
    }
  });

  /* ═══════════════════════════════════════════════════════════
     NOTES
     ═══════════════════════════════════════════════════════════ */
  function loadNotas(leadId) {
    var list = document.getElementById('notes-list');
    if (!list) return;
    list.innerHTML = '<p style="color:#4a6080;font-size:13px">Carregando…</p>';

    postJSON('/api/crm.php', { action: 'notas', lead_id: parseInt(leadId, 10) })
      .then(function (data) {
        if (!data.ok) throw new Error(data.erro || 'Erro');
        if (!data.notas || data.notas.length === 0) {
          list.innerHTML = '<p style="color:#4a6080;font-size:13px">Nenhuma nota ainda.</p>';
          return;
        }
        list.innerHTML = data.notas.map(function (n) {
          return (
            '<div class="note-item">' +
              '<div class="note-header">' +
                '<span class="note-author">' + escHtml(n.usuario_nome) + '</span>' +
                '<span class="note-date">' + escHtml(n.criado_em) + '</span>' +
              '</div>' +
              '<div class="note-text">' + escHtml(n.nota) + '</div>' +
            '</div>'
          );
        }).join('');
      })
      .catch(function () {
        if (list) list.innerHTML = '<p style="color:#dc2626;font-size:13px">Erro ao carregar notas.</p>';
      });
  }

  var noteForm  = document.getElementById('note-form');
  var noteInput = document.getElementById('note-input');

  if (noteForm) {
    noteForm.addEventListener('submit', function (e) {
      e.preventDefault();
      var text = noteInput ? noteInput.value.trim() : '';
      if (!text || !currentLeadId) return;

      var btn = noteForm.querySelector('.note-submit');
      if (btn) btn.disabled = true;

      postJSON('/api/crm.php', {
        action:  'adicionar_nota',
        lead_id: parseInt(currentLeadId, 10),
        nota:    text,
      })
        .then(function (data) {
          if (data.ok) {
            if (noteInput) noteInput.value = '';
            loadNotas(currentLeadId);
          } else {
            alert('Erro ao salvar nota: ' + (data.erro || ''));
          }
        })
        .catch(function () {
          alert('Erro de conexão. Tente novamente.');
        })
        .finally(function () {
          if (btn) btn.disabled = false;
        });
    });
  }

  /* ═══════════════════════════════════════════════════════════
     HISTÓRICO
     ═══════════════════════════════════════════════════════════ */
  var tipoIcons = {
    criacao: '✦',
    mover:   '→',
    nota:    '✎',
  };

  function loadHistorico(leadId) {
    var list = document.getElementById('historico-list');
    if (!list) return;
    list.innerHTML = '<p style="color:#4a6080;font-size:13px">Carregando…</p>';

    postJSON('/api/crm.php', { action: 'historico', lead_id: parseInt(leadId, 10) })
      .then(function (data) {
        if (!data.ok) throw new Error(data.erro || 'Erro');
        if (!data.historico || data.historico.length === 0) {
          list.innerHTML = '<p style="color:#4a6080;font-size:13px">Nenhuma movimentação registrada.</p>';
          return;
        }
        list.innerHTML = data.historico.map(function (h) {
          var icon = tipoIcons[h.tipo] || '·';
          var user = h.usuario_nome ? ' · ' + escHtml(h.usuario_nome) : '';
          return (
            '<div class="hist-item hist-' + escHtml(h.tipo) + '">' +
              '<span class="hist-type">' + icon + '</span>' +
              '<div class="hist-body">' +
                '<span class="hist-desc">' + escHtml(h.descricao) + '</span>' +
                '<span class="hist-date">' + escHtml(h.criado_em) + user + '</span>' +
              '</div>' +
            '</div>'
          );
        }).join('');
      })
      .catch(function () {
        if (list) list.innerHTML = '<p style="color:#dc2626;font-size:13px">Erro ao carregar histórico.</p>';
      });
  }

  /* ═══════════════════════════════════════════════════════════
     KANBAN — SortableJS drag-drop
     ═══════════════════════════════════════════════════════════ */
  function updateColCounts() {
    document.querySelectorAll('.kanban-col').forEach(function (col) {
      var count  = col.querySelectorAll('.kanban-card').length;
      var badge  = col.querySelector('.col-count');
      var empty  = col.querySelector('.kanban-empty');
      if (badge) badge.textContent = count;
      if (empty) empty.style.display = count > 0 ? 'none' : '';
    });
  }

  window.arquivarLead = function (id) {
    if (!confirm('Arquivar este contato? Ele continuará visível em Contatos.')) return;

    var fd = new FormData();
    fd.append('action', 'arquivar');
    fd.append('id', id);
    var csrf = document.querySelector('meta[name="csrf-token"]');
    if (csrf) fd.append('csrf_token', csrf.content);

    fetch(appUrl('/api/crm.php'), { method: 'POST', body: fd })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (data.ok) {
          var card = document.querySelector('.kanban-card[data-id="' + id + '"]');
          if (card) {
            card.style.transition = 'opacity .3s, transform .3s';
            card.style.opacity = '0';
            card.style.transform = 'scale(0.95)';
            setTimeout(function () {
              card.remove();
              updateColCounts();
            }, 300);
          }
        }
      });
  };

  if (typeof Sortable !== 'undefined') {
    document.querySelectorAll('.kanban-col-body').forEach(function (colBody) {
      Sortable.create(colBody, {
        group:      'leads',
        animation:  150,
        ghostClass: 'kanban-ghost',
        chosenClass:'kanban-chosen',
        dragClass:  'kanban-drag',

        onEnd: function (evt) {
          var leadId    = evt.item.dataset.id;
          var newStatus = evt.to.closest('.kanban-col').dataset.status;

          updateColCounts();

          postJSON('/api/crm.php', {
            action: 'mover',
            id:     parseInt(leadId, 10),
            status: newStatus,
          }).catch(function () {
            /* Revert on network failure */
            var oldIdx = evt.oldIndex;
            var ref    = evt.from.children[oldIdx] || null;
            evt.from.insertBefore(evt.item, ref);
            updateColCounts();
          });
        },
      });
    });

    updateColCounts();
  }

})();

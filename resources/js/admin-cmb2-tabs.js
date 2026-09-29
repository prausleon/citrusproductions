/**
 * CMB2 field tabs (wp-admin only).
 * ---------------------------------------------------------------------
 * Turns any CMB2 box that has 2+ `type => 'title'` divider fields (see
 * app/Fields/v0-auto-fields.php — the Homepage box uses these to mark out
 * "Hero Section", "About Section", etc.) into a tabbed panel, one tab per
 * divider, instead of one long scrolling list of fields.
 *
 * This is driven entirely by CMB2's own markup (`.cmb2-metabox` boxes,
 * `.cmb-row[data-fieldtype="title"]` dividers) rather than a hand-kept list
 * of field IDs, so it needs no updating when fields or whole sections are
 * added, renamed or reordered — new dividers just become new tabs
 * automatically. A box with fewer than 2 dividers (e.g. Project Details)
 * is left exactly as CMB2 renders it.
 *
 * Reordering .cmb-row elements into tab panels here doesn't disturb any of
 * CMB2's own JS (media uploads, repeatable-group sorting, etc.) — moving a
 * DOM node to a new parent keeps its event listeners intact, and CMB2's own
 * init re-queries the document by selector rather than by position.
 */
(function () {
  function buildTabs(box) {
    var rows = Array.prototype.slice.call(box.children).filter(function (el) {
      return el.classList && el.classList.contains('cmb-row')
    })

    var dividerIndexes = []
    rows.forEach(function (row, i) {
      if (row.getAttribute('data-fieldtype') === 'title') dividerIndexes.push(i)
    })

    if (dividerIndexes.length < 2) return // not worth tabbing

    var boxId = box.id || 'v0-cmb2-box'
    var wrap = document.createElement('div')
    wrap.className = 'v0-cmb2-tabs'

    var nav = document.createElement('div')
    nav.className = 'v0-cmb2-tabs-nav'
    nav.setAttribute('role', 'tablist')
    wrap.appendChild(nav)

    var panels = []
    var buttons = []

    dividerIndexes.forEach(function (startIdx, tabIndex) {
      var endIdx = dividerIndexes[tabIndex + 1] !== undefined ? dividerIndexes[tabIndex + 1] : rows.length
      var titleRow = rows[startIdx]
      var heading = titleRow.querySelector('.cmb2-metabox-title, .cmb2-metabox-title-anchor')
      var label = heading ? heading.textContent.trim() : 'Tab ' + (tabIndex + 1)

      var panelId = boxId + '-tab-panel-' + tabIndex
      var panel = document.createElement('div')
      panel.className = 'v0-cmb2-tab-panel'
      panel.id = panelId
      panel.setAttribute('role', 'tabpanel')
      panel.hidden = tabIndex !== 0

      for (var i = startIdx; i < endIdx; i++) {
        panel.appendChild(rows[i]) // moves the row (and its listeners) here
      }
      // The heading is redundant with the tab label itself; hide it, but
      // keep the row (and any description text under it) in the panel.
      if (heading) heading.style.display = 'none'

      wrap.appendChild(panel)
      panels.push(panel)

      var btn = document.createElement('button')
      btn.type = 'button'
      btn.className = 'v0-cmb2-tab-button'
      btn.textContent = label
      btn.id = boxId + '-tab-' + tabIndex
      btn.setAttribute('role', 'tab')
      btn.setAttribute('aria-selected', tabIndex === 0 ? 'true' : 'false')
      btn.setAttribute('aria-controls', panelId)
      btn.addEventListener('click', function () {
        panels.forEach(function (p, j) { p.hidden = j !== tabIndex })
        buttons.forEach(function (b) { b.setAttribute('aria-selected', 'false') })
        btn.setAttribute('aria-selected', 'true')
        try { sessionStorage.setItem('v0CmbTab:' + boxId, String(tabIndex)) } catch (e) {}
      })
      buttons.push(btn)
      nav.appendChild(btn)
    })

    box.insertBefore(wrap, box.firstChild)

    // Reopen whichever tab was last viewed in this browser tab/session.
    try {
      var saved = sessionStorage.getItem('v0CmbTab:' + boxId)
      var savedIndex = saved === null ? -1 : parseInt(saved, 10)
      if (savedIndex > 0 && buttons[savedIndex]) buttons[savedIndex].click()
    } catch (e) {}
  }

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.cmb2-metabox').forEach(buildTabs)
  })
})()

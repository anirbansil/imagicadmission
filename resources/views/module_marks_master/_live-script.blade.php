{{--
  Live total / percentage / grade while typing marks.
  Each row ($rowSelector) has two .js-ob inputs (theory, practical) and either its
  own two .js-full inputs or uses the page's #mk-theory-full / #mk-practical-full.
  With $overall, the course total in #mk-overall-pct / -grade is updated too.
--}}
<style>.mk-input.mk-bad { border-color: #c9302c; background: #fdf0ef; }</style>
<script>
(function () {
  var grades = @json(config('marks.grades'));
  var rows = document.querySelectorAll(@json($rowSelector));
  var overall = @json(!empty($overall));
  if (!rows.length) return;

  function num(input) {
    var v = (input && input.value || '').trim();
    return v === '' ? null : (/^\d+$/.test(v) ? parseInt(v, 10) : NaN);
  }
  function fmt(p) { return p === null ? '—' : (Math.round(p * 10) / 10) + '%'; }
  function gradeHtml(p) {
    if (p === null) return '<span class="mk-muted">—</span>';
    for (var i = 0; i < grades.length; i++) {
      if (p >= grades[i].min) {
        var cls = grades[i].grade.toLowerCase().replace('+', 'p');
        return '<span class="mk-grade mk-g-' + cls + '" title="' + grades[i].remark + '">' + grades[i].grade + '</span>';
      }
    }
    return '<span class="mk-muted">—</span>';
  }
  function fullInputs(row) {
    var own = row.querySelectorAll('.js-full');
    return own.length ? own : [document.getElementById('mk-theory-full'), document.getElementById('mk-practical-full')];
  }

  function update() {
    var sumOb = 0, sumFull = 0, evaluated = 0;
    rows.forEach(function (row) {
      var ob = row.querySelectorAll('.js-ob'), full = fullInputs(row);
      var t = num(ob[0]), p = num(ob[1]), ft = num(full[0]), fp = num(full[1]);
      var total = row.querySelector('.js-total'), pct = row.querySelector('.js-pct'), grade = row.querySelector('.js-grade');
      ob[0].classList.remove('mk-bad'); ob[1].classList.remove('mk-bad');

      if (t === null && p === null) {
        total.textContent = '—'; pct.textContent = '—'; grade.innerHTML = gradeHtml(null);
        return;
      }
      // A section with full marks 0 counts as 0 when left blank.
      if (t === null && ft === 0) t = 0;
      if (p === null && fp === 0) p = 0;
      var bad = false;
      if (t === null || isNaN(t) || (ft !== null && !isNaN(ft) && t > ft)) { ob[0].classList.add('mk-bad'); bad = true; }
      if (p === null || isNaN(p) || (fp !== null && !isNaN(fp) && p > fp)) { ob[1].classList.add('mk-bad'); bad = true; }
      if (bad || ft === null || fp === null || isNaN(ft) || isNaN(fp) || ft + fp === 0) {
        total.textContent = '—'; pct.textContent = '—'; grade.innerHTML = gradeHtml(null);
        return;
      }
      var percent = (t + p) * 100 / (ft + fp);
      total.textContent = (t + p) + ' / ' + (ft + fp);
      pct.textContent = fmt(percent);
      grade.innerHTML = gradeHtml(percent);
      sumOb += t + p; sumFull += ft + fp; evaluated++;
    });

    if (overall) {
      var p = evaluated ? sumOb * 100 / sumFull : null;
      var el = document.getElementById('mk-overall-pct');
      if (el) el.textContent = fmt(p);
      var g = document.getElementById('mk-overall-grade');
      if (g) g.innerHTML = gradeHtml(p);
    }
  }

  document.addEventListener('input', function (e) {
    if (e.target.classList.contains('js-ob') || e.target.classList.contains('js-full')) update();
  });
  update();
})();
</script>

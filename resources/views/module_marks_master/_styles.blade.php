<style>
  .mk-page { --mk-primary: #571d1f; --mk-primary-dark: #3a1214; --mk-accent: #eca107; --mk-ink: #2B2022; --mk-muted: #8a7f80; --mk-line: #ece6e3; --mk-soft: #faf7f5; color: var(--mk-ink); }
  .mk-page.dashboard-materials { margin-top: 12px; }
  .mk-page * { box-sizing: border-box; }
  .mk-card { background: #fff; border: 1px solid var(--mk-line); border-radius: 10px; box-shadow: 0 1px 3px rgba(43, 32, 34, .05); margin-bottom: 16px; }
  .mk-card-head { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; padding: 14px 18px; border-bottom: 1px solid var(--mk-line); }
  .mk-card-head h3 { font-size: 17px; font-weight: 600; margin: 0; }
  .mk-card-head p { margin: 2px 0 0; color: var(--mk-muted); font-size: 13px; }
  .mk-card-body { padding: 14px 18px; }

  .mk-filters { display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end; padding: 14px 18px; }
  .mk-filters label { display: block; font-size: 12px; font-weight: 600; color: var(--mk-muted); margin: 0 0 4px; text-transform: uppercase; letter-spacing: .3px; }
  .mk-filters .mk-field { flex: 1 1 170px; min-width: 0; }
  .mk-filters .mk-field.wide { flex-basis: 240px; }
  .mk-filters .form-control { height: 38px; }
  .mk-filters .mk-actions { display: flex; gap: 8px; align-items: center; }

  .mk-btn { display: inline-flex; align-items: center; justify-content: center; gap: 6px; border: 1px solid var(--mk-line); background: #fff; color: var(--mk-ink); border-radius: 6px; padding: 8px 14px; font-size: 13.5px; font-weight: 600; cursor: pointer; text-decoration: none; white-space: nowrap; line-height: 1.2; }
  .mk-btn:hover { border-color: var(--mk-primary); color: var(--mk-primary); text-decoration: none; }
  .mk-btn.primary { background: var(--mk-primary); border-color: var(--mk-primary); color: #fff; }
  .mk-btn.primary:hover { background: var(--mk-primary-dark); color: #fff; }
  .mk-btn.accent { background: var(--mk-accent); border-color: var(--mk-accent); color: var(--mk-ink); }
  .mk-btn.small { padding: 5px 10px; font-size: 12.5px; }
  .mk-link { color: var(--mk-muted); font-size: 13px; }

  .mk-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
  .mk-table th { background: var(--mk-soft); color: var(--mk-muted); font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; padding: 10px 12px; border-bottom: 1px solid var(--mk-line); text-align: left; white-space: nowrap; }
  .mk-table td { padding: 10px 12px; border-bottom: 1px solid var(--mk-line); vertical-align: middle; }
  .mk-table tr:last-child td { border-bottom: 0; }
  .mk-table .num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
  .mk-table .center { text-align: center; }
  .mk-table small { display: block; color: var(--mk-muted); font-size: 12px; }
  .mk-table tr.mk-row-error td { background: #fdf0ef; }
  .mk-table tfoot td { background: var(--mk-soft); font-weight: 700; }

  .mk-input { width: 72px; height: 34px; border: 1px solid #d9d0ce; border-radius: 6px; padding: 4px 8px; text-align: right; font-variant-numeric: tabular-nums; }
  .mk-input:focus { outline: none; border-color: var(--mk-primary); box-shadow: 0 0 0 2px rgba(87, 29, 31, .12); }
  .mk-of { color: var(--mk-muted); font-size: 12.5px; white-space: nowrap; }
  .mk-select { height: 34px; border: 1px solid #d9d0ce; border-radius: 6px; padding: 4px 6px; max-width: 180px; }
  .mk-date { height: 34px; border: 1px solid #d9d0ce; border-radius: 6px; padding: 4px 6px; }

  .mk-grade { display: inline-block; min-width: 34px; text-align: center; font-weight: 700; font-size: 12.5px; border-radius: 6px; padding: 3px 7px; }
  .mk-g-ap, .mk-g-a { background: #e3f4e8; color: #1e6b34; }
  .mk-g-bp, .mk-g-b { background: #e5eef9; color: #1f4f8a; }
  .mk-g-c { background: #fff3d6; color: #8a5a00; }
  .mk-g-d { background: #fde9dc; color: #9a4512; }
  .mk-g-f { background: #fbe3e1; color: #a12620; }
  .mk-muted { color: var(--mk-muted); }
  .mk-pill { display: inline-block; font-size: 12px; font-weight: 600; border-radius: 12px; padding: 2px 10px; white-space: nowrap; }
  .mk-pill.final { background: #e3f4e8; color: #1e6b34; }
  .mk-pill.partial { background: #fff3d6; color: #8a5a00; }
  .mk-pill.none { background: var(--mk-soft); color: var(--mk-muted); border: 1px solid var(--mk-line); }

  .mk-bar { display: flex; align-items: center; gap: 8px; }
  .mk-bar span { flex: 1; height: 6px; min-width: 50px; background: #efe7e6; border-radius: 3px; overflow: hidden; }
  .mk-bar i { display: block; height: 100%; background: var(--mk-primary); }

  .mk-summary { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); border-top: 1px solid var(--mk-line); }
  .mk-summary div { padding: 12px 18px; border-right: 1px solid var(--mk-line); }
  .mk-summary div:last-child { border-right: 0; }
  .mk-summary > div > span { display: block; font-size: 11.5px; color: var(--mk-muted); text-transform: uppercase; letter-spacing: .4px; font-weight: 600; }
  .mk-summary strong { font-size: 18px; }

  .mk-student-link { color: var(--mk-ink); }
  .mk-student-link:hover { color: var(--mk-primary); text-decoration: underline; }
  .mk-count { display: inline-block; margin-left: 6px; font-size: 11px; font-weight: 700; border-radius: 10px; padding: 1px 8px; background: #fff3d6; color: #8a5a00; vertical-align: 1px; white-space: nowrap; }
  .mk-tabs { display: flex; gap: 6px; flex-wrap: wrap; padding: 10px 18px; border-top: 1px solid var(--mk-line); background: var(--mk-soft); }
  .mk-tab { display: inline-flex; align-items: center; gap: 7px; max-width: 100%; border: 1px solid var(--mk-line); background: #fff; color: var(--mk-ink); border-radius: 18px; padding: 5px 12px; font-size: 13px; font-weight: 600; text-decoration: none; }
  .mk-tab:hover { border-color: var(--mk-primary); color: var(--mk-primary); text-decoration: none; }
  .mk-tab.active { background: var(--mk-primary); border-color: var(--mk-primary); color: #fff; }
  .mk-tab-name { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 260px; }
  .mk-tab small { font-weight: 600; opacity: .7; font-variant-numeric: tabular-nums; }
  .mk-tab-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; background: var(--mk-accent); }
  .mk-tab-dot.completed { background: #2f8a4c; }
  .mk-tab.active .mk-tab-dot { box-shadow: 0 0 0 2px #fff; }

  .mk-errors { background: #fdf0ef; border: 1px solid #f2c9c5; color: #8b1e17; border-radius: 8px; padding: 10px 14px; margin-bottom: 14px; font-size: 13.5px; }
  .mk-errors ul { margin: 4px 0 0; padding-left: 18px; }
  .mk-note { font-size: 13px; color: var(--mk-muted); margin: 0; }
  .mk-footer { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; padding: 12px 18px; border-top: 1px solid var(--mk-line); background: var(--mk-soft); border-radius: 0 0 10px 10px; }
  .mk-empty { padding: 26px 18px; text-align: center; color: var(--mk-muted); }

  @media (max-width: 575px) {
    .mk-card-head, .mk-card-body, .mk-filters, .mk-footer { padding-left: 12px; padding-right: 12px; }
  }
</style>

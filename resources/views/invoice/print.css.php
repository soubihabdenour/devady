/* ============================================================
   Invoice — print + screen styles.
   The .page block is paper-like and shared with the PDF view
   (pdf.php does NOT load brand.css and overrides body to white).
   Screen-only chrome (body bg, toolbar, share-box) uses brand
   tokens loaded via /css/brand.css in print.php.
   ============================================================ */

* { box-sizing: border-box; margin: 0; padding: 0; }

/* Default (PDF) body — pdf.php further forces white !important. */
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    font-size: 13px; color: #0f172a; line-height: 1.5; background: #f8fafc;
}

/* Screen-only: dark gradient backdrop, matching admin shell. */
body.invoice-screen {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    color: var(--text, #e2e8f0);
    background:
        radial-gradient(ellipse 1000px 500px at 20% -10%, rgba(56,189,248,0.10), transparent 50%),
        radial-gradient(ellipse 900px 500px at 80% -10%,  rgba(129,140,248,0.08), transparent 55%),
        var(--bg, #0a0e1a);
    min-height: 100vh;
    padding-bottom: 48px;
    -webkit-font-smoothing: antialiased;
}

/* === Toolbar (screen only) === */
.toolbar {
    position: sticky; top: 0; z-index: 50;
    background: rgba(10,14,26,0.85);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border-bottom: 1px solid var(--border, #1f2a44);
    padding: 12px 24px;
    display: flex; gap: 8px; justify-content: flex-end; align-items: center;
}
.toolbar .muted { margin-right: auto; font-size: 13px; color: var(--muted, #94a3b8); }

/* === Share box (screen only) === */
.share-box {
    display: flex; gap: 12px; align-items: center;
    padding: 10px 14px;
    background: rgba(56,189,248,0.08);
    border: 1px solid rgba(56,189,248,0.30);
    border-radius: 8px;
    font-size: 13px; color: var(--text, #e2e8f0);
}
.share-box strong { color: var(--accent, #38bdf8); }
.share-box input {
    flex: 1; border: 0; background: transparent;
    font-family: 'JetBrains Mono', Menlo, monospace;
    font-size: 12px; outline: none; color: var(--text, #e2e8f0);
    padding: 0;
}

/* === Invoice document — kept paper-like, identical to PDF === */
.page {
    max-width: 820px; margin: 24px auto; background: white;
    padding: 48px 56px; border: 1px solid #e2e8f0; color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}
body.invoice-screen .page {
    border: 0;
    border-radius: 4px;
    box-shadow: 0 24px 60px -20px rgba(0,0,0,0.55), 0 8px 24px -8px rgba(0,0,0,0.4);
    margin: 32px auto;
}
.header-row { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px; gap: 32px; }
.company { font-size: 12px; color: #334155; line-height: 1.55; flex: 1; }
.company .name { font-weight: 700; font-size: 18px; color: #0f172a; margin-bottom: 4px; }
.company .legal-line { color: #64748b; }
.invoice-title { text-align: right; }
.invoice-title h1 { font-size: 26px; font-weight: 700; letter-spacing: 1px; color: #0f172a; line-height: 1.1; }
.invoice-title .number { color: #0f172a; margin-top: 8px; font-size: 14px; font-variant-numeric: tabular-nums; font-weight: 600; }
.status-banner { display: inline-block; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
.status-banner.paid  { background: #dcfce7; color: #15803d; }
.status-banner.void  { background: #fee2e2; color: #b91c1c; }
.status-banner.draft { background: #f1f5f9; color: #475569; }
.status-banner.sent  { background: #dbeafe; color: #1d4ed8; }
.meta-row { display: grid; grid-template-columns: 1fr auto; gap: 32px; margin-bottom: 28px; }
.meta-row h3 { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #64748b; margin-bottom: 6px; font-weight: 700; }
.meta-row .body { font-size: 13px; line-height: 1.55; }
.dates table { width: auto; margin-left: auto; }
.dates td { padding: 2px 0 2px 16px; font-size: 13px; }
.dates td:first-child { color: #64748b; }
.dates td:last-child  { text-align: right; font-variant-numeric: tabular-nums; }
.lines { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
.lines th { background: #f8fafc; padding: 10px 12px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 700; border-bottom: 2px solid #cbd5e1; }
.lines td { padding: 12px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
.lines td.num, .lines th.num { text-align: right; font-variant-numeric: tabular-nums; }
.totals { width: 320px; margin-left: auto; border-collapse: collapse; }
.totals td { padding: 5px 0 5px 16px; font-size: 13px; }
.totals td:first-child { color: #64748b; }
.totals td:last-child { text-align: right; font-variant-numeric: tabular-nums; }
.totals tr.grand td { padding-top: 10px; border-top: 2px solid #0f172a; font-weight: 700; font-size: 15px; color: #0f172a; }
.amount-words { margin-top: 16px; padding: 10px 14px; background: #f8fafc; border-left: 3px solid #0f172a; font-size: 12px; color: #334155; font-style: italic; }
.legal { margin-top: 28px; padding-top: 18px; border-top: 1px solid #e2e8f0; font-size: 11px; color: #475569; line-height: 1.6; }
.legal h4 { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #0f172a; margin-bottom: 8px; }
.legal ul { list-style: none; padding-left: 0; }
.legal li { padding-left: 14px; position: relative; margin-bottom: 4px; }
.legal li::before { content: "•"; position: absolute; left: 2px; color: #94a3b8; }
.bank { margin-top: 22px; padding: 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 12px; }
.bank h4 { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #0f172a; margin-bottom: 8px; }
.bank-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 4px 24px; }
.bank-grid .k { color: #64748b; }
.bank-grid .v { font-variant-numeric: tabular-nums; }
.notes { margin-top: 20px; font-size: 12px; color: #475569; }
.notes strong { color: #0f172a; }
.signature { margin-top: 40px; display: flex; justify-content: flex-end; }
.signature .box { width: 300px; text-align: center; font-size: 12px; color: #64748b; }
.signature .marks { position: relative; height: 110px; margin-bottom: 4px; }
.signature .marks img { position: absolute; left: 50%; transform: translateX(-50%); }
.signature .marks .sig   { bottom: 22px; max-height: 70px;  max-width: 240px; }
.signature .marks .stamp { bottom: 6px;  max-height: 100px; max-width: 200px; opacity: 0.85; }
.signature .line { border-top: 1px solid #94a3b8; padding-top: 6px; }

@media print {
    body, body.invoice-screen { background: white !important; color: #0f172a; }
    .toolbar, .share-box { display: none; }
    .page, body.invoice-screen .page {
        margin: 0; padding: 32px 40px; border: none;
        max-width: none; box-shadow: none; border-radius: 0;
    }
}

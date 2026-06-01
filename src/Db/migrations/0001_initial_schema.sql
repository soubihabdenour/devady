CREATE TABLE IF NOT EXISTS settings (
    key   TEXT PRIMARY KEY,
    value TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS clients (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    name            TEXT NOT NULL,
    email           TEXT,
    phone           TEXT,
    address         TEXT,
    tax_id          TEXT,
    notes           TEXT,
    country         TEXT,
    business_reg_no TEXT,
    contact_name    TEXT,
    created_at      INTEGER NOT NULL,
    updated_at      INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS invoices (
    id               INTEGER PRIMARY KEY AUTOINCREMENT,
    number           TEXT NOT NULL UNIQUE,
    client_id        INTEGER NOT NULL REFERENCES clients(id),
    issue_date       TEXT NOT NULL,
    due_date         TEXT,
    status           TEXT NOT NULL DEFAULT 'draft',
    notes            TEXT,
    currency         TEXT NOT NULL DEFAULT 'DZD',
    subtotal         REAL NOT NULL DEFAULT 0,
    tax_total        REAL NOT NULL DEFAULT 0,
    total            REAL NOT NULL DEFAULT 0,
    paid_at          INTEGER,
    place_of_issue   TEXT,
    amount_in_words  TEXT,
    is_export        INTEGER NOT NULL DEFAULT 0,
    treaty_country   TEXT,
    signed_at        INTEGER,
    created_at       INTEGER NOT NULL,
    updated_at       INTEGER NOT NULL
);
CREATE INDEX IF NOT EXISTS idx_invoices_client ON invoices(client_id);
CREATE INDEX IF NOT EXISTS idx_invoices_status ON invoices(status);

CREATE TABLE IF NOT EXISTS invoice_lines (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    invoice_id    INTEGER NOT NULL REFERENCES invoices(id) ON DELETE CASCADE,
    position      INTEGER NOT NULL,
    description   TEXT NOT NULL,
    quantity      REAL NOT NULL DEFAULT 1,
    unit_price    REAL NOT NULL,
    tax_rate      REAL NOT NULL DEFAULT 0,
    line_subtotal REAL NOT NULL,
    line_tax      REAL NOT NULL,
    line_total    REAL NOT NULL
);
CREATE INDEX IF NOT EXISTS idx_lines_invoice ON invoice_lines(invoice_id);

CREATE TABLE IF NOT EXISTS leads (
    id                  INTEGER PRIMARY KEY AUTOINCREMENT,
    name                TEXT NOT NULL,
    email               TEXT,
    subject             TEXT,
    message             TEXT,
    ip                  TEXT,
    user_agent          TEXT,
    created_at          INTEGER NOT NULL,
    read_at             INTEGER,
    converted_client_id INTEGER REFERENCES clients(id) ON DELETE SET NULL
);
CREATE INDEX IF NOT EXISTS idx_leads_created ON leads(created_at DESC);

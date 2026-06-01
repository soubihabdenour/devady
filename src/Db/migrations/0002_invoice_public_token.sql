ALTER TABLE invoices ADD COLUMN public_token TEXT;
CREATE UNIQUE INDEX IF NOT EXISTS idx_invoices_token ON invoices(public_token);

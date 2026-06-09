<?php declare(strict_types=1);

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Stores\InvoiceStore;
use Dompdf\Dompdf;
use Dompdf\Options;
use Throwable;

final class InvoiceController extends Controller
{
    public function showForm(Request $request): Response
    {
        $id = (int)$request->param('id', '0');
        $invoice = $id ? $this->kernel->invoices()->find($id) : null;
        if ($id && !$invoice) return Response::notFound();

        return $this->view('admin/invoice_form', [
            'invoice'         => $invoice,
            'allClients'      => $this->kernel->clients()->all(),
            'companyDefaults' => $this->kernel->settings()->all(),
            'error'           => null,
        ]);
    }

    public function save(Request $request): Response
    {
        $id = (int)$request->param('id', '0');

        $data = [
            'client_id'       => (int)$request->input('client_id', '0'),
            'issue_date'      => $request->input('issue_date', date('Y-m-d')),
            'due_date'        => $request->input('due_date'),
            'status'          => in_array($request->input('status', ''), ['draft','sent','paid','void'], true)
                                    ? $request->input('status') : 'draft',
            'notes'           => trim((string)$request->input('notes', '')) ?: null,
            'currency'        => strtoupper(substr(trim((string)$request->input('currency', 'DZD')), 0, 3)) ?: 'DZD',
            'place_of_issue'  => trim((string)$request->input('place_of_issue', '')) ?: null,
            'is_export'       => !empty($_POST['is_export']),
            'treaty_country'  => trim((string)$request->input('treaty_country', '')) ?: null,
            'amount_in_words' => trim((string)$request->input('amount_in_words', '')) ?: null,
        ];

        $lines = [];
        foreach (($_POST['lines'] ?? []) as $line) {
            $desc = trim($line['description'] ?? '');
            if ($desc === '') continue;
            $lines[] = [
                'description' => $desc,
                'quantity'    => (float)($line['quantity']   ?? 1),
                'unit_price'  => (float)($line['unit_price'] ?? 0),
                'tax_rate'    => (float)($line['tax_rate']   ?? 0),
            ];
        }

        if ($data['amount_in_words'] === null && !empty($lines)) {
            $totals = InvoiceStore::computeTotals($lines);
            $data['amount_in_words'] = amount_in_words($totals['total'], $data['currency']);
        }

        $error = null;
        if (!$data['client_id']) {
            $error = 'Please pick a client.';
        } elseif (empty($lines)) {
            $error = 'Add at least one line with a description.';
        } else {
            try {
                if ($id) {
                    $this->kernel->invoices()->update($id, $data, $lines);
                } else {
                    $id = $this->kernel->invoices()->create($data, $lines, $this->kernel->settings());
                }
                return $this->redirect('/admin/invoices/' . $id);
            } catch (Throwable $e) {
                $error = $e->getMessage();
            }
        }

        return $this->view('admin/invoice_form', [
            'invoice'         => $id ? $this->kernel->invoices()->find($id) : null,
            'allClients'      => $this->kernel->clients()->all(),
            'companyDefaults' => $this->kernel->settings()->all(),
            'error'           => $error,
        ]);
    }

    public function show(Request $request): Response
    {
        $id      = (int)$request->param('id', '0');
        $invoice = $this->kernel->invoices()->find($id);
        if (!$invoice) return Response::notFound('Invoice not found.');

        return $this->view('invoice/print', [
            'invoice' => $invoice,
            'client'  => $this->kernel->clients()->find((int)$invoice['client_id']),
            'company' => $this->kernel->settings()->all(),
            'public'  => false,
        ]);
    }

    public function delete(Request $request): Response
    {
        $id = (int)$request->param('id', '0');
        $this->kernel->invoices()->delete($id);
        return $this->redirect('/admin');
    }

    public function sign(Request $request): Response
    {
        $id = (int)$request->param('id', '0');
        $s  = $this->kernel->settings();
        if (!$s->get('company_signature_file') && !$s->get('company_stamp_file')) {
            // Could surface as flash; for now just redirect back without signing.
            return $this->redirect('/admin/invoices/' . $id . '?err=' . urlencode('Upload a signature or stamp first.'));
        }
        $this->kernel->invoices()->sign($id);
        return $this->redirect('/admin/invoices/' . $id);
    }

    public function unsign(Request $request): Response
    {
        $id = (int)$request->param('id', '0');
        $this->kernel->invoices()->unsign($id);
        return $this->redirect('/admin/invoices/' . $id);
    }

    public function share(Request $request): Response
    {
        $id      = (int)$request->param('id', '0');
        $invoice = $this->kernel->invoices()->find($id);
        if (!$invoice) return Response::notFound();

        $token = $invoice['public_token'] ?: $this->kernel->invoices()->generatePublicToken($id);
        return $this->redirect('/admin/invoices/' . $id . '?token=' . urlencode($token));
    }

    public function revoke(Request $request): Response
    {
        $id = (int)$request->param('id', '0');
        $this->kernel->invoices()->revokePublicToken($id);
        return $this->redirect('/admin/invoices/' . $id);
    }

    public function pdf(Request $request): Response
    {
        $id      = (int)$request->param('id', '0');
        $invoice = $this->kernel->invoices()->find($id);
        if (!$invoice) return Response::notFound();

        $html = $this->kernel->view()->renderToString('invoice/pdf', [
            'invoice' => $invoice,
            'client'  => $this->kernel->clients()->find((int)$invoice['client_id']),
            'company' => $this->kernel->settings()->all(),
        ]);

        $options = new Options();
        // Local file:// images only; never let Dompdf fetch arbitrary URLs.
        $options->set('isRemoteEnabled', false);
        $options->set('isPhpEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response(
            $dompdf->output(),
            200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $invoice['number'] . '.pdf"',
            ],
        );
    }

    public function publicShow(Request $request): Response
    {
        $token   = (string)$request->param('token', '');
        $invoice = $this->kernel->invoices()->findByToken($token);
        if (!$invoice) return Response::notFound('This invoice link is invalid or has been revoked.');

        return $this->view('invoice/print', [
            'invoice' => $invoice,
            'client'  => $this->kernel->clients()->find((int)$invoice['client_id']),
            'company' => $this->kernel->settings()->all(),
            'public'  => true,
        ]);
    }
}

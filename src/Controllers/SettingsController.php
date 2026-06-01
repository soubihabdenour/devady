<?php declare(strict_types=1);

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use Throwable;

final class SettingsController extends Controller
{
    private const FIELDS = [
        'company_name', 'company_legal_name', 'company_address', 'company_city',
        'company_email', 'company_phone', 'company_tax_id', 'company_anae_no',
        'company_activity_code', 'company_activity_label',
        'company_bank_name', 'company_rib', 'company_iban',
        'company_swift', 'company_domiciliation',
        'currency', 'default_tax_rate', 'invoice_prefix', 'invoice_padding',
    ];

    public function show(Request $request): Response
    {
        return $this->view('admin/settings', [
            'settings' => $this->kernel->settings()->all(),
            'saved'    => isset($request->query['saved']),
            'error'    => null,
        ]);
    }

    public function update(Request $request): Response
    {
        $s = $this->kernel->settings();

        foreach (self::FIELDS as $key) {
            if (isset($_POST[$key])) {
                $s->set($key, (string)$_POST[$key]);
            }
        }

        $error = null;
        try {
            foreach (['signature', 'stamp'] as $slot) {
                if (!empty($_POST['remove_' . $slot])) {
                    delete_stored_image($s->get('company_' . $slot . '_file'), $this->kernel->base);
                    $s->set('company_' . $slot . '_file', '');
                    continue;
                }
                // Hand-drawn signature data URL (from canvas)
                if ($slot === 'signature') {
                    $drawing = (string)($_POST['signature_drawing'] ?? '');
                    if ($drawing !== '') {
                        $rel = store_signature_dataurl($drawing, 'signature', $this->kernel->base);
                        if ($rel) {
                            $s->set('company_signature_file', $rel);
                            continue;
                        }
                    }
                }
                $rel = store_image_upload($slot . '_upload', $slot, $this->kernel->base);
                if ($rel !== null) {
                    $s->set('company_' . $slot . '_file', $rel);
                }
            }
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }

        return $this->view('admin/settings', [
            'settings' => $s->all(),
            'saved'    => $error === null,
            'error'    => $error,
        ]);
    }
}

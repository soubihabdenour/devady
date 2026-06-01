<?php declare(strict_types=1);

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Support\I18n;
use App\Support\Notifier;

final class ContactController extends Controller
{
    public function submit(Request $request): Response
    {
        $lang = I18n::current();
        $back = '/?lang=' . urlencode($lang);

        // Honeypot — bots fill every input. A filled hidden field = silent success.
        if (!empty(trim($request->input('website', '')))) {
            return $this->redirect($back . '&sent=1#contact');
        }

        $name    = trim((string)$request->input('name'));
        $email   = trim((string)$request->input('email'));
        $subject = trim((string)$request->input('subject'));
        $message = trim((string)$request->input('message'));

        $err = null;
        if ($name === '' || $email === '' || $message === '') {
            $err = t('err_required');
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $err = t('err_email');
        } elseif (strlen($message) < 10) {
            $err = t('err_short');
        }

        if ($err) {
            return $this->redirect($back . '&err=' . urlencode($err) . '#contact');
        }

        $lead = [
            'name'       => $name,
            'email'      => $email,
            'subject'    => $subject,
            'message'    => $message,
            'ip'         => $request->ip(),
            'user_agent' => $request->userAgent(),
        ];
        $this->kernel->leads()->create($lead);
        Notifier::notifyNewLead($lead);

        return $this->redirect($back . '&sent=1#contact');
    }
}

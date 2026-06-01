<?php declare(strict_types=1);

namespace App\Support;

final class Notifier
{
    /** Send a notification email to NOTIFY_EMAIL. Returns true if dispatched. */
    public static function notifyNewLead(array $lead): bool
    {
        $to = Config::get('NOTIFY_EMAIL');
        if (!$to) return false;

        $from    = Config::get('NOTIFY_FROM', 'noreply@devady.com');
        $brand   = Config::get('APP_NAME', 'DevAdy');
        $subject = '[' . $brand . '] New lead: ' . ($lead['subject'] ?: $lead['name']);

        $body  = "A new lead just landed on your website.\n\n";
        $body .= "From:    " . ($lead['name']    ?? '') . "\n";
        $body .= "Email:   " . ($lead['email']   ?? '') . "\n";
        $body .= "Subject: " . ($lead['subject'] ?? '') . "\n\n";
        $body .= "Message:\n" . ($lead['message'] ?? '') . "\n";

        $headers = "From: $brand <$from>\r\n"
                 . "Reply-To: " . ($lead['email'] ?? $from) . "\r\n"
                 . "Content-Type: text/plain; charset=utf-8\r\n";

        // mail() is a best-effort. Failures are silent — the lead is already in the DB.
        return @mail($to, $subject, $body, $headers);
    }
}

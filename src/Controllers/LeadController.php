<?php declare(strict_types=1);

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;

final class LeadController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->view('admin/leads', [
            'leads' => $this->kernel->leads()->all(),
            'error' => null,
        ]);
    }

    public function show(Request $request): Response
    {
        $id   = (int)$request->param('id', '0');
        $lead = $this->kernel->leads()->find($id);
        if (!$lead) return Response::notFound('Lead not found.');
        if (empty($lead['read_at'])) {
            $this->kernel->leads()->markRead($id);
            $lead['read_at'] = time();
        }
        return $this->view('admin/lead_view', ['lead' => $lead]);
    }

    public function convert(Request $request): Response
    {
        $id   = (int)$request->param('id', '0');
        $lead = $this->kernel->leads()->find($id);
        if (!$lead) return Response::notFound();
        if (!empty($lead['converted_client_id'])) {
            return $this->redirect('/admin/clients/' . (int)$lead['converted_client_id'] . '/edit');
        }
        $clientId = $this->kernel->clients()->save([
            'name'  => $lead['name'],
            'email' => $lead['email'],
            'notes' => 'Converted from lead #' . $id
                . ($lead['subject'] ? "\nSubject: " . $lead['subject'] : '')
                . ($lead['message'] ? "\n\n" . $lead['message'] : ''),
        ]);
        $this->kernel->leads()->markConverted($id, $clientId);
        return $this->redirect('/admin/clients/' . $clientId . '/edit');
    }

    public function delete(Request $request): Response
    {
        $id = (int)$request->param('id', '0');
        $this->kernel->leads()->delete($id);
        return $this->redirect('/admin/leads');
    }
}

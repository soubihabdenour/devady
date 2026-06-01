<?php declare(strict_types=1);

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use Throwable;

final class ClientController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->view('admin/clients', [
            'clients' => $this->kernel->clients()->all(),
            'error'   => null,
        ]);
    }

    public function showForm(Request $request): Response
    {
        $id = (int)$request->param('id', '0');
        $client = $id ? $this->kernel->clients()->find($id) : null;
        if ($id && !$client) return Response::notFound();
        return $this->view('admin/client_form', ['client' => $client, 'error' => null]);
    }

    public function save(Request $request): Response
    {
        $id   = (int)$request->param('id', '0');
        $data = [
            'name'            => trim((string)$request->input('name', '')),
            'email'           => trim((string)$request->input('email', '')) ?: null,
            'phone'           => trim((string)$request->input('phone', '')) ?: null,
            'address'         => trim((string)$request->input('address', '')) ?: null,
            'tax_id'          => trim((string)$request->input('tax_id', '')) ?: null,
            'notes'           => trim((string)$request->input('notes', '')) ?: null,
            'country'         => trim((string)$request->input('country', '')) ?: null,
            'business_reg_no' => trim((string)$request->input('business_reg_no', '')) ?: null,
            'contact_name'    => trim((string)$request->input('contact_name', '')) ?: null,
        ];
        if ($data['name'] === '') {
            return $this->view('admin/client_form', [
                'client' => array_merge(['id' => $id ?: null], $data),
                'error'  => 'Name is required.',
            ]);
        }
        $this->kernel->clients()->save($data, $id ?: null);
        return $this->redirect('/admin/clients');
    }

    public function delete(Request $request): Response
    {
        $id = (int)$request->param('id', '0');
        try {
            $this->kernel->clients()->delete($id);
            return $this->redirect('/admin/clients');
        } catch (Throwable $e) {
            $client = $this->kernel->clients()->find($id);
            return $this->view('admin/client_form', ['client' => $client, 'error' => $e->getMessage()]);
        }
    }
}

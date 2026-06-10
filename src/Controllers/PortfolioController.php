<?php declare(strict_types=1);

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Stores\PortfolioStore;

final class PortfolioController extends Controller
{
    public function index(Request $request): Response
    {
        $items = $this->kernel->portfolio()->all();

        $research = [
            ['label' => 'Google Scholar', 'url' => 'https://scholar.google.com/citations?user=dxgFhR8AAAAJ'],
            ['label' => 'ORCID',          'url' => 'https://orcid.org/0009-0002-4648-9289'],
            ['label' => 'SKKU InfoLab',   'url' => 'https://infolab-skku.github.io/members/abdenour-soubih.html'],
        ];

        return $this->view('portfolio/index', [
            'settings' => $this->kernel->settings()->all(),
            'authed'   => $this->kernel->auth()->authed(),
            'items'    => $items,
            'research' => $research,
        ]);
    }

    // --- Admin --------------------------------------------------------------

    public function adminIndex(Request $request): Response
    {
        return $this->view('admin/portfolio_list', [
            'items' => $this->kernel->portfolio()->all(),
        ]);
    }

    public function showForm(Request $request): Response
    {
        $id   = (int)$request->param('id', '0');
        $item = $id ? $this->kernel->portfolio()->find($id) : null;
        if ($id && !$item) return Response::notFound();

        return $this->view('admin/portfolio_form', [
            'item'  => $item,
            'error' => null,
        ]);
    }

    public function save(Request $request): Response
    {
        $id   = (int)$request->param('id', '0');
        $data = [
            'title'    => trim((string)$request->input('title', '')),
            'period'   => trim((string)$request->input('period', '')),
            'role'     => trim((string)$request->input('role', '')),
            'body'     => trim((string)$request->input('body', '')),
            'tags'     => (string)$request->input('tags', ''),
            'url'      => trim((string)$request->input('url', '')),
            'position' => (int)$request->input('position', '0'),
        ];

        if ($data['title'] === '') {
            return $this->view('admin/portfolio_form', [
                'item'  => array_merge(['id' => $id ?: null], $data, [
                    'tags' => PortfolioStore::normalizeTags($data['tags']),
                ]),
                'error' => 'Title is required.',
            ]);
        }
        if ($data['url'] !== '' && !filter_var($data['url'], FILTER_VALIDATE_URL)) {
            return $this->view('admin/portfolio_form', [
                'item'  => array_merge(['id' => $id ?: null], $data, [
                    'tags' => PortfolioStore::normalizeTags($data['tags']),
                ]),
                'error' => 'URL is not valid.',
            ]);
        }

        $this->kernel->portfolio()->save($data, $id ?: null);
        return $this->redirect('/admin/portfolio');
    }

    public function delete(Request $request): Response
    {
        $id = (int)$request->param('id', '0');
        $this->kernel->portfolio()->delete($id);
        return $this->redirect('/admin/portfolio');
    }
}

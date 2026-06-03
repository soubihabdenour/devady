<?php declare(strict_types=1);

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;

final class PortfolioController extends Controller
{
    /**
     * Portfolio items are intentionally hardcoded — they change rarely
     * and are best curated via PR rather than an admin CRUD. Edit the
     * arrays below to add or rearrange entries. Item bodies and roles
     * are i18n keys (see src/Support/i18n/*.php).
     */
    public function index(Request $request): Response
    {
        $items = [
            [
                'title'  => 'streaming-chat-agent',
                'period' => '2026',
                'role'   => 'p1_role',
                'body'   => 'p1_body',
                'tags'   => ['TypeScript', 'LLM', 'SSE'],
                'url'    => 'https://github.com/soubihabdenour/streaming-chat-agent',
                'cta'    => 'portfolio_view_repo',
            ],
            [
                'title'  => 'ai-entity-extractor',
                'period' => '2026',
                'role'   => 'p2_role',
                'body'   => 'p2_body',
                'tags'   => ['Python', 'LLM', 'NLP'],
                'url'    => 'https://github.com/soubihabdenour/ai-entity-extractor',
                'cta'    => 'portfolio_view_repo',
            ],
            [
                'title'  => 'rag-mini-demo',
                'period' => '2026',
                'role'   => 'p3_role',
                'body'   => 'p3_body',
                'tags'   => ['JavaScript', 'RAG', 'Embeddings'],
                'url'    => 'https://github.com/soubihabdenour/rag-mini-demo',
                'cta'    => 'portfolio_view_repo',
            ],
            [
                'title'  => 'HARFED — Federated Learning research',
                'period' => '2024–2026',
                'role'   => 'p4_role',
                'body'   => 'p4_body',
                'tags'   => ['Python', 'PyTorch', 'Flower', 'Privacy'],
                'url'    => 'https://github.com/soubihabdenour/harfed',
                'cta'    => 'portfolio_view_repo',
            ],
            [
                'title'  => 'sympto-php',
                'period' => '2026',
                'role'   => 'p5_role',
                'body'   => 'p5_body',
                'tags'   => ['PHP', 'LLM', 'REST'],
                'url'    => 'https://github.com/soubihabdenour/sympto-php',
                'cta'    => 'portfolio_view_repo',
            ],
            [
                'title'  => 'strava-coach',
                'period' => '2026',
                'role'   => 'p6_role',
                'body'   => 'p6_body',
                'tags'   => ['PHP', 'Strava API', 'OAuth'],
                'url'    => 'https://github.com/soubihabdenour/strava-coach',
                'cta'    => 'portfolio_view_repo',
            ],
            [
                'title'  => 'arabic-for-korean',
                'period' => '2026',
                'role'   => 'p7_role',
                'body'   => 'p7_body',
                'tags'   => ['PHP', 'Arabic', 'Korean', 'Education'],
                'url'    => 'https://github.com/soubihabdenour/arabic-for-korean',
                'cta'    => 'portfolio_view_repo',
            ],
            [
                'title'  => 'Greenhouse',
                'period' => '2026',
                'role'   => 'p8_role',
                'body'   => 'p8_body',
                'tags'   => ['C++', 'Embedded', 'Sensors'],
                'url'    => 'https://github.com/soubihabdenour/Greenhouse',
                'cta'    => 'portfolio_view_repo',
            ],
        ];

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
}

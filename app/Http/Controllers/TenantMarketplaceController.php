<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class TenantMarketplaceController extends Controller
{
    public function index(): View
    {
        return $this->page('directory');
    }

    public function directory(): View
    {
        return $this->page('directory');
    }

    public function addInput(): View
    {
        return $this->page('add-input');
    }

    private function page(string $page): View
    {
        return view('tenant-marketplace.index', [
            'pageKey' => $page,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class BillingFinanceController extends Controller
{
    public function invoices(): View
    {
        return $this->page('invoices');
    }

    public function debtCollection(): View
    {
        return $this->page('debt-collection');
    }

    public function autoBills(): View
    {
        return $this->page('auto-bills');
    }

    public function historyPayment(): View
    {
        return $this->page('history-payment');
    }

    private function page(string $page): View
    {
        return view('billing-finance.index', [
            'pageKey' => $page,
        ]);
    }
}

<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RedemptionHistoryExport implements FromView {

    private $voucher_transactions;

    public function __construct($voucher_transactions) {
        $this->voucher_transactions = $voucher_transactions;
    }

    public function view(): View {
        return view('exports.redemptionhistory', [
            'voucher_transactions' => $this->voucher_transactions
        ]);
    }
}

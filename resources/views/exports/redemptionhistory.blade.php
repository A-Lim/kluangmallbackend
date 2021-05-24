<table>
    <thead>
        <tr>
            <th style="font-weight: bold">VOUCHER TITLE</th>
            <th style="font-weight: bold">VOUCHER TYPE</th>
            <th style="font-weight: bold">VOUCHER VALIDITY</th>
            <th style="font-weight: bold">USER</th>
            <th style="font-weight: bold">REDEEM / USE</th>
            <th style="font-weight: bold">DATE &amp; TIME</th>
        </tr>
    </thead>
    <tbody>
        @foreach($voucher_transactions as $voucher_transaction)
            <tr>
                <td>{{ $voucher_transaction->name }}</td>
                <td>
                    @switch($voucher_transaction->voucher_type)
                        @case(\App\Voucher::TYPE_DEDUCT_CASH)
                            promotion
                            @break

                        @case(\App\Voucher::TYPE_ADD_POINT)
                            free reward point
                            @break

                        @default
                    @endswitch
                </td>
                <td>{{ $voucher_transaction->fromDate }} - {{ $voucher_transaction->toDate }}</td>
                <td>{{ $voucher_transaction->user_name }}</td>
                <td>{{ $voucher_transaction->type }}</td>
                <td>{{ $voucher_transaction->created_at }}</td>
            </tr>
        @endforeach
        
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td style="font-weight: bold">Total Use</td>
            <td>{{ $voucher_transactions->where('type', \App\VoucherTransaction::TYPE_USE)->count() }}</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td style="font-weight: bold">Total Redeem</td>
            <td>{{ $voucher_transactions->where('type', \App\VoucherTransaction::TYPE_REDEEM)->count() }}</td>
        </tr>
    </tbody>
</table>
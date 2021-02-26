<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

use App\Repositories\Voucher\IVoucherRepository;
use App\Repositories\Voucher\IMyVoucherRepository;

use Carbon\Carbon;

class UpdateVoucherStatus extends Command {

    private $voucherRepository;
    private $myVoucherRepository;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:vouchers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update expired vouchers';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(IMyVoucherRepository $iMyVoucherRepository,
        IVoucherRepository $iVoucherRepository) {
        parent::__construct();
        $this->voucherRepository = $iVoucherRepository;
        $this->myVoucherRepository = $iMyVoucherRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $this->voucherRepository->updateExpired();
        $this->myVoucherRepository->updateExpired();
    }
}

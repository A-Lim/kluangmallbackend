<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

use App\Repositories\PointTransaction\IPointTransactionRepository;

use Carbon\Carbon;

class CreditPendingPoints extends Command {

    private $pointTransaction;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'credit:pending_points';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Credit user\'s pending points.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(IPointTransactionRepository $iPointTransactionRepository) {
        parent::__construct();
        $this->pointTransaction = $iPointTransactionRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $this->pointTransaction->creditPending();
    }
}

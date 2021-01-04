<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\ApiLog\IApiLogRepository;

class ClearOldLogs extends Command {

    private $apiLogRepository;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear old api logs';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(IApiLogRepository $iApiLogRepository) {
        parent::__construct();
        $this->apiLogRepository = $iApiLogRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $days = env('CLEAR_OLD_LOGS_DAYS', 30);
        $this->apiLogRepository->clear_old_logs($days);
        return 0;
    }
}

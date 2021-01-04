<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

use App\Repositories\Announcement\IAnnouncementRepository;
use App\Notifications\Announcement\AnnouncementPublished;

use Carbon\Carbon;
use App\Announcement;

class PushAnnouncement extends Command {

    private $announcementRepository;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'push:announcements';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push Notification for announcement';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(IAnnouncementRepository $iAnnouncementRepository) {
        parent::__construct();
        $this->announcementRepository = $iAnnouncementRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $today = Carbon::today();
        $filter = [
            'status' => 'equals:'.Announcement::STATUS_APPROVED,
            'publish_at' => 'equals:'.$today->format('Y-m-d')
        ];

        $announcements = $this->announcementRepository->list($filter, false);

        foreach($announcements as $announcement) {
            Notification::send(null, new AnnouncementPublished($announcement));
        }

        $announcement_ids = $announcements->pluck('id')->toArray();
        Announcement::whereIn('id', $announcement_ids)
            ->update(['status' => Announcement::STATUS_PUBLISHED]);
    }
}

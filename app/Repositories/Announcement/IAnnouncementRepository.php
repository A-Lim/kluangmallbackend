<?php
namespace App\Repositories\Announcement;

use App\Announcement;
use App\User;
use App\Merchant;

interface IAnnouncementRepository
{
    /**
     * List all announcements
     * @param User $user 
     * @param array $data
     * @param boolean $paginate
     * @return [Announcement] $announcements
     */
    public function list($data, $paginate = false);

    /**
     * Create announcement
     * @param array $data
     * @param integer $credit_paid
     * @param Merchant $merchant
     * @param array $files
     * @return Announcement
     */
    public function create($data, $credit_paid, Merchant $merchant = null, $files = null);
}
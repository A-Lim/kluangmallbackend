<?php
namespace App\Repositories\Dashboard;

interface IDashboardRepository {
     /**
     * Dashboard Stats
     * @return array []
     */
    public function stats();
}
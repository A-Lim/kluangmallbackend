<?php
namespace App\Repositories\Dashboard;

use DB;
use App\User;
use App\Merchant;
use App\Event;
use App\Promotion;
use App\MerchantVisit;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Carbon\CarbonImmutable;

class DashboardRepository implements IDashboardRepository {

     /**
     * {@inheritdoc}
     */
    public function stats() {
        $today = Carbon::today();

        $user_count = User::join('user_usergroup', 'user_usergroup.user_id', '=', 'users.id')
            ->join('usergroups', 'usergroups.id', '=', 'user_usergroup.usergroup_id')
            ->where('usergroups.code', 'user')
            ->where('users.status', User::STATUS_ACTIVE)
            ->count();

        $merchant_count = Merchant::where('status', Merchant::STATUS_ACTIVE)->count();
        $event_count = Event::whereDate('fromDate', '<=', $today)
            ->whereDate('toDate', '>=', $today)
            ->count();
        $promotion_count = Promotion::whereDate('fromDate', '<=', $today)
            ->whereDate('toDate', '>=', $today)
            ->count();

        return [
            'users_count' => $user_count,
            'merchants_count' => $merchant_count,
            'events_count' => $event_count,
            'promotions_count' => $promotion_count
        ];
    }

    public function top_merchants_visit() {
        return MerchantVisit::join('merchants', 'merchants.id', '=', 'merchant_visits.merchant_id')
            ->select('merchants.id', 'merchants.name', 'merchants.logo', DB::raw('COUNT(*) as visits'))
            ->groupBy('merchant_visits.merchant_id', 'merchants.name')
            ->orderBy('visits', 'desc')
            ->take(10)
            ->get();
    }

    public function new_users($data) {
        $now = CarbonImmutable::now();
        $query = User::where('status', User::STATUS_ACTIVE);
        $groupBy = null;

        if (!isset($data['range']))
            $data['range'] = 'week';

        switch ($data['range']) {
            case 'week':
                $day1 = $now->startOfWeek();
                $day2 = $now->endOfWeek();
                $groupBy = 'DATE';
                break;

            case 'month':
                $day1 = $now->startOfMonth();
                $day2 = $now->endOfMonth();
                $groupBy = 'DATE';
                break;
            
            case 'year':
                $day1 = $now->startOfYear();
                $day2 = $now->endOfYear();
                $groupBy = 'MONTH';
                break;
        }

        $result = $query->whereBetween('created_at', [$day1, $day2])
            ->select(DB::raw($groupBy.'(created_at) as date'), DB::raw('COUNT(*) as no_users'))
            ->groupBy('date')->get();
        return $this->formatResults($day1, $day2, $data['range'], $result);
    }

    private function formatResults($day1, $day2, $range, $result) {
        $period = null;
        $format = null;
        // date format of sql result;
        $compareFormat = null;
        $formatted = [];

        switch ($range) {
            case 'week':
                $period = CarbonPeriod::create($day1, $day2);
                $format = 'D';
                $compareFormat = 'Y-m-d';
                break;
            
            case 'month':
                $period = CarbonPeriod::create($day1, $day2);
                $format = 'j';
                $compareFormat = 'Y-m-d';
                break;

            case 'year':
                $period = CarbonPeriod::create($day1, '1 month', $day2);
                $format = 'M';
                $compareFormat = 'm';
                break;
        }

        foreach ($period as $date) {
            array_push($formatted, [
                'date' => $date->format($format),
                'no_users' => @$result->firstWhere('date', $date->format($compareFormat))->no_users ?? 0
            ]);
        }

        return $formatted;
    }
}
<?php
namespace App\Repositories\Landing;

use DB;
use App\Landing;
use App\Banner;
use App\Event;
use App\Promotion;

class LandingRepository implements ILandingRepository {

     /**
      * {@inheritdoc}
      */
     public function list($app) {
          if ($app == 'user') {
               return [
                    'banners' => $this->landingBanners($app),
                    'events' => $this->landingEvents(),
                    'promotions' => $this->landingPromotions(),
               ];
          } else {
               return [
                    'banners' => $this->landingBanners($app),
               ];
          }
     }

     /**
      * {@inheritdoc}
      */
     public function update($data) {
          DB::beginTransaction();
          // clear all
          Landing::query()
               ->where('app', $data['app'])
               ->delete();

          $insertData = [];

          if (isset($data['banners'])) {
               foreach ($data['banners'] as $banner) {
                    array_push($insertData, [
                         'app' => $data['app'],
                         'type' => Landing::TYPE_BANNER,
                         'type_id' => $banner['type_id'],
                         'seq' => $banner['seq'],
                    ]);
               }
          }
 
          if (isset($data['events'])) {
               foreach ($data['events'] as $event) {
                    array_push($insertData, [
                         'app' => $data['app'],
                         'type' => Landing::TYPE_EVENT,
                         'type_id' => $event['type_id'],
                         'seq' => $event['seq'],
                    ]);
               }
          }


          if (isset($data['promotions'])) {
               foreach ($data['promotions'] as $promotion) {
                    array_push($insertData, [
                         'app' => $data['app'],
                         'type' => Landing::TYPE_PROMOTION,
                         'type_id' => $promotion['type_id'],
                         'seq' => $promotion['seq'],
                    ]);
               }
          }

          DB::table('landings')->insert($insertData);
          DB::commit();
     }

     private function landingBanners($app) {
          return Banner::join('landings', 'landings.type_id', '=', 'banners.id')
               ->where('landings.type', Landing::TYPE_BANNER)
               ->where('banners.status', Event::STATUS_ACTIVE)
               ->where('banners.app', $app)
               ->select('banners.*')
               ->orderBy('landings.seq')
               ->get();
     }

     private function landingEvents() {
          return Event::join('landings', 'landings.type_id', '=', 'events.id')
               ->where('landings.type', Landing::TYPE_EVENT)
               ->where('events.status', Event::STATUS_ACTIVE)
               ->select('events.*')
               ->orderBy('landings.seq')
               ->get();
     }

     private function landingPromotions() {
          return Promotion::join('landings', 'landings.type_id', '=', 'promotions.id')
               ->where('landings.type', Landing::TYPE_PROMOTION)
               ->where('promotions.status', Promotion::STATUS_ACTIVE)
               ->select('promotions.*')
               ->orderBy('landings.seq')
               ->get();
     }
}
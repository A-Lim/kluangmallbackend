<?php
namespace App\Repositories\Landing;

interface ILandingRepository {
    /**
     * List Landing Page Details
     * @param string $app
     * @return array
     */
    public function list($app);


    /**
     * Update Landing Details
     * @return null;
     */
    public function update($data);
}
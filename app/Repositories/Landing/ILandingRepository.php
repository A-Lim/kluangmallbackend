<?php
namespace App\Repositories\Landing;

interface ILandingRepository {
    /**
     * List Landing Page Details
     * @return array
     */
    public function list();


    /**
     * Update Landing Details
     * @return null;
     */
    public function update($data);
}
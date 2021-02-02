<?php
namespace App\Repositories\Receipt;

use App\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface IReceiptRepository {
     /**
     * Upload receipt
     * @return Receipt
     */
    public function upload(User $user, $data, UploadedFile $image);
}
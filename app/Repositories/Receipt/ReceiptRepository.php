<?php
namespace App\Repositories\Receipt;

use App\User;
use App\Receipt;
use Carbon\Carbon;
use App\SystemSetting;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ReceiptRepository implements IReceiptRepository {

    /**
     * {@inheritdoc}
     */
    public function upload(User $user, $data, UploadedFile $image) {
        $date = Carbon::createFromFormat(env('DATE_FORMAT'), $data['date']);
        $systemSettings = SystemSetting::where('code', 'points_rate')
            ->first();

        $points_rate = @$systemSettings->value ?? 1;

        return Receipt::create([
            'merchant_id' => $data['merchant_id'],
            'user_id' => $user->id, 
            'image' => $this->saveImage($image, $date),
            'date' => $date,
            'amount' => $data['amount'],
            'points' => round($data['amount'], 0) * $points_rate
        ]);
    }

    private function saveImage(UploadedFile $file, Carbon $date) {
        $saveDirectory = 'receipts/'.$date->format('d-m-Y');

        $path = Storage::disk('s3')->putFile($saveDirectory, $file, 'public');
        return Storage::disk('s3')->url($path);
    }
}
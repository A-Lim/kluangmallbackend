<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

use Carbon\Carbon;
use App\Http\Traits\CustomQuery;
use App\Http\Traits\EnvTimezone;

class Announcement extends Model {
    use CustomQuery, EnvTimezone;

    protected $fillable = ['title', 'description', 'credit_paid', 'content', 'has_content', 'image', 'remark', 'audience', 'status', 'merchant_id', 'publish_at', 'requested_by', 'actioned_by'];
    protected $hidden = [];
    protected $casts = [
        'has_content' => 'boolean',
        'publish_at' => 'datetime:d M Y',
        'created_at' => 'datetime:d M Y H:i a',
        'updated_at' => 'datetime:d M Y H:i a',
    ];

    // list of properties queryable for datatable
    public static $queryable = ['title', 'type', 'merchant_id', 'target', 'status'];

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_PUBLISHED = 'published';

    const AUDIENCE_ALL = 'all';
    const AUDIENCE_MERCHANT = 'merchant';
    const AUDIENCE_USER = 'user';

    const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_APPROVED,
        self::STATUS_REJECTED,
        self::STATUS_PUBLISHED,
    ];

    const AUDIENCES = [
        self::AUDIENCE_ALL,
        self::AUDIENCE_MERCHANT,
        self::AUDIENCE_USER,
    ];

    public function merchant() {
        return $this->belongsTo(Merchant::class);
    }

    public static function boot() {
        parent::boot();

        self::creating(function($model) {
            if (empty($model->audience)) {
                $model->audience = self::AUDIENCE_USER;
            }

            if (empty($model->status)) {
                $model->status = self::STATUS_PENDING;
            }
        });
    }

    public function getImageAttribute($value) {
        if ($value != null)
            return json_decode($value);

        return null;
    }
}

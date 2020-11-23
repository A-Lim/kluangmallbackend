<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

use App\Http\Traits\CustomQuery;

class MerchantCategory extends Model {
    use CustomQuery;

    protected $table = 'merchant_categories';
    protected $fillable = ['name'];
    protected $hidden = [];
    protected $casts = [];

    // list of properties queryable for datatable
    public static $queryable = ['name'];
}

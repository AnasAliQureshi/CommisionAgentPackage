<?php

namespace Modules\CommissionAgent\Entities;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'sales_amount',
        'commission_type',
        'commission_amount',
        'transaction_date',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function category()
    {
        return $this->belongsTo(\App\Category::class);
    }
}

<?php

namespace Modules\CommissionAgent\Entities;

use Illuminate\Database\Eloquent\Model;

class SalesTarget extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'minimum_sales',
        'maximum_sales',
        'commission_type',
        'commission_value',
        'start_date',
        'end_date',
        'business_id'
    ];

    protected $casts = [
        'created_at' => 'date',
        'updated_at' => 'date',
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

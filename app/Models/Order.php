<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;



    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'customer_id',
        'order_date',

    ];

      /**
     * Get the products of the current order.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_orders')
                    ->withTimestamps()
                    ->withPivot('quantity', 'price');
    }



  /**
     * Get the customer of the current order.
     */
    public function  customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

}

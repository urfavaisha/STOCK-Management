<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'name',
        'description',
        'price',
        'picture',
        'category_id',
        'supplier_id'
    ];


      /**
     * Get the products of the current order.
     */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'product_orders')
                    ->withTimestamps()
                    ->withPivot('quantity', 'price');
    }


  /**
     * Get the category of the current product.
     */
    public function  category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }


  /**
     * Get the supplier of the current product.
     */
    public function  supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

     /**
     * Get the supplier of the current product.
     */
    public function  stock(): HasOne
    {
        return $this->hasOne(Stock::class);
    }


}

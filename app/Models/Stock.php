<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Stock extends Model
{
    /** @use HasFactory<\Database\Factories\StockFactory> */
    use HasFactory;


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'quantity_stock',
        'store_id',
        'product_id'
    ];



  /**
     * Get the product of the current stock.
     */
    public function  product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }



  /**
     * Get the store of the current stock.
     */
    public function  store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}

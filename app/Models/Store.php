<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Store extends Model
{
    /** @use HasFactory<\Database\Factories\StoreFactory> */
    use HasFactory;


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'address',
        'phone'
    ];



  /**
     * Get the stocks of the current store.
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }


    /**
     * Get all of the products for the current store.
     */
    public function products(): HasManyThrough
    {
        return $this->hasManyThrough(Product::class, Stock::class);
    }

}

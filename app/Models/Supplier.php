<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    /** @use HasFactory<\Database\Factories\SupplierFactory> */
    use HasFactory;





    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'address',
        'phone'

    ];


  /**
     * Get the products of the current supplier.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }



  /**
     * Get  all transactions of the current supplier.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

}

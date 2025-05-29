<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerFactory> */
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
     * Get the orders of the current customer.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }


  /**
     * Get  all transactions of the current customer.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }


}

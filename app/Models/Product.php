<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'category',
        'descriptions',        
        'qty',
        'unit',
        'costprice',
        'sellprice',
        'saleprice',
        'productpicture',
        'alertstocks',
        'criticalstocks',
        'createdat',
        'updatedat'        
    ];

}

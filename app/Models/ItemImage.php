<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemImage extends Model
{
    protected $fillable = ['item_id', 'path_foto', 'urutan'];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}

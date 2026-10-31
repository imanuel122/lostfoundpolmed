<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HandoverImage extends Model
{
    protected $fillable = ['item_id', 'claim_id', 'uploaded_by', 'path_foto', 'keterangan'];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function claim()
    {
        return $this->belongsTo(Claim::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}

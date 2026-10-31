<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClaimImage extends Model
{
    protected $fillable = ['claim_id', 'path_foto', 'keterangan', 'urutan'];

    public function claim()
    {
        return $this->belongsTo(Claim::class);
    }
}

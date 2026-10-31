<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    protected $fillable = [
        'item_id',
        'claimant_id',
        'jenis_klaim',
        'bukti_deskripsi',
        'lokasi_barang_sekarang',
        'status',
        'catatan_admin',
        'reviewed_by',
        'reviewed_at',
        'handed_over_at',
        'confirmed_at',
        'dispute_note',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'handed_over_at' => 'datetime',
        'confirmed_at' => 'datetime',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function claimant()
    {
        return $this->belongsTo(User::class, 'claimant_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function images()
    {
        return $this->hasMany(ClaimImage::class)->orderBy('urutan');
    }

    public function handoverImages()
    {
        return $this->hasMany(HandoverImage::class);
    }
}

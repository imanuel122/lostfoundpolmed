<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'user_id',
        'jenis',
        'judul',
        'category_id',
        'location_id',
        'detail_lokasi',
        'tanggal_kejadian',
        'deskripsi',
        'ciri_rahasia',
        'lokasi_penyimpanan',
        'status',
        'expires_at',
        'completed_at',
        'completed_claim_id',
    ];

    protected $casts = [
        'tanggal_kejadian' => 'date',
        'expires_at'       => 'date',
        'completed_at'     => 'datetime',
    ];

    // Ciri rahasia tidak ikut tampil kalau model diubah jadi array/JSON
    protected $hidden = ['ciri_rahasia'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function images()
    {
        return $this->hasMany(ItemImage::class)->orderBy('urutan');
    }

    public function claims()
    {
        return $this->hasMany(Claim::class);
    }

    public function completedClaim()
    {
        return $this->belongsTo(Claim::class, 'completed_claim_id');
    }

    public function handoverImages()
    {
        return $this->hasMany(HandoverImage::class);
    }

    // Foto utama untuk kartu di beranda
    public function getFotoUtamaAttribute()
    {
        $foto = $this->images->first();
        return $foto ? asset('storage/' . $foto->path_foto) : null;
    }

    // Filter: hanya postingan yang masih tampil di beranda
    public function scopeTampil($query)
    {
        return $query->whereIn('status', ['aktif', 'verifikasi', 'dikembalikan']);
    }
}

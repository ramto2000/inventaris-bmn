<?php

namespace App\Models;
use App\Models\Asset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    protected $table = 'rooms';

    protected $fillable = [
        'kode_ruangan',
        'nama_ruangan',
        'penanggung_jawab',
    ];
	protected $guarded = [];
	
    public function assets(): HasMany
	{
		return $this->hasMany(Asset::class, 'room_id');
	}
}
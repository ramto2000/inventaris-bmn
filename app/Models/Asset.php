<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Asset extends Model
{
    use SoftDeletes, LogsActivity;

    /**
     * Mass Assignment
     */
    protected $guarded = [];

    /**
     * Activity Log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Logic otomatis.
     */
    protected static function booted(): void
    {
        /*
         * CREATE / UPDATE
         * Ambil kode barang dari kategori.
         */
        static::saving(function ($asset) {
            if ($asset->category_id) {
                $category = Category::find($asset->category_id);

                if ($category) {
                    $asset->kode_barang = $category->kode_kategori;
                }
            }
        });

        /*
         * UPDATE
         * Catat mutasi jika room_id berubah.
         */
        static::updating(function ($asset) {
            if ($asset->isDirty('room_id')) {
                $oldRoomId = $asset->getOriginal('room_id');
                $newRoomId = $asset->room_id;

                // Ruangan asal
                $oldRoom = Room::find($oldRoomId);

                // Ruangan tujuan
                $roomTujuan = Room::find($newRoomId);

                AssetMutation::create([
                    'asset_id' => $asset->id,

                    'ruangan_asal' =>
                        $oldRoom?->nama_ruangan ?? 'Lokasi Awal',

                    'ruangan_tujuan' =>
                        $roomTujuan?->nama_ruangan ?? 'Ruangan Baru',

                    'petugas' =>
                        auth()->user()->name ?? 'System',

                    'penanggung_jawab_baru' =>
                        $roomTujuan?->penanggung_jawab
                        ?? $asset->nama_pemakai
                        ?? '-',

                    'alasan' =>
                        'Perubahan Lokasi Ruangan BMN',
                ]);
            }
        });
    }

    // =========================================================
    // RELASI DATABASE
    // =========================================================

    /**
     * Relasi ke Kategori.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi ke Ruangan.
     * Satu aset berada di satu ruangan.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    /**
     * Relasi ke Peminjaman.
     */
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class, 'asset_id');
    }

    /**
     * Relasi ke Pemeliharaan.
     */
    public function maintenances(): HasMany
    {
        return $this->hasMany(Maintenance::class, 'asset_id');
    }

    /**
     * Relasi ke Mutasi.
     */
    public function mutations(): HasMany
    {
        return $this->hasMany(AssetMutation::class, 'asset_id');
    }

    /**
     * Nilai Buku Aset.
     */
    public function getNilaiBukuAttribute()
    {
        $harga = (float) ($this->harga_perolehan ?? 0);

        if (!$this->tanggal_perolehan) {
            return $harga;
        }

        $tanggalPerolehan = Carbon::parse($this->tanggal_perolehan);

        $masaManfaat = (int) (
            $this->category?->masa_manfaat ?? 1
        );

        if ($masaManfaat <= 0) {
            $masaManfaat = 1;
        }

        $usiaBarang = $tanggalPerolehan->diffInYears(now());

        $penyusutanPerTahun = $harga / $masaManfaat;

        $totalPenyusutan = $penyusutanPerTahun * $usiaBarang;

        $nilaiBuku = $harga - $totalPenyusutan;

        return max(0, $nilaiBuku);
    }
}
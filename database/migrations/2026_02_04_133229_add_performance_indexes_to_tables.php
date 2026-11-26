<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /*
         * ASSETS
         *
         * category_id, room_id dan kondisi sudah memiliki index.
         * assets.status tidak ada.
         */
        Schema::table('assets', function (Blueprint $table) {
            $table->index(
                'tanggal_perolehan',
                'assets_tanggal_perolehan_index'
            );
        });

        /*
         * LOANS
         */
        Schema::table('loans', function (Blueprint $table) {
            $table->index(
                'asset_id',
                'loans_asset_id_index'
            );

            $table->index(
                'status',
                'loans_status_index'
            );

            $table->index(
                'tanggal_pinjam',
                'loans_tanggal_pinjam_index'
            );
        });

        /*
         * MAINTENANCES
         */
        Schema::table('maintenances', function (Blueprint $table) {
            $table->index(
                'asset_id',
                'maintenances_asset_id_index'
            );

            $table->index(
                'pelapor_id',
                'maintenances_pelapor_id_index'
            );

            $table->index(
                'status',
                'maintenances_status_index'
            );

            $table->index(
                'tanggal_servis',
                'maintenances_tanggal_servis_index'
            );
        });

        /*
         * PROCUREMENTS
         */
        Schema::table('procurements', function (Blueprint $table) {
            $table->index(
                'user_id',
                'procurements_user_id_index'
            );

            $table->index(
                'status',
                'procurements_status_index'
            );

            $table->index(
                'tgl_pengajuan',
                'procurements_tgl_pengajuan_index'
            );
        });

        /*
         * STOCK OPNAMES
         *
         * Kolom user_id TIDAK ADA.
         * Kolom yang tersedia adalah assigned_user_id.
         */
        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->index(
                'room_id',
                'stock_opnames_room_id_index'
            );

            $table->index(
                'assigned_user_id',
                'stock_opnames_assigned_user_id_index'
            );

            $table->index(
                'status',
                'stock_opnames_status_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropIndex('assets_tanggal_perolehan_index');
        });

        Schema::table('loans', function (Blueprint $table) {
            $table->dropIndex('loans_asset_id_index');
            $table->dropIndex('loans_status_index');
            $table->dropIndex('loans_tanggal_pinjam_index');
        });

        Schema::table('maintenances', function (Blueprint $table) {
            $table->dropIndex('maintenances_asset_id_index');
            $table->dropIndex('maintenances_pelapor_id_index');
            $table->dropIndex('maintenances_status_index');
            $table->dropIndex('maintenances_tanggal_servis_index');
        });

        Schema::table('procurements', function (Blueprint $table) {
            $table->dropIndex('procurements_user_id_index');
            $table->dropIndex('procurements_status_index');
            $table->dropIndex('procurements_tgl_pengajuan_index');
        });

        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->dropIndex('stock_opnames_room_id_index');
            $table->dropIndex(
                'stock_opnames_assigned_user_id_index'
            );
            $table->dropIndex('stock_opnames_status_index');
        });
    }
};

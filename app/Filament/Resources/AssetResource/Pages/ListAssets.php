<?php

namespace App\Filament\Resources\AssetResource\Pages;

use App\Filament\Resources\AssetResource;
use App\Exports\AssetsExport;
use App\Imports\AssetsImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class ListAssets extends ListRecords
{
    protected static string $resource = AssetResource::class;

    protected function getHeaderActions(): array
	{
		return [

			// 1. Export Excel
			Actions\Action::make('exportExcel')
				->label('Export Excel')
				->icon('heroicon-o-document-arrow-down')
				->action(function () {
					$query = $this->getFilteredTableQuery();
					$data = $query->get();

					return Excel::download(
						new AssetsExport($data),
						'assets.xlsx'
					);
				})
				->color('gray')
				->outlined(),

			// 2. Download Template
			Actions\Action::make('downloadTemplate')
				->label('Download Template')
				->icon('heroicon-o-arrow-down-tray')
				->url(url('template.xlsx'))
				->color('gray')
				->outlined(),

			// 3. Import Excel
			Actions\Action::make('importExcel')
				->label('Import Excel')
				->icon('heroicon-o-arrow-up-tray')
				->color('gray')
				->outlined()
				->form([
					FileUpload::make('attachment')
						->label('Upload File Excel (.xlsx)')
						->acceptedFileTypes([
							'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
							'application/vnd.ms-excel',
						])
						->disk('local')
						->directory('temp-import')
						->preserveFilenames()
						->required(),
				])
				->action(function (array $data) {
					try {

						$filePath = Storage::disk('local')->path(
							$data['attachment']
						);

						Excel::import(
							new AssetsImport,
							$filePath
						);

						Storage::disk('local')->delete(
							$data['attachment']
						);

						Notification::make()
							->title('Import Berhasil')
							->body('Data aset berhasil diimpor ke database.')
							->success()
							->send();

					} catch (\Exception $e) {

						Notification::make()
							->title('Import Gagal')
							->body('Terjadi kesalahan: ' . $e->getMessage())
							->danger()
							->send();
					}
				}),

			// 4. Laporan Penyusutan
			Actions\Action::make('cetak_penyusutan')
				->label('Laporan Penyusutan')
				->icon('heroicon-o-document-chart-bar')
				->color('gray')
				->outlined()
				->url(route('cetak_penyusutan'))
				->openUrlInNewTab(),

			// 5. Tambah Berkas
			Actions\CreateAction::make()
				->label('Tambah Berkas')
				->icon('heroicon-o-plus')
				->color('gray')
				->outlined(),
		];
	}
}
	
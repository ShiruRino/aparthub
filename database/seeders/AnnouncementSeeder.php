<?php

namespace Database\Seeders;

use App\Models\Announcement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AnnouncementSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        collect([
            [
                'title' => 'Elevator Maintenance Notice',
                'content' => "Pemeliharaan elevator Tower A akan dilakukan pada 10 Juni 2026.\nMohon gunakan lift servis selama jadwal pengerjaan berlangsung.",
                'category' => 'Maintenance',
                'status' => Announcement::STATUS_PUBLISHED,
                'is_pinned' => true,
                'published_at' => Carbon::parse('2026-06-07 08:00:00'),
            ],
            [
                'title' => 'Community Gathering This Weekend',
                'content' => "Acara community gathering akan berlangsung di Clubhouse Level 1 pada 22 Juni 2026 pukul 16.00.\nSilakan hadir bersama keluarga.",
                'category' => 'Event',
                'status' => Announcement::STATUS_PUBLISHED,
                'is_pinned' => false,
                'published_at' => Carbon::parse('2026-06-08 10:00:00'),
            ],
            [
                'title' => 'Resident Information Update',
                'content' => "Informasi operasional lobby dan paket kini diperbarui setiap hari kerja.\nSilakan cek aplikasi resident untuk pembaruan terbaru.",
                'category' => 'General',
                'status' => Announcement::STATUS_PUBLISHED,
                'is_pinned' => false,
                'published_at' => Carbon::parse('2026-06-09 09:30:00'),
            ],
            [
                'title' => 'Garbage Disposal Update',
                'content' => "Draft perubahan jadwal pengambilan sampah organik dan anorganik.\nMenunggu review dari building management.",
                'category' => 'Operations',
                'status' => Announcement::STATUS_DRAFT,
                'is_pinned' => false,
                'published_at' => null,
            ],
        ])->each(function (array $announcement): void {
            Announcement::query()->updateOrCreate(
                ['title' => $announcement['title']],
                $announcement
            );
        });
    }
}

<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@sekolah.id'], [
            'name' => 'Admin Sarana', 'role' => 'admin', 'password' => 'adminsiparis',
        ]);
        User::updateOrCreate(['email' => 'siswa@sekolah.id'], [
            'name' => 'Alya Pratama', 'role' => 'siswa', 'password' => 'siswasmkn5',
        ]);
        $teacher = User::whereIn('email', ['guru@sekolah.id', 'dwiesti@sekolah.id'])->first();

        if ($teacher) {
            $teacher->update([
                'name' => 'Dwi Esti',
                'email' => 'dwiesti@sekolah.id',
                'role' => 'guru',
                'password' => 'gurusmkn5',
            ]);
        } else {
            User::create([
                'name' => 'Dwi Esti',
                'email' => 'dwiesti@sekolah.id',
                'role' => 'guru',
                'password' => 'gurusmkn5',
            ]);
        }

        foreach ([
            ['code' => 'AST-001', 'name' => 'Proyektor Epson', 'category' => 'Elektronik', 'total_stock' => 4, 'image_path' => 'inventories/proyektor-epson.png'],
            ['code' => 'AST-002', 'name' => 'Kamera Canon EOS', 'category' => 'Multimedia', 'total_stock' => 2, 'image_path' => 'inventories/kamera-canon-eos.png'],
            ['code' => 'AST-006', 'name' => 'Laptop HP', 'category' => 'Elektronik', 'total_stock' => 3, 'image_path' => 'inventories/laptop-hp.png'],
            ['code' => 'AST-007', 'name' => 'Flashdisk', 'category' => 'Aksesori Komputer', 'total_stock' => 10, 'image_path' => 'inventories/flashdisk.png'],
            ['code' => 'AST-008', 'name' => 'Mouse', 'category' => 'Aksesori Komputer', 'total_stock' => 20, 'image_path' => 'inventories/mouse.png'],
            ['code' => 'AST-009', 'name' => 'Keyboard', 'category' => 'Aksesori Komputer', 'total_stock' => 10, 'image_path' => 'inventories/keyboard.png'],
            ['code' => 'AST-011', 'name' => 'Monitor', 'category' => 'Elektronik', 'total_stock' => 3, 'image_path' => 'inventories/monitor.png'],
            ['code' => 'AST-012', 'name' => 'Obeng mini (1 set)', 'category' => 'Perkakas', 'total_stock' => 1, 'image_path' => 'inventories/obeng-mini-set.png'],
            ['code' => 'AST-013', 'name' => 'Arduino Uno', 'category' => 'Elektronik', 'total_stock' => 30, 'image_path' => 'inventories/arduino-uno.png'],
            ['code' => 'AST-014', 'name' => 'Solder', 'category' => 'Perkakas', 'total_stock' => 1, 'image_path' => 'inventories/solder.png'],
            ['code' => 'AST-015', 'name' => 'Bor tembok', 'category' => 'Perkakas', 'total_stock' => 1, 'image_path' => 'inventories/bor-tembok.png'],
            ['code' => 'AST-016', 'name' => 'Bor mini listrik', 'category' => 'Perkakas', 'total_stock' => 1, 'image_path' => 'inventories/bor-mini-listrik.png'],
            ['code' => 'AST-017', 'name' => 'Speaker', 'category' => 'Audio', 'total_stock' => 1, 'image_path' => 'inventories/speaker.png'],
            ['code' => 'AST-018', 'name' => 'Mic + sound', 'category' => 'Audio', 'total_stock' => 1, 'image_path' => 'inventories/mic-sound.png'],
        ] as $item) {
            Inventory::firstOrCreate(['code' => $item['code']], $item + ['available_stock' => $item['total_stock'], 'condition' => 'Baik']);
        }
    }
}

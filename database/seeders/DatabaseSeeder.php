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
            'name' => 'Admin Sarana', 'role' => 'admin', 'password' => 'admin siparis',
        ]);
        User::updateOrCreate(['email' => 'siswa@sekolah.id'], [
            'name' => 'Alya Pratama', 'role' => 'siswa', 'password' => 'siswa smkn5',
        ]);
        foreach ([
            ['name' => 'Citra Lestari', 'email' => 'citra.lestari@sekolah.id'],
            ['name' => 'Dinda Maharani', 'email' => 'dinda.maharani@sekolah.id'],
            ['name' => 'Eka Putri', 'email' => 'eka.putri@sekolah.id'],
            ['name' => 'Farhan Maulana', 'email' => 'farhan.maulana@sekolah.id'],
            ['name' => 'Gita Anggraini', 'email' => 'gita.anggraini@sekolah.id'],
            ['name' => 'Hana Safitri', 'email' => 'hana.safitri@sekolah.id'],
            ['name' => 'Intan Permata', 'email' => 'intan.permata@sekolah.id'],
            ['name' => 'Joko Prasetyo', 'email' => 'joko.prasetyo@sekolah.id'],
            ['name' => 'Nabila Zahra', 'email' => 'nabila.zahra@sekolah.id'],
            ['name' => 'Rizky Ramadhan', 'email' => 'rizky.ramadhan@sekolah.id'],
        ] as $student) {
            User::updateOrCreate(['email' => $student['email']], [
                ...$student,
                'role' => 'siswa',
                'password' => 'siswa smkn5',
            ]);
        }
        $teacher = User::whereIn('email', ['guru@sekolah.id', 'dwiesti@sekolah.id'])->first();

        if ($teacher) {
            $teacher->update([
                'name' => 'Dwi Esti',
                'email' => 'dwiesti@sekolah.id',
                'role' => 'guru',
                'password' => 'guru smkn5',
            ]);
        } else {
            User::create([
                'name' => 'Dwi Esti',
                'email' => 'dwiesti@sekolah.id',
                'role' => 'guru',
                'password' => 'guru smkn5',
            ]);
        }

        foreach ([
            ['code' => 'AST-001', 'name' => 'Proyektor Epson', 'category' => 'Elektronik', 'total_stock' => 4],
            ['code' => 'AST-002', 'name' => 'Kamera Canon EOS', 'category' => 'Multimedia', 'total_stock' => 2],
            ['code' => 'AST-003', 'name' => 'Speaker Portable', 'category' => 'Audio', 'total_stock' => 6],
            ['code' => 'AST-006', 'name' => 'Laptop HP', 'category' => 'Elektronik', 'total_stock' => 3],
            ['code' => 'AST-007', 'name' => 'Flashdisk', 'category' => 'Aksesori Komputer', 'total_stock' => 10],
            ['code' => 'AST-008', 'name' => 'Mouse', 'category' => 'Aksesori Komputer', 'total_stock' => 20],
            ['code' => 'AST-009', 'name' => 'Keyboard', 'category' => 'Aksesori Komputer', 'total_stock' => 10],
            ['code' => 'AST-010', 'name' => 'Proyektor', 'category' => 'Elektronik', 'total_stock' => 3],
            ['code' => 'AST-011', 'name' => 'Monitor', 'category' => 'Elektronik', 'total_stock' => 3],
            ['code' => 'AST-012', 'name' => 'Obeng mini (1 set)', 'category' => 'Perkakas', 'total_stock' => 1],
            ['code' => 'AST-013', 'name' => 'Arduino Uno', 'category' => 'Elektronik', 'total_stock' => 30],
            ['code' => 'AST-014', 'name' => 'Solder', 'category' => 'Perkakas', 'total_stock' => 1],
            ['code' => 'AST-015', 'name' => 'Bor tembok', 'category' => 'Perkakas', 'total_stock' => 1],
            ['code' => 'AST-016', 'name' => 'Bor mini listrik', 'category' => 'Perkakas', 'total_stock' => 1],
            ['code' => 'AST-017', 'name' => 'Speaker', 'category' => 'Audio', 'total_stock' => 1],
            ['code' => 'AST-018', 'name' => 'Mic + sound', 'category' => 'Audio', 'total_stock' => 1],
        ] as $item) {
            Inventory::firstOrCreate(['code' => $item['code']], $item + ['available_stock' => $item['total_stock'], 'condition' => 'Baik']);
        }
    }
}

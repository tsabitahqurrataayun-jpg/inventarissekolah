<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    private const SAMPLE_STUDENT_EMAILS = [
        'citra.lestari@sekolah.id',
        'dinda.maharani@sekolah.id',
        'eka.putri@sekolah.id',
        'farhan.maulana@sekolah.id',
        'gita.anggraini@sekolah.id',
        'hana.safitri@sekolah.id',
        'intan.permata@sekolah.id',
        'joko.prasetyo@sekolah.id',
        'nabila.zahra@sekolah.id',
        'rizky.ramadhan@sekolah.id',
    ];

    public function up(): void
    {
        if (! app()->environment('testing')) {
            foreach (Storage::disk('local')->allFiles('loan-evidence') as $path) {
                if (
                    ! preg_match('#\Aloan-evidence/(borrowing|return)/[A-Za-z0-9._-]+\z#', $path)
                    || ! Storage::disk('local')->delete($path)
                ) {
                    throw new RuntimeException("Unable to safely delete loan evidence file: {$path}");
                }
            }
        }

        DB::transaction(function (): void {
            DB::table('loans')->delete();
            DB::table('password_reset_tokens')->whereIn('email', self::SAMPLE_STUDENT_EMAILS)->delete();
            DB::table('users')
                ->where('role', 'siswa')
                ->whereIn('email', self::SAMPLE_STUDENT_EMAILS)
                ->delete();

            foreach ([
                ['email' => 'admin@sekolah.id', 'role' => 'admin', 'password' => 'adminsiparis'],
                ['email' => 'siswa@sekolah.id', 'role' => 'siswa', 'password' => 'siswasmkn5'],
                ['email' => 'dwiesti@sekolah.id', 'role' => 'guru', 'password' => 'gurusmkn5'],
            ] as $account) {
                DB::table('users')
                    ->where('email', $account['email'])
                    ->where('role', $account['role'])
                    ->update([
                        'password' => Hash::make($account['password']),
                        'remember_token' => null,
                        'updated_at' => now(),
                    ]);
            }
        });
    }

    public function down(): void
    {
        throw new RuntimeException('Deleted sample accounts, loan history, and evidence cannot be restored.');
    }
};

<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class OwnerPassword extends Command
{
    protected $signature = 'owner:password';

    protected $description = 'Mengatur password akun Owner melalui input tersembunyi';

    public function handle(): int
    {
        $owner = User::where('email', config('grm.owner_email'))->first();
        if (! $owner) {
            $this->error('Isi OWNER_EMAIL dan OWNER_PASSWORD di .env lalu jalankan php artisan db:seed.');

            return self::FAILURE;
        }
        $password = $this->secret('Password baru (minimal 12 karakter)');
        $confirmation = $this->secret('Ulangi password baru');
        $validator = Validator::make(['password' => $password, 'password_confirmation' => $confirmation], ['password' => 'required|string|min:12|confirmed']);
        if ($validator->fails()) {
            $this->error($validator->errors()->first());

            return self::FAILURE;
        }
        $owner->update(['password' => $password]);
        $this->info('Password Owner diperbarui.');

        return self::SUCCESS;
    }
}

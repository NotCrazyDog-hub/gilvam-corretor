<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateAdmin extends Command
{
    protected $signature = 'admin:criar {email? : E-mail do administrador}';

    protected $description = 'Cria o primeiro administrador ou promove um usuário que já existe';

    public function handle(): int
    {
        $email = strtolower((string) ($this->argument('email') ?: $this->ask('E-mail')));

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->forceFill(['role' => User::ROLE_ADMIN])->save();
            $this->info("{$user->name} ({$user->email}) agora é administrador.");

            return self::SUCCESS;
        }

        $name = $this->ask('Nome');
        $password = $this->secret('Senha');

        $validator = Validator::make(
            ['name' => $name, 'email' => $email, 'password' => $password],
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'password' => ['required', Password::defaults()],
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $erro) {
                $this->error($erro);
            }

            return self::FAILURE;
        }

        $user = new User(['name' => $name, 'email' => $email, 'password' => $password]);
        $user->forceFill([
            'role' => User::ROLE_ADMIN,
            'email_verified_at' => now(),
        ])->save();

        $this->info("Administrador {$email} criado com sucesso.");

        return self::SUCCESS;
    }
}

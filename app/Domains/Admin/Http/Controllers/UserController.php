<?php

namespace App\Domains\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::orderBy('name')->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email:rfc,dns', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', Rule::in([User::ROLE_USER, User::ROLE_ADMIN])],
        ]);

        $user = new User([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'], // o cast 'hashed' do model faz o hash
        ]);

        // role e email_verified_at não ficam em $fillable de propósito
        $user->forceFill([
            'role' => $data['role'],
            'email_verified_at' => now(), // quem cadastra é o admin, não precisa verificar e-mail
        ])->save();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuário cadastrado com sucesso.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', ['user' => $user]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email:rfc,dns', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            // senha é opcional na edição: vazia = mantém a atual
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role' => ['required', Rule::in([User::ROLE_USER, User::ROLE_ADMIN])],
        ]);

        // Evita o admin se trancar para fora do próprio painel
        if ($user->is($request->user()) && $data['role'] !== User::ROLE_ADMIN) {
            return back()
                ->withInput()
                ->withErrors(['role' => 'Você não pode remover o seu próprio acesso de administrador.']);
        }

        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        if (! empty($data['password'])) {
            $user->password = $data['password']; // cast 'hashed' faz o hash
        }

        $user->forceFill(['role' => $data['role']])->save();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuário atualizado com sucesso.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        // O admin não pode apagar a si mesmo (evita ficar sem ninguém no painel)
        if ($user->is($request->user())) {
            return back()->with('error', 'Você não pode excluir a sua própria conta.');
        }

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Derruba as sessões abertas do usuário (só se as sessões estiverem no banco)
        if (config('session.driver') === 'database') {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuário excluído com sucesso.');
    }
}

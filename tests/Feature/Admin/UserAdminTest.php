<?php

use App\Models\User;

function dadosNovoUsuario(array $extra = []): array
{
    return array_merge([
        'name' => 'Maria Corretora',
        'email' => 'maria@example.com',
        'password' => 'SenhaForte#2026',
        'password_confirmation' => 'SenhaForte#2026',
        'role' => 'user',
    ], $extra);
}

test('visitante é mandado para o login', function () {
    $this->get('/admin/usuarios')->assertRedirect('/login');
    $this->post('/admin/usuarios', dadosNovoUsuario())->assertRedirect('/login');
});

test('usuário comum recebe 403 na tela e no cadastro', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/admin/usuarios')->assertForbidden();
    $this->actingAs($user)->get('/admin/usuarios/novo')->assertForbidden();
    $this->actingAs($user)->post('/admin/usuarios', dadosNovoUsuario())->assertForbidden();

    $this->assertDatabaseMissing('users', ['email' => 'maria@example.com']);
});

test('admin vê a lista e o formulário', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get('/admin/usuarios')->assertOk();
    $this->actingAs($admin)->get('/admin/usuarios/novo')->assertOk();
});

test('admin cadastra um usuário comum', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post('/admin/usuarios', dadosNovoUsuario())
        ->assertRedirect(route('admin.users.index'));

    $novo = User::where('email', 'maria@example.com')->first();

    expect($novo)->not->toBeNull()
        ->and($novo->isAdmin())->toBeFalse()
        ->and($novo->email_verified_at)->not->toBeNull();
});

test('role inválido é recusado', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post('/admin/usuarios', dadosNovoUsuario(['role' => 'superuser']))
        ->assertSessionHasErrors('role');
});

test('usuário comum não consegue editar', function () {
    $user = User::factory()->create();
    $alvo = User::factory()->create();

    $this->actingAs($user)->get(route('admin.users.edit', $alvo))->assertForbidden();
    $this->actingAs($user)
        ->put(route('admin.users.update', $alvo), ['name' => 'X', 'email' => 'x@example.com', 'role' => 'admin'])
        ->assertForbidden();

    expect($alvo->fresh()->isAdmin())->toBeFalse();
});

test('admin corrige nome e e-mail sem mexer na senha', function () {
    $admin = User::factory()->admin()->create();
    $alvo = User::factory()->create(['name' => 'Nome Errdo', 'email' => 'errado@example.com']);
    $senhaAntes = $alvo->password;

    $this->actingAs($admin)
        ->put(route('admin.users.update', $alvo), [
            'name' => 'Nome Certo',
            'email' => 'certo@example.com',
            'role' => 'user',
            'password' => '',
            'password_confirmation' => '',
        ])
        ->assertRedirect(route('admin.users.index'));

    $alvo->refresh();

    expect($alvo->name)->toBe('Nome Certo')
        ->and($alvo->email)->toBe('certo@example.com')
        ->and($alvo->password)->toBe($senhaAntes);
});

test('admin troca a senha de um usuário', function () {
    $admin = User::factory()->admin()->create();
    $alvo = User::factory()->create();
    $senhaAntes = $alvo->password;

    $this->actingAs($admin)->put(route('admin.users.update', $alvo), [
        'name' => $alvo->name,
        'email' => $alvo->email,
        'role' => 'user',
        'password' => 'NovaSenha#2026',
        'password_confirmation' => 'NovaSenha#2026',
    ])->assertRedirect(route('admin.users.index'));

    expect($alvo->fresh()->password)->not->toBe($senhaAntes);
});

test('e-mail repetido é recusado na edição', function () {
    $admin = User::factory()->admin()->create();
    $a = User::factory()->create();
    $b = User::factory()->create();

    $this->actingAs($admin)
        ->put(route('admin.users.update', $b), ['name' => $b->name, 'email' => $a->email, 'role' => 'user'])
        ->assertSessionHasErrors('email');
});

test('admin não pode tirar o próprio acesso de admin', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->put(route('admin.users.update', $admin), ['name' => $admin->name, 'email' => $admin->email, 'role' => 'user'])
        ->assertSessionHasErrors('role');

    expect($admin->fresh()->isAdmin())->toBeTrue();
});

<?php

test('cadastro público está desativado', function () {
    $this->get('/register')->assertNotFound();

    $this->post('/register', [
        'name' => 'Intruso',
        'email' => 'intruso@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertNotFound();

    $this->assertGuest();
    $this->assertDatabaseMissing('users', ['email' => 'intruso@example.com']);
});

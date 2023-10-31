<?php

use App\Models\User;

test("administrator can create a new user", function() {
    $admin = User::factory()
        ->admin()
        ->create();

    $this->actingAs($admin)
        ->get(route('user.create'))
        ->assertOk()
        ->assertSessionHasNoErrors();

    $this->actingAs($admin)
        ->post(route('user.store'), [
            'name' => 'Test User',
            'username' => 'user',
            'email' => 'user@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => User::Administrator
        ])
        ->assertRedirect(route('user.index'))
        ->assertSessionHas('success', 'Ο νέος χρήστης δημιουργήθηκε!');
})->only();

test("author cannot create a new user", function() {
    $author = User::factory()
        ->author()
        ->create();

    $this->actingAs($author)
        ->get(route('user.create'))
        ->assertForbidden();

    $this->actingAs($author)
        ->post(route('user.store'), [
            'name' => 'Test User',
            'username' => 'user',
            'email' => 'user@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => User::Administrator
        ])
        ->assertForbidden();
})->only();

test("administrator can update a user", function() {
    $admin = User::factory()
        ->admin()
        ->create();

    $user = User::factory()
        ->create();

    $this->actingAs($admin)
        ->get(route('user.edit', $user))
        ->assertOk()
        ->assertSessionHasNoErrors();

    $this->actingAs($admin)
        ->put(route('user.update', $user), [
            'name' => 'Test User',
            'username' => 'user',
            'email' => 'user@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => User::Administrator
        ])
        ->assertRedirect(route('user.index'))
        ->assertSessionHas('success', 'Τα στοιχεία του χρήστη ενημερώθηκαν!');

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Test User',
        'username' => 'user',
        'email' => 'user@example.com',
        'password' => 'password',
        'role' => User::Administrator
    ]);
})->only();

test("author cannot edit a user", function() {
    $author = User::factory()
        ->author()
        ->create();

    $user = User::factory()
        ->create();

    $this->actingAs($author)
        ->get(route('user.edit', $user))
        ->assertForbidden();

    $this->actingAs($author)
        ->put(route('user.update', $user), [
            'id' => $user->id,
            'name' => 'Test User',
            'username' => 'user',
            'email' => 'user@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => User::Administrator
        ])
        ->assertForbidden();
})->only();

test("administrator can delete a user", function() {
    $admin = User::factory()
        ->admin()
        ->create();

    $user = User::factory()
        ->create();

    $this->actingAs($admin)
        ->delete(route('user.destroy', $user))
        ->assertRedirect(route('user.index'))
        ->assertSessionHas('success', 'O χρήστης διαγράφηκε!');

    $this->assertDatabaseMissing('users', $user->toArray());
})->only();

test("author cannot delete a user", function() {
    $author = User::factory()
        ->author()
        ->create();

    $user = User::factory()
        ->create();

    $this->actingAs($author)
        ->delete(route('user.destroy', $user))
        ->assertForbidden();
})->only();

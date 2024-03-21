<?php

it('can access all public routes', function(string $route) {
    $this->get($route)
        ->assertOk()
        ->assertSessionHasNoErrors();
})->with('public_routes');

it('cannot access protected routes', function(string $route) {
    $this->get($route)
        ->assertRedirect();
})->with('protected_routes');

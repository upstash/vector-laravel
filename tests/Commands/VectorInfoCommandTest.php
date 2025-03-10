<?php

use function Pest\Laravel\artisan;

it('can run', function () {
    artisan('vector:info')
        ->assertSuccessful();
})->only();

<?php

use App\Models\User;

function createTestUser() {
    return User::factory()->create([
        'password' => bcrypt('password'),
        'email_verified_at' => now(),  // Make sure email is verified
    ]);
}

test('profile page is displayed', function () {
    $user = createTestUser();

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response->assertOk();
});

test('profile information can be updated', function () {
    $user = createTestUser();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    $this->assertSame('Test User', $user->name);
    $this->assertSame('test@example.com', $user->email);
    $this->assertNull($user->email_verified_at); // Email should not be verified if unchanged
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = createTestUser();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => $user->email, // Keeping email the same
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    // Verify email verification status remains the same
    $this->assertNotNull($user->refresh()->email_verified_at);
});

test('user can delete their account', function () {
    $user = createTestUser();

    $response = $this
        ->actingAs($user)
        ->delete('/profile', [
            'password' => 'password', // Make sure the password matches
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest(); // Ensure user is logged out after deletion
    $this->assertNull($user->fresh()); // Ensure user is deleted
});

test('correct password must be provided to delete account', function () {
    $user = createTestUser();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->delete('/profile', [
            'password' => 'wrong-password', // Testing with wrong password
        ]);

    $response
        ->assertSessionHasErrorsIn('userDeletion', 'password')
        ->assertRedirect('/profile');

    $this->assertNotNull($user->fresh()); // Ensure user is not deleted
});

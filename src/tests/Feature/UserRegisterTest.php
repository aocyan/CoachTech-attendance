<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserRegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_name_field_is_required()
    {
        $response = $this -> get('/register');

        $response -> assertStatus(200);

        $response = $this -> post('/register/store', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => '1234abcd',
            'password_confirmation' => '1234abcd',
        ]);

        $response -> assertStatus(302);
        $response -> assertSessionHasErrors(['name']);

        $errors = session('errors') -> get('name');
        $this -> assertEquals('お名前を入力してください', $errors[0]);
    }

    /** @test */
    public function test_email_field_is_required()
    {
        $response = $this -> get('/register');

        $response -> assertStatus(200);

        $response = $this -> post('/register/store', [
            'name' => '山田　太郎',
            'email' => '',
            'password' => '1234abcd',
            'password_confirmation' => '1234abcd',
        ]);

        $response -> assertStatus(302);
        $response -> assertSessionHasErrors(['email']);

        $errors = session('errors') -> get('email');
        $this -> assertEquals('メールアドレスを入力してください', $errors[0]);
    }

    /** @test */
    public function test_password_should_be_at_least_8_characters()
    {
        $response = $this -> get('/register');

        $response -> assertStatus(200);

        $response = $this -> post('/register/store', [
            'name' => '山本　太郎',
            'email' => 'test1@example.com',
            'password' => '1234abc',
            'password_confirmation' => '1234abc',
        ]);

        $response -> assertStatus(302);
        $response -> assertSessionHasErrors(['password']);

        $errors = session('errors') -> get('password');
        $this -> assertEquals('パスワードは8文字以上で入力してください', $errors[0]);
    }

    /** @test */
    public function test_password_and_confirmation_should_match()
    {
        $response = $this -> get('/register');

        $response -> assertStatus(200);

        $response = $this -> post('/register/store', [
            'name' => '山本　太郎',
            'email' => 'test1@example.com',
            'password' => '1234abcd',
            'password_confirmation' => '1234abcde',
        ]);

        $response -> assertStatus(302);
        $response -> assertSessionHasErrors(['password']);

        $errors = session('errors') -> get('password');
        $this -> assertEquals('パスワードと一致しません', $errors[0]);
    }

    /** @test */
    public function test_password_field_is_required()
    {
        $response = $this -> get('/register');

        $response -> assertStatus(200);

        $response = $this -> post('/register/store', [
            'name' => '山本　太郎',
            'email' => 'test1@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response -> assertStatus(302);
        $response -> assertSessionHasErrors(['password']);

        $errors = session('errors') -> get('password');
        $this -> assertEquals('パスワードを入力してください', $errors[0]);
    }

    /** @test */
    public function test_user_registration_should_redirect_to_edit_page_on_success()
    {
        $response = $this -> get('/register');

        $response -> assertStatus(200);

        $response = $this -> post('/register/store', [
            'name' => '山本　太郎',
            'email' => 'test1@example.com',
            'password' => '1234abcd',
            'password_confirmation' => '1234abcd',
        ]);

        $response -> assertRedirect('/attendance/default');
        $response -> assertStatus(302);
    }
}

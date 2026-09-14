<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_the_application_redirects_guests_to_login(): void
    {
        $this->withoutVite();

        $response = $this->get('/');

        $response->assertRedirect(route('DangNhapController.dangNhap'));
    }

    public function test_the_application_returns_a_successful_response_for_logged_in_users(): void
    {
        $this->withoutVite();

        $response = $this->withSession([
            'tai_khoan' => [
                'id' => 1,
                'ho_ten' => 'Quản trị viên',
                'email' => 'admin@example.com',
            ],
        ])->get('/');

        $response->assertOk();
    }
}

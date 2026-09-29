<?php

namespace Tests\Feature\Auth;

use App\Models\Empleado;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DefaultPasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_default_password_requires_policy_acceptance(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('1234'),
        ]);

        $empleado = Empleado::create([
            'nombre' => 'Test',
            'apellidos' => 'Empleado',
            'email' => $user->email,
            'dni' => '12345678A',
            'telefono_principal' => '600000000',
            'direccion' => 'Calle Mayor 1',
            'localidad' => 'Utrera',
            'codigo_postal' => '41710',
            'provincia' => 'Sevilla',
            'fecha_nacimiento' => '1990-01-01',
            'onboarding_completado' => true,
            'politicas_aceptadas_at' => null,
        ]);

        // Intentar actualizar sin aceptar normativas debe fallar
        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => '1234',
                'password' => 'nuevaPassword123!',
                'password_confirmation' => 'nuevaPassword123!',
            ]);

        $response->assertSessionHasErrors(['acepta_rgpd', 'acepta_normativa', 'acepta_prl'], null, 'updatePassword');
        $this->assertTrue(Hash::check('1234', $user->refresh()->password));
        $this->assertNull($empleado->refresh()->politicas_aceptadas_at);

        // Actualizar aceptando las normativas debe funcionar correctamente
        $responseSuccess = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => '1234',
                'password' => 'nuevaPassword123!',
                'password_confirmation' => 'nuevaPassword123!',
                'acepta_rgpd' => '1',
                'acepta_normativa' => '1',
                'acepta_prl' => '1',
                'check_normativas_present' => '1',
            ]);

        $responseSuccess->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('nuevaPassword123!', $user->refresh()->password));
        $this->assertNotNull($empleado->refresh()->politicas_aceptadas_at);
        $this->assertEquals(3, $empleado->refresh()->onboarding_paso_actual);
    }
}

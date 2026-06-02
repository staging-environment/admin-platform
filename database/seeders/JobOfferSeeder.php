<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JobOffer;

class JobOfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar registros previos
        \App\Models\JobApplication::query()->delete();
        JobOffer::query()->delete();

        // Crear ofertas de trabajo de muestra
        $offer1 = JobOffer::create([
            'title' => 'Operador de Estación de Servicio',
            'description' => 'Responsable de la atención al cliente y gestión de surtidores.',
            'salary' => 1500.00,
            'location' => 'Sevilla',
            'active' => true,
        ]);

        $offer2 = JobOffer::create([
            'title' => 'Técnico de Mantenimiento de Equipos',
            'description' => 'Mantenimiento preventivo y correctivo de equipos de dispensado.',
            'salary' => 2100.00,
            'location' => 'Madrid',
            'active' => true,
        ]);

        // Crear candidaturas de muestra
        \App\Models\JobApplication::create([
            'job_offer_id' => $offer1->id,
            'first_name' => 'Juan',
            'last_name' => 'García Pérez',
            'email' => 'juan.garcia@example.com',
            'phone' => '600123456',
            'profile_description' => 'Tengo 3 años de experiencia trabajando en atención al cliente y caja en gasolineras.',
            'cover_letter' => 'Estimados señores, les escribo para postular a la vacante de Operador...',
            'cv_path' => 'cvs/mock_cv_juan.pdf',
        ]);

        \App\Models\JobApplication::create([
            'job_offer_id' => $offer1->id,
            'first_name' => 'María',
            'last_name' => 'López Gómez',
            'email' => 'maria.lopez@example.com',
            'phone' => '611987654',
            'profile_description' => 'Estudiante de administración con muchas ganas de trabajar en verano.',
            'cover_letter' => 'Me gustaría formar parte de su equipo...',
            'cv_path' => 'cvs/mock_cv_maria.pdf',
        ]);

        \App\Models\JobApplication::create([
            'job_offer_id' => $offer2->id,
            'first_name' => 'Carlos',
            'last_name' => 'Sánchez Martín',
            'email' => 'carlos.sanchez@example.com',
            'phone' => '622456789',
            'profile_description' => 'Técnico superior en electromecánica con experiencia en mantenimiento industrial.',
            'cover_letter' => 'Adjunto mi CV para la posición de técnico...',
            'cv_path' => 'cvs/mock_cv_carlos.pdf',
        ]);
    }
}

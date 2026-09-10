<?php

namespace Tests\Feature;

use App\Models\HomeConfig;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;

class SyncFeriaUtreraDatesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Create home_configs table in sqlite testing database if not exists
        if (!Schema::connection('mariadb')->hasTable('home_configs')) {
            Schema::connection('mariadb')->create('home_configs', function (Blueprint $table) {
                $table->id();
                $table->string('titulo')->nullable();
                $table->text('subtitulo')->nullable();
                $table->json('slider_images')->nullable();
                $table->dateTime('feria_utrera_inicio')->nullable();
                $table->dateTime('feria_utrera_fin')->nullable();
                $table->dateTime('feria_utrera_synced_at')->nullable();
                $table->string('feria_utrera_source')->nullable();
                $table->timestamps();
            });
        }
    }

    public function test_is_feria_utrera_activa_returns_true_during_feria(): void
    {
        $config = HomeConfig::first() ?? new HomeConfig();
        $config->feria_utrera_inicio = Carbon::create(2026, 9, 4, 20, 0, 0);
        $config->feria_utrera_fin = Carbon::create(2026, 9, 8, 23, 59, 59);
        $config->save();

        // During fair: September 5th
        $duringFair = Carbon::create(2026, 9, 5, 15, 0, 0);
        $this->assertTrue(HomeConfig::isFeriaUtreraActiva($duringFair));

        // After fair: September 9th
        $afterFair = Carbon::create(2026, 9, 9, 10, 0, 0);
        $this->assertFalse(HomeConfig::isFeriaUtreraActiva($afterFair));

        // Before fair: September 4th at 12:00
        $beforeFair = Carbon::create(2026, 9, 4, 12, 0, 0);
        $this->assertFalse(HomeConfig::isFeriaUtreraActiva($beforeFair));
    }

    public function test_sync_command_saves_dates_to_database(): void
    {
        Http::fake([
            'https://www.utrera.org/?s=*' => Http::response('
                <html>
                    <body>
                        <article>
                            <h2><a href="https://www.utrera.org/noticia-feria-2027">Feria 2027</a></h2>
                        </article>
                    </body>
                </html>
            ', 200),
            'https://www.utrera.org/noticia-feria-2027' => Http::response('
                <html>
                    <body>
                        <div class="entry-content">
                            La Feria de Consolación tendrá lugar con 4 días de celebración, del 5 al 8 de septiembre de 2027.
                        </div>
                    </body>
                </html>
            ', 200),
        ]);

        $this->artisan('app:sync-feria-utrera-dates', ['--year' => 2027])
            ->assertSuccessful();

        $config = HomeConfig::first();
        $this->assertNotNull($config);
        $this->assertEquals('2027-09-05 20:00:00', $config->feria_utrera_inicio->toDateTimeString());
        $this->assertEquals('2027-09-08 23:59:59', $config->feria_utrera_fin->toDateTimeString());
        $this->assertNotNull($config->feria_utrera_synced_at);
        $this->assertStringContainsString('noticia-feria-2027', $config->feria_utrera_source);
    }
}

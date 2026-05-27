<?php

namespace App\Filament\Pages;

use App\Models\HomeConfig;
use App\Models\ContactoMensaje;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class ManageHome extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Configuración de Portada';
    protected static ?string $title = 'Configuración de Portada';
    protected string $view = 'filament.pages.manage-home';

    protected static string|\UnitEnum|null $navigationGroup = 'Administración de la plataforma';

    public function getBreadcrumbs(): array
    {
        return [
            '#' => 'Administración de la plataforma',
            static::getNavigationLabel(),
        ];
    }

    public ?array $data = [];

    public function mount(): void
    {
        $config = HomeConfig::firstOrCreate([
            'id' => 1
        ], [
            'titulo' => 'Red de Estaciones de Servicio',
            'subtitulo' => 'Precios en tiempo real and servicios premium en carretera. Consulta los combustibles de cada estación y planifica tu ruta.',
            'slider_images' => []
        ]);

        $this->form->fill($config->toArray());
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                TextInput::make('titulo')
                    ->label('Título de Portada')
                    ->required(),
                TextInput::make('subtitulo')
                    ->label('Subtítulo de Portada')
                    ->required(),
                FileUpload::make('slider_images')
                    ->label('Imágenes del Carrusel Principal (Home)')
                    ->multiple()
                    ->image()
                    ->disk('public')
                    ->directory('home/slider')
                    ->reorderable()
                    ->columnSpanFull(),
                RichEditor::make('texto_inicio')
                    ->label('Presentación de Inicio (Global)')
                    ->helperText('Texto introductorio que se mostrará en la pestaña Inicio de la página principal.')
                    ->columnSpanFull(),
                RichEditor::make('quienes_somos')
                    ->label('Quiénes Somos (Global)')
                    ->helperText('Redacta la información institucional y la historia de la empresa.')
                    ->columnSpanFull(),
                RichEditor::make('condiciones_uso')
                    ->label('Condiciones de Uso (Global)')
                    ->helperText('Configura los términos legales y condiciones de uso del portal público.')
                    ->columnSpanFull(),
                TextInput::make('contacto_email')
                    ->label('Email de Contacto (Global)')
                    ->email()
                    ->maxLength(255),
                TextInput::make('contacto_telefono')
                    ->label('Teléfono de Contacto (Global)')
                    ->maxLength(50),
                TextInput::make('contacto_direccion')
                    ->label('Dirección de la Sede (Global)')
                    ->placeholder('Ej: Avenida de la Castellana 15, Madrid')
                    ->helperText('Escribe la dirección de la sede y haz clic en la lupa para ubicarla en el mapa automáticamente.')
                    ->maxLength(255)
                    ->columnSpanFull()
                    ->suffixAction(
                        \Filament\Actions\Action::make('buscar')
                            ->icon('heroicon-o-magnifying-glass')
                            ->action(function ($set, $state) {
                                if (empty($state)) return;
                                
                                $response = \Illuminate\Support\Facades\Http::withHeaders([
                                    'User-Agent' => 'AdminPlatform/1.0',
                                ])->get('https://nominatim.openstreetmap.org/search', [
                                    'q' => $state,
                                    'format' => 'json',
                                    'limit' => 1,
                                ]);
                                
                                $data = $response->json();
                                if (!empty($data) && isset($data[0])) {
                                    $set('location', [
                                        'lat' => (float) $data[0]['lat'],
                                        'lng' => (float) $data[0]['lon'],
                                    ]);
                                    $set('latitud', $data[0]['lat']);
                                    $set('longitud', $data[0]['lon']);
                                    
                                    // Also set with absolute path in case state path overrides it:
                                    $set('data.location', [
                                        'lat' => (float) $data[0]['lat'],
                                        'lng' => (float) $data[0]['lon'],
                                    ]);
                                    $set('data.latitud', $data[0]['lat']);
                                    $set('data.longitud', $data[0]['lon']);
                                }
                            })
                    ),
                \Filament\Forms\Components\Hidden::make('latitud'),
                \Filament\Forms\Components\Hidden::make('longitud'),
                \Dotswan\MapPicker\Fields\Map::make('location')
                    ->label('Ubicación de la Sede en el Mapa')
                    ->helperText('Haz clic en el mapa para establecer la ubicación exacta de la sede central.')
                    ->columnSpanFull()
                    ->defaultLocation(40.4168, -3.7038)
                    ->afterStateUpdated(function ($set, ?array $state): void {
                        $set('latitud', $state['lat'] ?? null);
                        $set('longitud', $state['lng'] ?? null);
                    })
                    ->afterStateHydrated(function ($get, $set, $state): void {
                        $lat = $get('latitud');
                        $lng = $get('longitud');
                        if ($lat && $lng) {
                            $set('location', [
                                'lat' => (float) $lat,
                                'lng' => (float) $lng,
                            ]);
                        }
                    })
                    ->live(onBlur: true)
                    ->showMarker(true)
                    ->markerColor('#ef4444')
                    ->showFullscreenControl(true)
                    ->showZoomControl(true)
                    ->draggable(true)
                    ->clickable(true)
                    ->dehydrated(false),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $config = HomeConfig::find(1);
        if (!$config) {
            $config = new HomeConfig();
            $config->id = 1;
        }
        $config->fill($this->form->getState());
        $config->save();

        Notification::make()
            ->title('Configuración guardada correctamente')
            ->success()
            ->send();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ContactoMensaje::query()->whereNull('gasolinera_codigo'))
            ->columns([
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('nombre')
                    ->label('Remitente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('mensaje')
                    ->label('Mensaje')
                    ->limit(50)
                    ->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make()
                    ->form([
                        TextInput::make('nombre')
                            ->label('Nombre')
                            ->disabled(),
                        TextInput::make('email')
                            ->label('Email')
                            ->disabled(),
                        Textarea::make('mensaje')
                            ->label('Mensaje')
                            ->disabled()
                            ->rows(6)
                            ->columnSpanFull(),
                        TextInput::make('created_at')
                            ->label('Fecha de Envío')
                            ->disabled(),
                    ])
                    ->label('Ver Mensaje'),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

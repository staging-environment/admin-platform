<?php

namespace App\Filament\Resources\Gasolineras\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\RichEditor;

class GasolineraForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información Operativa (Lectura)')
                    ->description('Datos importados automáticamente desde la base de datos central de Virtusgesnet. Estos campos no son editables.')
                    ->schema([
                        TextInput::make('Nombre')
                            ->label('Nombre comercial')
                            ->disabled(),
                        TextInput::make('Direccion')
                            ->label('Dirección')
                            ->disabled(),
                        TextInput::make('Poblacion')
                            ->label('Población')
                            ->disabled(),
                        TextInput::make('Provincia')
                            ->label('Provincia')
                            ->disabled(),
                        TextInput::make('DP')
                            ->label('Código Postal')
                            ->disabled(),
                        TextInput::make('marca')
                            ->label('Marca / Bandera')
                            ->disabled(),
                            
                        \Filament\Forms\Components\ViewField::make('beneficios_chart')
                            ->view('filament.forms.components.beneficios-chart')
                            ->label('')
                            ->columnSpanFull()
                            ->dehydrated(false),
                    ])->columns(3)->columnSpan(['lg' => 1]),

                Section::make('Contenido Web Personalizado')
                    ->description('Configura los datos del slider, información corporativa, ubicación y servicios para esta estación de servicio.')
                    ->relationship('contenido')
                    ->schema([
                        FileUpload::make('slider_images')
                            ->label('Imágenes para el Slider')
                            ->helperText('Sube imágenes atractivas para el carrusel superior. Se redimensionarán automáticamente.')
                            ->multiple()
                            ->image()
                            ->disk('public')
                            ->directory('gasolineras/sliders')
                            ->columnSpanFull()
                            ->reorderable(),

                        \Filament\Forms\Components\RichEditor::make('texto_inicio')
                            ->label('Texto Principal (Pestaña Inicio)')
                            ->helperText('Este texto aparecerá en la página principal (Inicio) de la gasolinera.')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'strike',
                                'link',
                                'h2',
                                'h3',
                                'bulletList',
                                'orderedList',
                                'redo',
                                'undo',
                            ])
                            ->columnSpanFull(),

                        RichEditor::make('quienes_somos')
                            ->label('Sección: Quiénes Somos')
                            ->helperText('Redacta un texto introductorio sobre la historia o el equipo de esta gasolinera.')
                            ->columnSpanFull(),

                        Textarea::make('donde_estamos_texto')
                            ->label('Sección: Dónde Estamos (Indicaciones adicionales)')
                            ->placeholder('Ej: Junto a la salida 14 de la autovía A-4, frente al centro comercial...')
                            ->columnSpanFull()
                            ->rows(3),

                        TextInput::make('contacto_email')
                            ->label('Email de Contacto')
                            ->helperText('Correo donde se recibirán los mensajes enviados a esta estación.')
                            ->email()
                            ->maxLength(255),

                        TextInput::make('contacto_telefono')
                            ->label('Teléfono de Contacto')
                            ->helperText('Teléfono de atención al cliente de la estación.')
                            ->tel()
                            ->maxLength(50),

                        TextInput::make('horario')
                            ->label('Horario Comercial')
                            ->placeholder('Ej: 24h, o Lunes a Viernes 07:00 a 23:00')
                            ->maxLength(255),

                        Select::make('servicios')
                            ->label('Servicios de la Estación')
                            ->multiple()
                            ->options([
                                'Tienda' => 'Tienda / Supermercado',
                                'Lavado' => 'Centro de Lavado',
                                'Cafeteria' => 'Cafetería / Bar',
                                'Restaurante' => 'Restaurante',
                                'GLP' => 'Autogas / GLP',
                                'Electrico' => 'Punto de recarga eléctrica',
                                'Parking' => 'Parking Camiones / Turismos',
                            ]),

                        \Filament\Forms\Components\Hidden::make('latitud'),
                        \Filament\Forms\Components\Hidden::make('longitud'),

                        \Filament\Forms\Components\TextInput::make('busqueda_direccion')
                            ->label('Buscar Dirección (Autoubicación)')
                            ->placeholder('Ej: Avenida de la Castellana 15, Madrid')
                            ->helperText('Escribe la dirección y haz clic en la lupa para ubicar el mapa automáticamente.')
                            ->dehydrated(false)
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
                                        }
                                    })
                            ),

                        \Dotswan\MapPicker\Fields\Map::make('location')
                            ->label('Ubicación en el Mapa')
                            ->helperText('Busca una dirección o haz clic en el mapa para establecer la ubicación exacta de la gasolinera.')
                            ->columnSpanFull()
                            ->defaultLocation(40.4168, -3.7038)
                            ->afterStateUpdated(function ($set, ?array $state): void {
                                $set('latitud', $state['lat'] ?? null);
                                $set('longitud', $state['lng'] ?? null);
                            })
                            ->afterStateHydrated(function ($get, $set, $state, $record): void {
                                if ($record && $record->latitud && $record->longitud) {
                                    $set('location', [
                                        'lat' => (float) $record->latitud,
                                        'lng' => (float) $record->longitud,
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
                    ])->columns(2)->columnSpan(['lg' => 2]),
            ])->columns(3);
    }
}

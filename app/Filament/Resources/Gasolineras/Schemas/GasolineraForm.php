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
                        FileUpload::make('imagen')
                            ->label('Imagen Principal / Destacada')
                            ->helperText('Sube una imagen representativa para la estación de servicio. Se mostrará en la página de inicio.')
                            ->image()
                            ->disk('public')
                            ->directory('gasolineras/imagenes')
                            ->columnSpanFull(),

                        FileUpload::make('slider_images')
                            ->label('Imágenes para el Slider')
                            ->helperText('Se recomienda subir imágenes de alta resolución. El editor forzará un recorte en proporción 3.5:1 para coincidir con el banner del portal público.')
                            ->multiple()
                            ->image()
                            ->imageEditor()
                            ->imageEditorViewportWidth(1400)
                            ->imageEditorViewportHeight(400)
                            ->imageEditorAspectRatios([
                                '3.5:1',
                            ])
                            ->itemPanelAspectRatio('1:3.5')
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
                                        
                                        // 1. Try Google Maps Geocoding API if key is present
                                        $googleKey = env('GOOGLE_MAPS_API_KEY');
                                        if ($googleKey) {
                                            try {
                                                $response = \Illuminate\Support\Facades\Http::timeout(5)->get('https://maps.googleapis.com/maps/api/geocode/json', [
                                                    'address' => $state,
                                                    'key' => $googleKey,
                                                    'language' => 'es',
                                                    'region' => 'es',
                                                ]);
                                                
                                                if ($response->successful()) {
                                                    $data = $response->json();
                                                    if (!empty($data['results']) && isset($data['results'][0]['geometry']['location'])) {
                                                        $loc = $data['results'][0]['geometry']['location'];
                                                        $lat = (float) $loc['lat'];
                                                        $lng = (float) $loc['lng'];
                                                        $formattedAddress = $data['results'][0]['formatted_address'] ?? $state;
                                                        
                                                        $set('location', [
                                                            'lat' => $lat,
                                                            'lng' => $lng,
                                                        ]);
                                                        $set('latitud', $lat);
                                                        $set('longitud', $lng);
                                                        
                                                        \Filament\Notifications\Notification::make()
                                                            ->title('Dirección encontrada (Google)')
                                                            ->body($formattedAddress)
                                                            ->success()
                                                            ->send();
                                                        return;
                                                    }
                                                }
                                            } catch (\Exception $e) {
                                                // Fail silently and fall through to free providers
                                            }
                                        }

                                        // 2. Try Photon API (Elasticsearch over OSM, fuzzy & keyless, biased to Spain)
                                        try {
                                            $response = \Illuminate\Support\Facades\Http::timeout(5)->get('https://photon.komoot.io/api/', [
                                                'q' => $state,
                                                'limit' => 1,
                                                'lang' => 'es',
                                                'lat' => 40.4168,
                                                'lon' => -3.7038,
                                            ]);
                                            
                                            if ($response->successful()) {
                                                $data = $response->json();
                                                if (!empty($data['features']) && isset($data['features'][0])) {
                                                    $feature = $data['features'][0];
                                                    $lon = (float) $feature['geometry']['coordinates'][0];
                                                    $lat = (float) $feature['geometry']['coordinates'][1];
                                                    
                                                    $props = $feature['properties'] ?? [];
                                                    $name = $props['name'] ?? '';
                                                    $city = $props['city'] ?? '';
                                                    $stateName = $props['state'] ?? '';
                                                    $label = trim(implode(', ', array_filter([$name, $city, $stateName])));
                                                    if (empty($label)) $label = $state;

                                                    $set('location', [
                                                        'lat' => $lat,
                                                        'lng' => $lon,
                                                    ]);
                                                    $set('latitud', $lat);
                                                    $set('longitud', $lon);
                                                    
                                                    \Filament\Notifications\Notification::make()
                                                        ->title('Dirección encontrada (Fuzzy)')
                                                        ->body($label)
                                                        ->success()
                                                        ->send();
                                                    return;
                                                }
                                            }
                                        } catch (\Exception $e) {
                                            // Fall through to Nominatim
                                        }

                                        // 3. Fallback to raw Nominatim
                                        try {
                                            $response = \Illuminate\Support\Facades\Http::withHeaders([
                                                'User-Agent' => 'AdminPlatform/1.0',
                                            ])->timeout(5)->get('https://nominatim.openstreetmap.org/search', [
                                                'q' => $state,
                                                'format' => 'json',
                                                'limit' => 1,
                                            ]);
                                            
                                            if ($response->successful()) {
                                                $data = $response->json();
                                                if (!empty($data) && isset($data[0])) {
                                                    $lat = (float) $data[0]['lat'];
                                                    $lon = (float) $data[0]['lon'];
                                                    $displayName = $data[0]['display_name'] ?? $state;

                                                    $set('location', [
                                                        'lat' => $lat,
                                                        'lng' => $lon,
                                                    ]);
                                                    $set('latitud', $lat);
                                                    $set('longitud', $lon);
                                                    
                                                    \Filament\Notifications\Notification::make()
                                                        ->title('Dirección encontrada')
                                                        ->body($displayName)
                                                        ->success()
                                                        ->send();
                                                    return;
                                                }
                                            }
                                        } catch (\Exception $e) {
                                            // Handled by final notification
                                        }

                                        \Filament\Notifications\Notification::make()
                                            ->title('No se pudo encontrar la dirección')
                                            ->body('Intente con una dirección más simple o haga clic directamente en el mapa.')
                                            ->danger()
                                            ->send();
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

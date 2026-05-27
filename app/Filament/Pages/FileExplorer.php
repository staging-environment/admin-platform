<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Filament\Notifications\Notification;

class FileExplorer extends Page
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-folder-open';
    protected static ?string $navigationLabel = 'Explorador de Archivos';
    protected static ?string $title = 'Explorador de Archivos';
    protected string $view = 'filament.pages.file-explorer';

    protected static string|\UnitEnum|null $navigationGroup = 'Administración de la plataforma';

    public string $currentPath = '';
    public string $search = '';
    public string $selectedDisk = 'public';
    public string $viewMode = 'grid';

    protected $queryString = [
        'currentPath' => ['except' => ''],
        'selectedDisk' => ['except' => 'public'],
        'viewMode' => ['except' => 'grid'],
    ];

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        
        return method_exists($user, 'hasRole') && ($user->hasRole('admin') || $user->hasRole('super-admin'))
            || method_exists($user, 'can') && $user->can('manage-users');
    }

    public function mount(): void
    {
        // Keep within safe root
        $this->currentPath = $this->sanitizePath($this->currentPath);
    }

    protected function sanitizePath(string $path): string
    {
        $path = trim($path, '/');
        // Prevent path traversal
        $path = str_replace(['..', '\\'], '', $path);
        return $path;
    }

    public function getDisks(): array
    {
        return [
            'public' => 'Almacenamiento Público (Storage)',
            'local' => 'Almacenamiento Local (App)',
        ];
    }

    public function selectDisk(string $disk): void
    {
        if (array_key_exists($disk, $this->getDisks())) {
            $this->selectedDisk = $disk;
            $this->currentPath = '';
            $this->search = '';
        }
    }

    public function goToPath(string $path): void
    {
        $this->currentPath = $this->sanitizePath($path);
        $this->search = '';
    }

    public function getExplorerBreadcrumbs(): array
    {
        $breadcrumbs = [
            ['label' => 'Raíz', 'path' => '']
        ];

        if (empty($this->currentPath)) {
            return $breadcrumbs;
        }

        $parts = explode('/', $this->currentPath);
        $accumulated = '';

        foreach ($parts as $part) {
            $accumulated = trim($accumulated . '/' . $part, '/');
            $breadcrumbs[] = [
                'label' => $part,
                'path' => $accumulated
            ];
        }

        return $breadcrumbs;
    }

    public function getItems(): array
    {
        try {
            $disk = Storage::disk($this->selectedDisk);
            
            // Ensure path exists
            if (!$disk->exists($this->currentPath)) {
                $disk->makeDirectory($this->currentPath);
            }

            $directories = $disk->directories($this->currentPath);
            $files = $disk->files($this->currentPath);
            $items = [];

            // Folders
            foreach ($directories as $dir) {
                $name = basename($dir);
                if ($this->search && !Str::contains(strtolower($name), strtolower($this->search))) {
                    continue;
                }

                $items[] = [
                    'name' => $name,
                    'path' => $dir,
                    'type' => 'folder',
                    'size' => '--',
                    'last_modified' => date('d/m/Y H:i', $disk->lastModified($dir)),
                    'icon' => 'heroicon-o-folder',
                    'color' => 'text-blue-500',
                ];
            }

            // Files
            foreach ($files as $file) {
                $name = basename($file);
                if ($this->search && !Str::contains(strtolower($name), strtolower($this->search))) {
                    continue;
                }

                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                [$icon, $color] = $this->getFileIconAndColor($extension);

                $items[] = [
                    'name' => $name,
                    'path' => $file,
                    'type' => 'file',
                    'size' => $this->formatSize($disk->size($file)),
                    'last_modified' => date('d/m/Y H:i', $disk->lastModified($file)),
                    'extension' => $extension,
                    'icon' => $icon,
                    'color' => $color,
                ];
            }

            return $items;
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error al leer el directorio')
                ->body($e->getMessage())
                ->danger()
                ->send();
            return [];
        }
    }

    protected function formatSize(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' B';
        }
    }

    protected function getFileIconAndColor(string $extension): array
    {
        return match ($extension) {
            'jpg', 'jpeg', 'png', 'gif', 'svg', 'webp' => ['heroicon-o-photo', 'text-emerald-500'],
            'pdf' => ['heroicon-o-document-text', 'text-red-500'],
            'doc', 'docx', 'odt' => ['heroicon-o-document-text', 'text-blue-500'],
            'xls', 'xlsx', 'csv' => ['heroicon-o-table-cells', 'text-green-500'],
            'zip', 'rar', 'tar', 'gz', '7z' => ['heroicon-o-archive-box', 'text-amber-500'],
            'mp3', 'wav', 'ogg', 'm4a' => ['heroicon-o-musical-note', 'text-violet-500'],
            'mp4', 'avi', 'mkv', 'mov' => ['heroicon-o-play', 'text-indigo-500'],
            'txt', 'md', 'json', 'xml', 'html', 'css', 'js', 'php' => ['heroicon-o-document-text', 'text-slate-500'],
            default => ['heroicon-o-document', 'text-slate-400'],
        };
    }

    public function downloadFile(string $path)
    {
        try {
            $path = $this->sanitizePath($path);
            $disk = Storage::disk($this->selectedDisk);
            
            if (!$disk->exists($path)) {
                Notification::make()->title('El archivo no existe')->danger()->send();
                return null;
            }

            return $disk->download($path);
        } catch (\Exception $e) {
            Notification::make()->title('Error al descargar el archivo')->body($e->getMessage())->danger()->send();
            return null;
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createFolder')
                ->label('Crear Carpeta')
                ->icon('heroicon-o-folder-plus')
                ->form([
                    TextInput::make('folderName')
                        ->label('Nombre de la carpeta')
                        ->required()
                        ->maxLength(255)
                        ->rules(['regex:/^[a-zA-Z0-9_\-\s]+$/']),
                ])
                ->action(function (array $data) {
                    try {
                        $newPath = trim($this->currentPath . '/' . $data['folderName'], '/');
                        $disk = Storage::disk($this->selectedDisk);
                        
                        if ($disk->exists($newPath)) {
                            Notification::make()->title('La carpeta ya existe')->danger()->send();
                            return;
                        }
                        
                        $disk->makeDirectory($newPath);
                        Notification::make()->title('Carpeta creada correctamente')->success()->send();
                    } catch (\Exception $e) {
                        Notification::make()->title('Error al crear carpeta')->body($e->getMessage())->danger()->send();
                    }
                }),

            Action::make('uploadFiles')
                ->label('Subir Archivos')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    FileUpload::make('files')
                        ->label('Seleccionar Archivos')
                        ->multiple()
                        ->required()
                        ->disk(fn() => $this->selectedDisk)
                        ->directory(fn() => $this->currentPath ?: '/')
                        ->preserveFilenames()
                ])
                ->action(function (array $data) {
                    Notification::make()->title('Archivos subidos correctamente')->success()->send();
                }),
        ];
    }

    protected function getActions(): array
    {
        return [
            Action::make('deleteItem')
                ->label('Eliminar')
                ->requiresConfirmation()
                ->action(function (array $arguments) {
                    try {
                        $path = $this->sanitizePath($arguments['path']);
                        $type = $arguments['type'];
                        $disk = Storage::disk($this->selectedDisk);
                        
                        if ($type === 'folder') {
                            $disk->deleteDirectory($path);
                        } else {
                            $disk->delete($path);
                        }
                        
                        Notification::make()->title('Eliminado correctamente')->success()->send();
                    } catch (\Exception $e) {
                        Notification::make()->title('Error al eliminar')->body($e->getMessage())->danger()->send();
                    }
                }),

            Action::make('previewFile')
                ->label('Previsualizar')
                ->modalContent(function (array $arguments) {
                    try {
                        $path = $this->sanitizePath($arguments['path']);
                        $disk = Storage::disk($this->selectedDisk);
                        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                        
                        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']);
                        $isPdf = $extension === 'pdf';
                        
                        $canReadText = in_array($extension, ['txt', 'md', 'json', 'xml', 'html', 'css', 'js', 'php', 'log']);
                        $textContent = null;
                        
                        if ($canReadText && $disk->size($path) < 1024 * 1024) { // Max 1MB
                            $textContent = $disk->get($path);
                        }
                        
                        $url = null;
                        if ($this->selectedDisk === 'public') {
                            $url = Storage::disk('public')->url($path);
                        } else {
                            if ($isImage && $disk->size($path) < 10 * 1024 * 1024) {
                                $fileData = $disk->get($path);
                                $mime = ($extension === 'svg') ? 'image/svg+xml' : 'image/' . $extension;
                                $url = 'data:' . $mime . ';base64,' . base64_encode($fileData);
                            } elseif ($isPdf && $disk->size($path) < 5 * 1024 * 1024) {
                                $fileData = $disk->get($path);
                                $url = 'data:application/pdf;base64,' . base64_encode($fileData);
                            }
                        }

                        return view('filament.pages.file-preview', [
                            'name' => basename($path),
                            'path' => $path,
                            'url' => $url,
                            'isImage' => $isImage,
                            'isPdf' => $isPdf,
                            'canReadText' => $canReadText,
                            'textContent' => $textContent,
                            'extension' => $extension,
                            'size' => $this->formatSize($disk->size($path)),
                            'last_modified' => date('d/m/Y H:i', $disk->lastModified($path)),
                        ]);
                    } catch (\Exception $e) {
                        return view('filament.pages.file-preview-error', ['error' => $e->getMessage()]);
                    }
                })
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Cerrar'),
        ];
    }
}

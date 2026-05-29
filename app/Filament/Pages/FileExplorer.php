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

    public function getBreadcrumbs(): array
    {
        return [
            '#' => 'Administración de la plataforma',
            static::getNavigationLabel(),
        ];
    }

    public string $currentPath = '';
    public string $search = '';
    public string $selectedDisk = 'local';
    public string $viewMode = 'grid';
    public array $selectedItems = [];
    public bool $showHiddenFiles = false;

    protected $queryString = [
        'currentPath' => ['except' => ''],
        'selectedDisk' => ['except' => 'local'],
        'viewMode' => ['except' => 'grid'],
        'showHiddenFiles' => ['except' => false],
    ];

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        if ($user->email === 'jarodriguezbonilla@gmail.com' || $user->id === 1) return true;
        return $user->hasRole('Admin') || $user->can('utilizar_explorador');
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

    protected function getPhysicalPath(string $virtualPath): string
    {
        $virtualPath = $this->sanitizePath($virtualPath);
        if ($this->selectedDisk === 'personal') {
            $userId = auth()->id() ?? 'guest';
            return trim("users/{$userId}/{$virtualPath}", '/');
        }
        return $virtualPath;
    }

    protected function getVirtualPath(string $physicalPath): string
    {
        if ($this->selectedDisk === 'personal') {
            $userId = auth()->id() ?? 'guest';
            $prefix = "users/{$userId}";
            if (str_starts_with($physicalPath, $prefix)) {
                return trim(substr($physicalPath, strlen($prefix)), '/');
            }
        }
        return $physicalPath;
    }

    public function getDisks(): array
    {
        return [
            'local' => 'Almacenamiento Local (App)',
            'personal' => 'Mis Archivos (Personal)',
            'public' => 'Almacenamiento Público (Storage)',
        ];
    }

    public function selectDisk(string $disk): void
    {
        if (array_key_exists($disk, $this->getDisks())) {
            $this->selectedDisk = $disk;
            $this->currentPath = '';
            $this->search = '';
            $this->selectedItems = [];
        }
    }

    public function goToPath(string $path): void
    {
        $this->currentPath = $this->sanitizePath($path);
        $this->search = '';
        $this->selectedItems = [];
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
            $disk = Storage::disk($this->selectedDisk === 'personal' ? 'local' : $this->selectedDisk);
            $physicalCurrentPath = $this->getPhysicalPath($this->currentPath);
            
            // Ensure path exists
            if (!$disk->exists($physicalCurrentPath)) {
                $disk->makeDirectory($physicalCurrentPath);
            }

            $directories = $disk->directories($physicalCurrentPath);
            $files = $disk->files($physicalCurrentPath);
            $items = [];

            // Folders
            foreach ($directories as $dir) {
                $name = basename($dir);
                
                // Hide system temporary folders from the explorer view unless configured to show
                if (!$this->showHiddenFiles && $name === 'livewire-tmp') {
                    continue;
                }

                // Hide the 'users' directory from the root of the 'local' disk
                if ($this->selectedDisk === 'local' && $this->currentPath === '' && $name === 'users') {
                    continue;
                }

                if ($this->search && !Str::contains(strtolower($name), strtolower($this->search))) {
                    continue;
                }

                $items[] = [
                    'name' => $name,
                    'path' => $this->getVirtualPath($dir),
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

                // Hide hidden files/dotfiles (e.g. .gitignore) unless configured to show
                if (!$this->showHiddenFiles && str_starts_with($name, '.')) {
                    continue;
                }

                if ($this->search && !Str::contains(strtolower($name), strtolower($this->search))) {
                    continue;
                }

                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                [$icon, $color] = $this->getFileIconAndColor($extension);
                
                $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']);
                $url = null;

                if ($isImage) {
                    if ($this->selectedDisk === 'public') {
                        $url = Storage::disk('public')->url($file);
                    } elseif ($disk->size($file) < 500 * 1024) { // Max 500KB for private thumbnails to keep list load fast
                        try {
                            $fileData = $disk->get($file);
                            $mime = ($extension === 'svg') ? 'image/svg+xml' : 'image/' . $extension;
                            $url = 'data:' . $mime . ';base64,' . base64_encode($fileData);
                        } catch (\Exception $e) {
                            $url = null;
                        }
                    }
                }

                $items[] = [
                    'name' => $name,
                    'path' => $this->getVirtualPath($file),
                    'type' => 'file',
                    'size' => $this->formatSize($disk->size($file)),
                    'last_modified' => date('d/m/Y H:i', $disk->lastModified($file)),
                    'extension' => $extension,
                    'icon' => $icon,
                    'color' => $color,
                    'url' => $url,
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
            $disk = Storage::disk($this->selectedDisk === 'personal' ? 'local' : $this->selectedDisk);
            $physicalPath = $this->getPhysicalPath($path);
            
            if (!$disk->exists($physicalPath)) {
                Notification::make()->title('El archivo no existe')->danger()->send();
                return null;
            }

            return $disk->download($physicalPath);
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
                        ->rules(['regex:/^[a-zA-Z0-9_\-\s\.\(\)\[\]áéíóúÁÉÍÓÚñÑüÜ]+$/u'])
                        ->validationMessages([
                            'regex' => 'El nombre de la carpeta solo puede contener letras, números, espacios, guiones, puntos y paréntesis.',
                        ]),
                ])
                ->action(function (array $data) {
                    try {
                        $newPath = trim($this->currentPath . '/' . $data['folderName'], '/');
                        $physicalNewPath = $this->getPhysicalPath($newPath);
                        $disk = Storage::disk($this->selectedDisk === 'personal' ? 'local' : $this->selectedDisk);
                        
                        if ($disk->exists($physicalNewPath)) {
                            Notification::make()->title('La carpeta ya existe')->danger()->send();
                            return;
                        }
                        
                        $disk->makeDirectory($physicalNewPath);
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
                        ->disk(fn() => $this->selectedDisk === 'personal' ? 'local' : $this->selectedDisk)
                        ->directory(fn() => $this->getPhysicalPath($this->currentPath) ?: '/')
                        ->preserveFilenames()
                ])
                ->action(function (array $data) {
                    Notification::make()->title('Archivos subidos correctamente')->success()->send();
                }),
        ];
    }

    public function deleteItemAction(): Action
    {
        return Action::make('deleteItem')
            ->label('Eliminar')
            ->requiresConfirmation()
            ->action(function (array $arguments) {
                try {
                    $path = $this->sanitizePath($arguments['path']);
                    $type = $arguments['type'];
                    $disk = Storage::disk($this->selectedDisk === 'personal' ? 'local' : $this->selectedDisk);
                    $physicalPath = $this->getPhysicalPath($path);
                    
                    if ($type === 'folder') {
                        $disk->deleteDirectory($physicalPath);
                    } else {
                        $disk->delete($physicalPath);
                    }
                    
                    Notification::make()->title('Eliminado correctamente')->success()->send();
                } catch (\Exception $e) {
                    Notification::make()->title('Error al eliminar')->body($e->getMessage())->danger()->send();
                }
            });
    }

    public function deleteSelectedAction(): Action
    {
        return Action::make('deleteSelected')
            ->label('Eliminar Seleccionados')
            ->requiresConfirmation()
            ->modalHeading('¿Eliminar elementos seleccionados?')
            ->modalDescription('Esta acción es irreversible y eliminará todos los archivos/carpetas seleccionados.')
            ->color('danger')
            ->action(function () {
                try {
                    if (empty($this->selectedItems)) {
                        return;
                    }
                    
                    $disk = Storage::disk($this->selectedDisk === 'personal' ? 'local' : $this->selectedDisk);
                    $deletedCount = 0;
                    
                    foreach ($this->selectedItems as $serialized) {
                        $parts = explode('|', $serialized);
                        if (count($parts) < 2) continue;
                        
                        $path = $this->sanitizePath($parts[0]);
                        $type = $parts[1];
                        $physicalPath = $this->getPhysicalPath($path);
                        
                        if ($type === 'folder') {
                            $disk->deleteDirectory($physicalPath);
                        } else {
                            $disk->delete($physicalPath);
                        }
                        $deletedCount++;
                    }
                    
                    $this->selectedItems = [];
                    Notification::make()->title("{$deletedCount} elementos eliminados correctamente")->success()->send();
                } catch (\Exception $e) {
                    Notification::make()->title('Error al eliminar')->body($e->getMessage())->danger()->send();
                }
            });
    }

    public function toggleSelectAll(): void
    {
        $items = $this->getItems();
        $allItemsSerialized = collect($items)->map(fn($item) => $item['path'] . '|' . $item['type'])->toArray();

        $allSelected = count($allItemsSerialized) > 0;
        foreach ($allItemsSerialized as $serialized) {
            if (!in_array($serialized, $this->selectedItems)) {
                $allSelected = false;
                break;
            }
        }

        if ($allSelected) {
            $this->selectedItems = array_values(array_diff($this->selectedItems, $allItemsSerialized));
        } else {
            $this->selectedItems = array_values(array_unique(array_merge($this->selectedItems, $allItemsSerialized)));
        }
    }

    public function previewFileAction(): Action
    {
        return Action::make('previewFile')
            ->label('Previsualizar')
            ->modalContent(function (array $arguments) {
                try {
                    $path = $this->sanitizePath($arguments['path']);
                    $disk = Storage::disk($this->selectedDisk === 'personal' ? 'local' : $this->selectedDisk);
                    $physicalPath = $this->getPhysicalPath($path);
                    $extension = strtolower(pathinfo($physicalPath, PATHINFO_EXTENSION));
                    
                    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']);
                    $isPdf = $extension === 'pdf';
                    
                    $canReadText = in_array($extension, ['txt', 'md', 'json', 'xml', 'html', 'css', 'js', 'php', 'log']);
                    $textContent = null;
                    
                    if ($canReadText && $disk->size($physicalPath) < 1024 * 1024) { // Max 1MB
                        $textContent = $disk->get($physicalPath);
                    }
                    
                    $url = null;
                    if ($this->selectedDisk === 'public') {
                        $url = Storage::disk('public')->url($physicalPath);
                    } else {
                        if ($isImage && $disk->size($physicalPath) < 10 * 1024 * 1024) {
                            $fileData = $disk->get($physicalPath);
                            $mime = ($extension === 'svg') ? 'image/svg+xml' : 'image/' . $extension;
                            $url = 'data:' . $mime . ';base64,' . base64_encode($fileData);
                        } elseif ($isPdf && $disk->size($physicalPath) < 5 * 1024 * 1024) {
                            $fileData = $disk->get($physicalPath);
                            $url = 'data:application/pdf;base64,' . base64_encode($fileData);
                        }
                    }

                    return view('filament.pages.file-preview', [
                        'name' => basename($physicalPath),
                        'path' => $path,
                        'url' => $url,
                        'isImage' => $isImage,
                        'isPdf' => $isPdf,
                        'canReadText' => $canReadText,
                        'textContent' => $textContent,
                        'extension' => $extension,
                        'size' => $this->formatSize($disk->size($physicalPath)),
                        'last_modified' => date('d/m/Y H:i', $disk->lastModified($physicalPath)),
                    ]);
                } catch (\Exception $e) {
                    return view('filament.pages.file-preview-error', ['error' => $e->getMessage()]);
                }
            })
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Cerrar');
    }

    public function moveItems(array $paths, string $targetFolderVirtualPath): void
    {
        try {
            if ($targetFolderVirtualPath === '_root_') {
                $targetFolderVirtualPath = '';
            }

            $disk = Storage::disk($this->selectedDisk === 'personal' ? 'local' : $this->selectedDisk);
            $targetFolderPhysical = $this->getPhysicalPath($targetFolderVirtualPath);

            // Ensure target is a directory and exists
            if (!$disk->exists($targetFolderPhysical)) {
                $disk->makeDirectory($targetFolderPhysical);
            }

            $movedCount = 0;
            foreach ($paths as $path) {
                $path = $this->sanitizePath($path);
                $physicalSource = $this->getPhysicalPath($path);
                $fileName = basename($physicalSource);
                $physicalTarget = trim($targetFolderPhysical . '/' . $fileName, '/');

                if ($physicalSource === $physicalTarget) {
                    continue; // Can't move onto itself
                }

                // Check if source exists
                if (!$disk->exists($physicalSource)) {
                    continue;
                }

                // Prevent moving a folder inside itself or its children
                if ($physicalSource === $targetFolderPhysical || str_starts_with($targetFolderPhysical, $physicalSource . '/')) {
                    Notification::make()->title('Acción no permitida')->body("No se puede mover la carpeta '{$fileName}' dentro de sí misma o de sus subcarpetas.")->warning()->send();
                    continue;
                }

                if ($disk->move($physicalSource, $physicalTarget)) {
                    $movedCount++;
                }
            }

            $this->selectedItems = [];
            Notification::make()->title("{$movedCount} elementos movidos correctamente")->success()->send();
        } catch (\Exception $e) {
            Notification::make()->title('Error al mover elementos')->body($e->getMessage())->danger()->send();
        }
    }

    public function handleDrop(string $draggedDataJson, string $targetFolderVirtualPath): void
    {
        try {
            $draggedData = json_decode($draggedDataJson, true);
            if (!$draggedData || !isset($draggedData['path'])) {
                return;
            }

            $sourcePath = $draggedData['path'];
            $sourceType = $draggedData['type'];

            $pathsToMove = [];
            $draggedItemSerialized = $sourcePath . '|' . $sourceType;

            if (in_array($draggedItemSerialized, $this->selectedItems)) {
                foreach ($this->selectedItems as $serialized) {
                    $parts = explode('|', $serialized);
                    if (count($parts) >= 1) {
                        $pathsToMove[] = $parts[0];
                    }
                }
            } else {
                $pathsToMove[] = $sourcePath;
            }

            $this->moveItems($pathsToMove, $targetFolderVirtualPath);
        } catch (\Exception $e) {
            Notification::make()->title('Error al procesar el arrastre')->body($e->getMessage())->danger()->send();
        }
    }

    public function getMoveFolderOptions(): array
    {
        try {
            $disk = Storage::disk($this->selectedDisk === 'personal' ? 'local' : $this->selectedDisk);
            $physicalCurrentPath = $this->getPhysicalPath($this->currentPath);
            
            $options = [];
            
            // Add parent directory option if not in root
            if (!empty($this->currentPath)) {
                $parentPath = dirname($this->currentPath);
                if ($parentPath === '.' || $parentPath === '') {
                    $parentPath = '_root_';
                }
                $options[$parentPath] = '.. (Subir un nivel)';
                $options['_root_'] = 'Raíz (/)';
            }
            
            // Add subfolders
            $directories = $disk->directories($physicalCurrentPath);
            foreach ($directories as $dir) {
                $virtual = $this->getVirtualPath($dir);
                $options[$virtual] = '/' . basename($dir);
            }
            
            return $options;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function moveItemAction(): Action
    {
        return Action::make('moveItem')
            ->label('Mover')
            ->modalHeading('Mover elemento')
            ->modalDescription('Elige la carpeta de destino para este elemento.')
            ->form([
                \Filament\Forms\Components\Select::make('targetFolder')
                    ->label('Carpeta Destino')
                    ->options(fn() => $this->getMoveFolderOptions())
                    ->placeholder('Selecciona una carpeta')
                    ->required(),
            ])
            ->action(function (array $data, array $arguments) {
                try {
                    $path = $this->sanitizePath($arguments['path']);
                    $this->moveItems([$path], $data['targetFolder']);
                } catch (\Exception $e) {
                    Notification::make()->title('Error al mover')->body($e->getMessage())->danger()->send();
                }
            });
    }

    public function moveSelectedAction(): Action
    {
        return Action::make('moveSelected')
            ->label('Mover Seleccionados')
            ->modalHeading('Mover elementos seleccionados')
            ->modalDescription('Elige la carpeta de destino para los elementos seleccionados.')
            ->form([
                \Filament\Forms\Components\Select::make('targetFolder')
                    ->label('Carpeta Destino')
                    ->options(fn() => $this->getMoveFolderOptions())
                    ->placeholder('Selecciona una carpeta')
                    ->required(),
            ])
            ->action(function (array $data) {
                try {
                    if (empty($this->selectedItems)) {
                        return;
                    }
                    
                    $pathsToMove = [];
                    foreach ($this->selectedItems as $serialized) {
                        $parts = explode('|', $serialized);
                        if (count($parts) >= 1) {
                            $pathsToMove[] = $parts[0];
                        }
                    }
                    
                    $this->moveItems($pathsToMove, $data['targetFolder']);
                } catch (\Exception $e) {
                    Notification::make()->title('Error al mover')->body($e->getMessage())->danger()->send();
                }
            });
    }
}

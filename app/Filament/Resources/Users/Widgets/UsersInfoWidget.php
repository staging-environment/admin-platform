<?php

namespace App\Filament\Resources\Users\Widgets;

use Filament\Widgets\Widget;

class UsersInfoWidget extends Widget
{
    protected string $view = 'filament.resources.users.widgets.users-info-widget';

    protected int | string | array $columnSpan = 'full';
}

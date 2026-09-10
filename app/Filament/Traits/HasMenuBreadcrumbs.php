<?php

namespace App\Filament\Traits;

use App\Filament\Resources\JobOffers\JobOfferResource;
use App\Filament\Resources\JobApplications\JobApplicationResource;
use App\Filament\Resources\Empleados\EmpleadoResource;

trait HasMenuBreadcrumbs
{
    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();

        // 1. Caso especial: JobOffers (Ofertas de Empleo)
        // Jerarqu?a directa: Recursos humanos > Ofertas de Empleo > Listado
        if ($resource === JobOfferResource::class) {
            $action = $this->getBreadcrumb();
            $isIndex = in_array(strtolower($action), ['listado', 'list', '']);

            if ($isIndex) {
                return [
                    'Recursos humanos',
                    'Ofertas de Empleo',
                    'Listado',
                ];
            }

            return [
                'Recursos humanos',
                $resource::getUrl('index') => 'Ofertas de Empleo',
                $action,
            ];
        }

        // 2. Caso especial: JobApplications (Inscritos a Ofertas)
        // Jerarqu?a directa: Recursos humanos > Inscritos a Ofertas > Listado
        if ($resource === JobApplicationResource::class) {
            $action = $this->getBreadcrumb();
            $isIndex = in_array(strtolower($action), ['listado', 'list', 'inscripciones', '']);

            if ($isIndex) {
                return [
                    'Recursos humanos',
                    'Inscritos a Ofertas',
                    'Listado',
                ];
            }

            return [
                'Recursos humanos',
                $resource::getUrl('index') => 'Inscritos a Ofertas',
                $action,
            ];
        }

        // 3. Obtener grupo padre seg?n navegaci?n
        $group = $resource::getNavigationGroup();
        $groupName = is_string($group) ? $group : ($group ? $group->getLabel() : null);

        if (!$groupName && $resource === EmpleadoResource::class) {
            $groupName = 'Recursos humanos';
        }

        $resourceBreadcrumb = $resource::getBreadcrumb();
        $action = $this->getBreadcrumb();
        $isIndex = in_array(strtolower($action), ['listado', 'list', '']);

        $breadcrumbs = [];

        if ($groupName) {
            $breadcrumbs[] = $groupName;
        }

        if ($isIndex) {
            $breadcrumbs[] = $resourceBreadcrumb;
            $breadcrumbs[] = 'Listado';
        } else {
            $breadcrumbs[$resource::getUrl('index')] = $resourceBreadcrumb;
            $breadcrumbs[] = $action;
        }

        return $breadcrumbs;
    }
}

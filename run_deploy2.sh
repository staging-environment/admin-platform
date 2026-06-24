#!/usr/bin/env bash
cd /home/bonilla/Projects/admin-platform
./.ddev/commands/host/deploy "feat: añadir campos de discapacidad y contrato en empleado (creacion, edicion, vista)"

ssh developer@164.68.101.69 'cd ~/Projects/admin-platform && ddev exec php artisan migrate && ddev exec php artisan optimize:clear && ddev exec php artisan optimize'

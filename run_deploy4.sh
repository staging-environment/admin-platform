#!/usr/bin/env bash
cd /home/bonilla/Projects/admin-platform
./.ddev/commands/host/deploy "style: destacar menu activo y asegurar visibilidad de breadcrumbs"

ssh developer@164.68.101.69 'cd ~/Projects/admin-platform && ddev exec php artisan optimize:clear && ddev exec php artisan optimize'

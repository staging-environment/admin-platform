#!/usr/bin/env bash
cd /home/bonilla/Projects/admin-platform
./.ddev/commands/host/deploy "fix: evitar que se remarque Administracion al estar en Inicio"

ssh developer@164.68.101.69 'cd ~/Projects/admin-platform && ddev exec php artisan optimize:clear && ddev exec php artisan optimize'

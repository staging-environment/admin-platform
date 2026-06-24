#!/usr/bin/env bash
cd /home/bonilla/Projects/admin-platform
./.ddev/commands/host/deploy "feat: asociar empleado a una gasolinera y añadir filtro en el listado"

ssh developer@164.68.101.69 'cd ~/Projects/admin-platform && ddev exec php artisan migrate --force && ddev exec php artisan optimize:clear && ddev exec php artisan optimize'

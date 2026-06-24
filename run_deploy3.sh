#!/usr/bin/env bash
cd /home/bonilla/Projects/admin-platform
./.ddev/commands/host/deploy "fix: eliminar campo incapacidad redundante en empleado"

ssh developer@164.68.101.69 'cd ~/Projects/admin-platform && ddev exec php artisan optimize:clear && ddev exec php artisan optimize'

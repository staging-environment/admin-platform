#!/bin/bash
scp update_perms.php developer@164.68.101.69:/home/developer/Projects/admin-platform/update_perms.php
ssh developer@164.68.101.69 "cd /home/developer/Projects/admin-platform && ddev exec php artisan tinker update_perms.php && rm update_perms.php"

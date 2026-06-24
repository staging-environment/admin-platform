#!/usr/bin/env bash
scp check_perms.php developer@164.68.101.69:~/Projects/admin-platform/check_perms_prod.php
ssh developer@164.68.101.69 'cd ~/Projects/admin-platform && ddev exec php check_perms_prod.php && rm check_perms_prod.php'

<?php
\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'gestion_comentarios_empleados']);
\Spatie\Permission\Models\Role::findByName('Admin')->givePermissionTo('gestion_comentarios_empleados');
\Spatie\Permission\Models\Role::findByName('Gestor')->givePermissionTo('gestion_comentarios_empleados');

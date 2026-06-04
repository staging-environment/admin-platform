<?php
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

Permission::firstOrCreate(['name' => 'gestion_comentarios_empleados']);
Role::findByName('Admin')->givePermissionTo('gestion_comentarios_empleados');
Role::findByName('Gestor')->givePermissionTo('gestion_comentarios_empleados');

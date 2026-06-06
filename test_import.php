<?php
try { 
    $exp = DB::connection('virtusgesnet')->table('expendedores')->first(); 
    if ($exp) { 
        $emp = \App\Models\Empleado::create([
            'virtus_codigo' => $exp->Codigo, 
            'nombre' => $exp->Nombre, 
            'apellidos' => null, 
            'telefono_principal' => $exp->Telefono ?? $exp->Movil, 
            'telefono_secundario' => ($exp->Telefono && $exp->Movil) ? $exp->Movil : null, 
            'direccion' => $exp->Domicilio, 
            'localidad' => $exp->Poblacion, 
            'provincia' => $exp->Provincia, 
            'codigo_postal' => $exp->DP, 
            'email' => $exp->Email
        ]); 
        echo json_encode($emp); 
    } else { 
        echo 'No expendedores found'; 
    } 
} catch (\Exception $e) { 
    echo 'Error: ' . $e->getMessage(); 
}

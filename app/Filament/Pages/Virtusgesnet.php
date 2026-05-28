<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Virtusgesnet extends Page
{
    protected string $view = 'filament.pages.virtusgesnet';

    protected static ?string $title = 'Virtusgesnet';
    protected static ?string $navigationLabel = 'Virtusgesnet';

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        return $user->hasRole('Admin');
    }

    public static array $groups = [
        'Ventas y Facturación' => [
            'albaranesdecompra', 'albaranesdeventa', 'detalledealbaranesdecompra', 'detalledealbaranesdeventa',
            'facturasdecompra', 'facturasyticketsdeventa', 'detalledefacturasdecompra', 'detalledefacturasyticketsdeventa',
            'descuentosycargosdefacturasdecompra', 'descuentosycargosdefacturasyticketsdeventa',
            'ivadealbaranesdecompra', 'ivadealbaranesdeventa', 'ivadefacturasdecompra', 'ivadefacturasyticketsdeventa',
            'seriesdealbaranesdecompra', 'seriesdealbaranesdeventa', 'seriesdefacturasdecompra', 'seriesdefacturasyticketsdeventa',
            'ventasencurso', 'detalledeventasencurso', 'ticketsdelavado'
        ],
        'Clientes, Proveedores y Choferes' => [
            'clientes', 'clientesdelgrupo', 'gruposdeclientes', 'visibilidaddeclientes',
            'proveedores', 'proveedoresdeproductos', 'choferes', 'vehiculos',
            'tarjetasdeclientes', 'productosadmitidosportarjetasdeclientes', 'centrosadministrativosdeclientes'
        ],
        'Inventario, Productos y Precios' => [
            'productos', 'productosdedescuentoporprocesadoresdetarjetas', 'productosh24', 'productosrepsol',
            'gruposdeproductos', 'gruposdeproductosaux', 'regimendeexplotaciondeproductos',
            'almacenesytanques', 'almacendegrupodeproductos', 'almacendeproductos',
            'movimientosdealmacen', 'regularizacionesdealmacen', 'detallederegularizacionesdealmacen',
            'traspasosdealmacen', 'detalledetraspasosdealmacen', 'minimosymaximos', 'motivosderegularizacion',
            'preciosdeproductos', 'preciosprogramados', 'preciosprogramadoslocales', 'detalledepreciosprogramados', 'detalledepreciosprogramadoslocales', 'programaciondeprecios',
            'blendingdeproducto'
        ],
        'Estaciones y Terminales (TPVs)' => [
            'estaciones', 'estacionesdelgrupo', 'gruposdeestaciones', 'reddeestaciones', 'criteriosdeagrupaciondeestaciones',
            'expendedores', 'expendedoresdelturno', 'islas', 'botones', 'grupodeparrillasdebotones',
            'tpvs', 'seriesportpv', 'horariosterminalesdesatendidos'
        ],
        'Cobros, Pagos y Caja' => [
            'cobros', 'detalledecobros', 'pagos', 'detalledepagos', 'formasdepago', 'vencimientosdeformasdepago',
            'cajas', 'conceptosdecaja', 'incidenciasdecaja', 'ingresosbancarios', 'ingresosdeseguridad', 'turnosingresosbancarios',
            'cuentasbancarias', 'cuentasbancariasderemesadeclientes', 'cuentasbancariasderemesadeproveedores',
            'remesasdecobro', 'remesasdepago', 'mediosdepago', 'mediosdepagodelaestacion',
            'cuentascontablesdecajas', 'cuentascontablesdeclientes', 'cuentascontablesdeconceptosdecaja', 'cuentascontablesdedescuentosycargos', 'cuentascontablesdeexpendedores', 'cuentascontablesdegruposdeproductos', 'cuentascontablesdemediosdepago', 'cuentascontablesdeproductos', 'cuentascontablesdeproveedores', 'cuentascontablesdetiposdeivaeigic'
        ],
        'Tarjetas y Procesadores' => [
            'binesporprocesadoresdetarjetas', 'codigosdegruposdeproductosporprocesadoresdetarjetas', 'codigosdeproductosporprocesadoresdetarjetas',
            'procesadoresdetarjetas', 'protocolospermitidosporprocesadoresdetarjetas',
            'servidoresdemediosdepago', 'nodosdeservidoresdemediosdepago',
            'nodossmpdefidelizacionporempresaytpv', 'nodossmpporempresamediodepagoytpv', 'pinesbancarios'
        ],
        'Fidelización y Cupones' => [
            'cupones', 'cuponespremiados', 'condicionesdeemisiondecupones',
            'promocionescupones', 'promocionespack', 'productosdepromocionespack', 'tiposdepromociones',
            'reglasdepuntos', 'operacionesdepuntos'
        ],
        'Pedidos de Compra' => [
            'pedidosdecompra', 'detalledepedidodecompra', 'descuentosycargosdepedidosdecompra', 'ivadepedidosdecompra', 'seriesdepedidosdecompra'
        ],
        'Geografía y Localización' => [
            'paises', 'provincias', 'municipios', 'comunidadesautonomas'
        ],
        'Logística y Otros' => [
            'expediciones', 'expedicionespendientesdeliquidar', 'parametrosdeexpediciones', 'tramosdeexpediciones',
            'emailings', 'detalledeemailings', 'emails', 'empresaspropias',
            'contadoresdemaquinasexpendedoras', 'maquinasexpendedoras', 'visualizaciondemaquinasexpendedorasportpv',
            'lecturasdecontadores', 'operacionesaceptador', 'plantillasexportacion',
            'accionesdeeventos', 'eventos', 'tablasconeventos', 'iniciosdesesion', 'registrosincronizacion'
        ]
    ];

    public string $search = '';

    public function getTablesData(): array
    {
        $grouped = [];
        try {
            $allTableNames = [];
            $tablesResult = DB::connection('virtusgesnet')->select('SHOW TABLES');
            foreach ($tablesResult as $tableRow) {
                $array = (array)$tableRow;
                $allTableNames[] = reset($array);
            }

            // Clean list by matching with search
            $filteredTableNames = $allTableNames;
            if ($this->search !== '') {
                $filteredTableNames = array_filter($allTableNames, function($name) {
                    return stripos($name, $this->search) !== false;
                });
            }

            // Group tables
            $allGroupedTableNames = [];
            foreach (self::$groups as $groupName => $tables) {
                $matchingTables = array_intersect($tables, $filteredTableNames);
                if (!empty($matchingTables)) {
                    $grouped[$groupName] = array_values($matchingTables);
                }
                $allGroupedTableNames = array_merge($allGroupedTableNames, $tables);
            }

            // Handle ungrouped
            $ungrouped = array_diff($filteredTableNames, $allGroupedTableNames);
            if (!empty($ungrouped)) {
                $grouped['Tablas no agrupadas'] = array_values($ungrouped);
            }

        } catch (\Exception $e) {
            // Handle DB connection error or missing tables
            $grouped['Error de conexión: ' . $e->getMessage()] = [];
        }

        return $grouped;
    }
}

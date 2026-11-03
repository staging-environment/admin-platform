# Módulo de Planificación y Gestión de Turnos (Planing)

Este documento recoge las reglas de negocio, la estructura operativa de las estaciones de servicio de Utrecar y la arquitectura prevista para el desarrollo del módulo de **Planing de Turnos**.

---

## 1. Contexto Operativo y Distribución por Gasolinera

Utrecar opera con **4 estaciones de servicio**, cada una con particularidades horarias y de plantilla:

| Gasolinera | Código | Horario | Estructura de Turnos Diaria | Plantilla Base Asignada (Ej. Noviembre 2026) |
|---|:---:|---|---|---|
| **Vistalegre** (Utrera) | 1 | Diurno (Sin noche) | 1 Puesto: Mañana (M) y Tarde (T) | 3 personas: Ant. Jesús, Felisa, Miguel Ángel (Sustituta habitual: Consolación). |
| **Ronda Norte** (Utrera) | 2 | **24 Horas Permanente** | Puesto 1 (M/T), Puesto 2 (M/T) y Turno Noche (N) | **Puesto 1**: Mari Carmen, Carvajal, Jesús.<br>**Puesto 2**: Laura, Sergio, Israel.<br>**Noche**: Fran, Romero. |
| **Rodalabota** (El Cuervo) | 3 | Diurno (Sin noche) | 1 Puesto: Mañana (M) y Tarde (T) | 3 personas: Marrufo, Borja, Emilio. |
| **Atenas** (Lebrija) | 4 | Diurno (Sin noche) | 1 Puesto: Mañana (M) y Tarde (T) | 3 personas: Toni, Zambrano, Joaquín. |

---

## 2. El Patrón Rotativo 4x2 (Ciclo de 12 Días)

En todas las líneas de trabajo diurno se aplica un ciclo rotativo estricto de **12 días** entre **3 empleados**:

1. **4 días** en turno de **Mañana (M)**.
2. **2 días** de **Descanso (D)**.
3. **4 días** en turno de **Tarde (T)**.
4. **2 días** de **Descanso (D)**.

### Matriz de Cobertura Continua:
```text
Día:         01 02 03 04 05 06 07 08 09 10 11 12
Empleado A:   M  M  M  M  D  D  T  T  T  T  D  D  -> Se repite
Empleado B:   T  T  D  D  M  M  M  M  D  D  T  T
Empleado C:   D  D  T  T  T  T  D  D  M  M  M  M
```
* **Garantía operativa**: En todo momento coinciden exactamente 2 empleados trabajando (1 mañana, 1 tarde) y 1 descansando.
* El mes siguiente **continúa la secuencia matemática** sin reiniciarse al día 1.

---

## 3. Sustituciones, Suplencias y Vacaciones

El cuadrante mensual cuenta con una **base teórica (4x2)** y **modificaciones operativas reales**:
* **Vacaciones / Bajas**: Cuando un empleado titular no puede realizar su turno asignado, se asigna un suplente (ej. *José Manuel sustituyendo a Zambrano o Emilio*, *Javier sustituyendo a Carvajal*, *Conso sustituyendo a Miguel Ángel*).
* **Representación Visual**:
  * La celda del turno debe destacar con el color del empleado asignado.
  * Si hay una sustitución, debe indicarse con un estilo diferenciado (ej. borde punteado o píldora de suplente) mostrando quién está trabajando realmente y quién es el titular ausente.
* **Observaciones y Acuerdos de Mes**: Espacio de texto libre al pie del cuadrante para reflejar acuerdos de gerencia (ej. *apoyo en cafetería*, *horas fuera de turno*, etc.).

---

## 4. Requisitos de Interfaz y Navegación (Filament / Blade)

### Ubicación en el Menú:
* Se ubica en el menú principal dentro de **Recursos humanos**, inmediatamente debajo de **Vacaciones**.
* Ítem: **`Planing`** (desplegable de tercer nivel).
  * └─ **Vistalegre** (`/admin/planing/vistalegre`)
  * └─ **Ronda Norte** (`/admin/planing/ronda-norte`)
  * └─ **Rodalabota** (`/admin/planing/rodalabota`)
  * └─ **Atenas** (`/admin/planing/atenas`)

### Cuadrícula Mensual:
* Selector de Mes y Año con navegación anterior / siguiente.
* Botón **"Generar Cuadrante Teórico 4x2"** que rellena automáticamente el mes continuando el ciclo anterior.
* Cada celda es interactiva: clic para cambiar empleado, registrar sustituto o añadir una nota.
* Botón de **"Exportar Cuadrante en PDF"** formateado para imprimir y colgar en la cartelera de la gasolinera.

---

## 5. Arquitectura de Datos Prevista (Laravel)

* **`cuadrantes_mensuales`**:
  * `id`, `gasolinera_codigo` (1, 2, 3, 4), `anio`, `mes`, `observaciones`, `estado` (borrador, publicado), timestamps.
* **`cuadrante_asignaciones`**:
  * `id`, `cuadrante_mensual_id`, `fecha` (date), `puesto` (Puesto 1, Puesto 2, Noche), `turno` (M, T, N, D), `empleado_id` (quien trabaja), `empleado_titular_id` (a quien le tocaba por 4x2), `tipo` (ordinario, sustitucion, refuerzo), `motivo_sustitucion`, `notas`, timestamps.
* **`empleados.color_hex`**: Color corporativo de cada trabajador para pintar las celdas del cuadrante de forma inmediata.

---

## 6. Recursos ya Desarrollados en el Proyecto
* **Comando Artisan**: `app/Console/Commands/GenerarPropuestaPlaningPdfCommand.php` (`php artisan planing:generar-propuesta-pdf`).
* **Vista Blade PDF Ejecutiva**: `resources/views/pdf/propuesta-planing-turnos.blade.php`.
* **Dossier Ejecutivo PDF para Dirección**: `Propuesta_Modulo_Planing_Turnos_Utrecar.pdf` (3 páginas, A4 portrait, libre de jerga técnica).

# Módulo de Planificación y Gestión de Turnos (Planing)

Este documento recoge las reglas de negocio, la estructura operativa de las estaciones de servicio de Utrecar y la arquitectura del módulo de **Planing de Turnos**. Documento de requisitos completo en [`docs/PRD_MODULO_PLANING_TURNOS.md`](file:///docs/PRD_MODULO_PLANING_TURNOS.md).

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

## 2. Regla Visual del Planing: Solo Turnos de Trabajo (REGLA CRÍTICA)

* **En el cuadrante NO se muestran filas de descanso ni quién está librando.**
* La cuadrícula mensual y el informe impreso muestran **única y exclusivamente quién trabaja cada día y en qué turno** (Mañana, Tarde y Noche).
* Quien no aparece asignado a un turno en un día determinado se entiende automáticamente que está en su jornada de descanso reglamentario (D) o permiso.

### Filas por Gasolinera en la Cuadrícula:
* **Estaciones Diurnas (Vistalegre, Rodalabota, Atenas)**:
  * `MAÑANA`
  * `TARDE`
* **Estación 24 Horas (Ronda Norte)**:
  * `PUESTO 1 - MAÑANA`
  * `PUESTO 1 - TARDE`
  * `PUESTO 2 - MAÑANA`
  * `PUESTO 2 - TARDE`
  * `NOCHE`

---

## 3. El Patrón Rotativo 4x2 (Ciclo de 12 Días)

En todas las líneas de trabajo diurno se aplica un ciclo rotativo estricto de **12 días** entre **3 empleados**:
1. **4 días** en turno de **Mañana (M)**.
2. **2 días** de **Descanso (D)**.
3. **4 días** en turno de **Tarde (T)**.
4. **2 días** de **Descanso (D)**.

### Matriz de Cobertura Continua (Fases a intervalos de 4 días):
```text
Día:         01 02 03 04 05 06 07 08 09 10 11 12
Empleado A:   M  M  M  M  D  D  T  T  T  T  D  D  -> Se repite
Empleado B:   T  T  D  D  M  M  M  M  D  D  T  T
Empleado C:   D  D  T  T  T  T  D  D  M  M  M  M

Visible en Pantalla:
MAÑANA:       A  A  A  A  B  B  B  B  C  C  C  C
TARDE:        B  B  C  C  C  C  A  A  A  A  B  B
```
* **Garantía operativa**: En todo momento coinciden exactamente 2 empleados trabajando (1 mañana, 1 tarde) y 1 descansando.
* El mes siguiente **continúa la secuencia matemática** sin reiniciarse al día 1.

---

## 4. Sustituciones, Suplencias y Vacaciones

* **Base teórica (4x2)** vs **Modificaciones operativas reales**:
  * Cuando un titular no puede realizar su turno (vacaciones, baja, permiso), se asigna un suplente.
  * La celda del turno conserva quién es el titular teórico y quién está trabajando realmente, con un estilo visual diferenciado.
* **Observaciones del Mes**: Espacio de texto libre al pie del cuadrante para reflejar acuerdos de gerencia.

---

## 5. Requisitos de Interfaz y Navegación (Filament v5)

* **Ubicación en el Menú**:
  * Menú principal en **Recursos humanos**, debajo de **Vacaciones**.
  * Ítem: **`Planing`** (`/admin/planing-turnos`).
* **Permiso Spatie**: `gestion_planing` (y compatibilidad con `gestion_recursos_humanos`).
* **Acciones en Cuadrante**:
  * Selector ágil de gasolinera (Vistalegre, Ronda Norte, Rodalabota, Atenas).
  * Selector de Mes y Año con navegación anterior / siguiente.
  * Botón **"Generar Cuadrante 4x2"** que rellena automáticamente el mes continuando el ciclo.
  * Clic en celda para modificar empleado, registrar sustituto o añadir una nota.
  * Botón **"Exportar PDF"** formateado para imprimir y colgar en la cartelera.

---

## 6. Arquitectura de Datos (Laravel)

* **`planing_cuadrantes`**:
  * `id`, `gasolinera_codigo` (1, 2, 3, 4), `anio`, `mes`, `observaciones`, `estado` (borrador, publicado), timestamps.
* **`planing_turnos`**:
  * `id`, `planing_cuadrante_id`, `fecha` (date), `puesto` (Puesto 1, Puesto 2, Puesto Único, Noche), `turno` (M, T, N), `empleado_id` (quien trabaja), `empleado_titular_id` (a quien le tocaba por 4x2), `es_sustitucion` (bool), `motivo_sustitucion`, `notas`, timestamps.
* **`empleados.color_hex`**: Color corporativo de cada trabajador para pintar las celdas del cuadrante de forma inmediata.

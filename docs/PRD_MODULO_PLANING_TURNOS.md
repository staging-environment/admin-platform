# PRD: Módulo de Planificación y Gestión de Turnos (Planing)

Este documento define los requisitos funcionales, la arquitectura técnica, las reglas de negocio y la experiencia de usuario para el módulo de **Planing de Turnos** de **Utrecar S.L.** en la plataforma `admin-platform`.

---

## 1. Alcance y Objetivos de Negocio

El módulo de Planing sustituye las hojas de cálculo aisladas (Excel) por un sistema nativo, seguro e interactivo integrado en el panel de control de Utrecar (Laravel + Filament v5).

### Objetivos Clave:
1. **Digitalización integral**: Gestión centralizada de los cuadrantes de las 4 estaciones de servicio.
2. **Generación automática 4x2**: Cálculo instantáneo del cuadrante teórico mensual respetando la secuencia matemática del mes anterior.
3. **Gestión de suplencias**: Modificación ágil de turnos ante vacaciones, bajas o refuerzos con trazabilidad del titular original y del suplente.
4. **Exportación para cartelera**: Generación de PDF mensual listo para imprimir y colgar en la cabina de la gasolinera.
5. **Máxima claridad visual**: Visualización enfocada exclusivamente en los turnos efectivos de trabajo, eliminando ruidos visuales innecesarios.

---

## 2. Estaciones y Estructura Operativa de Turnos

Utrecar opera con **4 estaciones de servicio**, agrupadas en dos tipologías operativas:

| Código | Gasolinera | Municipio | Horario | Líneas / Puestos | Turnos Diarios Visibles | Plantilla Base Típica |
|:---:|---|---|---|---|---|---|
| **1** | **Vistalegre** | Utrera | Diurno | 1 Puesto Diurno | **Mañana (M)**<br>**Tarde (T)** | 3 personas (rotativo 4x2) |
| **2** | **Ronda Norte** | Utrera | **24 Horas** | Puesto 1 (Día)<br>Puesto 2 (Día)<br>Noche (24h) | **Puesto 1: Mañana (M)**<br>**Puesto 1: Tarde (T)**<br>**Puesto 2: Mañana (M)**<br>**Puesto 2: Tarde (T)**<br>**Noche (N)** | Puesto 1: 3 personas (4x2)<br>Puesto 2: 3 personas (4x2)<br>Noche: 2 personas |
| **3** | **Rodalabota** | El Cuervo | Diurno | 1 Puesto Diurno | **Mañana (M)**<br>**Tarde (T)** | 3 personas (rotativo 4x2) |
| **4** | **Atenas** | Lebrija | Diurno | 1 Puesto Diurno | **Mañana (M)**<br>**Tarde (T)** | 3 personas (rotativo 4x2) |

---

## 3. Regla Visual del Planing: Solo Turnos de Trabajo (REGLA CRÍTICA)

> [!IMPORTANT]
> **En el cuadrante NO se muestran filas de descanso ni quién está librando.**
> La cuadrícula mensual y el informe PDF muestran **única y exclusivamente quién trabaja cada día y en qué turno** (Mañana, Tarde y Noche).
> Quien no aparece asignado a un turno en un día determinado se entiende automáticamente que está en su jornada de libranza/descanso reglamentario o suplencia.

### Estructura de la Matriz Mensual:
* **Columnas**: Días naturales del mes (1 al 28, 29, 30 o 31), con indicación del día de la semana (L, M, X, J, V, S, D). Los fines de semana (S, D) se destacan visualmente con un tono sutil.
* **Filas por Gasolinera**:
  * **Estaciones 1, 3 y 4 (Vistalegre, Rodalabota, Atenas)**:
    1. `MAÑANA`
    2. `TARDE`
  * **Estación 2 (Ronda Norte)**:
    1. `PUESTO 1 - MAÑANA`
    2. `PUESTO 1 - TARDE`
    3. `PUESTO 2 - MAÑANA`
    4. `PUESTO 2 - TARDE`
    5. `NOCHE`
* **Contenido de la Celda**:
  * Nombre corto del trabajador visible (`nombre_corto`).
  * **Color asignado al TURNO**: El color de fondo y borde pertenece al turno rotativo 4x2 (`Turno 1: Rojo`, `Turno 2: Verde`, `Turno 3: Azul`), no al perfil del trabajador.
  * Si se realiza una **sustitución puntual**, el color de fondo **permanece inalterado**, actualizándose exclusivamente el nombre del trabajador asignado con un asterisco indicativo (`*`).
  * Al hacer clic en la celda se abre el modal de asignación/modificación de turno.

---

## 4. El Ciclo Rotativo 4x2 (12 Días)

En todas las líneas de trabajo diurno se aplica un ciclo rotativo estricto de **12 días** entre **3 empleados**:
* **4 días** en turno de **Mañana (M)**.
* **2 días** de **Descanso (D)**.
* **4 días** en turno de **Tarde (T)**.
* **2 días** de **Descanso (D)**.

### Matriz de Distribución Matemática (Fases desfasadas 4 días):
```text
Día del ciclo:  01 02 03 04 05 06 07 08 09 10 11 12
Empleado 1:      M  M  M  M  D  D  T  T  T  T  D  D
Empleado 2:      T  T  D  D  M  M  M  M  D  D  T  T
Empleado 3:      D  D  T  T  T  T  D  D  M  M  M  M

Resultado en Planing (Solo se reflejan M y T):
MAÑANA:         E1 E1 E1 E1 E2 E2 E2 E2 E3 E3 E3 E3
TARDE:          E2 E2 E3 E3 E3 E3 E1 E1 E1 E1 E2 E2
(Descansando):  E3 E3 E2 E2 E1 E1 E2 E2 E2 E2 E1 E1 -> No se dibuja en cuadrante
```
* **Garantía**: En todo momento hay exactamente **1 Mañana y 1 Tarde** cubiertas por línea diurna.
* **Continuidad temporal**: Al cambiar de mes (ej. del 31 de mayo al 1 de junio), el día 1 de junio continúa la fase matemática siguiente al día 31 de mayo sin reinicios artificiales.

---

## 5. Arquitectura de Datos (Laravel)

### Tabla `planing_cuadrantes`
Representa el contenedor mensual por estación de servicio:
* `id`: Clave primaria autoincremental.
* `gasolinera_codigo`: Código de estación (1, 2, 3, 4).
* `anio`: Año (ej. 2026).
* `mes`: Mes (1 a 12).
* `estado`: `borrador` | `publicado` (por defecto `publicado`).
* `observaciones`: Texto libre para acuerdos de gerencia, cambios acordados o notas del mes.
* `created_by` / `updated_by`: ID del usuario administrador que realiza las modificaciones.
* `timestamps`: `created_at` y `updated_at`.
* *Índice único*: `[gasolinera_codigo, anio, mes]`.

### Tabla `planing_turnos`
Representa cada turno concreto asignado en una fecha determinada:
* `id`: Clave primaria autoincremental.
* `planing_cuadrante_id`: Foreign key a `planing_cuadrantes` (con borrado en cascada).
* `fecha`: Fecha del turno (`date`).
* `puesto`: Puesto o línea de servicio (`Puesto 1`, `Puesto 2`, `Puesto Único`, `Noche`).
* `turno`: Código de turno (`M` = Mañana, `T` = Tarde, `N` = Noche).
* `empleado_id`: ID del empleado que efectivamente trabaja ese día (`foreign key` a `empleados.id`, nullable si queda vacante).
* `empleado_titular_id`: ID del empleado al que le correspondía el turno por el 4x2 teórico (`foreign key` a `empleados.id`, nullable).
* `es_sustitucion`: Booleano (`true` si el empleado difiere del titular original).
* `motivo_sustitucion`: `vacaciones`, `baja`, `permiso`, `cambio`, `refuerzo`, `otro`.
* `notas`: Cadena corta con anotaciones específicas de ese día o turno.
* `timestamps`: `created_at` y `updated_at`.
* *Índice único*: `[planing_cuadrante_id, fecha, puesto, turno]`.

### Modelo de Colores de Turno (`planing_turnos.estilo_color`):
* El color de cada celda se calcula dinámicamente mediante `PlaningTurnosService::resolverColorTurno`.
* **Esquema Estándar (Rojo, Verde y Azul)**: Aplicado a las 4 gasolineras (Vistalegre, Rodalabota, Atenas y Ronda Norte Puesto 2).
  * `Turno 1`: Rojo (`#ef4444`, texto blanco)
  * `Turno 2`: Verde (`#a9d08e`, texto gris oscuro)
  * `Turno 3`: Azul (`#8ea9db`, texto gris oscuro)
* **Excepción Ronda Norte (Puesto 1 y Noche)**:
  * `Puesto 1`: Mari Carmen (Azul `#8ea9db`), Antonio Carvajal (Verde `#a9d08e`), Jesús Arcos (Naranja `#ed7d31`).
  * `Turno Noche`: Francisco Acaiña / Fran (Púrpura `#7030a0`), Antonio Romero (Amarillo `#ffff00`).

---

## 6. Control de Acceso y Autorización (Spatie Permissions)

En estricto cumplimiento de la regla de seguridad del proyecto:
* **Permiso Spatie**: `gestion_planing` (y compatibilidad con `gestion_recursos_humanos`).
* **Verificación de Acceso**:
  ```php
  public static function canAccess(): bool
  {
      $user = auth()->user();
      if (!$user) return false;

      return $user->can('gestion_planing')
          || $user->can('gestion_recursos_humanos')
          || $user->email === 'jarodriguezbonilla@gmail.com'
          || $user->id === 1;
  }
  ```
* Se crea migración para registrar el permiso `gestion_planing` y asignarlo por defecto al rol `Admin`.

---

## 7. Experiencia de Usuario y Flujo en Filament

### Ubicación en Navegación:
* **Grupo**: `Recursos humanos`.
* **Etiqueta**: `Planing`.
* **Icono**: `heroicon-o-calendar-days` o `heroicon-o-table-cells`.
* **Orden**: `navigationSort = 5` (inmediatamente después de `Vacaciones`).

### Componentes de la Pantalla:
1. **Selector de Gasolinera**: Botones/Pestañas destacadas para alternar entre Vistalegre, Ronda Norte, Rodalabota y Atenas.
2. **Navegación Temporal**:
   * Botón `<` Mes Anterior.
   * Desplegable de Mes y Año.
   * Botón `>` Mes Siguiente.
   * Botón **"Generar Cuadrante 4x2"**: Rellena automáticamente los turnos del mes según la rotación teórica.
   * Botón **"Exportar PDF"**: Descarga el cuadrante mensual formateado en PDF para imprimir.
3. **Cuadrícula Interactiva**:
   * Tabla con cabecera de días (número y día de la semana).
   * Filas por turno.
   * Clic en celda para abrir el modal de asignación o sustitución.
4. **Caja de Observaciones del Mes**:
   * Campo de texto libre para guardar notas operativas del mes.
5. **Leyenda de Turnos y Código de Colores**:
   * Lista visual que muestra los turnos de la estación (Turno 1: Rojo, Turno 2: Verde, Turno 3: Azul, o desglose en Ronda Norte) con el titular habitual de cada turno y una nota aclaratoria sobre la persistencia del fondo ante sustituciones.

---

## 8. Exportación PDF para Tablón

* Formato **A4 Apaisado (Landscape)**.
* Cabecera con logotipo de Utrecar, nombre de la estación, mes y año.
* Matriz de turnos con los colores asignados al turno y tipografía clara y legible desde media distancia.
* Pie de página con las observaciones del mes, **Leyenda de Código de Colores por Turno** y espacio de firmas de conformidad.


# Especificación Técnica y Funcional: Módulo de Planing de Turnos

Este documento complementa el [PRD Oficial del Módulo de Planing](PRD_MODULO_PLANING_TURNOS.md) y detalla la implementación técnica en Laravel y Filament.

---

## 1. Alcance y Objetivos
* **Plataforma**: Laravel 13 + Filament 5 + Livewire + TailwindCSS.
* **Ubicación en Navegación**: Menú *Recursos humanos* > *Planing*.
* **Alcance**: 4 Estaciones de servicio (Vistalegre, Ronda Norte, Rodalabota y Atenas).
* **Criterio Visual**: El cuadrante muestra **exclusivamente turnos de trabajo** (Mañana, Tarde y Noche), sin filas de descanso.

---

## 2. Estructura de Turnos Diarios por Estación

### Estaciones Diurnas (1 Puesto)
* **Vistalegre (Cod 1)**, **Rodalabota (Cod 3)**, **Atenas (Cod 4)**.
* 2 filas diarias:
  * **Mañana (M)**
  * **Tarde (T)**
* Ciclo 4x2 continuo entre 3 personas (fases desfasadas 4 días).

### Estación Central (Ronda Norte - 24 Horas - Cod 2)
* 5 filas diarias:
  * **Puesto 1 - Mañana (M)**
  * **Puesto 1 - Tarde (T)**
  * **Puesto 2 - Mañana (M)**
  * **Puesto 2 - Tarde (T)**
  * **Noche (N)**
* Doble línea diurna en ciclo 4x2 + 2 personas rotando en turno nocturno.

---

## 3. Sustituciones y Modificaciones de Cuadrante
* **Cuadrante Teórico**: Generado automáticamente aplicando la fórmula rotativa a partir del último turno del mes anterior.
* **Cuadrante Real (Suplencias)**:
  * Permite asignar un sustituto a cualquier turno (vacaciones, bajas, permisos, etc.).
  * La celda del turno conserva el dato del titular original y registra al suplente, el motivo y notas.
* **Observaciones del Mes**: Campo de texto libre para documentar acuerdos de gerencia, adaptaciones horarias y refuerzos.

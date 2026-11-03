# Especificación Técnica y Funcional: Módulo de Planing de Turnos

Este documento detalla la especificación del módulo de **Planing de Turnos** para las estaciones de servicio del Grupo Utrecar.

---

## 1. Alcance y Objetivos
Digitalizar y sustituir los cuadrantes en hojas de cálculo (Excel) por un módulo nativo integrado en la plataforma web interna de Utrecar.
* **Plataforma**: Laravel 13 + Filament 5 + Livewire + TailwindCSS.
* **Ubicación en Navegación**: Menú *Recursos humanos* > *Planing* (submenú con las 4 estaciones).
* **Alcance**: 4 Estaciones de servicio (Vistalegre, Ronda Norte, Rodalabota y Atenas).

---

## 2. Lógica de Cuadrantes y Turnos

### A. Estaciones de 1 Puesto Diurno (Vistalegre, Rodalabota, Atenas)
* **Horario**: Diurno, sin apertura nocturna.
* **Turnos diarios**: Mañana (M) y Tarde (T).
* **Plantilla base**: 3 trabajadores por estación.
* **Ciclo Rotativo 4x2 (12 días de periodo)**:
  * 4 días Mañana (M)
  * 2 días Descanso (D)
  * 4 días Tarde (T)
  * 2 días Descanso (D)
* **Propiedad matemática**: En cualquier día del mes, exactamente 1 persona está en Mañana, 1 persona en Tarde y 1 persona en Descanso.

### B. Estación Central Ronda Norte (24 Horas)
* **Horario**: 24 Horas permanente.
* **Líneas de servicio simultáneas**:
  * **Puesto 1 (Día)**: Mañana y Tarde (3 personas en ciclo 4x2).
  * **Puesto 2 (Día)**: Mañana y Tarde (3 personas en ciclo 4x2).
  * **Turno Noche**: Fran y Romero rotando en turno nocturno.

---

## 3. Sustituciones y Modificaciones de Cuadrante
* **Cuadrante Teórico**: Generado automáticamente aplicando la fórmula rotativa a partir del último turno del mes anterior.
* **Cuadrante Real (Suplencias)**:
  * Permite asignar un sustituto a cualquier turno (ej. *José Manuel sustituyendo a Zambrano por vacaciones*).
  * La celda del turno conserva el dato del titular original y registra al suplente, el motivo (*Vacaciones, Baja, Permiso, Cambio acordado*) y notas.
* **Observaciones del Mes**: Campo de texto libre para documentar acuerdos de gerencia, adaptaciones horarias y refuerzos.

---

## 4. Recursos del Proyecto
* **Especificación de reglas para agentes**: `.agents/rules/planing_turnos.md`
* **Plantilla PDF de la Propuesta Ejecutiva**: `resources/views/pdf/propuesta-planing-turnos.blade.php`
* **Comando generador**: `app/Console/Commands/GenerarPropuestaPlaningPdfCommand.php` (`php artisan planing:generar-propuesta-pdf`)
* **Dossier Ejecutivo PDF para Dirección**: `Propuesta_Modulo_Planing_Turnos_Utrecar.pdf`

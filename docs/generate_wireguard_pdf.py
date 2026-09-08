import os
from fpdf import FPDF

class WireGuardGuidePDF(FPDF):
    def header(self):
        # Top banner background
        self.set_fill_color(30, 41, 59) # Slate 800
        self.rect(0, 0, 210, 30, 'F')
        
        # Header Accent Line
        self.set_fill_color(14, 165, 233) # Sky 500
        self.rect(0, 29, 210, 1.5, 'F')
        
        # Title text
        self.set_text_color(255, 255, 255)
        self.set_font('helvetica', 'B', 14)
        self.set_xy(10, 7)
        self.cell(0, 7, 'PLATAFORMA UTRECAR | INFRAESTRUCTURA DE RED', align='C', new_x="LMARGIN", new_y="NEXT")
        
        # Subtitle text
        self.set_font('helvetica', 'B', 10)
        self.set_text_color(186, 230, 253) # Sky 200
        self.cell(0, 5, 'Guia de Configuracion: WireGuard como Servicio de Windows (Equipos Locales)', align='C', new_x="LMARGIN", new_y="NEXT")
        
        # Reset positioning
        self.set_text_color(0, 0, 0)
        self.set_xy(15, 36)

    def footer(self):
        self.set_y(-14)
        self.set_font('helvetica', 'I', 8)
        self.set_text_color(156, 163, 175) # Gray 400
        self.cell(0, 8, f'Pagina {self.page_no()}/{{nb}}  |  Documentacion Tecnica Utrecar S.L. 2026', align='C')

    def code_box(self, code_text):
        self.set_fill_color(241, 245, 249) # Slate 100
        self.set_draw_color(203, 213, 225) # Slate 300
        self.set_text_color(15, 23, 42)    # Slate 900
        self.set_font('courier', 'B', 8.5)
        
        # Calculate height based on lines
        lines = code_text.split('\n')
        box_height = max(10, len(lines) * 6 + 4)
        
        cur_y = self.get_y()
        self.rect(15, cur_y, 180, box_height, 'DF')
        self.set_xy(18, cur_y + 2.5)
        self.multi_cell(174, 5, code_text)
        self.set_xy(15, cur_y + box_height + 3)

    def section_title(self, title):
        self.set_font('helvetica', 'B', 11)
        self.set_text_color(30, 41, 59) # Slate 800
        self.cell(0, 6, title, new_x="LMARGIN", new_y="NEXT")
        self.ln(1)

    def body_text(self, text):
        self.set_font('helvetica', '', 9.5)
        self.set_text_color(51, 65, 85) # Slate 700
        self.multi_cell(0, 4.5, text)
        self.ln(3)


def create_pdf(output_path):
    pdf = WireGuardGuidePDF()
    pdf.alias_nb_pages()
    pdf.set_margins(15, 36, 15)
    pdf.set_auto_page_break(auto=True, margin=16)
    
    # ═══════════════════════════════════════════
    # PÁGINA 1
    # ═══════════════════════════════════════════
    pdf.add_page()
    
    pdf.section_title('1. Objetivo y Ventajas Operativas')
    pdf.body_text(
        'En entornos de estaciones de servicio, oficinas y locales comerciales, ejecutar WireGuard como un '
        'Servicio Nativo de Windows (Windows Service) es la solucion recomendada por los siguientes motivos:\n\n'
        '  - Conectividad Previa al Inicio de Sesion (Pre-Logon): El tunel se levanta automaticamente tan pronto '
        'enciende el PC, sin necesidad de que un usuario inicie sesion en Windows.\n'
        '  - Disponibilidad 24/7 y Tolerancia a Reinicios: Tras cortes de luz o actualizaciones de Windows, la red '
        'se reconecta de forma desatendida, manteniendo comunicados TPVs, bases de datos y acceso remoto.\n'
        '  - Cero Manipulacion por Personal de Tienda: Al no depender de la aplicacion grafica ni del icono en la '
        'barra de tareas, los empleados no pueden desactivar la VPN accidentalmente.\n'
        '  - Rendimiento Maximo: Utiliza el controlador de kernel Wintun de alto rendimiento con prioridad de sistema.'
    )
    
    pdf.section_title('2. Preparacion del Archivo de Configuracion (.conf)')
    pdf.body_text(
        'Antes de instalar el servicio, verifique que el archivo del cliente en el local (ej. "utrecar.conf") '
        'incluya la directiva PersistentKeepalive en la seccion [Peer] para que la conexion no se cierre tras el router:\n\n'
        '  [Peer]\n'
        '  PublicKey = <Clave_Publica_del_Servidor>\n'
        '  Endpoint = 164.68.101.69:51820\n'
        '  AllowedIPs = 10.8.0.0/24\n'
        '  PersistentKeepalive = 25\n\n'
        'Guarde el archivo en una ruta permanente y segura del equipo local, por ejemplo:\n'
        'C:\\Program Files\\WireGuard\\Data\\Configurations\\utrecar.conf'
    )
    
    pdf.section_title('3. Instalacion del Tunel como Servicio de Windows')
    pdf.body_text(
        'Abra una ventana de Simbolo del Sistema (CMD) o PowerShell como Administrador en el PC local y ejecute '
        'el comando de instalacion nativo de WireGuard:'
    )
    pdf.code_box('"C:\\Program Files\\WireGuard\\wireguard.exe" /installtunnelservice "C:\\Program Files\\WireGuard\\Data\\Configurations\\utrecar.conf"')
    pdf.body_text(
        'Esto creara e iniciara de inmediato un nuevo servicio en Windows con el nombre: "WireGuardTunnel$utrecar", '
        'configurado automaticamente con tipo de inicio "Automatico".'
    )
    
    # ═══════════════════════════════════════════
    # PÁGINA 2
    # ═══════════════════════════════════════════
    pdf.add_page()
    
    pdf.section_title('4. Configuracion de Auto-Recuperacion ante Caidas')
    pdf.body_text(
        'Para garantizar que Windows relance el servicio inmediatamente si el proceso sufriera algun fallo o cierre '
        'inesperado, configure las acciones de recuperacion del Administrador de Servicios (SCM) con este comando:'
    )
    pdf.code_box('sc failure "WireGuardTunnel$utrecar" reset= 86400 actions= restart/5000/restart/5000/restart/5000')
    pdf.body_text(
        'Significado del ajuste:\n'
        '  - 1er, 2do y siguientes errores: Reinicia el servicio automaticamente tras 5 segundos (5000 ms).\n'
        '  - Reinicio de contador: Restablece el contador de fallos cada 24 horas (86400 segundos).\n\n'
        'Nota sobre cortes de Internet: Si se corta la fibra o el router del local se reinicia, el servicio de '
        'WireGuard NO se cae (no tiene estado TCP). Tan pronto el router vuelve a tener conexion, WireGuard restablece '
        'el trafico de manera instantanea y transparente sin intervencion manual.'
    )
    
    pdf.section_title('5. Desactivar la Interfaz Grafica (GUI) para los Empleados')
    pdf.body_text(
        'IMPORTANTE: NO se debe desinstalar el programa WireGuard de Windows, ya que el servicio depende del ejecutable '
        '"wireguard.exe" y del controlador "wintun.sys" instalados en Archivos de Programa.\n\n'
        'Lo que se debe hacer es evitar que la aplicacion de usuario se abra al iniciar sesion los empleados:\n'
        '  1. Abra el Administrador de Tareas (Ctrl + Shift + Esc).\n'
        '  2. Vaya a la pestana "Inicio" (o "Aplicaciones de inicio").\n'
        '  3. Localice "WireGuard", haga clic derecho y seleccione "Deshabilitar".\n\n'
        'De este modo, los empleados nunca veran la ventana ni el icono en la barra de tareas, mientras el servicio '
        'trabaja en segundo plano de forma silenciosa e ininterrumpida.'
    )
    
    pdf.section_title('6. Comprobacion, Diagnostico y Desinstalacion')
    pdf.body_text(
        'Comandos utiles para comprobacion y mantenimiento:\n'
        '  - Comprobar estado del servicio:   sc query "WireGuardTunnel$utrecar"\n'
        '  - Ver adaptador y trafico IP:      wireguard.exe /dumplog\n\n'
        'En caso de necesitar desinstalar el servicio para cambiar la configuracion:'
    )
    pdf.code_box('"C:\\Program Files\\WireGuard\\wireguard.exe" /uninstalltunnelservice utrecar')
    
    # Cuadro informativo de soporte
    pdf.ln(2)
    cur_y = pdf.get_y()
    pdf.set_fill_color(248, 250, 252) # Slate 50
    pdf.set_draw_color(226, 232, 240) # Slate 200
    pdf.rect(15, cur_y, 180, 22, 'DF')
    pdf.set_xy(18, cur_y + 3)
    pdf.set_font('helvetica', 'B', 8.5)
    pdf.set_text_color(15, 23, 42)
    pdf.cell(0, 4, 'SOPORTE Y ASISTENCIA TECNICA - UTRECAR', new_x="LMARGIN", new_y="NEXT")
    pdf.set_font('helvetica', '', 8)
    pdf.set_text_color(71, 85, 105)
    pdf.cell(0, 4, 'Para incidencias de red, consulta de rangos IP o altas de nuevos tuneles VPN para locales,', new_x="LMARGIN", new_y="NEXT")
    pdf.cell(0, 4, 'contactar con el Departamento de Sistemas: informatica@utrecar.com | admin-platform.', new_x="LMARGIN", new_y="NEXT")
    
    pdf.output(output_path)
    print(f"PDF generado correctamente en: {output_path}")

if __name__ == "__main__":
    out_dir = "/home/bonilla/Projects/admin-platform/docs"
    os.makedirs(out_dir, exist_ok=True)
    pdf_file = os.path.join(out_dir, "Guia_Configuracion_WireGuard_Servicio_Windows.pdf")
    create_pdf(pdf_file)

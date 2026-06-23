import os
from fpdf import FPDF

class UtrecarManualPDF(FPDF):
    def header(self):
        # Draw top banner background
        self.set_fill_color(30, 41, 59) # Slate 800
        self.rect(0, 0, 210, 32, 'F')
        
        # Title text
        self.set_text_color(255, 255, 255)
        self.set_font('helvetica', 'B', 15)
        self.set_xy(10, 8)
        self.cell(0, 8, 'PLATAFORMA ENERGÉTICA UTRECAR', align='C', new_x="LMARGIN", new_y="NEXT")
        
        # Subtitle text
        self.set_font('helvetica', 'B', 11)
        self.set_text_color(147, 197, 253) # Light Blue 300
        self.cell(0, 6, 'Manual de Configuración de Alertas (Telegram Bot)', align='C', new_x="LMARGIN", new_y="NEXT")
        
        # Restore font color and margin
        self.set_text_color(0, 0, 0)
        self.set_xy(15, 38)

    def footer(self):
        self.set_y(-15)
        self.set_font('helvetica', 'I', 8)
        self.set_text_color(156, 163, 175) # Gray 400
        self.cell(0, 10, f'Página {self.page_no()}/{{nb}} | Utrecar S.L. 2026', align='C')

def create_manual():
    pdf = UtrecarManualPDF()
    pdf.alias_nb_pages()
    pdf.set_margins(15, 38, 15)
    pdf.add_page()
    
    # ─── INTRODUCCIÓN ───
    pdf.set_font('helvetica', 'B', 14)
    pdf.set_text_color(30, 41, 59) # Slate 800
    pdf.cell(0, 8, '1. Introducción al Sistema de Alertas', new_x="LMARGIN", new_y="NEXT")
    pdf.ln(2)
    
    pdf.set_font('helvetica', '', 10)
    pdf.set_text_color(55, 65, 81) # Gray 700
    intro_text = (
        "El bot de Telegram (utrecar_alertas_bot) es un canal automatizado diseñado para notificar en "
        "tiempo real a los usuarios autorizados de Utrecar sobre cualquier cambio en los precios de la competencia. "
        "El bot consulta la base de datos oficial del Ministerio (MITECO) cada 15 minutos y, en caso de detectar "
        "una variación de precios en las localidades monitorizadas (Utrera, Sevilla, El Cuervo, Lebrija), "
        "envía una alerta inmediata con el listado del Top 5 de competidores para Diésel y Gasolina 95."
    )
    pdf.multi_cell(0, 5, intro_text)
    pdf.ln(5)
    
    # ─── PASO 1 ───
    pdf.set_font('helvetica', 'B', 12)
    pdf.set_text_color(30, 41, 59)
    pdf.cell(0, 6, 'Paso 1: Instalar Telegram en su dispositivo móvil', new_x="LMARGIN", new_y="NEXT")
    pdf.ln(1)
    
    pdf.set_font('helvetica', '', 10)
    pdf.set_text_color(55, 65, 81)
    step1_text = (
        "Para recibir las notificaciones en su teléfono móvil (Android o iOS), debe tener la aplicación "
        "oficial de Telegram instalada:\n"
        "  - Android (Samsung, Xiaomi, etc.): Abra la aplicación Google Play Store y busque \"Telegram\".\n"
        "  - Apple (iPhone): Abra la App Store y busque \"Telegram\".\n"
        "  - Descargue e instale la aplicación, y regístrese con su número de teléfono si no tiene una cuenta activa."
    )
    pdf.multi_cell(0, 5, step1_text)
    pdf.ln(5)

    # ─── PASO 2 ───
    pdf.set_font('helvetica', 'B', 12)
    pdf.set_text_color(30, 41, 59)
    pdf.cell(0, 6, 'Paso 2: Registrar su número de teléfono en la plataforma', new_x="LMARGIN", new_y="NEXT")
    pdf.ln(1)
    
    pdf.set_font('helvetica', '', 10)
    pdf.set_text_color(55, 65, 81)
    step2_text = (
        "La vinculación se realiza automáticamente mediante su número de teléfono móvil:\n"
        "  1. Inicie sesión en la plataforma de Utrecar (https://utrecar.com/admin).\n"
        "  2. Diríjase a su Perfil de Usuario (arriba a la derecha o en la URL https://utrecar.com/profile).\n"
        "  3. Asegúrese de añadir o corregir su número de teléfono móvil y guarde los cambios.\n"
        "  Nota: Si tiene el permiso de alertas pero aún no ha vinculado Telegram, el Panel de Control\n"
        "        le mostrará un aviso recordatorio en la parte superior."
    )
    pdf.multi_cell(0, 5, step2_text)
    pdf.ln(5)

    # ─── PASO 3 ───
    pdf.set_font('helvetica', 'B', 12)
    pdf.set_text_color(30, 41, 59)
    pdf.cell(0, 6, 'Paso 3: Buscar el bot en Telegram e iniciar la conversación', new_x="LMARGIN", new_y="NEXT")
    pdf.ln(1)
    
    pdf.set_font('helvetica', '', 10)
    pdf.set_text_color(55, 65, 81)
    step3_text = (
        "Una vez dentro de Telegram:\n"
        "  1. Pulse sobre el icono de lupa (Buscar) en la esquina superior derecha.\n"
        "  2. Escriba exactamente el nombre del bot: utrecar_alertas_bot (o entre en https://t.me/utrecar_alertas_bot).\n"
        "  3. Seleccione el bot que aparece en la lista de resultados.\n"
        "  4. Pulse el botón inferior \"Iniciar\" (o escriba /start) para activar el bot."
    )
    pdf.multi_cell(0, 5, step3_text)
    pdf.ln(5)

    # ─── PASO 4 ───
    pdf.set_font('helvetica', 'B', 12)
    pdf.set_text_color(30, 41, 59)
    pdf.cell(0, 6, 'Paso 4: Compartir el teléfono y activar las alertas', new_x="LMARGIN", new_y="NEXT")
    pdf.ln(1)
    
    pdf.set_font('helvetica', '', 10)
    pdf.set_text_color(55, 65, 81)
    step4_text = (
        "Para completar el proceso y activar la recepción de notificaciones:\n"
        "  1. Tras iniciar el bot, le aparecerá un botón grande abajo que dice \"Compartir Teléfono\".\n"
        "  2. Pulse ese botón. Telegram le pedirá confirmación para compartir su número de móvil.\n"
        "  3. El bot buscará su número en la plataforma y guardará automáticamente su identificador (Chat ID).\n"
        "  4. Recibirá un mensaje de confirmación: \"¡Vinculación completada con éxito!\".\n\n"
        "Nota: Recuerde que el administrador debe asignarle el permiso \"recibir_notificaciones_competencia\"."
    )
    pdf.multi_cell(0, 5, step4_text)
    pdf.ln(8)
    
    # ─── CUADRO DE SOPORTE / CONTACTO ───
    pdf.set_fill_color(243, 244, 246) # Light gray 100
    pdf.set_draw_color(209, 213, 219) # Gray 300
    pdf.rect(15, 185, 180, 25, 'DF')
    
    pdf.set_xy(20, 188)
    pdf.set_font('helvetica', 'B', 9)
    pdf.set_text_color(30, 41, 59)
    pdf.cell(0, 4, 'SOPORTE TÉCNICO Y AYUDA', new_x="LMARGIN", new_y="NEXT")
    pdf.set_font('helvetica', '', 9)
    pdf.set_text_color(75, 85, 99)
    pdf.cell(0, 4, 'Si experimenta problemas al recibir las alertas o vinculando su cuenta, póngase en', new_x="LMARGIN", new_y="NEXT")
    pdf.cell(0, 4, 'contacto con el administrador del sistema en admin@utrecar.com.', new_x="LMARGIN", new_y="NEXT")

    pdf.output("Manual_Alertas_Telegram.pdf")
    print("PDF generated successfully.")

if __name__ == "__main__":
    create_manual()

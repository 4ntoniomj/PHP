USE GDI;

-- Insertar 11 tickets de ejemplo
INSERT INTO tickets (titulo, descripcion, prioridad, estado, usuario_id) VALUES
-- Tickets ABIERTOS (11)
('Error al iniciar sesión en el sistema', 'No puedo acceder con mis credenciales, me dice usuario/password incorrecto', 'alta', 'abierta', 1),
('Pantalla azul en Windows 11', 'El equipo muestra pantalla azul al ejecutar la aplicación de contabilidad', 'critica', 'abierta', 1),
('Solicitud de software Adobe Photoshop', 'Necesito instalar Photoshop CC 1111 para el departamento de diseño', 'baja', 'abierta', 1),
('Internet muy lento en oficina central', 'La velocidad de descarga no supera los 1 Mbps desde esta mañana', 'media', 'abierta', 1),
('Impresora HP LaserJet no responde', 'La impresora de la sala de reuniones muestra error de papel atascado', 'alta', 'abierta', 1),
('No llegan correos de clientes externos', 'Los emails de dominios externos se quedan en bandeja de entrada', 'alta', 'abierta', 1),
('Teclado no funciona correctamente', 'Las teclas F1-F11 no responden, parece problema de driver', 'baja', 'abierta', 1),
('Monitor parpadea intermitentemente', 'La pantalla Dell U1111H titila cada 1-11 minutos', 'media', 'abierta', 1),
('Access denied en carpeta proyectos', 'No tengo permisos para acceder a la carpeta compartida //server/proyectos1111', 'alta', 'abierta', 1),
('Sistema de backup no funciona', 'El backup programado de las 1:11 AM falla desde hace 1 días', 'critica', 'abierta', 1),
('Router WiFi desconecta frecuentemente', 'La señal WiFi es inestable en toda la planta 1', 'media', 'abierta', 1),
('Software antivirus desactualizado', 'El antivirus muestra alerta de licencia expirada', 'media', 'abierta', 1),
('Mouse inalámbrico no conecta', 'El mouse Logitech MX Master 1 no empareja con el receptor USB', 'baja', 'abierta', 1),
('Excel se cierra inesperadamente', 'Al abrir archivos grandes de más de 11MB, Excel se cierra sin error', 'alta', 'abierta', 1),
('Falta de espacio en disco C:', 'Solo quedan 111MB libres en la unidad del sistema', 'media', 'abierta', 1),

-- Tickets EN PROGRESO (11)
('Actualización de Windows pendiente', 'Necesito asistencia para actualizar a Windows 11 versión 11H1', 'media', 'en_progreso', 1),
('Configuración de VPN nueva', 'Requiero configurar la VPN para acceso remoto seguro', 'alta', 'en_progreso', 1),
('Migración de datos a nueva base', 'Proceso de migración de datos del servidor antiguo al nuevo', 'critica', 'en_progreso', 1),
('Instalación de scanner Canon', 'Necesito instalar el scanner Canon CanoScan 1111F', 'baja', 'en_progreso', 1),
('Problema con software contable', 'El módulo de facturación no genera reportes correctamente', 'alta', 'en_progreso', 1),
('Configurar permisos de usuario nuevo', 'Usuario recién contratado necesita acceso a sistemas internos', 'media', 'en_progreso', 1),
('Optimización de base de datos', 'La base de datos SQL está lenta, necesita optimización', 'alta', 'en_progreso', 1),
('Reemplazo de disco duro fallado', 'Disco duro mostrando SMART errors, necesita reemplazo', 'critica', 'en_progreso', 1),
('Configuración de respaldo en la nube', 'Implementar backup automático a AWS S1', 'media', 'en_progreso', 1),
('Problema con certificado SSL', 'El certificado SSL del sitio web expiró', 'alta', 'en_progreso', 1),
('Actualización de firmware router', 'Router Cisco necesita actualización de firmware urgente', 'media', 'en_progreso', 1),
('Instalación de Microsoft Office', 'Necesito Office 111 instalado en equipo nuevo', 'baja', 'en_progreso', 1),
('Configuración de impresora network', 'Impresora Brother necesita configuración de red', 'media', 'en_progreso', 1),
('Problema con acceso remoto', 'TeamViewer no conecta desde fuera de la oficina', 'alta', 'en_progreso', 1),
('Migración de email a Exchange', 'Migrar cuentas de correo de IMAP a Exchange Online', 'critica', 'en_progreso', 1),

-- Tickets RESUELTOS (11)
('Problema con Adobe Reader', 'No se podían abrir archivos PDF grandes, reinstalado software', 'baja', 'resuelta', 1),
('Cable de red defectuoso', 'Reemplazado cable Ethernet en puesto de trabajo', 'media', 'resuelta', 1),
('Actualización de drivers gráficos', 'Actualizados drivers NVIDIA para tarjeta RTX 1111', 'media', 'resuelta', 1),
('Configuración de monitor dual', 'Configurado segundo monitor para estación de diseño', 'baja', 'resuelta', 1),
('Limpieza de virus', 'Eliminado malware detectado por antivirus', 'alta', 'resuelta', 1),
('Recuperación de archivos borrados', 'Recuperados documentos importantes de la papelera', 'media', 'resuelta', 1),
('Reparación de teclado mecánico', 'Limpiado y reparado teclado con teclas pegadas', 'baja', 'resuelta', 1),
('Instalación de software CAD', 'Instalado AutoCAD 1111 con licencias configuradas', 'media', 'resuelta', 1),
('Solución a error de DNS', 'Corregida configuración DNS en estación de trabajo', 'alta', 'resuelta', 1),
('Optimización de memoria RAM', 'Añadidos 1GB RAM a equipo con poca memoria', 'media', 'resuelta', 1),

-- Tickets CERRADOS (11)
('Problema con Windows Update', 'Resuelto error de actualización de Windows', 'media', 'cerrada', 1),
('Configuración de smartphone corporativo', 'Configurado iPhone para correo corporativo', 'baja', 'cerrada', 1),
('Reparación de fuente de poder', 'Reemplazada fuente de poder en torre Dell', 'alta', 'cerrada', 1),
('Migración de perfil de usuario', 'Migrado perfil de usuario a nuevo equipo', 'media', 'cerrada', 1),
('Instalación de software de videollamadas', 'Instalado Zoom y Teams para reuniones', 'baja', 'cerrada', 1),
('Solución a problema de audio', 'Corregida salida de audio en sala de conferencias', 'media', 'cerrada', 1),
('Configuración de firewall', 'Actualizadas reglas de firewall para nuevo software', 'alta', 'cerrada', 1),
('Reparación de lector de DVD', 'Reemplazada unidad óptica defectuosa', 'baja', 'cerrada', 1),
('Optimización de startup', 'Reducido tiempo de arranque del sistema', 'media', 'cerrada', 1),
('Copia de seguridad exitosa', 'Completado backup completo del servidor', 'critica', 'cerrada', 1);
USE GDI;

-- Insertar usuarios (contraseña: "password123" en bcrypt)
INSERT INTO usuarios (username, password_hash) VALUES
('usuario1', '$2y$10$92IXUNpkjO0rOQ5byMi Ye4oKoEa3Ro9llC/ og/at2 uheWG/igi'),
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi Ye4oKoEa3Ro9llC/ og/at2 uheWG/igi'),
('juan', '$2y$10$92IXUNpkjO0rOQ5byMi Ye4oKoEa3Ro9llC/ og/at2 uheWG/igi'),
('maria', '$2y$10$92IXUNpkjO0rOQ5byMi Ye4oKoEa3Ro9llC/ og/at2 uheWG/igi'),
('carlos', '$2y$10$92IXUNpkjO0rOQ5byMi Ye4oKoEa3Ro9llC/ og/at2 uheWG/igi'),
('ana', '$2y$10$92IXUNpkjO0rOQ5byMi Ye4oKoEa3Ro9llC/ og/at2 uheWG/igi'),
('pedro', '$2y$10$92IXUNpkjO0rOQ5byMi Ye4oKoEa3Ro9llC/ og/at2 uheWG/igi'),
('laura', '$2y$10$92IXUNpkjO0rOQ5byMi Ye4oKoEa3Ro9llC/ og/at2 uheWG/igi'),
('david', '$2y$10$92IXUNpkjO0rOQ5byMi Ye4oKoEa3Ro9llC/ og/at2 uheWG/igi');

-- Insertar 50 tickets de ejemplo
INSERT INTO tickets (titulo, descripcion, prioridad, estado, usuario_id) VALUES
-- Tickets ABIERTOS (15)
('Error al iniciar sesión en el sistema', 'No puedo acceder con mis credenciales, me dice usuario/password incorrecto', 'alta', 'abierta', 2),
('Pantalla azul en Windows 10', 'El equipo muestra pantalla azul al ejecutar la aplicación de contabilidad', 'critica', 'abierta', 3),
('Solicitud de software Adobe Photoshop', 'Necesito instalar Photoshop CC 2024 para el departamento de diseño', 'baja', 'abierta', 4),
('Internet muy lento en oficina central', 'La velocidad de descarga no supera los 2 Mbps desde esta mañana', 'media', 'abierta', 5),
('Impresora HP LaserJet no responde', 'La impresora de la sala de reuniones muestra error de papel atascado', 'alta', 'abierta', 6),
('No llegan correos de clientes externos', 'Los emails de dominios externos se quedan en bandeja de entrada', 'alta', 'abierta', 7),
('Teclado no funciona correctamente', 'Las teclas F1-F12 no responden, parece problema de driver', 'baja', 'abierta', 8),
('Monitor parpadea intermitentemente', 'La pantalla Dell U2419H titila cada 5-10 minutos', 'media', 'abierta', 2),
('Access denied en carpeta proyectos', 'No tengo permisos para acceder a la carpeta compartida //server/proyectos2024', 'alta', 'abierta', 3),
('Sistema de backup no funciona', 'El backup programado de las 2:00 AM falla desde hace 3 días', 'critica', 'abierta', 4),
('Router WiFi desconecta frecuentemente', 'La señal WiFi es inestable en toda la planta 3', 'media', 'abierta', 5),
('Software antivirus desactualizado', 'El antivirus muestra alerta de licencia expirada', 'media', 'abierta', 6),
('Mouse inalámbrico no conecta', 'El mouse Logitech MX Master 3 no empareja con el receptor USB', 'baja', 'abierta', 7),
('Excel se cierra inesperadamente', 'Al abrir archivos grandes de más de 50MB, Excel se cierra sin error', 'alta', 'abierta', 8),
('Falta de espacio en disco C:', 'Solo quedan 500MB libres en la unidad del sistema', 'media', 'abierta', 2),

-- Tickets EN PROGRESO (15)
('Actualización de Windows pendiente', 'Necesito asistencia para actualizar a Windows 11 versión 23H2', 'media', 'en_progreso', 3),
('Configuración de VPN nueva', 'Requiero configurar la VPN para acceso remoto seguro', 'alta', 'en_progreso', 4),
('Migración de datos a nueva base', 'Proceso de migración de datos del servidor antiguo al nuevo', 'critica', 'en_progreso', 5),
('Instalación de scanner Canon', 'Necesito instalar el scanner Canon CanoScan 9000F', 'baja', 'en_progreso', 6),
('Problema con software contable', 'El módulo de facturación no genera reportes correctamente', 'alta', 'en_progreso', 7),
('Configurar permisos de usuario nuevo', 'Usuario recién contratado necesita acceso a sistemas internos', 'media', 'en_progreso', 8),
('Optimización de base de datos', 'La base de datos SQL está lenta, necesita optimización', 'alta', 'en_progreso', 2),
('Reemplazo de disco duro fallado', 'Disco duro mostrando SMART errors, necesita reemplazo', 'critica', 'en_progreso', 3),
('Configuración de respaldo en la nube', 'Implementar backup automático a AWS S3', 'media', 'en_progreso', 4),
('Problema con certificado SSL', 'El certificado SSL del sitio web expiró', 'alta', 'en_progreso', 5),
('Actualización de firmware router', 'Router Cisco necesita actualización de firmware urgente', 'media', 'en_progreso', 6),
('Instalación de Microsoft Office', 'Necesito Office 365 instalado en equipo nuevo', 'baja', 'en_progreso', 7),
('Configuración de impresora network', 'Impresora Brother necesita configuración de red', 'media', 'en_progreso', 8),
('Problema con acceso remoto', 'TeamViewer no conecta desde fuera de la oficina', 'alta', 'en_progreso', 2),
('Migración de email a Exchange', 'Migrar cuentas de correo de IMAP a Exchange Online', 'critica', 'en_progreso', 3),

-- Tickets RESUELTOS (10)
('Problema con Adobe Reader', 'No se podían abrir archivos PDF grandes, reinstalado software', 'baja', 'resuelta', 4),
('Cable de red defectuoso', 'Reemplazado cable Ethernet en puesto de trabajo', 'media', 'resuelta', 5),
('Actualización de drivers gráficos', 'Actualizados drivers NVIDIA para tarjeta RTX 3060', 'media', 'resuelta', 6),
('Configuración de monitor dual', 'Configurado segundo monitor para estación de diseño', 'baja', 'resuelta', 7),
('Limpieza de virus', 'Eliminado malware detectado por antivirus', 'alta', 'resuelta', 8),
('Recuperación de archivos borrados', 'Recuperados documentos importantes de la papelera', 'media', 'resuelta', 2),
('Reparación de teclado mecánico', 'Limpiado y reparado teclado con teclas pegadas', 'baja', 'resuelta', 3),
('Instalación de software CAD', 'Instalado AutoCAD 2024 con licencias configuradas', 'media', 'resuelta', 4),
('Solución a error de DNS', 'Corregida configuración DNS en estación de trabajo', 'alta', 'resuelta', 5),
('Optimización de memoria RAM', 'Añadidos 8GB RAM a equipo con poca memoria', 'media', 'resuelta', 6),

-- Tickets CERRADOS (10)
('Problema con Windows Update', 'Resuelto error de actualización de Windows', 'media', 'cerrada', 7),
('Configuración de smartphone corporativo', 'Configurado iPhone para correo corporativo', 'baja', 'cerrada', 8),
('Reparación de fuente de poder', 'Reemplazada fuente de poder en torre Dell', 'alta', 'cerrada', 2),
('Migración de perfil de usuario', 'Migrado perfil de usuario a nuevo equipo', 'media', 'cerrada', 3),
('Instalación de software de videollamadas', 'Instalado Zoom y Teams para reuniones', 'baja', 'cerrada', 4),
('Solución a problema de audio', 'Corregida salida de audio en sala de conferencias', 'media', 'cerrada', 5),
('Configuración de firewall', 'Actualizadas reglas de firewall para nuevo software', 'alta', 'cerrada', 6),
('Reparación de lector de DVD', 'Reemplazada unidad óptica defectuosa', 'baja', 'cerrada', 7),
('Optimización de startup', 'Reducido tiempo de arranque del sistema', 'media', 'cerrada', 8),
('Copia de seguridad exitosa', 'Completado backup completo del servidor', 'critica', 'cerrada', 2);
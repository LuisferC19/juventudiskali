# Módulo de Respaldos - Sistema ISKALLI

## Descripción

El módulo de Respaldos permite generar y descargar respaldos completos de la base de datos del sistema ISKALLI en formato SQL. Este módulo está diseñado para ser utilizado únicamente por usuarios con rol de Administrador, garantizando la seguridad de los datos sensibles del sistema.

## Características

- **Generación automática**: Crea respaldos completos con un solo clic
- **Descarga inmediata**: El archivo SQL se descarga automáticamente al navegador
- **Nombres descriptivos**: Los archivos incluyen fecha y hora de creación
- **Seguridad**: Solo administradores pueden acceder al módulo
- **Compatibilidad**: Funciona en Windows con Laragon y MySQL 8.0+
- **Estructura completa**: Incluye tablas, datos, rutinas y triggers

## Requisitos del Sistema

### Servidor
- **Sistema Operativo**: Windows 10/11
- **Servidor Web**: Laragon
- **Base de Datos**: MySQL 8.0+
- **PHP**: Versión 8.0 o superior

### Configuración de PHP
- Función `exec()` habilitada en `php.ini`
- Permisos de escritura en el directorio del proyecto

### Base de Datos
- Nombre: `iskali`
- Usuario con permisos de respaldo
- Acceso desde localhost

## Instalación

### 1. Verificar Ruta de mysqldump

Primero, confirma la ubicación de `mysqldump.exe` en tu instalación de Laragon:

```
C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysqldump.exe
```

Si la versión de MySQL es diferente, ajusta la ruta en `models/BackupModel.php`.

### 2. Habilitar exec() en PHP

Edita el archivo `php.ini` de Laragon (generalmente en `C:\laragon\bin\php\php-8.x.x\php.ini`):

1. Busca la línea:
   ```
   ;disable_functions =
   ```
2. Asegúrate de que `exec` NO esté en la lista de funciones deshabilitadas.

3. Reinicia Laragon después de los cambios.

### 3. Configurar Laragon

1. Asegúrate de que MySQL esté ejecutándose en Laragon.
2. Verifica que puedas conectarte a la base de datos `iskali` desde PHP.

### 4. Crear Directorio de Respaldos

El módulo crea automáticamente la carpeta `/Respaldos` en la raíz del proyecto. Si hay problemas de permisos:

```bash
# Desde el directorio del proyecto
mkdir Respaldos
```

### 5. Verificar Permisos de Escritura

Asegúrate de que PHP tenga permisos de escritura en:
- Directorio `/Respaldos`
- Archivos temporales durante la generación

## Configuración

### Archivo de Configuración

El módulo utiliza la configuración existente en `env.php`:

```php
return [
    'DB_HOST'    => 'localhost',
    'DB_NAME'    => 'iskali',
    'DB_USER'    => 'root',
    'DB_PASS'    => '',
    'DB_CHARSET' => 'utf8mb4',
];
```

### Personalización de Ruta de mysqldump

Si tu instalación de MySQL está en una ruta diferente, edita `models/BackupModel.php`:

```php
// Cambia esta línea con tu ruta correcta
$mysqldumpPath = 'C:\\tu\\ruta\\a\\mysqldump.exe';
```

## Cómo Usar

### Acceso al Módulo

1. Inicia sesión en el sistema ISKALLI con un usuario Administrador.
2. En el menú lateral, haz clic en "Respaldos" (en la sección "Control").
3. Verás la página principal del módulo.

### Generar un Respaldo

1. En la página de Respaldos, haz clic en el botón "Generar Respaldo".
2. El sistema ejecutará `mysqldump` y generará el archivo SQL.
3. La descarga comenzará automáticamente.
4. El archivo tendrá un nombre como: `backup_iskali_2026-05-11_14-30-00.sql`

### Verificar el Respaldo

Después de descargar, puedes verificar el archivo SQL con cualquier editor de texto o importarlo en MySQL para probar.

## Estructura de Archivos

```
juventudiskali/
├── controllers/
│   └── BackupController.php      # Controlador principal
├── models/
│   └── BackupModel.php           # Modelo con configuración
├── views/
│   └── pages/
│       └── respaldos.php         # Vista del módulo
├── Respaldos/                    # Directorio de respaldos temporales
└── index.php                     # Router actualizado
```

## Seguridad

### Control de Acceso

- Solo usuarios con rol "Administrador" pueden acceder al módulo.
- Los usuarios sin permisos verán un mensaje "Acceso denegado".
- La sesión se verifica en cada petición.

### Manejo de Archivos

- Los archivos de respaldo se eliminan automáticamente después de la descarga.
- No se almacenan respaldos permanentemente en el servidor.
- Los nombres de archivo no revelan información sensible.

## Solución de Problemas

### Error: "No se pudo crear el directorio de respaldos"

**Solución**: Verifica permisos de escritura en el directorio del proyecto.

```bash
# Otorga permisos completos (Windows)
icacls "C:\laragon\www\juventudiskali" /grant "IIS_IUSRS:(OI)(CI)F" /T
```

### Error: "Error al ejecutar mysqldump"

**Posibles causas**:
1. Ruta incorrecta de `mysqldump.exe`
2. Función `exec()` deshabilitada en PHP
3. Credenciales de base de datos incorrectas
4. MySQL no está ejecutándose

**Soluciones**:
1. Verifica la ruta en `BackupModel.php`
2. Habilita `exec()` en `php.ini`
3. Revisa las credenciales en `env.php`
4. Inicia MySQL en Laragon

### Error: "Archivo no encontrado para descarga"

**Solución**: Verifica que el directorio `/Respaldos` tenga permisos de escritura y que no haya antivirus bloqueando la creación de archivos.

### El respaldo se descarga pero está vacío

**Solución**: Revisa los logs de MySQL y verifica que el usuario tenga permisos de SELECT en todas las tablas.

## Restaurar un Respaldo

### Método 1: Usando phpMyAdmin (recomendado)

1. Abre phpMyAdmin en Laragon.
2. Selecciona la base de datos `iskali`.
3. Ve a la pestaña "Importar".
4. Selecciona el archivo `.sql` descargado.
5. Haz clic en "Continuar".

### Método 2: Usando línea de comandos

```bash
mysql -u root -p iskali < backup_iskali_2026-05-11_14-30-00.sql
```

### Método 3: Usando Laragon

1. Abre el Terminal de Laragon.
2. Ejecuta:
   ```bash
   mysql -u root -p iskali < ruta\al\archivo.sql
   ```

## Requisitos de Rendimiento

- **Espacio en disco**: El respaldo puede ocupar espacio temporal durante la generación.
- **Memoria**: Suficiente RAM para manejar el proceso de mysqldump.
- **Tiempo**: La generación puede tomar tiempo dependiendo del tamaño de la base de datos.

## Mantenimiento

### Limpiar Archivos Temporales

Aunque los archivos se eliminan automáticamente, puedes limpiar manualmente el directorio `/Respaldos` si es necesario.

### Monitoreo

- Revisa los logs de PHP para errores durante la generación.
- Verifica que los respaldos se descarguen correctamente.
- Monitorea el uso de espacio en disco.

## Soporte

Si encuentras problemas:

1. Revisa esta documentación completa.
2. Verifica los logs de error de PHP.
3. Confirma la configuración de Laragon y MySQL.
4. Asegúrate de que todos los permisos estén correctos.

## Historial de Cambios

- **v1.0**: Implementación inicial del módulo de respaldos.
- Funcionalidad básica de generación y descarga.
- Control de acceso para administradores.
- Integración con arquitectura MVC existente.

---

**Nota**: Este módulo está diseñado específicamente para el sistema ISKALLI y puede requerir ajustes para otros proyectos.
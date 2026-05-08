# Módulo de Beneficiarios

## Descripción General

El módulo de Beneficiarios gestiona el registro y administración de personas físicas y morales que reciben apoyo del programa Juventud ISKALI. El sistema soporta tanto individuos como organizaciones mediante una estructura de herencia SQL similar al módulo de donadores.

## Estructura del Módulo

### Archivos Principales

#### Modelo (`models/BeneficiarioModel.php`)
- **consultar()**: Obtiene todos los beneficiarios con información de comunidad y usuario registrador
- **consultarPorId(int $id)**: Obtiene un beneficiario específico por ID
- **obtenerTotal()**: Cuenta total de beneficiarios
- **obtenerTotalActivos()**: Cuenta beneficiarios activos
- **consultarComunidades()**: Lista todas las comunidades disponibles
- **insertar()**: Crea un nuevo beneficiario
- **actualizar()**: Modifica un beneficiario existente
- **eliminar()**: Elimina un beneficiario y sus dependencias

#### Controlador (`controllers/BeneficiariosController.php`)
- **index()**: Punto de entrada principal que maneja todas las acciones
- **listar()**: Muestra la lista de beneficiarios
- **crear()**: Procesa creación de beneficiario
- **formularioNuevo()**: Muestra formulario de creación
- **formularioEditar()**: Muestra formulario de edición
- **actualizar()**: Procesa actualización
- **eliminar()**: Procesa eliminación

#### Vistas
- **`views/pages/beneficiarios.php`**: Lista de beneficiarios con tabla y búsqueda
- **`views/pages/beneficiario_form.php`**: Formulario para crear/editar beneficiarios

### Base de Datos

#### Tabla `beneficiarios` (Datos comunes)
```sql
CREATE TABLE beneficiarios (
    id_beneficiario        INT AUTO_INCREMENT PRIMARY KEY,
    tipo_persona           ENUM('fisica','moral') NOT NULL DEFAULT 'fisica',
    id_comunidad           INT,
    direccion              VARCHAR(255),
    telefono               VARCHAR(20),
    estado                 ENUM('activo','inactivo','en_espera') DEFAULT 'activo',
    notas                  TEXT,
    id_usuario_registrador INT NOT NULL,
    created_at             DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at             DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### Tabla `beneficiarios_fisicos` (Datos específicos de personas físicas)
```sql
CREATE TABLE beneficiarios_fisicos (
    id_beneficiario  INT PRIMARY KEY,
    nombre           VARCHAR(100) NOT NULL,
    apellido         VARCHAR(100) NOT NULL,
    edad             TINYINT UNSIGNED,
    curp             VARCHAR(18),
    fecha_nacimiento DATE,
    FOREIGN KEY (id_beneficiario) REFERENCES beneficiarios(id_beneficiario) ON DELETE CASCADE
);
```

#### Tabla `beneficiarios_morales` (Datos específicos de personas morales)
```sql
CREATE TABLE beneficiarios_morales (
    id_beneficiario INT PRIMARY KEY,
    razon_social    VARCHAR(150) NOT NULL,
    rfc             VARCHAR(13),
    FOREIGN KEY (id_beneficiario) REFERENCES beneficiarios(id_beneficiario) ON DELETE CASCADE
);
```
```

#### Tabla `beneficiario_tipos_apoyo`
Relaciona beneficiarios con tipos de apoyo disponibles.

## Funcionalidades

### Gestión CRUD
- ✅ Crear beneficiarios
- ✅ Leer/listar beneficiarios
- ✅ Actualizar beneficiarios
- ✅ Eliminar beneficiarios

### Características
- Búsqueda en tiempo real en la tabla
- Estados: Activo, En espera, Inactivo
- Asociación con comunidades
- Registro de usuario que crea/edita
- Notas adicionales
- Eliminación en cascada de dependencias

## Problema Actual: Tipos de Persona

### Situación Actual
El módulo actual trata a todos los beneficiarios como **personas físicas individuales**, sin distinción entre:
- Personas físicas (individuos)
- Personas morales (organizaciones, empresas, instituciones)

### Comparación con Módulo de Donadores

El módulo de donadores implementa correctamente la distinción de tipos de persona usando **herencia SQL**:

#### Estructura de Donadores
- **`donadores`**: Datos comunes (email, teléfono, puntos, estado)
- **`donadores_fisicos`**: Datos específicos de personas físicas (nombre, apellido, CURP, fecha nacimiento)
- **`donadores_morales`**: Datos específicos de personas morales (razón social, RFC, representante legal, giro comercial)

#### Ventajas de la Estructura de Donadores
- ✅ Flexibilidad para diferentes tipos de entidades
- ✅ Campos específicos por tipo de persona
- ✅ Validaciones apropiadas por tipo
- ✅ Mejor organización de datos
- ✅ Escalabilidad para nuevos tipos

## Solución Propuesta

Implementar una estructura similar a la de donadores para beneficiarios:

### Nueva Estructura de Base de Datos
```sql
-- Datos comunes a todos los beneficiarios
CREATE TABLE beneficiarios (
    id_beneficiario        INT AUTO_INCREMENT PRIMARY KEY,
    tipo_persona           ENUM('fisica','moral') NOT NULL,
    id_comunidad           INT,
    direccion              VARCHAR(255),
    telefono               VARCHAR(20),
    estado                 ENUM('activo','inactivo','en_espera') DEFAULT 'activo',
    notas                  TEXT,
    id_usuario_registrador INT NOT NULL,
    created_at             DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at             DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Datos específicos de personas físicas
CREATE TABLE beneficiarios_fisicos (
    id_beneficiario  INT PRIMARY KEY,
    nombre           VARCHAR(100) NOT NULL,
    apellido         VARCHAR(100) NOT NULL,
    edad             TINYINT UNSIGNED,
    curp             VARCHAR(18),
    fecha_nacimiento DATE
);

-- Datos específicos de personas morales
CREATE TABLE beneficiarios_morales (
    id_beneficiario     INT PRIMARY KEY,
    razon_social        VARCHAR(150) NOT NULL,
    rfc                 VARCHAR(13),
    representante_legal VARCHAR(200),
    giro_comercial      VARCHAR(100)
);
```

### Cambios Necesarios

#### 1. Modelo (`BeneficiarioModel.php`)
- Actualizar consultas para usar JOIN con tablas específicas
- Modificar métodos `insertar()` y `actualizar()` para manejar tipos de persona
- Agregar método para determinar tipo de persona

#### 2. Controlador (`BeneficiariosController.php`)
- Agregar validación de `tipo_persona`
- Modificar lógica de creación/edición para campos específicos por tipo

#### 3. Vista (`beneficiario_form.php`)
- Agregar selector de tipo de persona
- Campos dinámicos que cambian según el tipo seleccionado
- JavaScript para mostrar/ocultar campos apropiados

#### 4. Vista (`beneficiarios.php`)
- Mostrar columna "Tipo" en la tabla
- Adaptar nombre completo según tipo de persona

## Implementación Paso a Paso

### Paso 1: Actualizar Base de Datos
1. Crear nuevas tablas `beneficiarios_fisicos` y `beneficiarios_morales`
2. Migrar datos existentes a la nueva estructura
3. Actualizar tabla `beneficiarios` con columna `tipo_persona`

### Paso 2: Actualizar Modelo
1. Modificar consultas para usar JOIN
2. Actualizar métodos CRUD para manejar herencia

### Paso 3: Actualizar Controlador
1. Agregar validación de tipo de persona
2. Modificar lógica de formularios

### Paso 4: Actualizar Vistas
1. Agregar selector de tipo en formulario
2. Implementar campos dinámicos con JavaScript

### Paso 5: Pruebas
1. Verificar migración de datos
2. Probar creación de ambos tipos de beneficiarios
3. Validar listados y búsquedas

## Beneficios de la Solución

- ✅ **Flexibilidad**: Soporte para individuos y organizaciones
- ✅ **Consistencia**: Estructura similar al módulo de donadores
- ✅ **Escalabilidad**: Fácil agregar nuevos tipos de persona
- ✅ **Integridad**: Mejor organización de datos
- ✅ **Mantenibilidad**: Código más limpio y estructurado

## Consideraciones Adicionales

- **Migración de Datos**: Los beneficiarios existentes deben clasificarse como "fisica"
- **Validaciones**: Campos requeridos diferentes por tipo
- **UI/UX**: Interfaz intuitiva para selección de tipo
- **Reportes**: Adaptar consultas de reportes a nueva estructura

## Cambios Recientes (May 8, 2026)

### Optimización de Campos para Personas Morales
Se simplificó la estructura de `beneficiarios_morales` removiendo campos innecesarios:

#### Campos Eliminados
- ❌ `representante_legal` (VARCHAR(200))
- ❌ `giro_comercial` (VARCHAR(100))

#### Campos Mantenidos
- ✅ `id_beneficiario` (PRIMARY KEY)
- ✅ `razon_social` (VARCHAR(150) NOT NULL)
- ✅ `rfc` (VARCHAR(13))

#### Archivos Actualizados
1. **`migracion_beneficiarios_tipos_persona.sql`**: Actualizada definición de tabla
2. **`migracion_quitar_campos_beneficiarios.sql`**: Nueva migración para eliminar campos
3. **`models/BeneficiarioModel.php`**: Removidos campos de consultas y métodos
4. **`controllers/BeneficiariosController.php`**: Removidos campos de procesamiento
5. **`views/pages/beneficiario_form.php`**: Removidos campos del formulario

#### Justificación
- **Simplicidad**: Los campos `representante_legal` y `giro_comercial` no son esenciales para la operación básica
- **Consistencia**: Alineación con requerimientos mínimos para personas morales
- **Mantenibilidad**: Menos campos = menos complejidad en validaciones y mantenimiento</content>
<parameter name="filePath">c:\laragon\www\juventudiskali\README_BENEFICIARIOS.md
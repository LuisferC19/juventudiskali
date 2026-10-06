# Juventud Iskali

Plataforma de gestión para la Fundación Juventud Iskali, diseñada para administrar campañas, donadores, beneficiarios, voluntariado, reportes y respaldos de base de datos.

## Requisitos

- PHP 8.2 o superior
- MySQL 8.x
- Extensión PDO habilitada
- Laragon, XAMPP, WAMP o un servidor local equivalente

## Instalación

1. Clona este repositorio en tu servidor local.
2. Crea un archivo `env.php` a partir de `env.example.php` y ajusta los valores de conexión.
3. Importa la base de datos ubicada en `base_datos/juventud_iskali_base_datos.sql` en MySQL.
4. Configura `BASE_URL` en `config/app.php` para que coincida con la ruta del proyecto en tu entorno local.
5. Inicia el proyecto desde tu servidor local y accede por la URL configurada.

## Estructura principal

- `controllers/` — controladores MVC
- `models/` — lógica de acceso a datos
- `views/` — plantillas y páginas
- `config/` — configuración de la app y conexión a la base de datos
- `public/` — archivos estáticos CSS, JS e imágenes
- `base_datos/` — scripts SQL del proyecto

## Variables de entorno

El proyecto espera un archivo `env.php` con la configuración de la base de datos. La estructura base es:

```php
<?php
return [
    'DB_HOST' => 'localhost',
    'DB_NAME' => 'iskali',
    'DB_USER' => 'root',
    'DB_PASS' => '',
    'DB_CHARSET' => 'utf8mb4',
];
?>
```

## Uso

- Accede a la aplicación desde la ruta base de tu entorno.
- Inicia sesión con una cuenta administradora o crea una solicitud desde el registro.
- Consulta, edita y administra módulos del sistema según el rol asignado.

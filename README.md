# Portfolio - Jhosting

Portafolio personal con proyectos desarrollados en PHP.

## Estructura

```
.
├── index.php                  # Página principal del portfolio
├── assets/                    # CSS, JS, imágenes y CV
└── proyectos/
    ├── sistema-academico/     # Sistema de gestión académica (PHP + MySQL, patrón MVC)
    ├── sistema-gestion/       # Sistema de gestión de clientes y proyectos (PHP + MySQL, con reportes PDF via FPDF)
    └── tienda/                # Tienda en línea (PHP + MySQL)
```

## Configuración

Cada proyecto que usa base de datos incluye un archivo `config.env.example`
dentro de su carpeta `config/`. Antes de ejecutar el proyecto localmente:

1. Copia el archivo de ejemplo:
   ```bash
   cp proyectos/sistema-academico/config/config.env.example proyectos/sistema-academico/config/config.env
   cp proyectos/sistema-gestion/config/config.env.example proyectos/sistema-gestion/config/config.env
   ```
2. Edita `config.env` con tus propias credenciales de base de datos.
3. Carga esas variables como variables de entorno de tu servidor (por ejemplo,
   con Apache/`SetEnv`, un `.htaccess`, o tu propio bootstrap de PHP), o
   adapta `getenv()` según tu entorno de despliegue.

**Importante:** nunca subas archivos `config.env` reales ni credenciales
en texto plano al repositorio. El `.gitignore` ya excluye estos archivos.

## Tecnologías

- PHP (procedural y MVC)
- MySQL / MySQLi / PDO
- FPDF (generación de reportes PDF)
- HTML, CSS, JavaScript

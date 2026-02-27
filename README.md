# IQGYM — Sistema de Gestión de Gimnasios

**IQGYM** es una aplicación web desarrollada en PHP para la gestión integral de gimnasios. Permite administrar clientes, planes, pagos, agenda de clases y generar reportes desde un panel centralizado.

---

## Tabla de Contenidos

- [Características](#características)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Uso](#uso)
- [Módulos](#módulos)
- [Contribución](#contribución)
- [Licencia](#licencia)

---

## Características

- Gestión completa de clientes y usuarios
- Agenda de clases y actividades
- Control de pagos y facturación
- Administración de planes y membresías
- Reportes y estadísticas
- Sistema de autenticación y control de acceso
- Interfaz web responsive

---

## Estructura del Proyecto

```
IQGYM/
├── agenda/          # Módulo de agenda y clases
├── assets/          # Recursos estáticos (CSS, imágenes, JS)
├── client/          # Gestión de clientes
├── config/          # Configuración de base de datos y entorno
├── includes/        # Archivos reutilizables (header, footer, etc.)
├── pagos/           # Módulo de pagos y facturación
├── planes/          # Gestión de planes y membresías
├── reportes/        # Generación de reportes
├── usuarios/        # Gestión de usuarios y roles
├── dashboard.php    # Panel de control principal
├── index.php        # Página de inicio / login
└── logout.php       # Cierre de sesión
```

---

## Requisitos

- PHP >= 7.4
- MySQL >= 5.7 o MariaDB
- Servidor web: Apache o Nginx
- Extensiones PHP: `pdo`, `pdo_mysql`, `mysqli`, `session`

> Recomendado: usar [XAMPP](https://www.apachefriends.org/) o [WAMP](https://www.wampserver.com/) para desarrollo local.

---

## Instalación

1. **Clona el repositorio:**

```bash
git clone https://github.com/gsicarius/IQGYM.git
cd IQGYM
```

2. **Copia los archivos al directorio raíz de tu servidor:**

```bash
# En XAMPP:
cp -r IQGYM/ /xampp/htdocs/IQGYM
```

3. **Crea la base de datos:**

Importa el archivo SQL incluido (si existe) desde phpMyAdmin o por terminal:

```bash
mysql -u root -p < database.sql
```

4. **Configura la conexión a la base de datos:**

Edita el archivo `config/` con tus credenciales:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_contraseña');
define('DB_NAME', 'iqgym');
```

5. **Accede desde el navegador:**

```
http://localhost/IQGYM/
```

---

## Uso

Una vez instalado, accede con tus credenciales de administrador. Desde el **dashboard** podrás navegar a cada módulo del sistema para registrar y administrar clientes, crear y asignar planes de membresía, registrar pagos, gestionar la agenda de clases y consultar reportes de actividad.

---

## Módulos

| Módulo | Descripción |
|--------|-------------|
| `client/` | Alta, baja y modificación de clientes |
| `agenda/` | Programación de clases y actividades |
| `pagos/` | Registro y seguimiento de pagos |
| `planes/` | Creación y asignación de planes/membresías |
| `reportes/` | Reportes de ingresos, clientes y actividad |
| `usuarios/` | Gestión de usuarios con roles y permisos |

---

## Contribución

Las contribuciones son bienvenidas. Si deseas mejorar el proyecto:

1. Haz un fork del repositorio
2. Crea una rama: `git checkout -b feature/nueva-funcionalidad`
3. Realiza tus cambios y haz commit: `git commit -m "Agrega nueva funcionalidad"`
4. Sube los cambios: `git push origin feature/nueva-funcionalidad`
5. Abre un Pull Request


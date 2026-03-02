# IQGYM — Gym Management System

IQGYM is a modular web-based gym management system built with PHP and MySQL. It centralizes client administration, membership management, payment processing, scheduling, and reporting within a secure administrative dashboard.

The system is structured with modular separation of concerns and role-based access control, making it suitable for small-to-medium fitness centers.

---

## Core Capabilities

- **Client lifecycle management** — create, update, deactivate
- **Membership and plan administration**
- **Payment tracking and revenue monitoring**
- **Class and activity scheduling**
- **Role-based authentication and authorization**
- **Administrative dashboard with operational metrics**
- **Responsive user interface**

---

## Architecture Overview

IQGYM follows a modular directory-based architecture to improve maintainability and scalability:

```
IQGYM/
├── agenda/          # Scheduling and class management module
├── assets/          # Static assets (CSS, JS, images)
├── client/          # Client management logic and views
├── config/          # Environment and database configuration
├── includes/        # Shared layout components
├── pagos/           # Payment and billing processing
├── planes/          # Membership and plan management
├── reportes/        # Reporting and data aggregation
├── usuarios/        # User management and access control
├── dashboard.php    # Central administrative panel
├── index.php        # Authentication entry point
└── logout.php       # Session termination
```

### Architectural Principles

- Modular feature isolation
- Clear separation between configuration, business logic, and presentation
- Session-based authentication
- PDO-based database interaction
- Structured server-side validation

---

## Technology Stack

| Layer | Technology |
|---|---|
| Backend | PHP 7.4+ |
| Database | MySQL 5.7+ or MariaDB |
| Web Server | Apache or Nginx |
| PHP Extensions | PDO, PDO_MySQL, MySQLi, Sessions |
| Frontend | HTML5, CSS3, JavaScript |

**Recommended local development environments:** XAMPP, WAMP

---

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/gsicarius/IQGYM.git
cd IQGYM
```

### 2. Deploy to your local server

```bash
# Example for XAMPP
cp -r IQGYM/ /xampp/htdocs/IQGYM
```

### 3. Database Setup

Import the provided SQL schema (if available):

```bash
mysql -u root -p < database.sql
```

### 4. Configure Database Credentials

Update the configuration file inside `/config/`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_user');
define('DB_PASS', 'your_password');
define('DB_NAME', 'iqgym');
```

### 5. Access the Application

```
http://localhost/IQGYM/
```

---

## System Modules

| Module | Responsibility |
|---|---|
| **Client** | Full client lifecycle management |
| **Agenda** | Class and activity scheduling |
| **Payments** | Payment registration and revenue tracking |
| **Plans** | Membership configuration and assignment |
| **Reports** | Income, activity, and client analytics |
| **Users** | Role-based user management |

---

## Security Considerations

- Session-based authentication
- Role-restricted route access
- PDO-based database abstraction
- Server-side input validation

---

## Project Objective

IQGYM was developed to simulate a real-world administrative system with modular organization, relational database integration, and access control mechanisms. The goal is to reflect practical backend development principles, system organization, and production-oriented structure.

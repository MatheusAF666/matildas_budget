# 💰 Matilda's Budget - Sistema de Gestión de Presupuestos

Sistema completo de gestión de presupuestos construido con Laravel + Vue.js + Inertia.js.

## 🚀 Características

- ✅ Gestión de clientes
- ✅ Creación de presupuestos
- ✅ Generación de PDFs
- ✅ Envío de presupuestos por email
- ✅ Autenticación de usuarios
- ✅ Panel de control intuitivo

## 🛠️ Tecnologías

- **Backend:** Laravel 12
- **Frontend:** Vue.js 3 + TypeScript
- **UI:** Tailwind CSS
- **Base de Datos:** MySQL/PostgreSQL
- **PDF:** DomPDF

## 📦 Instalación Local

```bash
# Clonar el repositorio
git clone https://github.com/TU-USUARIO/matildas-budget.git
cd matildas-budget

# Instalar dependencias
composer install
npm install

# Configurar entorno
cp .env.example .env
php artisan key:generate

# Configurar base de datos en .env
# Luego ejecutar migraciones
php artisan migrate

# Compilar assets
npm run dev

# Iniciar servidor
php artisan serve
```

## 🌐 Despliegue en Render

Lee la [Guía de Despliegue](DEPLOYMENT_GUIDE.md) completa para instrucciones detalladas.

**Pasos rápidos:**

1. Sube tu código a GitHub
2. Crea una cuenta en [Render.com](https://render.com)
3. Crea una base de datos PostgreSQL
4. Crea un Web Service conectado a tu repositorio
5. Configura las variables de entorno
6. ¡Despliega!

## 📝 Licencia

MIT License

## 👤 Autor

Matilda's Budget System

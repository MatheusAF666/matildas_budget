# 🚀 Guía de Despliegue en Render.com

## 📋 Prerequisitos

1. **Cuenta en GitHub** (para conectar el repositorio)
2. **Cuenta en Render.com** (regístrate gratis en [render.com](https://render.com))
3. **Git instalado** en tu computadora

---

## 🔧 Paso 1: Preparar el Proyecto

### 1.1 Crear repositorio en GitHub

```bash
# En tu terminal, dentro del proyecto:
git init
git add .
git commit -m "Initial commit - Matilda's Budget"
```

Luego:
1. Ve a [GitHub.com](https://github.com) y crea un nuevo repositorio
2. Llámalo `matildas-budget`
3. Sigue las instrucciones para subir tu código:

```bash
git remote add origin https://github.com/TU-USUARIO/matildas-budget.git
git branch -M main
git push -u origin main
```

### 1.2 Generar APP_KEY

```bash
php artisan key:generate --show
```

Copia la clave generada, la necesitarás más adelante.

---

## 🎨 Paso 2: Configurar Render

## 🎨 Paso 2: Configurar Render

### 2.1 Crear Base de Datos PostgreSQL

1. Ve a [Render Dashboard](https://dashboard.render.com)
2. Click en **"New +"** → **"PostgreSQL"**
3. Configura:
   - **Name:** `matildas-budget-db`
   - **Database:** `matildas_budget`
   - **User:** `matildas_user`
   - **Region:** Frankfurt (o el más cercano)
   - **Plan:** **Free**
4. Click en **"Create Database"**
5. Espera a que se cree (1-2 minutos)

### 2.2 Crear Web Service

1. Click en **"New +"** → **"Web Service"**
2. Conecta tu repositorio de GitHub
3. Configura:
   - **Name:** `matildas-budget`
   - **Region:** Frankfurt (mismo que la DB)
   - **Branch:** `main`
   - **Runtime:** **Docker**
   - **Plan:** **Free**

### 2.3 Configurar Variables de Entorno

En tu Web Service, ve a **"Environment"** y agrega:

```env
APP_NAME=Matilda's Budget
APP_ENV=production
APP_KEY=base64:TU-CLAVE-GENERADA-AQUI
APP_DEBUG=false
APP_URL=https://matildas-budget.onrender.com

LOG_LEVEL=error
LOG_CHANNEL=stack

SESSION_DRIVER=database
SESSION_LIFETIME=120

DB_CONNECTION=pgsql

# Email (usa tu configuración de Gmail)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-email@gmail.com
MAIL_PASSWORD=tu-app-password-de-gmail
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu-email@gmail.com
MAIL_FROM_NAME=Matilda's Budget
```

### 2.4 Conectar Base de Datos

1. En la sección de variables de entorno, busca **"Add from Database"**
2. Selecciona tu base de datos `matildas-budget-db`
3. Click en **"Add connection"**
4. Render agregará automáticamente la variable `DATABASE_URL`

### 2.5 Configurar Health Check (Opcional pero recomendado)

1. En **"Settings"** del Web Service
2. En **"Health Check Path"** pon: `/`

---

## 🎯 Paso 3: Desplegar

1. Click en **"Manual Deploy"** → **"Deploy latest commit"**
2. El despliegue tomará 5-10 minutos la primera vez
3. Puedes ver los logs en tiempo real

### 3.1 Verificar el despliegue

1. Espera a que el estado sea **"Live"**
2. Click en el URL de tu servicio (ej: `https://matildas-budget.onrender.com`)
3. ¡Tu aplicación está en vivo!

---

## 🔄 Actualizaciones Futuras

Para actualizar tu aplicación:

```bash
# Haz cambios en tu código
git add .
git commit -m "Descripción de los cambios"
git push
```

Render desplegará automáticamente los cambios (toma 5-10 minutos).

---

## 🐛 Solución de Problemas

### Error: "Build failed"

1. Ve a los logs del build en Render
2. Verifica que todos los archivos estén en el repositorio
3. Asegúrate de que `build.sh` tenga permisos de ejecución

### Error: "500 Internal Server Error"

1. Ve a los logs en Render
2. Verifica que todas las variables de entorno estén configuradas
3. Asegúrate de que la `APP_KEY` esté configurada
4. Verifica que `DATABASE_URL` esté presente

### Error: "Database connection failed"

1. Verifica que la base de datos esté en estado "Available"
2. Asegúrate de haber agregado la conexión desde "Add from Database"
3. Verifica que `DB_CONNECTION=pgsql` esté configurado

### El servicio se duerme (Free tier)

- Los servicios gratuitos se duermen tras 15 minutos sin actividad
- El primer acceso después puede tomar 30-60 segundos
- Esto es normal en el tier gratuito

---

## 💰 Plan Gratuito de Render

**Incluye:**
- ✅ 750 horas de servicio/mes (suficiente para 1 app corriendo 24/7)
- ✅ Base de datos PostgreSQL (90 días de retención de datos)
- ✅ SSL automático
- ✅ Despliegues automáticos desde GitHub
- ⚠️ El servicio se duerme tras 15 min sin uso
- ⚠️ Datos de DB se eliminan después de 90 días (necesitas hacer upgrade para persistencia)

---

## 🎉 ¡Listo!

Tu aplicación está ahora en producción y accesible desde cualquier lugar.

**URL de ejemplo:** https://matildas-budget.onrender.com

---

## 📝 Notas Importantes

- ✅ SSL está incluido automáticamente
- ✅ Los logs están disponibles en tiempo real
- ✅ Despliegues automáticos desde GitHub
- ⚠️ El servicio gratuito se duerme tras inactividad (primer acceso toma ~1 min)
- ⚠️ Base de datos gratuita: datos persisten 90 días
- 🔄 Para producción seria, considera el plan de pago ($7/mes) para BD persistente

---

## 📞 Soporte

Si tienes problemas:
1. Revisa los logs en Render Dashboard
2. Consulta la [documentación de Render](https://render.com/docs)
3. Revisa este archivo nuevamente

---

## 🔧 Cambios Realizados para PostgreSQL

Tu aplicación ahora usa PostgreSQL en lugar de MySQL. Los cambios son transparentes:

- ✅ Laravel soporta PostgreSQL nativamente
- ✅ Todas las migraciones funcionan igual
- ✅ No necesitas cambiar código de la aplicación
- ✅ Solo cambia la configuración de conexión

# Operaciones — SIMCO en producción

Guía única para preparar, desplegar, operar y diagnosticar SIMCO en producción.
Sustituye a los antiguos `DEPLOYMENT.md`, `DEPLOYMENT-CPANEL.md`, `QUICK-START-PRODUCTION.md`,
`RESUMEN-PRODUCCION.md`, `MAPA-PROYECTO.md` y `COMANDOS-UTILES.md`.

- Para **instalar en local** y entender los formularios: [README.md](README.md).
- Para **entender el código** (arquitectura, rutas, modelo de datos, trampas): [CLAUDE.md](CLAUDE.md).
- Para **desplegar en Render con Docker** en lugar de cPanel: sección 12 de este documento.

> **Convención de rutas.** Todos los comandos locales se ejecutan **desde la raíz del repo**, con
> rutas relativas. En el servidor, `<cpaneluser>` es tu usuario de cPanel y `tudominio.com` tu
> dominio: sustitúyelos.

---

## 1. Requisitos del hosting

Verifica esto **antes de contratar o desplegar**. El punto crítico es PostgreSQL: muchos hostings
compartidos solo ofrecen MySQL.

| Requisito | Mínimo | Nota |
|---|---|---|
| PHP | 8.2 | El código usa `readonly` properties y tipos estrictos |
| Base de datos | **PostgreSQL 12+** | Obligatorio, ver aviso abajo |
| Extensiones PHP | `pdo`, `pdo_pgsql`, `pgsql`, `mbstring`, `curl`, `gd`, `xml`, `zip`, `fileinfo` | |
| Upload máximo | 25 MB por archivo, 30 MB por POST | Límite de adjuntos de la app |
| SSL | Let's Encrypt / AutoSSL gratis | |
| Espacio | 1 GB inicial | Crece con los adjuntos en `Anexos/` |
| Acceso SSH | Opcional pero muy recomendable | Sin SSH, importar la BD es tedioso |

### MySQL no está soportado

No basta con cambiar el DSN. El código depende de sintaxis exclusiva de PostgreSQL:

- [`PqrsfModel::create()`](app/Models/PqrsfModel.php#L31) obtiene el ID con
  `SELECT NEXTVAL('formulario_pqr_formulario_pqr_id_seq')`.
- [`SatisfactionModel`](app/Models/SatisfactionModel.php#L30) concatena con `||`, que en MySQL es
  el operador lógico OR.

Migrar a MySQL exige reescribir los modelos. Si el hosting no ofrece PostgreSQL, descártalo.

### Preguntas para el soporte del hosting

```
1. ¿Soportan PHP 8.2 o superior?
2. ¿Ofrecen PostgreSQL 12 o superior? (si la respuesta es no, el hosting no sirve)
3. ¿Está disponible la extensión pdo_pgsql?
4. ¿Hay acceso SSH?
5. ¿Cuál es el límite de upload_max_filesize y post_max_size, y puedo modificarlo?
6. ¿Incluyen SSL gratuito (AutoSSL / Let's Encrypt)?
7. ¿Hacen backups automáticos y con qué retención?
```

---

## 2. Preparar el release (en tu equipo, Windows)

### Vía recomendada: script automático

Desde la raíz del repo, en PowerShell:

```powershell
.\prepare-production.ps1
```

**El script no modifica el árbol de trabajo.** Construye el paquete en un directorio aparte, así
que puedes seguir desarrollando inmediatamente después: `vendor/` conserva las dependencias de
desarrollo, `node_modules/` se queda donde está y tu `output.css` sin minificar no se toca.

Qué hace:

1. Verifica que existan los archivos necesarios.
2. Crea (o limpia) `build/simco-produccion/`.
3. Copia `app/`, `config/`, `public_html/`, `resources/`, `routes/`, `composer.json`,
   `composer.lock`, `.htaccess` y `.env.example`.
4. Instala `vendor/` **dentro del paquete** con `composer install --no-dev --optimize-autoloader
   --working-dir=…`.
5. Compila el CSS minificado **dentro del paquete**.
6. Vacía `public_html/Anexos/` en el paquete: los adjuntos de usuarios se quedan en el servidor.
7. Genera `build/DEPLOYMENT-CHECKLIST.txt` (fuera del paquete, es para ti) y
   `build/simco-produccion.zip`.

Opciones: `-SinZip` para omitir el ZIP, `-Destino <ruta>` para cambiar el directorio de salida.
En Linux/macOS el equivalente es `./prepare-production.sh [--sin-zip] [--destino RUTA]`.

Todo `build/` está en `.gitignore`: se regenera con cada release.

### Qué contiene el paquete

| Incluido | Excluido a propósito |
|---|---|
| `app/`, `config/`, `resources/`, `routes/` | **`.env`** — se crea en el servidor, nunca se sube |
| `public_html/` con el `output.css` minificado | `node_modules/` — no se necesita en producción |
| `vendor/` sin dependencias de desarrollo | `.git/`, `*.md`, `build/` |
| `composer.json`, `composer.lock`, `.htaccess` | `prepare-production.*`, `verify-setup.ps1` |
| `.env.example` como plantilla | `public_html/Anexos/` con contenido |

> **El `.env` nunca viaja en el paquete.** Contiene credenciales de BD y SMTP; un ZIP se copia, se
> reenvía y a veces queda accesible por web. Se crea directamente en el servidor (paso 6).

Son unos 228 archivos y el ZIP ronda los 10 MB.

### Vía manual (si el script falla)

Sustituye `<destino>` por una carpeta vacía fuera del repo:

```powershell
composer install --no-dev --optimize-autoloader --working-dir="<destino>"
npx @tailwindcss/cli -i ./resources/css/input.css -o "<destino>/public_html/assets/css/output.css" --minify
```

> Usa siempre `npx @tailwindcss/cli`. El comando `npx tailwindcss` es de Tailwind v3 y **no
> funciona** en este proyecto: [package.json](package.json) instala `@tailwindcss/cli` v4.
>
> Y no compiles sobre `./public_html/assets/css/output.css`: ese es el de desarrollo, está
> versionado y te quedaría un diff en git con el CSS minificado.

---

## 3. Configurar cPanel

### Versión de PHP

**Select PHP Version** (o *MultiPHP Manager*) → selecciona tu dominio → **PHP 8.2** o superior →
*Apply*.

### Extensiones

En **Select PHP Version → Extensions**, activa:

`curl`, `fileinfo`, `gd`, `mbstring`, `pdo`, `pdo_pgsql`, `pgsql`, `xml`, `zip`

### php.ini

En **MultiPHP INI Editor** (modo Editor):

```ini
upload_max_filesize = 25M
post_max_size = 30M
max_execution_time = 300
max_input_time = 300
memory_limit = 256M
display_errors = Off
log_errors = On
```

`upload_max_filesize` debe ser ≥ 25 MB porque ese es el límite total de adjuntos que valida la app
en [`InputValidator::validateFiles()`](app/Services/InputValidator.php) y en
[`form-validation.js`](public_html/assets/js/form-validation.js).

---

## 4. Conectar la base de datos

**No hay nada que importar.** La base de datos ya existe y la administra otro equipo; este
repositorio no incluye schema ni migraciones. Este paso consiste en dar acceso a la base existente
y verificar que la aplicación la alcanza.

### Credenciales

Si la base vive en el mismo hosting, cPanel → **PostgreSQL Databases** → *Add User To Database*
para asignar el usuario de la aplicación con los privilegios necesarios. cPanel antepone tu usuario
como prefijo, así que anota los nombres **completos**:

```
DB_HOST=localhost
DB_DATABASE=<cpaneluser>_simco_prod
DB_USERNAME=<cpaneluser>_simco_user
```

Si la base es externa (servidor de la clínica), pide al equipo que la administra el host, el puerto
y un usuario con permisos de **lectura y escritura** sobre las tablas del PQRSF y de **solo
lectura** sobre las del HIS.

### Verificar que las tablas son accesibles

La aplicación necesita estas 9 tablas y 1 secuencia. Ejecuta esta consulta con el **mismo usuario**
que pusiste en el `.env` — lista primero las que falten:

```sql
SELECT t.nombre, to_regclass(t.nombre) IS NOT NULL AS existe
FROM unnest(ARRAY[
  'formulario_pqr', 'formulario_pqr_anexos', 'formulario_pqr_seguimiento',
  'formulario_pqr_tipo_fuente', 'formulario_pqr_presento_evento',
  'formulario_pqr_tipo_enfoque_diferencial',
  'tipos_id_pacientes', 'planes', 'terceros'
]) AS t(nombre)
ORDER BY existe, t.nombre;

SELECT to_regclass('formulario_pqr_formulario_pqr_id_seq') IS NOT NULL AS secuencia_ok;
```

Qué es cada una y qué parte del sistema depende de ella: [CLAUDE.md](CLAUDE.md), sección «Modelo de
datos».

> Si falta alguna, la aplicación **no falla al arrancar**: el sitio carga y los desplegables salen
> vacíos, o el formulario revienta al enviar. Resuélvelo con el equipo que administra la base antes
> de continuar.

---

## 5. Subir archivos y configurar el document root

Sube el ZIP a `/home/<cpaneluser>/` con **File Manager** (*Upload*) o por SFTP, y extráelo en
`/home/<cpaneluser>/simco/`. Debe quedar así:

```
/home/<cpaneluser>/simco/
├── app/  config/  public_html/  resources/  routes/  vendor/
├── composer.json  composer.lock
└── .htaccess
```

Con SSH puedes clonar en lugar de subir un ZIP:

```bash
cd /home/<cpaneluser>
git clone http://gitlab.lan.cdo-sa.com/desarrollo-occidente/simco.git
cd simco && git checkout master
composer install --no-dev --optimize-autoloader
```

Ahora elige **una** de estas dos vías. Ninguna requiere editar código.

### Vía A — Subdominio con document root propio (recomendada)

cPanel → **Subdomains**:

- Subdomain: `simco`
- Domain: `tudominio.com`
- **Document Root: `/home/<cpaneluser>/simco/public_html`**

El sitio queda en `https://simco.tudominio.com`. `app/`, `config/`, `vendor/` y `.env` quedan
fuera de la raíz web, que es lo más seguro.

### Vía B — Proyecto dentro del document root (si no puedes fijar el doc root)

Sube el proyecto completo dentro de `/home/<cpaneluser>/public_html/`, de modo que quede
`/home/<cpaneluser>/public_html/public_html/index.php`.

El [`.htaccess`](.htaccess) de la raíz del repo ya resuelve esto: reescribe **toda** petición a
`public_html/`, con el efecto lateral de dejar `app/`, `config/` y `vendor/` inalcanzables desde
el navegador.

> **No edites `ROOT_PATH` en `public_html/index.php`.** Guías anteriores lo pedían; es innecesario
> y contraproducente: `dirname(__DIR__)` resuelve correctamente en ambas vías, y cualquier cambio
> manual se pierde en el siguiente `git pull` o redespliegue.

---

## 6. Crear el `.env` en el servidor

> ⚠️ **Solo en el primer despliegue.** Si el proyecto ya está en producción, el servidor ya tiene
> su `.env` con las credenciales reales: **no lo vuelvas a crear ni a subir**. Sobrescribirlo con
> el `.env` local tumba la conexión a la base de datos y el envío de correos. En las
> actualizaciones (paso 10) el `.env` del servidor se deja intacto.

En File Manager, dentro de la raíz del proyecto (`/home/<cpaneluser>/simco/`), crea un archivo
`.env` (*+ File*) y edítalo:

```bash
# BASE DE DATOS
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=<cpaneluser>_simco_prod
DB_USERNAME=<cpaneluser>_simco_user
DB_PASSWORD=la_contraseña_real

# APLICACIÓN
APP_ENV=production
APP_DEBUG=false

# CORREO
MAIL_MAILER=smtp
MAIL_HOST=mail.tudominio.com
MAIL_PORT=587
MAIL_USERNAME=noreply@tudominio.com
MAIL_PASSWORD=la_contraseña_del_buzón
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@tudominio.com
MAIL_FROM_NAME="SIMCO - Sistema de PQRSF"
```

Después, *Change Permissions* → **600** (solo lectura/escritura del propietario).

### Cuenta de correo

cPanel → **Email Accounts** → crea `noreply@tudominio.com`. En *Connect Devices* verás los datos
SMTP. Usa `mail.tudominio.com` (no `smtp.`) y el **correo completo** como usuario.

El envío se puede desactivar por completo poniendo `mail.enabled => false` en
[`config/app.php`](config/app.php), útil para probar sin mandar correos reales.

---

## 7. `.htaccess` y permisos

### `.htaccess` del document root

En la carpeta que sirve como document root (`public_html/` del proyecto en la vía A, o
`/home/<cpaneluser>/public_html/` en la vía B):

```apache
Options -Indexes

<IfModule mod_rewrite.c>
    RewriteEngine On

    # Forzar HTTPS — descomentar DESPUÉS de activar SSL (paso 8)
    # RewriteCond %{HTTPS} off
    # RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^ index.php [L]
</IfModule>

# Archivos y directorios sensibles
<FilesMatch "^\.env$">
    Require all denied
</FilesMatch>
<FilesMatch "^composer\.(json|lock)$">
    Require all denied
</FilesMatch>
RedirectMatch 403 ^/(vendor|app|config|routes)/

# Impedir ejecución de PHP en los adjuntos subidos por usuarios
<Directory "Anexos">
    php_flag engine off
    RemoveHandler .php .phtml .php3 .php4 .php5
    RemoveType .php .phtml .php3 .php4 .php5
</Directory>

<IfModule mod_headers.c>
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>

<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/css text/javascript text/plain
    AddOutputFilterByType DEFLATE application/javascript application/json image/svg+xml
</IfModule>

<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/x-icon "access plus 1 year"
    ExpiresByType text/html "access plus 0 seconds"
</IfModule>

php_flag display_errors Off
php_flag log_errors On
```

> El bloque `<Directory "Anexos">` es importante: los adjuntos los suben usuarios anónimos desde
> un formulario público.

### Permisos

| Ruta | Permiso |
|---|---|
| Directorios en general | `755` |
| Archivos en general | `644` |
| `public_html/Anexos/` (recursivo) | `775` |
| `.env` | `600` |

Con SSH, desde la raíz del proyecto:

```bash
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
chmod -R 775 public_html/Anexos
chmod 600 .env
```

Sin SSH: File Manager → clic derecho → *Change Permissions* (marca *Recurse into subdirectories*
para carpetas).

Si `prepare-production.ps1` no creó los directorios de adjuntos, créalos en el servidor:

```bash
mkdir -p public_html/Anexos/{2025,2026,2027}/{01,02,03,04,05,06,07,08,09,10,11,12}
chmod -R 775 public_html/Anexos
```

---

## 8. SSL

cPanel → **SSL/TLS Status** → marca el dominio y los subdominios → **Run AutoSSL**. Tarda 5–10
minutos. Verifica el candado en el navegador.

Con el certificado ya activo, descomenta el bloque *Forzar HTTPS* del `.htaccess` (paso 7).

Si el hosting tiene **ModSecurity**, actívalo para el dominio con las reglas por defecto.

---

## 9. Verificación post-despliegue

Recórrela entera la primera vez y tras cada actualización importante.

**Infraestructura**
- [ ] `https://simco.tudominio.com` carga con candado válido
- [ ] `http://` redirige a `https://`
- [ ] Los estilos se aplican (si no, falta `output.css`, ver Troubleshooting)
- [ ] Las imágenes y el favicon se ven
- [ ] Sin errores en la consola del navegador
- [ ] `https://simco.tudominio.com/.env` devuelve 403 o 404, **nunca** el contenido

**Formulario público (`/`)**
- [ ] Los desplegables traen datos (tipo de documento, solicitante, servicio, aseguradora)
- [ ] Los enfoques diferenciales aparecen y al marcar «Otro» se muestra el campo de texto
- [ ] Adjuntar archivos funciona (prueba con un PDF; el límite es 5 archivos / 25 MB)
- [ ] Enviar redirige a la página de éxito con el número de PQRSF
- [ ] El registro aparece en `formulario_pqr`
- [ ] El archivo se guardó en `public_html/Anexos/AAAA/MM/DD/`
- [ ] Llega el correo de confirmación al paciente

**Formulario de aseguradoras (`/aseguradoras`)**
- [ ] Carga y envía correctamente
- [ ] El correo de confirmación llega **al asesor**, no al paciente

**Encuesta de conformidad (`/satisfaccion`)**
- [ ] Con un `?id=` válido muestra los datos de la PQRSF
- [ ] Responder «No» exige el motivo (mínimo 10 caracteres)
- [ ] La respuesta queda en `formulario_pqr_seguimiento`
- [ ] Llega la notificación al equipo de servicio al cliente

**Transversal**
- [ ] El cambio de idioma ES/EN funciona en todas las páginas
- [ ] El modo claro/oscuro funciona y persiste
- [ ] La página de éxito se ve bien: `/pqrsf/success?test=1` (renderiza datos de prueba sin
      registrar nada)
- [ ] Una URL inexistente devuelve el 404 de la aplicación
- [ ] Sin errores en cPanel → *Errors* tras 24 horas

---

## 10. Operación

### Actualizar el proyecto

Con SSH:

```bash
cd /home/<cpaneluser>/simco
cp -r /home/<cpaneluser>/simco /home/<cpaneluser>/simco_backup   # respaldo previo
git pull origin master
composer install --no-dev --optimize-autoloader
```

Sin SSH: compila el CSS y ejecuta Composer **en local**, sube por FTP solo las carpetas que
cambiaron (normalmente `app/`, `resources/` y `public_html/assets/css/output.css`).

> El repositorio es `http://gitlab.lan.cdo-sa.com/desarrollo-occidente/simco`. Producción sale de
> **`master`**; el trabajo diario va en **`develop`**. No existe una rama `main`.

Si cambió el CSS, recompílalo y sube `public_html/assets/css/output.css`:

```powershell
npx @tailwindcss/cli -i ./resources/css/input.css -o ./public_html/assets/css/output.css --minify
```

### Backups

**Automáticos de cPanel:** cPanel → *Backup* → *Backup Wizard* → *Full Backup*, con notificación
por correo. Confirma la retención con tu proveedor.

**Backup programado de la BD:** cPanel → *Cron Jobs* → *Add New Cron Job*, frecuencia diaria a
las 2 AM:

```bash
pg_dump -h localhost -U <cpaneluser>_simco_prod_user <cpaneluser>_simco_prod | gzip > /home/<cpaneluser>/backups/db_$(date +\%Y\%m\%d).sql.gz
```

Crea antes `/home/<cpaneluser>/backups/`. Los `%` deben ir escapados como `\%` en cron.

**Los adjuntos también necesitan backup** — están en disco, no en la BD:

```bash
tar -czf /home/<cpaneluser>/backups/anexos_$(date +%Y%m%d).tar.gz /home/<cpaneluser>/simco/public_html/Anexos
```

**Purgar backups viejos:**

```bash
find /home/<cpaneluser>/backups -type f -mtime +30 -delete
```

### Restaurar y hacer rollback

```bash
# Restaurar la base de datos
gunzip -c /home/<cpaneluser>/backups/db_AAAAMMDD.sql.gz | psql -h localhost -U <cpaneluser>_simco_user -d <cpaneluser>_simco_prod

# Restaurar los adjuntos
tar -xzf /home/<cpaneluser>/backups/anexos_AAAAMMDD.tar.gz -C /

# Volver a la versión anterior del código
cd /home/<cpaneluser>/simco
git log --oneline -5          # identifica el commit bueno
git reset --hard <commit>
composer install --no-dev --optimize-autoloader
```

### Logs

| Qué | Dónde |
|---|---|
| Errores de PHP y de la aplicación | cPanel → **Errors**, o el archivo de `error_log` de tu `php.ini` |
| Accesos y errores del sitio | cPanel → **Raw Access Logs** / **Metrics** |
| Errores registrados por la app | La app usa `error_log()`; van al log de errores de PHP |

La aplicación **no** escribe logs propios en el proyecto: no busques una carpeta `storage/logs`.

### Consultas útiles (`psql` por SSH)

```sql
-- Tamaño de la base de datos
SELECT pg_size_pretty(pg_database_size(current_database()));

-- PQRSF registradas en las últimas 24 horas
SELECT COUNT(*) FROM formulario_pqr WHERE created_at > NOW() - INTERVAL '1 day';

-- Últimas 10 PQRSF
SELECT formulario_pqr_id, nombres, created_at
FROM formulario_pqr ORDER BY created_at DESC LIMIT 10;

-- PQRSF por tipo de solicitante
SELECT tf.descripcion, COUNT(*) AS total
FROM formulario_pqr p
LEFT JOIN formulario_pqr_tipo_fuente tf ON p.tipo_fuente_id = tf.tipo_fuente_id
GROUP BY tf.descripcion ORDER BY total DESC;

-- Conformidad de las respuestas (la app escribe en formulario_pqr_seguimiento)
SELECT conforme, COUNT(*) AS total
FROM formulario_pqr_seguimiento
WHERE fecha_respuesta_conformidad IS NOT NULL
GROUP BY conforme;
```

### Rutina de mantenimiento

- **Diario:** revisar cPanel → *Errors*; comprobar que el sitio carga.
- **Semanal:** verificar que los backups se generaron; revisar espacio en disco.
- **Mensual:** confirmar la vigencia del certificado SSL; revisar el crecimiento de
  `public_html/Anexos/`.

---

## 11. Troubleshooting

| Síntoma | Causa probable | Solución |
|---|---|---|
| **Error 500** | `.htaccess` con directivas no soportadas | Renombra `.htaccess` a `.htaccess.bak`; si carga, reintroduce los bloques de a uno |
| **Error 500** | Versión de PHP incorrecta | *Select PHP Version* → 8.2+ |
| **Error 500** | Falta `vendor/` o el autoloader | Vuelve a subir `vendor/`, o ejecuta `composer install --no-dev --optimize-autoloader` por SSH |
| **Pantalla en blanco** | Error fatal con `display_errors Off` | Mira cPanel → *Errors*. Como último recurso, activa `display_errors On` temporalmente y desactívalo enseguida |
| **Página sin estilos** | `output.css` no se compiló o no se subió | `npx @tailwindcss/cli ... --minify` en local y sube `public_html/assets/css/output.css` (permiso 644) |
| **Página sin estilos** | `.htaccess` bloquea los assets | Revisa que las reglas `RedirectMatch 403` no alcancen `/assets/` |
| **Página de error de BD (503)** | Credenciales incorrectas en `.env` | Recuerda el prefijo `<cpaneluser>_` en BD y usuario. Prueba: `psql -h localhost -U <cpaneluser>_simco_user -d <cpaneluser>_simco_prod` |
| **Página de error de BD (503)** | Falta `pdo_pgsql` | Actívala en *Select PHP Version → Extensions* |
| **Los desplegables salen vacíos** | Falta alguna tabla o catálogo, o el usuario no tiene permiso sobre ellos | Ejecuta la consulta de verificación del paso 4 y coordina lo que falte con quien administra la base |
| **No se suben archivos** | Permisos de `Anexos/` | `chmod -R 775 public_html/Anexos` |
| **No se suben archivos** | Límites de PHP por debajo de 25 MB | Ajusta `upload_max_filesize` y `post_max_size` en *MultiPHP INI Editor* |
| **No se suben archivos** | El directorio del día no existe y no se puede crear | Verifica permisos de escritura sobre `public_html/Anexos` |
| **No llegan correos** | Host SMTP incorrecto | Usa `mail.tudominio.com`, no `smtp.tudominio.com` |
| **No llegan correos** | Usuario incompleto | `MAIL_USERNAME` debe ser el correo completo |
| **No llegan correos** | Puerto/cifrado desalineados | 587 con `tls`, o 465 con `ssl` |
| **No llegan correos** | Límite de envíos por hora del hosting | Consulta con el proveedor |
| **404 en todas las rutas** | `mod_rewrite` inactivo o `.htaccess` ausente | Verifica el `.htaccess` del document root y que `AllowOverride` esté permitido |
| **Disco lleno** | Acumulación de adjuntos o backups | `du -sh /home/<cpaneluser>/*`; purga backups antiguos y archiva `Anexos/` de años cerrados |

Al escalar al soporte del hosting, adjunta: mensaje de error exacto, URL, hora, qué acción lo
provocó y el extracto de cPanel → *Errors*.

---

## 12. Alternativa: desplegar en Render (Docker)

Las secciones anteriores describen el despliegue en **cPanel**, que es el entorno de producción
actual. El repositorio incluye además una imagen Docker para desplegar en **Render**, como
alternativa. Elige una de las dos vías: no se despliega en ambas a la vez.

### Archivos

| Archivo | Para qué |
|---|---|
| [Dockerfile](Dockerfile) | Imagen multi-etapa: compila el CSS, resuelve `vendor/` y arma PHP 8.2 + Apache |
| [render.yaml](render.yaml) | Blueprint: define el servicio, el disco y las variables de entorno |
| [docker/entrypoint.sh](docker/entrypoint.sh) | Arranque: genera el `.env`, fija el puerto, prepara `Anexos/` |
| [docker/generate-env.php](docker/generate-env.php) | Escribe el `.env` desde las variables de entorno |
| [docker/vhost.conf](docker/vhost.conf) | VirtualHost de Apache |
| [docker/php-production.ini](docker/php-production.ini) | Límites de subida, OPcache, errores al log |
| [.dockerignore](.dockerignore) | Impide que el `.env`, los adjuntos y `vendor/` entren en la imagen |

### Puesta en marcha

1. En Render: **New → Blueprint**, apuntando al repositorio. Render lee `render.yaml`.
2. Rellena en el panel las variables marcadas como `sync: false`: `DB_HOST`, `DB_DATABASE`,
   `DB_USERNAME`, `DB_PASSWORD`, `MAIL_HOST`, `MAIL_USERNAME`, `MAIL_PASSWORD` y
   `MAIL_FROM_ADDRESS`.
3. Despliega. La primera construcción tarda unos minutos.

**Aquí no se sube ningún `.env`.** La configuración vive en las variables de entorno de Render y
`entrypoint.sh` genera el `.env` en cada arranque, dentro del contenedor y fuera del document root.
Hace falta porque `index.php` usa `Dotenv::createImmutable(...)->load()`, que lanza una excepción
si el archivo no existe.

Si usaras el PostgreSQL gestionado de Render bastaría con `DATABASE_URL`: `generate-env.php` la
descompone con `parse_url()`. Pero la base de SIMCO es la de la clínica, así que normalmente
usarás las `DB_*` por separado.

### Dos cosas que hay que tener presentes

**El disco de los adjuntos.** Sin el bloque `disk` de `render.yaml` el sistema de archivos es
efímero: cada despliegue o reinicio **borra los adjuntos** que los usuarios subieron a sus PQRSF.
El blueprint monta un disco persistente de 5 GB en `/var/www/html/public_html/Anexos`. Los planes
gratuitos de Render no admiten discos, así que en ellos los adjuntos se pierden.

**La base de datos tiene que ser alcanzable desde internet.** Render no está en la red de la
clínica. Si la base solo acepta conexiones internas hay que abrirle paso (IP de salida fija, VPN o
túnel) antes de que esta vía sirva. El `healthCheckPath` es `/`, y esa ruta responde **503** cuando
la conexión falla, así que un despliegue con la base inalcanzable se marcará como no saludable.

### Construir y probar en local

```bash
docker build -t simco .
docker run --rm -p 8080:10000 -e DB_HOST=... -e DB_DATABASE=... -e DB_USERNAME=... -e DB_PASSWORD=... simco
```

Abre `http://localhost:8080`. Los logs de Apache y los errores de PHP salen por la salida estándar
del contenedor, que es lo que Render recoge.

---

El roadmap del proyecto, incluidas las mejoras de seguridad pendientes, está en
[README.md](README.md).

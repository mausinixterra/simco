# SIMCO — Sistema de Registro PQRSF

Sistema web de la Clínica de Occidente para registrar **P**eticiones, **Q**uejas, **R**eclamos,
**S**ugerencias y **F**elicitaciones. Pacientes y aseguradoras registran manifestaciones, adjuntan
documentos y reciben confirmación por correo; después pueden responder una encuesta de conformidad
sobre la respuesta recibida.

Es un sitio público: no tiene login ni back-office. La gestión posterior de las PQRSF ocurre en el
HIS de la clínica, fuera de este repositorio.

**Este documento cubre el desarrollo en local.** Para producción, ver
**[OPERACIONES.md](OPERACIONES.md)**.

### Características

- Formulario público para pacientes y portal exclusivo para aseguradoras
- Encuesta de conformidad sobre la respuesta
- Adjuntos (PDF, Office, imágenes): máximo 5 archivos, 25 MB en total
- Notificaciones por correo (SMTP vía PHPMailer)
- Bilingüe español/inglés y modo claro/oscuro
- Validación en cliente y en servidor
- Diseño responsive con TailwindCSS

---

## Stack

| Capa | Tecnología |
|---|---|
| Backend | PHP 8.2+ con micro-framework MVC propio (sin Laravel/Symfony) |
| Base de datos | PostgreSQL 12+ vía PDO (sin ORM) |
| Frontend | TailwindCSS 4.x, JavaScript vanilla, Choices.js (CDN) |
| Correo | PHPMailer sobre SMTP |
| Dependencias | `vlucas/phpdotenv`, `phpmailer/phpmailer`, `@tailwindcss/cli` |

---

## Estructura del proyecto

```
simco/
├── app/
│   ├── Config/Database.php       # Conexión PDO (singleton)
│   ├── Controllers/              # Entrada HTTP: PqrsfController, SatisfactionController
│   ├── Core/                     # Router.php y Lang.php (i18n)
│   ├── Helpers/TextHelper.php
│   ├── Models/                   # Consultas PDO
│   ├── Services/                 # Lógica de negocio, validación, correo, adjuntos
│   └── Views/                    # emails/ errors/ pqrsf/ satisfaction/ shared/
├── config/app.php                # Idiomas soportados e interruptor de correo
├── public_html/                  # Raíz web
│   ├── index.php                 # Front controller
│   ├── server.php                # Router del servidor embebido de PHP
│   ├── Anexos/                   # Adjuntos subidos (AAAA/MM/DD)
│   ├── assets/{css,img,js}
│   └── documents/                # PDFs de política de datos
├── resources/
│   ├── css/input.css             # Fuente de TailwindCSS
│   └── lang/{es,en}.php          # Traducciones
├── routes/web.php
├── .htaccess                     # Reescritura hacia public_html/ (hosting compartido)
├── .env                          # Credenciales — NO versionado
└── .env.example
```

Para entender cómo encajan las capas entre sí, ver [CLAUDE.md](CLAUDE.md).

---

## Instalación en local

### Prerrequisitos

- **PHP 8.2+** con las extensiones `pdo_pgsql`, `pgsql`, `mbstring`, `curl`, `gd`, `xml`, `zip`
- **PostgreSQL 12+**
- **Composer** — https://getcomposer.org
- **Node.js 16+** (solo para compilar el CSS)

No hace falta Apache ni Nginx: el servidor embebido de PHP es suficiente para desarrollar.

### Paso 1 — Clonar

```bash
git clone http://gitlab.lan.cdo-sa.com/desarrollo-occidente/simco.git
cd simco
git checkout develop
```

### Paso 2 — Instalar dependencias

```bash
composer install
npm install
```

### Paso 3 — Configurar el entorno

```bash
cp .env.example .env      # PowerShell: Copy-Item .env.example .env
```

Edita `.env`:

```bash
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=simco
DB_USERNAME=tu_usuario_postgres
DB_PASSWORD=tu_contraseña

APP_ENV=development
APP_DEBUG=true

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_correo@gmail.com
MAIL_PASSWORD=tu_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@simco.com
MAIL_FROM_NAME="SIMCO - Sistema de PQRSF"
```

> `APP_ENV` no lo lee ningún archivo del proyecto. `APP_DEBUG=true` solo hace una cosa: que la
> página de error de base de datos muestre el mensaje técnico de PostgreSQL en lugar de un aviso
> genérico ([app/Views/errors/database.php](app/Views/errors/database.php)). No activa modo debug
> global ni logging adicional.

### Paso 4 — Conectar la base de datos

**La base de datos ya existe y la administra otro equipo.** Este repositorio no incluye schema ni
migraciones: no hay nada que importar. Lo que necesitas es acceso a una base de **desarrollo**
—nunca a producción— y poner esas credenciales en el `.env` del paso anterior.

Pídele al equipo que administra la base una copia de desarrollo que contenga estas tablas:

| Tabla | Para qué |
|---|---|
| `formulario_pqr` | Registro principal de las PQRSF (+ secuencia `formulario_pqr_formulario_pqr_id_seq`) |
| `formulario_pqr_anexos` | Archivos adjuntos |
| `formulario_pqr_seguimiento` | Respuesta de conformidad |
| `formulario_pqr_tipo_fuente` | Catálogo: tipo de solicitante |
| `formulario_pqr_presento_evento` | Catálogo: servicio donde ocurrió el evento |
| `formulario_pqr_tipo_enfoque_diferencial` | Catálogo: enfoques diferenciales |
| `tipos_id_pacientes` | Del HIS: tipos de documento |
| `planes` + `terceros` | Del HIS: aseguradoras |

Comprueba que el usuario configurado las alcanza todas — esta consulta marca las que falten:

```sql
SELECT t.nombre, to_regclass(t.nombre) IS NOT NULL AS existe
FROM unnest(ARRAY[
  'formulario_pqr', 'formulario_pqr_anexos', 'formulario_pqr_seguimiento',
  'formulario_pqr_tipo_fuente', 'formulario_pqr_presento_evento',
  'formulario_pqr_tipo_enfoque_diferencial',
  'tipos_id_pacientes', 'planes', 'terceros'
]) AS t(nombre)
ORDER BY existe, t.nombre;
```

> Si falta alguna, la aplicación **no falla al arrancar**: los desplegables salen vacíos o el
> formulario revienta al enviar. El detalle de cada tabla está en [CLAUDE.md](CLAUDE.md),
> sección «Modelo de datos».

### Paso 5 — Compilar el CSS

Déjalo corriendo en una terminal aparte mientras desarrollas:

```bash
npx @tailwindcss/cli -i ./resources/css/input.css -o ./public_html/assets/css/output.css --watch
```

> Usa `@tailwindcss/cli`. El comando `npx tailwindcss` es de Tailwind v3 y no funciona aquí.

### Paso 6 — Levantar el servidor

```bash
php -S localhost:8000 -t public_html public_html/server.php
```

La aplicación queda en **http://localhost:8000**.

> El `public_html/server.php` final es necesario: sirve los assets directamente y manda el resto al
> front controller. Sin él, las rutas no resuelven.

### Paso 7 — Verificar la instalación (opcional, Windows)

```powershell
.\verify-setup.ps1
```

Comprueba que estén PHP, Composer, Node, `vendor/`, `node_modules/`, el `.env`, el CSS compilado y
los assets.

---

## Trabajar sin enviar correos

Para probar el registro de PQRSF sin mandar correos reales, en
[config/app.php](config/app.php):

```php
'mail' => [
    'enabled' => false,
    // ...
]
```

Si sí quieres probar el envío con Gmail: activa la verificación en dos pasos, genera una
**contraseña de aplicación** en https://myaccount.google.com/apppasswords y úsala como
`MAIL_PASSWORD`. La contraseña normal de la cuenta no funciona.

---

## Rutas

| Ruta | Método | Qué hace |
|---|---|---|
| `/` | GET · POST | Formulario público de PQRSF y su envío |
| `/pqrsf/success` | GET | Página de confirmación tras registrar |
| `/aseguradoras` | GET · POST | Formulario exclusivo para aseguradoras y su envío |
| `/satisfaccion` | GET · POST | Encuesta de conformidad y su envío |

Cualquier otra URL devuelve el 404 de la aplicación.

**Parámetros de query:**

- `?lang=es` / `?lang=en` — cambia el idioma en cualquier página y lo guarda en sesión.
- `/satisfaccion?id={id}` — el `id` es el identificador de la PQRSF **codificado en base64**
  (no cifrado, no firmado). La encuesta solo se muestra si aún no se ha respondido.
- `/satisfaccion?mensaje=exito` / `?mensaje=error` — pantalla de resultado tras responder.
- `/pqrsf/success?test=1` — renderiza la página de éxito con datos ficticios, sin registrar nada.
  Útil para trabajar en esa vista. Funciona siempre, no depende de `APP_DEBUG`.

---

## Campos de los formularios

Nombres reales de los campos HTML (`name=`), no de las columnas de la base de datos.

### Formulario público — `/`

**Paciente** — `tipo_documento`\*, `documento`\*, `nombres`\*, `correo_electronico`\*,
`telefono_celular`\*

**Evento** — `tipo_solicitante`\* (tipo de solicitante), `servicio`\* (dónde ocurrió),
`aseguradora`\*, `observacion`\* (descripción de los hechos)

**Enfoque diferencial** — `enfoques_diferenciales[]`\* (checkboxes, al menos uno) y
`enfoque_diferencial_otro`, obligatorio solo si se marca la opción «Otro»

**Peticionario** (quien registra, todos opcionales) — `tipo_documento_peticionario`,
`documento_peticionario`, `nombres_peticionario`, `direccion_peticionario`,
`correo_electronico_peticionario`, `telefono_celular_peticionario`

**Adjuntos y autorización** — `attachments[]` (opcional), `authorization`\* (tratamiento de datos)

\* Obligatorio. La validación de servidor está en
[`InputValidator::validatePqrsfForm()`](app/Services/InputValidator.php).

El correo de confirmación se envía **al paciente**.

### Formulario de aseguradoras — `/aseguradoras`

Mismos campos de paciente, evento, enfoque diferencial, adjuntos y autorización, pero **sin la
sección de peticionario**. En su lugar pide los datos del asesor: `nombre_asesor`\*,
`telefono_asesor`\*, `correo_asesor`\*.

El controlador remapea esos tres campos a los campos de peticionario antes de guardar, y el correo
de confirmación se envía **al asesor**, no al paciente.

### Encuesta de conformidad — `/satisfaccion`

`id` (oculto, base64), `conformidad`\* (`si` / `no`), `motivo` (obligatorio si `conformidad = no`,
mínimo 10 caracteres).

### Adjuntos permitidos

`.pdf` · `.doc` `.docx` · `.xls` `.xlsx` · `.ppt` `.pptx` · `.jpg` `.jpeg` `.png` `.gif`

Máximo **5 archivos** y **25 MB en total**. El límite está duplicado en cliente
([form-validation.js](public_html/assets/js/form-validation.js)) y servidor
([InputValidator.php](app/Services/InputValidator.php)): si cambias uno, cambia el otro.

---

## Cómo funciona (resumen)

```
Petición → public_html/index.php → Router → Controller → Service → Model → View
```

Todo POST usa **PRG (Post-Redirect-Get)**: se procesa, se guarda el resultado en sesión como
mensaje flash y se redirige, para que recargar la página no reenvíe el formulario.

Registro de una PQRSF:

1. El navegador valida en cliente y envía `POST /`
2. `InputValidator` sanea y valida en servidor
3. `PqrsfService` guarda el registro, sube los adjuntos a `public_html/Anexos/AAAA/MM/DD/` y
   dispara el correo de confirmación
4. Redirección a `/pqrsf/success` con el número de PQRSF

La arquitectura por capas, el modelo de datos y las convenciones están documentados en
**[CLAUDE.md](CLAUDE.md)**.

---

## Convenciones

- `declare(strict_types=1);` en todos los archivos PHP; namespaces PSR-4 bajo `App\`
- Textos de interfaz y comentarios en español; nombres de clases y métodos en inglés
- Toda salida en vistas pasa por `htmlspecialchars($x, ENT_QUOTES, 'UTF-8')`
- Todo SQL con sentencias preparadas
- **Al añadir una clave de idioma, agrégala en `es.php` y en `en.php`.** Hoy están en paridad
  exacta (141 claves cada uno). Si además la usa el JavaScript, inclúyela en el objeto
  `translations` de la vista.

### Git

- Se trabaja sobre **`develop`**; **`master`** es producción. No existe una rama `main`.
- Prefijos de commit: `feat:`, `fix:`, `refactor:`, `style:`, `chore:`, `i18n:`

```bash
git checkout develop && git pull
git checkout -b feature/nueva-funcionalidad
git commit -m "feat: descripción del cambio"
git push -u origin feature/nueva-funcionalidad
```

Luego abre el Merge Request contra `develop` en GitLab.

---

## Problemas comunes en local

| Síntoma | Solución |
|---|---|
| Página de error de conexión a BD | Revisa credenciales en `.env`, que PostgreSQL esté corriendo y que la base exista. Con `APP_DEBUG=true` la propia página muestra el error de PostgreSQL |
| `Class not found` | `composer dump-autoload` |
| La página se ve sin estilos | Falta `public_html/assets/css/output.css`: ejecuta el comando de Tailwind del paso 5 |
| Los desplegables salen vacíos | Falta alguna tabla o catálogo en la base; ejecuta la consulta de comprobación del paso 4 |
| 404 en todas las rutas | Levantaste el servidor sin `public_html/server.php` al final del comando |
| Los adjuntos no se guardan | `public_html/Anexos/` debe existir y tener permiso de escritura |
| No llegan los correos | Con Gmail hace falta una contraseña de aplicación; o desactiva el envío con `mail.enabled => false` |

---

## Roadmap

- [ ] Panel administrativo para gestionar las PQRSF
- [ ] Reportes y estadísticas de conformidad
- [ ] Protección CSRF en los tres formularios y *rate limiting* en el envío público
- [ ] Firmar el identificador del enlace de conformidad (hoy es base64 sin firma, enumerable)
- [ ] API REST para integraciones y autenticación de usuarios
- [ ] Integración más estrecha con los sistemas hospitalarios

Contexto técnico de los puntos de seguridad en [CLAUDE.md](CLAUDE.md), «Trampas conocidas».

---

## Documentación

| Documento | Para qué |
|---|---|
| **README.md** | Este archivo: qué es el proyecto, instalación local, formularios y rutas |
| **[OPERACIONES.md](OPERACIONES.md)** | Producción: preparar el release, desplegar en cPanel, operar y diagnosticar |
| **[CLAUDE.md](CLAUDE.md)** | El código por dentro: arquitectura, modelo de datos, convenciones y trampas conocidas |

| Script | Para qué | Ejecutar en |
|---|---|---|
| `verify-setup.ps1` | Verifica que el entorno de desarrollo esté completo | Windows |
| `prepare-production.ps1` | Construye el paquete de producción en `build/`, sin tocar tu entorno de desarrollo | Windows |
| `prepare-production.sh` | Equivalente del anterior | Linux / macOS |

---

## Contacto

Servicio al Cliente — servicioalcliente@clinicadeoccidente.com.co

Código propietario · Clínica de Occidente © 2025

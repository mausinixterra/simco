# SIMCO — Sistema de PQRSF, Clínica de Occidente

Aplicación web pública que permite a pacientes, familiares y aseguradoras registrar
**P**eticiones, **Q**uejas, **R**eclamos, **S**ugerencias y **F**elicitaciones, y responder
después una encuesta de conformidad sobre la respuesta recibida. Bilingüe (ES/EN), con
modo claro/oscuro. No tiene módulo de autenticación ni back-office: la gestión posterior
de las PQRSF ocurre en el HIS de la clínica, fuera de este repositorio.

---

## Stack y comandos

- **PHP 8.2** con micro-framework MVC propio (no Laravel, no Symfony). Autoload PSR-4 `App\ → app/`.
- **PostgreSQL** por PDO directo (sin ORM).
- **TailwindCSS v4** (`@tailwindcss/cli`), Choices.js por CDN, JavaScript vanilla en módulos.
- Dependencias PHP: `vlucas/phpdotenv`, `phpmailer/phpmailer`.

```bash
composer install
npm install

# Compilar CSS (watch en desarrollo; --minify para producción)
npx @tailwindcss/cli -i ./resources/css/input.css -o ./public_html/assets/css/output.css --watch

# Servidor de desarrollo
php -S localhost:8000 -t public_html public_html/server.php
```

**No hay suite de pruebas.** La verificación es manual en el navegador. Atajo útil:
`/pqrsf/success?test=1` renderiza la página de éxito con datos ficticios sin registrar nada.

Configuración en `.env` (ver [.env.example](.env.example)): credenciales de BD y SMTP.
Los scripts [verify-setup.ps1](verify-setup.ps1) y [prepare-production.ps1](prepare-production.ps1)
(con sus equivalentes `.sh`) validan el entorno y arman el paquete de despliegue.

---

## Arquitectura

```
Petición HTTP
  └─ public_html/index.php          front controller
       ├─ session_start()
       ├─ Dotenv                    carga .env
       ├─ resuelve idioma           ?lang → $_SESSION['lang'] → config default
       ├─ Lang::load()
       └─ Router::direct()
            └─ Controller           sólo HTTP: lee $_POST/$_GET, redirige, renderiza
                 └─ Service         lógica de negocio y orquestación
                      └─ Model      SQL con PDO
                 └─ View            PHP plano con Lang::get() y htmlspecialchars()
```

| Capa | Archivos | Responsabilidad |
|---|---|---|
| Core | [Router.php](app/Core/Router.php), [Lang.php](app/Core/Lang.php) | Enrutado por coincidencia exacta de string (sin parámetros de ruta ni middleware) e i18n |
| Config | [Database.php](app/Config/Database.php) | Singleton PDO. Si falla la conexión, responde **HTTP 503** y renderiza [errors/database.php](app/Views/errors/database.php) |
| Controllers | [PqrsfController.php](app/Controllers/PqrsfController.php), [SatisfactionController.php](app/Controllers/SatisfactionController.php) | Un mismo método atiende GET y POST, ramificando por `$_SERVER['REQUEST_METHOD']` |
| Services | [app/Services/](app/Services/) | Negocio: validación, uploads, correo, mensajes flash |
| Models | [app/Models/](app/Models/) | Consultas PDO preparadas |
| Views | [app/Views/](app/Views/) | Plantillas PHP; las de correo se renderizan con output buffering |

**Patrón PRG obligatorio.** Todo POST termina en `header('Location: ...')` + `exit`. El
resultado viaja en sesión vía [FlashMessageService](app/Services/FlashMessageService.php)
(claves `pqrsf_success` y `form_status` bajo `_flash_messages`).

---

## Rutas ([routes/web.php](routes/web.php))

| Ruta | Método | Controlador | Vista |
|---|---|---|---|
| `/` | GET, POST | `PqrsfController::publicForm` | [pqrsf/public.php](app/Views/pqrsf/public.php) |
| `/pqrsf/success` | GET | `PqrsfController::showSuccess` | [pqrsf/success.php](app/Views/pqrsf/success.php) |
| `/aseguradoras` | GET, POST | `PqrsfController::insuranceForm` | [pqrsf/insurance.php](app/Views/pqrsf/insurance.php) |
| `/satisfaccion` | GET, POST | `SatisfactionController::showForm` | [satisfaction/satisfaction.php](app/Views/satisfaction/satisfaction.php) |

Cualquier otra URI devuelve 404 con [errors/404.php](app/Views/errors/404.php).

**Los dos formularios PQRSF no son iguales:**

- `/` — datos completos de paciente + peticionario. El correo de confirmación va **al paciente**.
- `/aseguradoras` — captura datos del asesor (`nombre_asesor`, `telefono_asesor`, `correo_asesor`).
  `handleInsuranceFormSubmission()` los **remapea a los campos `*_peticionario` mutando `$_POST`**
  antes de validar, y llama `createPqrsf(..., isInsuranceForm: true)` para que el correo vaya **al asesor**.

**Layout:** sólo las vistas PQRSF usan [pqrsf/layout.php](app/Views/pqrsf/layout.php)
(+ `shared/header|navbar|footer`). `success.php` y `satisfaction.php` son páginas
independientes que arman su propio HTML.

---

## Modelo de datos

**La base de datos ya existe y la administra otro equipo.** Este repositorio no la crea, no la
migra y no versiona ningún schema: solo la consulta. Estas son todas las tablas que toca.

**Propias del PQRSF — lectura y escritura**

| Tabla | Uso | Modelo |
|---|---|---|
| `formulario_pqr` | INSERT del registro principal y SELECT por ID. La PK se obtiene antes con `NEXTVAL('formulario_pqr_formulario_pqr_id_seq')` | [PqrsfModel](app/Models/PqrsfModel.php) |
| `formulario_pqr_anexos` | INSERT y SELECT de adjuntos (`ruta`, `archivo`) | [PqrsfModel](app/Models/PqrsfModel.php) |
| `formulario_pqr_seguimiento` | SELECT para mostrar la encuesta y **UPDATE** de la conformidad (`conforme`, `motivo`, `fecha_respuesta_conformidad`, `ip_respuesta`) | [SatisfactionModel](app/Models/SatisfactionModel.php) |

**Catálogos del PQRSF — solo lectura**, cargados vía [`TipoModel::getTipos()`](app/Models/TipoModel.php)

| Tabla | Alimenta |
|---|---|
| `formulario_pqr_tipo_fuente` | Desplegable «tipo de solicitante» |
| `formulario_pqr_presento_evento` | Desplegable «servicio donde ocurrió el evento» |
| `formulario_pqr_tipo_enfoque_diferencial` | Casillas de enfoque diferencial |

**Del HIS de la clínica — solo lectura, ajenas a este repositorio**

| Tabla | Alimenta | Modelo |
|---|---|---|
| `tipos_id_pacientes` | Desplegables de tipo de documento | [TipoIdPaciente](app/Models/TipoIdPaciente.php) |
| `planes` + `terceros` | Desplegable de aseguradora | [AseguradoraModel](app/Models/AseguradoraModel.php) |

El `<option>` de aseguradora codifica dos valores en uno con el separador `*_*`
(`tipo_id_tercero*_*tercero_id`), que `InputValidator` vuelve a partir con `explode`.

---

## Subsistemas

**i18n.** [resources/lang/es.php](resources/lang/es.php) y [en.php](resources/lang/en.php),
141 claves cada uno, actualmente en paridad exacta — **al añadir una clave, agrégala en ambos**.
En servidor: `Lang::get('clave')`. En cliente: las cadenas se inyectan desde la vista en un
objeto `translations` que se pasa a `FormInitModule.init(translations)`; una clave nueva usada
en JS hay que añadirla también a ese objeto en la vista.

**Correo.** PHPMailer sobre SMTP. Interruptor global: `mail.enabled` en
[config/app.php](config/app.php) — ponlo en `false` para probar sin enviar nada.
[EmailService](app/Services/EmailService.php) configura el transporte y adjunta el logo
embebido; [EmailTemplateService](app/Services/EmailTemplateService.php) renderiza las
plantillas de [app/Views/emails/](app/Views/emails/) (versión `.html.php` y `.txt.php` de cada
una) y llama `Lang::load($lang)` para respetar el idioma de la sesión. Los destinatarios de la
notificación de conformidad están **hardcodeados** en `EmailService::sendSatisfactionNotification()`.

**Adjuntos.** Máx. 5 archivos, 25 MB en total; PDF, Office e imágenes. **El límite está
duplicado** en cliente (`CONFIG` en [form-validation.js](public_html/assets/js/form-validation.js))
y servidor (`InputValidator::validateFiles()`) — si cambias uno, cambia el otro.
[FileUploadService](app/Services/FileUploadService.php) guarda en
`public_html/Anexos/AAAA/MM/DD/` con el nombre `{pqrId}_{nombreSaneado}_{índice}.{ext}`.

**Frontend.** Dark mode por clase (`darkMode: 'class'`), toggle y menú de idioma en
[navbar.js](public_html/assets/js/navbar.js). El `<select>` de aseguradora usa Choices.js,
cuyo estilo oscuro se parchea con CSS inline en [shared/header.php](app/Views/shared/header.php).

---

## Convenciones

- `declare(strict_types=1);` en todos los archivos PHP.
- Namespaces PSR-4 bajo `App\`; una clase por archivo.
- **Mensajes al usuario, comentarios y textos de vista en español**; nombres de clases, métodos,
  variables y docblocks en inglés. Los nombres de columnas y campos de formulario son en español
  (`nombres`, `observacion`, `tipo_fuente_id`).
- Toda salida en vistas va por `htmlspecialchars($x, ENT_QUOTES, 'UTF-8')`.
- Todo SQL con sentencias preparadas; los errores se registran con `error_log()` y el método
  devuelve `false`/`[]` en lugar de propagar la excepción.
- Commits: prefijos `feat:`, `fix:`, `refactor:`, `style:`, `chore:`, `i18n:`. Mayoritariamente en español.
- Ramas: se trabaja sobre **`develop`**; **`master`** es producción. Remoto: GitLab interno
  (`gitlab.lan.cdo-sa.com/desarrollo-occidente/simco`).

---

## Trampas conocidas

1. **El proyecto no crea ni migra su esquema.** No hay archivos de schema ni migraciones: la base
   la administra otro equipo. Si una columna o un catálogo falta, no falla el arranque — falla el
   formulario en tiempo de ejecución, a veces en silencio (un desplegable vacío). Al añadir un
   campo, coordina el cambio de estructura con quien administra la base **antes** de tocar el
   código.
2. **`insertSatisfactionResponse()` no inserta: actualiza.** Hace un **UPDATE** sobre
   `formulario_pqr_seguimiento` (`conforme`, `motivo`, `fecha_respuesta_conformidad`,
   `ip_respuesta`). La fila debe existir ya, creada por otro sistema; si no existe, el UPDATE
   afecta cero filas y la operación se reporta como exitosa.
3. **Hay tablas ajenas a este repo.** `planes`, `terceros` y `tipos_id_pacientes` pertenecen al HIS.
   Solo se leen: no las modifiques desde este proyecto.
4. **Los enfoques diferenciales se guardan aplanados**, con `implode(', ', ...)` en una sola
   columna, no como relación N:M. Cualquier reporte que necesite filtrar por enfoque tendrá que
   parsear texto o rediseñar el almacenamiento.
5. **El ID `'12'` está hardcodeado** en `InputValidator::validatePqrsfData()` como la opción
   "Otro" del enfoque diferencial (es la que exige llenar `enfoque_diferencial_otro`). Si cambian
   los IDs del catálogo, esta validación se rompe en silencio.
6. **El enlace de satisfacción es enumerable.** `/satisfaccion?id=` sólo lleva el ID en
   `base64_encode`, sin firma. La única barrera es que la consulta exige
   `fecha_respuesta_conformidad IS NULL`. No tratar ese parámetro como secreto.
7. **No hay protección CSRF** en ninguno de los tres formularios.
8. **`TipoModel::getTipos()` interpola nombres de tabla y columna en el SQL**, protegido sólo por
   un `preg_match('/^[a-zA-Z0-9_]+$/')`. Nunca le pases valores derivados de la entrada del usuario.
9. `.gitignore` lista `composer.lock`, pero el archivo sí está versionado.
10. `Database::getInstance()` no relanza la excepción: imprime la página de error y hace `exit`.
    No esperes poder capturar un fallo de conexión desde arriba.

---

## Documentación relacionada

Ninguno de estos hace falta para trabajar en el código; consúltalos por su tema:

| Archivo | Para qué |
|---|---|
| [README.md](README.md) | Instalación en local, descripción de campos de cada formulario |
| [OPERACIONES.md](OPERACIONES.md) | Preparar el release, desplegar en cPanel, operar y diagnosticar producción |

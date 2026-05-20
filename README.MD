# PROJECTE 6 — Farmàcia Soler

Plataforma web per a la **Farmàcia Soler** (Figueres). Cobreix tota
l'operativa pública i interna del client: reserva de cites, gestió de
serveis i horaris, inscripcions a tallers, encàrrecs a domicili amb
seguiment, mapa de farmàcies de guàrdia, gestió de correus i panell
d'administració complet.

## Equip

- **Adrien Schirmer** (Scrum Manager)
- **Brandon Reyes**
- **Fran Olivera**
- **Charfah Ilyass**

## Tecnologies

- **Backend**: Laravel 13 (PHP 8.3), Fortify (auth + 2FA), Inertia,
  Laravel Wayfinder, `lorisleiva/laravel-actions`,
  `barryvdh/laravel-dompdf`.
- **Frontend**: Vue 3 + TypeScript + Composition API, Tailwind CSS 4,
  reka-ui, lucide-vue-next, `@iconify/vue`, Leaflet (mapes), Swiper
  (sliders), TipTap (WYSIWYG).
- **BD**: MySQL 8 (Laravel Sail / Docker).
- **Email**: SMTP (Dinahosting per defecte), Mailpit en local.
- **Verificació humana**: Cloudflare Turnstile.
- **Testos**: Pest 4 (unit + feature) + Playwright (E2E).
- **CI/CD**: GitHub Actions, desplegament automàtic.

## Requisits

- Docker + Docker Compose (per Laravel Sail).
- Node 20+.
- Composer 2.

## Posada en marxa (local)

```bash
# 1. instal·lar dependències PHP i copiar .env
composer install
cp .env.example .env
php artisan key:generate

# 2. arrencar la pila amb Sail
./vendor/bin/sail up -d

# 3. migracions + storage
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail artisan storage:link

# 4. assets
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

Configuració del `.env` (essencial):

```env
APP_URL=http://localhost
DB_CONNECTION=mysql
DB_HOST=mysql
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password

MAIL_MAILER=smtp           # o 'log' en dev (escriu els correus a storage/logs/laravel.log)
MAIL_HOST=mailpit          # en local
MAIL_PORT=1025

TURNSTILE_SITE_KEY=...     # opcional en dev (si està buit, els widgets es relaxen)
TURNSTILE_SECRET_KEY=...
```

## Patró arquitectònic

Tot el codi nou ha de seguir aquest flux, sense excepcions:

```
Route → FormRequest (validació + autorització) → Controller (prim, només orquestra)
      → Action (lògica de negoci) → Model → DB
```

El controlador **no** conté lògica de negoci. La validació i l'autorització
viuen al FormRequest. Les operacions de persistència viuen en Actions
reutilitzables i testables aïlladament.

---

## Mòduls del projecte

### 1. Home i mapa de farmàcies de guàrdia

Pàgina d'entrada amb un mapa interactiu (Leaflet) que mostra les
farmàcies obertes en aquell moment.

| Capa | Fitxer |
|---|---|
| **Controller** | `app/Http/Controllers/HomeController.php` (endpoint AJAX `getpg`) |
| **Models** | `app/Models/Pharmacy.php` (hasMany PharmacyGuard), `PharmacyGuard.php` (belongsTo Pharmacy) |
| **Vista** | `resources/js/pages/Home.vue` |
| **Ruta** | `GET /` (home), `GET /pharmacyguard/{data}` (AJAX) |

### 2. Reserva de cites (PedirCita)

Mòdul públic més complet del projecte. Permet a un client triar un
servei, una data i una hora, validar amb Turnstile, rebre un correu de
confirmació amb PDF adjunt i descarregar el mateix PDF des de la
pantalla d'èxit.

| Capa | Fitxer |
|---|---|
| **Migracions** | `2026_04_10_135414_create_services_table.php`, `…_create_service_schedules_table.php`, `…_create_service_appointments_table.php`, `…_add_icon_to_services_table.php`, `…_create_calendar_exceptions.php` |
| **FormRequests** | `StoreAppointmentRequest.php` (regex telèfon, Turnstile), `GetBookedTimesRequest.php`, `GetScheduleRequest.php`, `DownloadAppointmentPdfRequest.php`, `StoreServiceRequest.php`, `UpdateServiceRequest.php` |
| **Controllers** | `AppointmentsController.php` (públic), `Admin/AdminServiceController.php`, `Admin/ServiceScheduleController.php` |
| **Models** | `Service.php` (hasMany schedules), `ServiceSchedule.php` (belongsTo Service), `ServiceAppointment.php`, `calendar_exceptions.php` |
| **Vistes** | `resources/js/pages/Appointments/Create.vue`, `admin/Services/{Index,Create,Edit}.vue`, `admin/ServiceSchedules/{Create,Show}.vue` |
| **Mail + Blade** | `ReservationCreated.php`, `ReservationCreatedAdmin.php` + plantilles a `resources/views/mail/` |
| **PDF** | `resources/views/pdf/appointment.blade.php` (DomPDF) |
| **Rutes** | `GET /appointments/create`, `POST /appointments`, `GET /appointments/pdf`, `GET /appointments/booked-times` (AJAX), `GET /appointments/schedule` (AJAX) |
| **Tests** | `tests/Feature/AppointmentsTest.php`, `AdminServicesTest.php`, `AdminServiceSchedulesTest.php` |

### 3. Tallers (Workshops) i inscripcions

Catàleg públic de tallers amb pàgina de detall i formulari d'inscripció
expandible amb Turnstile, control de capacitat (max_attendees) i correus
de confirmació al client i a l'administrador.

| Capa | Fitxer |
|---|---|
| **Migracions** | `2026_05_08_000000_create_workshops_table.php`, `2026_05_18_134310_create_workshops_appointment_table.php` (taula `workshop_inscriptions`) |
| **FormRequest** | `StoreWorkshopInscriptionRequest.php` (regex nom, email + `Rule::unique` per workshop_id, telèfon 9 dígits opcionalment +34, Turnstile condicional) |
| **Action** | `app/Actions/Workshops/CreateWorkshopInscriptionAction.php` |
| **Controllers** | `public_workshops_controller.php` (mètode `inscribe` amb doble check de capacitat), `Admin/admin_workshops_controller.php` |
| **Models** | `Workshop.php` (hasMany Inscriptions), `WorkshopInscription.php` (belongsTo Workshop) |
| **Vistes** | `resources/js/pages/workshops/index.vue`, `workshops/workshopdetails.vue` (formulari amb botó expandible), `admin/Workshops/{Index,Create,Edit}.vue` |
| **Mail + Blade** | `WorkshopInscripted.php`, `WorkshopInscriptedAdmin.php` + plantilles a `resources/views/mail/workshop-inscribed*.blade.php` |
| **Rutes** | `GET /workshops`, `GET /workshops/{workshop}`, `POST /workshops/{workshop}/inscribe`, `Route::resource('admin/workshops', …)` |

### 4. Encàrrecs a domicili (Assignments)

Sistema d'encàrrecs amb codi de seguiment. El client crea una sol·licitud
des d'un formulari públic; rep un codi únic per consultar l'estat més
endavant.

| Capa | Fitxer |
|---|---|
| **Migració** | `2026_04_10_135843_create_assignments_table.php` |
| **FormRequest** | `CreateAssignmentRequest.php` |
| **Action** | `app/Actions/Assignments/CreateAssignmentAction.php` |
| **Controllers** | `AssignmentsController.php` (públic, amb codi de seguiment via Cache + Hash), `Admin/AssignmentsController.php` |
| **Model** | `assignments.php` |
| **Vistes** | `resources/js/pages/Assignments/{Create,Index}.vue`, `admin/Assignments/Index.vue` |
| **Mail + Blade** | `AssignmentCreated.php`, `AssignmentCreatedAdmin.php`, `AssignmentListCode.php` |
| **Rutes** | `Route::resource('assignments', …)`, `POST /assignments/code` (genera codi), `POST /assignments/verify-code` (verifica) |

### 5. Farmàcies i farmàcies de guàrdia (admin)

CRUD complet de farmàcies amb coordenades (latitude/longitude) i
calendari de guàrdies. El llistat té un endpoint AJAX per filtrar.

| Capa | Fitxer |
|---|---|
| **Migracions** | `2026_04_10_132013_create_pharmacies_table.php`, `2026_04_10_132021_create_shifts_table.php`, `2026_04_23_143311_add_table_pharmacyguards.php` |
| **Controllers** | `Admin/admin_pharmacies_controller.php` (+ mètode `filter` AJAX), `Admin/admin_pharmacyguards_controller.php` (+ mètode `filter` AJAX) |
| **Models** | `Pharmacy.php` (hasMany PharmacyGuard), `PharmacyGuard.php` (belongsTo Pharmacy) |
| **Vistes** | `admin/Pharmacies/Index.vue`, `admin/PharmacyGuards/Index.vue` |
| **Rutes** | `Route::resource('admin/pharmacies', …)`, `GET admin/pharmacies/filter` (AJAX), idem per `pharmacyguards` |

### 6. Usuaris (admin) + Autenticació + 2FA

Gestió d'usuaris amb rols (admin / client), més tot el flux d'auth de
Fortify amb verificació d'email, recuperació de contrasenya i
autenticació de dos factors.

| Capa | Fitxer |
|---|---|
| **Auth** | Laravel Fortify (registre, login, reset, verify email, 2FA) |
| **Settings controllers** | `Settings/ProfileController.php`, `Settings/SecurityController.php` |
| **FormRequests Settings** | `Settings/ProfileUpdateRequest.php`, `ProfileDeleteRequest.php`, `PasswordUpdateRequest.php`, `TwoFactorAuthenticationRequest.php` |
| **Admin controller** | `Admin/admin_users_controller.php` |
| **Model** | `User.php` (amb camps de 2FA i rol) |
| **Vistes auth** | `resources/js/pages/auth/{Login,Register,ForgotPassword,ResetPassword,VerifyEmail,ConfirmPassword,TwoFactorChallenge}.vue` |
| **Vistes settings** | `resources/js/pages/settings/{Profile,Security,Appearance}.vue` |
| **Vistes admin** | `admin/Users/{Index,Create}.vue` |
| **Middleware** | `isAdmin` (al grup de rutes `/admin`) |
| **Tests** | `tests/Feature/Auth/*.php`, `tests/Feature/Settings/*.php` |

### 7. Correus (Emails + Mail)

Llistat de correus de destinació per a notificacions internes (els que
reben còpia quan hi ha una cita o inscripció) i editor WYSIWYG per
compondre i enviar correus massius.

| Capa | Fitxer |
|---|---|
| **Migració** | `2026_05_13_165627_create_table_emails.php` |
| **FormRequest** | `CreateEmailRequest.php` |
| **Action** | `app/Actions/Emails/CreateEmailAction.php` |
| **Controllers** | `Admin/EmailsController.php` (llistat de correus actius), `Admin/MailController.php` (editor + enviament) |
| **Model** | `Email.php` |
| **Vistes** | `admin/Emails/{Index,Create}.vue`, `admin/Mail/Index.vue` |
| **WYSIWYG** | `resources/js/components/TiptapEditor.vue` |

### 8. Contacte

Pàgina informativa amb formulari de contacte.

| Capa | Fitxer |
|---|---|
| **Controller** | `Contactans.php` |
| **Vista** | `resources/js/pages/contactans.vue` |
| **Ruta** | `GET /contact-us` |

---

## Endpoints AJAX (resum)

| Endpoint | Mètode | Controlador | Funció | Descripció |
|---|---|---|---|---|
| `/pharmacyguard/{data}` | GET | `HomeController` | `getpg` | Farmàcies de guàrdia d'una data |
| `/appointments/booked-times` | GET | `AppointmentsController` | `getBookedTimes` | Hores ja reservades d'un servei en una data |
| `/appointments/schedule` | GET | `AppointmentsController` | `getSchedule` | Slots disponibles d'un servei per dia de la setmana |
| `/admin/pharmacies/filter` | GET | `admin_pharmacies_controller` | `filter` | Filtra el llistat de farmàcies sense recàrrega |
| `/admin/pharmacyguards/filter` | GET | `admin_pharmacyguards_controller` | `filter` | Filtra el llistat de guàrdies sense recàrrega |
| `/admin/emails` | GET | `EmailsController` | `index` | Filtra correus per estat (actiu/inactiu) |

Tots passen per un FormRequest que filtra les dades d'entrada.

---

## Estructura general de carpetes

```
app/
  Actions/                 ← lògica de negoci (1 Action per cas d'ús)
    Assignments/  Emails/  Fortify/  Workshops/
  Http/
    Controllers/           ← controllers prims
      Admin/               ← rutes /admin (middleware isAdmin)
      Settings/            ← perfil + seguretat de l'usuari logat
    Requests/              ← FormRequests (validació + autorització)
      Settings/
    Middleware/            ← isAdmin, HandleAppearance, etc.
  Mail/                    ← Mailables (Reservation, Assignment, WorkshopInscripted, …)
  Models/                  ← Eloquent (Service, Workshop, Pharmacy, User, …)
  Rules/                   ← Custom validation rules (TurnstileRule)

resources/
  js/
    pages/                 ← una carpeta per feature (Inertia)
      admin/               ← pàgines del panell d'administració
      auth/  settings/  workshops/  Assignments/  Appointments/
    components/            ← components reutilitzables
      ui/                  ← components shadcn-style (Button, Alert, …)
    layouts/               ← WebAppLayout, AppLayout, AuthLayout
  views/
    mail/                  ← plantilles Blade dels correus
    pdf/                   ← plantilla Blade per al PDF de cites

routes/
  web.php                  ← totes les rutes web
  settings.php             ← rutes de /settings

database/
  migrations/              ← històric de schemes
  seeders/                 ← dades de demo
  factories/               ← factories per tests

tests/
  Feature/                 ← tests d'integració (cobreixen rutes)
  Unit/                    ← tests unitaris (TurnstileRule, etc.)
```

---

## Pipeline (CI/CD)

```bash
composer ci:check   # linting + format + types + testos (gate del deploy)
```

Veure `.github/workflows/CI.yml` i `Deploy.yml`. El deploy automàtic
només s'executa si tot CI és verd a la branca `develop`.

---

## Estil de codi i convencions

Veure [`CONTRIBUTING.md`](./CONTRIBUTING.md) per a tota la informació
sobre estil PHP/Vue, branques Git, política d'ús d'IA, patrons recomanats
amb Inertia + Turnstile i checklist de PR.

---

## Llicència

Ús educatiu — Projecte 6 de l'institut Cendrassos.
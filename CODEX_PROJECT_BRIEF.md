# Codex Project Brief for Aparthub

Use this note before asking Codex to implement large UI or module changes in this project.

## Important Warning

Do **not** paste a generic frontend prompt into Codex blindly.

That can easily cause:

- duplicate routes
- duplicate modules or pages
- components that do not match the current Blade layout
- React-style assumptions in a Laravel app
- broken navigation or permissions

Any implementation prompt should be adjusted to the **actual structure of this repo** first.

## Real Project Baseline

This project is **not** a React SPA. It is currently:

- **Laravel**
- **Blade views**
- **PHP controllers and route groups**
- **Vite**
- **Tailwind available in `package.json`**
- existing native CSS patterns already living in `resources/views/layouts/app.blade.php`

### Current root structure

```text
app/
bootstrap/
config/
database/
public/
resources/
routes/
storage/
tests/
vendor/
package.json
vite.config.js
composer.json
```

### Frontend / build baseline

From `package.json`:

```json
{
  "private": true,
  "type": "module",
  "scripts": {
    "build": "vite build",
    "dev": "vite"
  },
  "devDependencies": {
    "@tailwindcss/vite": "^4.0.0",
    "axios": "^1.7.4",
    "concurrently": "^9.0.1",
    "laravel-vite-plugin": "^1.2.0",
    "tailwindcss": "^4.0.0",
    "vite": "^6.0.11"
  }
}
```

### Routing baseline

Routing is handled in **`routes/web.php`**, with module route groups already defined for:

- Resident Management
- Billing & Finance
- Visitor Management
- Service Request
- Security Management
- Community Management
- Tenant Marketplace
- Package Center
- Users / Roles / Modules / Access

So any future prompt must assume:

- page/module routing already exists
- permission middleware already exists
- modules should be extended carefully, not re-invented

### Current dynamic data baseline

Resident Management is already using real Laravel models and CRUD patterns, not mock-only UI.

Example model:

- `app/Models/Resident.php`

Current `Resident` fields:

- `unit_id`
- `name`
- `resident_type`
- `status`
- `move_in_date`
- `move_out_date`
- `avatar_tone`

Resident relationships already exist for:

- `unit()`
- `familyMembers()`
- `moveRequests()`
- `vehicles()`

This means future prompts should **not** assume Resident Management is still a purely static module.

## Highest-Value Inputs to Give Codex

Before asking for a large feature or redesign, provide these real project artifacts.

### 1. Project structure

Share or confirm:

- root folders/files
- `package.json`
- `routes/web.php`
- if relevant, `vite.config.js`

### 2. Current module files

The most important files to share are:

- Resident Management page/controller/view files
- Service Request page/controller/view files
- Admin Dashboard page/controller/view files
- Visitor Management page/controller/view files
- Sidebar / navigation layout

In this repo, likely starting points include:

- `resources/views/layouts/app.blade.php`
- `app/Http/Controllers/ResidentManagementController.php`
- `resources/views/resident-management/*`
- `resources/views/visitor-management/*`
- `resources/views/service-request/*`

### 3. Current data approach

Clarify which modules are:

- fully dynamic with database-backed CRUD
- partially dynamic
- still mock / static only
- localStorage based, if any

For this project right now:

- Resident Management is already database-backed
- other operational modules may still be mostly static Blade UI

### 4. Visual baseline

Provide screenshots for:

- current admin dashboard
- resident detail modal / resident workspace
- service request page
- visitor management page
- sidebar / topbar state

This prevents Codex from redesigning the app into a different product style.

### 5. Demo scope decision

Be explicit about what must actually work now versus what may remain presentational.

Examples:

- CRUD must function
- filters may be real or dummy
- export may be UI-only or real file download
- QR features may be static visual placeholders
- reports may be mock cards only

## Best Way to Hand Off Work

Best options:

1. Upload the project as a `.zip`, or
2. Share the file tree plus the key routing/layout/module files, or
3. Point directly to the exact files that must be modified

Then the implementation request can be converted into:

- a file-specific execution plan
- what to modify
- what to keep
- what to avoid touching
- what is allowed to stay mock

## Safe Prompting Guidance

When prompting Codex for implementation in this repo, prefer instructions like:

- "extend the existing Laravel Blade module"
- "reuse current route groups in `routes/web.php`"
- "do not introduce React or SPA routing"
- "preserve existing permission middleware"
- "follow the current `layouts/app.blade.php` visual system"
- "do not duplicate existing modules or controllers"
- "keep dynamic Resident Management intact"

Avoid vague prompts like:

- "build a full dashboard from scratch"
- "make this a React admin panel"
- "recreate the whole navigation"

unless that is truly the intention.

## Recommended Files to Attach for Future Requests

If asking for major work, include these first:

- `package.json`
- `routes/web.php`
- `resources/views/layouts/app.blade.php`
- relevant controller for the target module
- relevant Blade view(s) for the target module
- related model(s) if the feature is dynamic
- screenshots of the current UI

## Short Version

This project already has a real Laravel module structure.

So before asking Codex to implement a big feature:

- ground the prompt in the real files
- state which module is dynamic vs static
- state what must really function
- state what should remain mock
- avoid generic SPA/frontend assumptions

That will keep Codex from drifting away from the codebase you already have.

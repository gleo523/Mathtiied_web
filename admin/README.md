# Mathtified Admin Portal

The admin portal is restricted to users with `role = 'admin'` and is backed by the
PHP/MySQL APIs in the project root.

## Active admin scope

The handoff defines these administrator responsibilities:

1. Create teacher accounts.
2. View all students, including their teacher assignment.
3. View teacher login/logout records.
4. Configure the DWTI review threshold.
5. Review live account and assignment summaries.

The portal contains only these active pages:

| Page | Purpose |
|---|---|
| `index.php` | Administrator login |
| `pages/dashboard.php` | Live user, teacher, student, authentication, and assignment overview |
| `pages/authentication.php` | Teacher login/logout history |
| `pages/management.php` | Create teachers and review teachers, students, and auth logs |
| `pages/settings.php` | Edit the DWTI review threshold |

Monitoring, reporting, calendar, and notification screens were removed because
they were visual prototypes without a handoff-defined workflow or database model.

## Backend endpoints

- `api/login.php`
- `api/logout.php`
- `api/admin/admin_summary.php`
- `api/admin/admin_auth_logs.php`
- `api/admin/admin_overview.php`
- `api/admin/admin_teachers.php`
- `api/admin/admin_settings.php`

All admin endpoints call `require_role('admin')`.

## Database tables used

- `users`
- `teacher_auth_logs`
- `system_settings`

The dashboard also reads assignment information through `users.teacher_id`.

## Layout and responsive behavior

All active pages load:

1. `css/base.css`
2. `css/layout.css`
3. `css/components.css`
4. `css/responsive.css`

The shared layout explicitly constrains grid children, tables, and long text so
cards do not overlap at desktop, tablet, or mobile widths.

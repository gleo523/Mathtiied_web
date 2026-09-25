# API layout

The API directory is organized by ownership while preserving the public paths
used by the Godot/DataConnector integration.

## Admin APIs

The admin-only endpoints are under `api/admin/`:

- `admin_summary.php`
- `admin_auth_logs.php`
- `admin_overview.php`
- `admin_teachers.php`
- `admin_settings.php`

They require an authenticated administrator session.

## Shared authentication

These remain directly under `api/` because both the teacher portal and admin
portal use them:

- `login.php`
- `logout.php`

## Teacher portal and game APIs

The remaining direct `api/` endpoints serve teacher pages, student pages, or
the Godot/DataConnector contract. They are intentionally not moved or renamed:

- Teacher pages: `dashboard_data.php`, `student_details.php`, `wave_history.php`,
  `get_content.php`, `save_content.php`, `review_modules.php` (teacher-owned
  review module creation, assignment, listing, and status changes)
- Student/game registration: `register_student.php`, `check_account.php`
- Game connector persistence: `save_game.php`, `save_wave_score.php`,
  `sync_data.php`, `get_user_progress.php`, `get_dwti_settings.php`

Keeping these public paths stable prevents breaking the game connector or
existing teacher-page requests.

## Student/game identity

Student registration and login return a stable `game_identity`, which is the
student's database username. The DataConnector should send that value as
`username`, `student_id`, `name`, or `user` when calling `sync_data.php`,
`save_game.php`, or `get_user_progress.php`. The account lookup endpoint also
returns the same identity:

```text
GET /api/check_account.php?student_id=<student-id>
```

## Browser-to-game login handoff

The Godot game can create a short-lived handoff:

```text
POST /api/create_game_handoff.php
```

The response contains `handoff_id` and `login_url`. Open `login_url` in the
system browser. After the student logs in, the web page authenticates the
handoff. The game polls:

```text
GET /api/complete_game_handoff.php?handoff_id=<handoff_id>
```

The confirmation is one-time and expires after ten minutes. A successful
response contains `game_identity`, which should be assigned to
`Global.player_name` before syncing game progress.

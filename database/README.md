# Database

This folder contains the **public demo database** for Sujan Top-Up.

> **Privacy:** No production data is included. `schema.sql` contains structure
> only, and `demo_seed.sql` contains entirely fictional rows.

## Files

| File | Purpose |
|------|---------|
| `schema.sql` | Table definitions (structure only, no rows) |
| `demo_seed.sql` | Fictional demo users, admins, catalog, orders and reviews |
| `README.md` | This document |

## Setup

Create the database and import both files:

```bash
mysql -u root -p -e "CREATE DATABASE sujan_topup_demo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p sujan_topup_demo < database/schema.sql
mysql -u root -p sujan_topup_demo < database/demo_seed.sql
```

Then point your `.env` at it:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=sujan_topup_demo
DB_USER=root
DB_PASSWORD=
```

## Demo credentials

| Role | Login | Password |
|------|-------|----------|
| User | `demo.user@example.test` | `DemoUser@123` |
| Admin | `demo_admin` (or `demo.admin@example.test`) | `DemoAdmin@123` |

Passwords are stored only as `password_hash()` values. There is **no built-in
administrator** in the PHP source — the demo admin exists solely in
`demo_seed.sql`.

## Schema notes

The application is a legacy project, so several concepts are represented by
per-service order tables (for example `pubg_website_orders` and
`pubg_whatsapp_orders`) rather than a single normalized `orders` table. This
mirrors the original production structure and is documented in
`../docs/LEGACY_AUDIT.md`.

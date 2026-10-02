# Legacy Audit

This document records what was **kept, changed or removed** when the original
Sujan Top-Up codebase was sanitized and prepared for public release. It is a
preservation pass, not a rewrite, so legacy architecture that still works has
been left in place.

## Classification key

| Status | Meaning |
|--------|---------|
| **ACTIVE** | Part of the working public demo; requested and used. |
| **LEGACY** | Older code kept for historical value; may be redundant but functional. |
| **DEAD** | Unreferenced or broken; removed from the public edition. |
| **DEBUG** | Scratch/dev artifact; removed from the public edition. |

## Removed files

All removed files were copied to the private backup at
`C:\private-project-backups\sujan-topup-original-2026-10-02\removed-from-public\`
before deletion. Nothing was permanently destroyed.

| File | Status | Reason |
|------|--------|--------|
| `..php` | DEBUG | Malformed filename; unreferenced scratch page. |
| `t.php` | DEBUG | Standalone `CREATE TABLE` dev script. |
| `ta.php` | DEAD | Orphaned gallery fragment; `index.php` has its own gallery. |
| `ss.php` (root) | DEAD | Duplicate of `admin/ss.php`; superseded by `payment_proof.php`. |
| `pptop.php` | DEAD | Unreferenced, superseded by `ptptop.php`. |
| `t_spotoppti.php` | DEAD | Unreferenced. |
| `secondary.php` | DEAD | Router used only by the removed `mmain.js` flow. |
| `fetch_notifications.php` | DEAD | Hard-coded placeholder database credentials; unused. |
| `dbcon_backup.php` | DEAD | Duplicate database credentials file. |
| `billing_page.php` | DEAD | Queried columns that never existed; unreferenced. |
| `admin/t.php` | DEBUG | Orphaned JavaScript fragment. |
| `admin/ss.php` | DEAD | Duplicate upload endpoint; superseded by `payment_proof.php`. |
| `admin/demo.php` | DEBUG | Placeholder page that only printed "hi". |
| `admin/register.php` | LEGACY | Admin self-registration; removed with its form. |
| `admin/sign.php` | DEAD | "Add admin" form; unreferenced (superadmin management removed for the demo). |
| `admin/storeOrder.php` | DEAD | Unreferenced; raw SQL and a non-existent table. |
| `admin/fund/` | DEAD | Leftover from an unrelated project. |
| `admin/whatsapp/` | DEAD | Duplicate copies of the WhatsApp order pages. |
| `protect/` | DEAD | Parallel admin login with a hard-coded default admin; unreferenced. |
| `admin/protect/` | DEAD | Same as above. |
| `assets/img/Sujan Top-Up ... .html` | DEAD | Saved webpage snapshot containing personal data. |
| `assets img/..._files/` | DEAD | Assets for the saved snapshot above. |
| `admin/uploads240_*.jpg` | DEAD | Stray private images in the admin root. |

## Removed directories from source control

| Path | Reason |
|------|--------|
| `vendor/`, `admin/vendor/`, `sign/vendor/` | Dependencies; reproduce with `composer install`. |
| `node_modules/` | Orphaned Gulp build leftovers; no `package.json` exists. |

## Kept architecture (LEGACY)

| Area | Note |
|------|------|
| Per-service order tables | `pubg_website_orders`, `freefire_whatsapp_orders`, etc. Kept to match the original schema. |
| `admin/sign/login.php` + `loginpro.php` | The active admin login. The duplicate default-admin login was removed. |
| `seson.php` | Admin session guard used by every admin page. |
| `fetch.php` / `feedphp.php` / `feed.php` | Reviews listing and submission. |
| `games/*/web.php` and `games/*/whats.php` | Per-service order submission handlers. |
| `assets/`, `admin/assets/` | First-party static assets and the AdminLTE admin theme. |

## Known legacy quirks

* Orders live in per-service tables rather than one normalized table.
* Some game pages duplicate markup; contact details are now centralised in `config/app.php`.
* The eFootball password field stores a user-supplied password in the database —
  this is preserved legacy behaviour, shown only with fictional demo data, and
  is flagged in `SECURITY.md`.

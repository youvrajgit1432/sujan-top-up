# Contributing

Thanks for taking an interest in Sujan Top-Up! This is a preserved legacy
PHP/MySQL project, so contributions should respect its existing architecture.

## Ground rules

* **Never commit secrets.** Database and SMTP credentials go in `.env`
  (gitignored). Use `.env.example` as the template.
* **Never commit real personal or customer data.** Only fictional demo data.
* **Do not commit uploaded media.** `uploads/` is gitignored except `.gitkeep`.
* **Do not commit `vendor/` or `node_modules/`.** Use `composer install`.
* Keep changes focused; this is a preservation pass, not a framework rewrite.

## Local setup

```bash
composer install
cp .env.example .env            # then edit DB settings
mysql -u root -p -e "CREATE DATABASE sujan_topup_demo CHARACTER SET utf8mb4;"
mysql -u root -p sujan_topup_demo < database/schema.sql
mysql -u root -p sujan_topup_demo < database/demo_seed.sql
php -S 127.0.0.1:8000
```

## Before opening a pull request

1. Lint every PHP file you touched:
   ```bash
   find . -name '*.php' -not -path './vendor/*' -print0 | xargs -0 -n1 php -l
   ```
2. Confirm the demo still runs and key pages return no PHP fatal errors.
3. Update documentation (`README.md`, `docs/`) when behaviour changes.

## Reporting bugs / vulnerabilities

* Functional bugs: open a GitHub issue using the bug template.
* Security issues: see `SECURITY.md` — report privately.

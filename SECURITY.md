# Security Policy

## Reporting a vulnerability

This is a preserved legacy learning project that is published as an
open-source demo. It is **not** running as a production service and does not
hold any real user data.

If you find a security issue, please report it privately:

1. Open a GitHub **Security Advisory** (Security tab → "Report a vulnerability"), or
2. Contact the maintainer through the repository's issue tracker without
   including exploit details in the public issue.

Please include the affected file/endpoint, a description, and reproduction
steps. We aim to acknowledge reports within a reasonable time.

## Scope and expectations

This project is a **demo/portfolio** codebase and should not be treated as
production-grade software. Known limitations:

* Several legacy endpoints predate modern security practices.
* The eFootball order flow stores a user-supplied in-game password in the
  database. This is preserved legacy behaviour; do not enter real credentials.
* Payment methods are **manual/offline only** — no real payment gateway is
  integrated.
* Only fictional demo data is shipped.

## What was fixed for the public release

* Hard-coded database and SMTP credentials removed; configuration is via `.env`.
* Hard-coded default administrator account removed from the source.
* SQL injection in the image-gallery and video upload endpoints removed
  (prepared statements).
* CSRF protection added to order status updates, deletions and cancellations.
* IDOR fixed: users can only cancel/update their own orders; the payment-proof
  endpoint verifies order ownership.
* File uploads hardened: allowlist, MIME sniffing, size limits, randomised
  filenames, no script uploads.
* Admin panels require an authenticated admin session.
* Email/OTP flows degrade gracefully when SMTP is not configured.

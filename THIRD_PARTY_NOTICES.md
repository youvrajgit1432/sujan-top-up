# Third-Party Notices

The MIT license in `LICENSE` covers the original Sujan Top-Up project code
only. The following third-party components remain under their own licenses and
are **not** owned by this project.

| Component | Use | License | Home |
|-----------|-----|---------|------|
| PHPMailer | SMTP email | LGPL-2.1 | https://github.com/PHPMailer/PHPMailer |
| Bootstrap | Front-end framework | MIT | https://getbootstrap.com |
| Bootstrap Icons | Icons | MIT | https://icons.getbootstrap.com |
| Font Awesome (Free) | Icons | CC BY 4.0 / SIL OFL / MIT | https://fontawesome.com |
| jQuery | DOM library | MIT | https://jquery.com |
| AOS (Animate On Scroll) | Scroll animations | MIT | https://michalsnik.github.io/aos/ |
| Swiper | Carousel | MIT | https://swiperjs.com |
| GLightbox | Lightbox | MIT | https://biati-digital.github.io/glightbox/ |
| AdminLTE | Admin theme | MIT | https://adminlte.io |
| Colorlib "Signup Form" template (`sign/`) | Auth UI | See `sign/readme.txt` | https://colorlib.com |

## Notes

* PHP dependencies are declared in `composer.json` and are not committed to the
  repository; run `composer install` to fetch them.
* Static front-end libraries are vendored under `assets/vendor/` and
  `admin/assets/` for offline/demo convenience. They are redistributed under
  their respective licenses.
* If you intend to reuse this project commercially, review each third-party
  license above, especially the Colorlib template terms.

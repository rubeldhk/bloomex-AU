# Bloomex Australia Platform

Bloomex Australia is a legacy Joomla-based e-commerce platform customised for order processing, merchandising, and systems integrations. The entry point bootstraps Joomla, sets up debugging utilities, and configures request handling for storefront operations. 【F:index.php†L1-L88】

## Repository Layout
- `components/`, `modules/`, `mambots/`, and `templates/` contain the Joomla extensions and presentation layers that power the catalogue, checkout, and marketing pages.
- `includes/` provides the Joomla framework bootstrap, routing, and debugging helpers used throughout the application lifecycle. 【F:index.php†L19-L87】
- `vendor/` hosts third-party libraries such as the EMS API SDK that complement the legacy Joomla stack. 【F:index.php†L83-L87】【F:vendor/autoload.php†L1-L18】【F:vendor/EmsApi/Config.php†L1-L52】
- `scripts/`, `cron/`, and `call*` directories provide operational tooling for data imports, scheduled jobs, and telephony integrations.

## Technical Specifications
- **Language & Framework:** PHP application leveraging Joomla core files (`includes/joomla.php`) and legacy global handling utilities (`globals.php`). 【F:index.php†L83-L86】【F:globals.php†L1-L78】
- **Runtime Requirements:** Requires a PHP runtime with support for legacy register globals emulation and compatibility with debugging extensions such as Krumo and Kint. 【F:index.php†L19-L46】【F:globals.php†L15-L78】
- **Autoloading & Dependencies:** Composer-style autoloading is invoked through `vendor/autoload.php`, enabling integration with packaged services like the EMS API client. 【F:index.php†L83-L87】
- **Routing:** Incoming requests are processed through `includes/router.php` and a custom SEF router (`newSef`) for clean URLs. 【F:index.php†L88-L94】
- **Debugging Controls:** Cookie-driven toggles allow enabling verbose error reporting by setting `tgn_debug`, which activates runtime diagnostics in development environments. 【F:index.php†L24-L47】

## Getting Started
1. Follow the configuration instructions in `config/deployment.example.ini` to define database, caching, and integration credentials for your environment.
2. Review the deployment guide (`docs/DEPLOYMENT.md`) for prerequisites, file permissions, and asset build steps.
3. Configure your web server to serve the repository root as the document root and ensure URL rewriting rules forward requests to `index.php`.

## Contributing
Given the age of the Joomla codebase, modern PHP best practices should be introduced incrementally. When contributing:
- Preserve backwards compatibility with Joomla 1.0-style globals unless refactoring the surrounding component.
- Add automated tests or smoke scripts when feasible to validate business-critical flows such as order placement and payment integrations.
- Document new scripts, cron jobs, or integrations within the deployment guide to keep operational knowledge up to date.

# Deployment Guide

This guide describes how to deploy the Bloomex Australia Joomla storefront across staging and production environments.

## 1. Prepare the Environment
1. Provision a Linux host with PHP support for legacy Joomla applications. The platform relies on register_globals emulation and runtime debugging toggles provided in `globals.php` and `index.php`. 【F:globals.php†L15-L78】【F:index.php†L19-L47】
2. Install PHP extensions commonly required by Joomla (mysqli, mbstring, curl, gd) and ensure the CLI version matches the web server runtime.
3. Configure MySQL or MariaDB and create an empty schema for storefront data. Credentials will be supplied through the deployment configuration file.
4. Ensure the web server user (e.g., `www-data`) can read and write to cache directories (`cache/`, `images_upload/`, `scripts/cron` outputs) for thumbnail generation and scheduled job logging.

## 2. Configure Application Settings
1. Copy `config/deployment.example.ini` to `config/deployment.ini` and edit the values for base URL, database credentials, caching, integrations, and security controls. 【F:config/deployment.example.ini†L1-L44】
2. Map these values into `configuration.php` or environment variables consumed by the Joomla bootstrap so that `index.php` can resolve absolute paths and load dependencies. 【F:index.php†L83-L94】
3. Update any credentials for downstream services such as EMS Marketing API keys, logistics webhooks, and email relays to match the target environment. 【F:config/deployment.example.ini†L25-L36】

## 3. Deploy Application Files
1. Check out the repository to the target path specified in the configuration (`absolute_path`). 【F:config/deployment.example.ini†L6-L11】
2. Sync the `images/`, `templates/`, and `vendor/` directories to ensure product assets and third-party libraries remain intact. 【F:index.php†L83-L87】【F:config/deployment.example.ini†L17-L23】
3. Review the `vendor/` folder for updates to packaged libraries like the EMS API client, which is loaded through the project autoloader. 【F:vendor/autoload.php†L1-L18】【F:vendor/EmsApi/Config.php†L1-L52】

## 4. Web Server Configuration
1. Point the virtual host document root to the repository root so all requests flow through `index.php`. 【F:index.php†L1-L94】
2. Enable URL rewriting to support the SEF router invoked in `includes/router.php`. 【F:index.php†L88-L94】
3. Configure HTTPS redirects and trusted IP restrictions in your reverse proxy or load balancer in line with the security settings defined in `config/deployment.ini`. 【F:config/deployment.example.ini†L37-L44】
4. Whitelist operational IPs that must toggle maintenance or debug flags. This aligns with the debugging cookies handled inside `index.php`. 【F:index.php†L24-L47】【F:config/deployment.example.ini†L37-L44】

## 5. Database Migration
1. Import the latest schema dump into the target database.
2. Run any outstanding Joomla migrations or custom SQL scripts located within `migration/` or `scripts/` directories.
3. Verify that core configuration tables reference the correct site URL and mail settings.

## 6. Scheduled Jobs and Integrations
1. Configure cron entries for scripts under `cron/`, `scripts/`, and integration directories such as `detrack_api/` and `gopeople_api/` to maintain data synchronisation. 【F:cron/cleanup_cron_queries.php†L1-L24】【F:scripts/api_orders.php†L1-L34】【F:detrack_api/push.php†L1-L26】【F:gopeople_api/gopeople.php†L1-L24】
2. Provide environment variables or `.ini` overrides for API keys defined in the deployment configuration.
3. Monitor logs in accordance with the paths defined in `config/deployment.ini` to catch integration errors early. 【F:config/deployment.example.ini†L19-L33】

## 7. Verification Checklist
1. Access the storefront homepage and confirm assets load without PHP warnings.
2. Place a test order or run smoke scripts to validate checkout flows and payment integrations.
3. Trigger at least one scheduled task manually to ensure credentials and network access are functioning as expected.

Document any deviations or environment-specific workarounds in this guide to keep operational knowledge current.

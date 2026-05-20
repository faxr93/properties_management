# Properties POC — Laravel 12 + PostgreSQL

A complete Proof-of-Concept property management web system showcasing:

- **Laravel 12** with PHP 8.3+ and **PostgreSQL** (works with vanilla PostgreSQL — **no PostGIS extension required**)
- **Polygon / GIS features** — every property has a GeoJSON `Polygon` boundary and `Point` location stored as native **JSONB** columns. Drawn interactively on the map with **Leaflet + Leaflet.draw**, rendered straight into the map. A GIN index on `boundary` enables fast JSONB lookups.
- A modern, professional **Tailwind UI** with a fixed sidebar, responsive layouts, Chart.js dashboard insights and OpenStreetMap tiles (no API key required).

## Modules

| Module | Description |
| ------ | ----------- |
| **Dashboard** | KPIs (total properties, active rentals, portfolio value, monthly rent revenue), a PostGIS-driven map of all polygons, valuation trend (6 months), mix-by-type breakdown, recent activity. |
| **Properties Info** | Searchable / filterable index, single-property detail with map, full CRUD with **interactive polygon drawing**. |
| **Properties Admin → Valuations** | Full CRUD on valuation records, multiple appraisal methods, valuer details, currency, notes. |
| **Properties Admin → Rentals** | Full CRUD on rental records, tenant info, lease period, monthly rent, deposit, payment cycle, status. |

---

## Quick Start

### 1. Start PostgreSQL

Any vanilla PostgreSQL 12+ works (EDB installer, Homebrew, Docker, RDS, etc.). Make sure the database in `.env` exists, or let `php artisan migrate` create it for you.

> The bundled `docker-compose.yml` also works if you don't have PostgreSQL locally: `docker compose up -d`.

### 2. Install PHP & JS dependencies (already done if you ran the bootstrap)

```bash
composer install
npm install
```

### 3. Generate app key (already set if `.env` was copied from `.env.example`)

```bash
php artisan key:generate
```

### 4. Migrate & seed sample data

```bash
php artisan migrate --seed
```

This will:
- Create `properties`, `property_valuations`, `property_rentals` tables with **JSONB GeoJSON columns** and a GIN index on `boundary`
- Seed 6 sample properties in Cyberjaya, Selangor (each with a polygon, point, valuations, and some active leases) plus an admin user

### 5. Build assets & start the app

```bash
npm run build          # or: npm run dev (for HMR while developing)
php artisan serve
```

Visit **http://127.0.0.1:8000** — you will be redirected to `/dashboard`.

---

## Architecture Notes

### Spatial Stack

- Polygons and points are stored as **GeoJSON in PostgreSQL JSONB columns** — `{ type: "Polygon", coordinates: [[[lng,lat],…]] }` and `{ type: "Point", coordinates: [lng,lat] }`. SRID 4326 (WGS84) is used by convention.
- Eloquent casts (`'boundary' => 'array'`) automatically marshal between the DB JSONB and PHP arrays.
- A **GIN index** on the `boundary` JSONB column accelerates path / containment queries.
- The controller passes the GeoJSON straight through to Leaflet — no conversion needed.
- This design keeps the dependency footprint minimal (no PostGIS) while preserving every front-end GIS capability. **Upgrade path**: when PostGIS is later available, the JSONB can be migrated to `geometry(Polygon, 4326)` via `ST_GeomFromGeoJSON` in a single migration.

### Frontend

- Tailwind CSS 3 with `@tailwindcss/forms` + `@tailwindcss/typography`, Inter font.
- Alpine.js for the sidebar / lightweight interactions.
- Leaflet + Leaflet.draw for interactive polygon authoring on the property form.
- Chart.js for the valuation-trend chart on the dashboard.

### Folder Map

```
app/
  Http/Controllers/
    DashboardController.php          # KPIs, map GeoJSON, charts
    PropertyController.php           # CRUD + polygon ingest / GeoJSON output
    PropertyValuationController.php
    PropertyRentalController.php
  Models/
    Property.php                     # Casts: location => array, boundary => array (GeoJSON)
    PropertyValuation.php
    PropertyRental.php

database/
  migrations/
    *_create_properties_table.php    # JSONB GeoJSON columns + GIN index
    *_create_property_valuations_table.php
    *_create_property_rentals_table.php
  seeders/
    PropertySeeder.php               # 5 sample properties with polygons

resources/
  css/app.css                        # Tailwind + Leaflet styles
  js/
    app.js                           # Bootstraps Alpine, Leaflet, modules
    modules/                         # Map / Chart initializers
  views/
    layouts/app.blade.php            # Sidebar + topbar layout
    dashboard.blade.php
    properties/{index,show,form}.blade.php
    admin/valuations/{index,form}.blade.php
    admin/rentals/{index,form}.blade.php
```

### How polygon drawing works

1. On the property form (`properties/form.blade.php`) Leaflet.draw exposes a polygon tool.
2. When the user finishes drawing, the GeoJSON `Polygon` geometry is serialized into a hidden field (`boundary`) and the centroid into `location`.
3. The controller decodes the GeoJSON string and stores it as a PHP array. The Eloquent `array` cast writes it to the JSONB column.
4. On the show / index pages, the GeoJSON is read straight out of JSONB and pushed to Leaflet which renders, colours, and binds popups per feature.

---

## Default Credentials

The seeder creates an `admin@example.com` user (no authentication is wired into the POC routes — feel free to add Laravel Breeze on top: `composer require laravel/breeze && php artisan breeze:install blade`).

---

## Reset everything

```bash
php artisan migrate:fresh --seed
```

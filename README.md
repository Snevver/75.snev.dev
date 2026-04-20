# 75 Hard Progress Tracker (PWA)

Production-ready Laravel + Vue Progressive Web App for tracking a 75 Hard challenge with daily/weekly tasks, notes, photos, public profile sharing, and admin tools.

## Stack

- Laravel 12 + PHP 8.2+ (project supports 8.3+)
- Vue 3 + Inertia + Tailwind CSS v4
- Vite + `@vitejs/plugin-vue` + `vite-plugin-pwa`
- MySQL
- Auth with Laravel Breeze (Vue/Inertia)

## Setup

1. Install dependencies:
   - `composer install`
   - `npm install`
2. Copy environment:
   - `cp .env.example .env`
3. Generate app key:
   - `php artisan key:generate`
4. Configure MySQL credentials in `.env`.
5. Run migrations and seed data:
   - `php artisan migrate:fresh --seed`
6. Start app:
   - `composer run dev`

## Demo Accounts

- Admin: `admin@example.com` / `password`
- User: `demo@example.com` / `password`

## Main Features

- User authentication, password reset, profile settings
- Timezone-aware day tracking
- Daily and weekly task completion
- Daily notes and secure photo uploads
- Monthly calendar overview + photo gallery
- Public profile URL at `/u/{username}`
- Admin panel for tasks, users, and global stats
- PWA support (manifest + service worker + offline route)

## Storage

- Progress photos are stored on the local private disk (`storage/app/private` via `local` disk root).
- Public avatars are stored on `public` disk.

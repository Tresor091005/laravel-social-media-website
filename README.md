# Laravel Social Media Website

This is a mini social media platform where users can:
- Create posts
- Follow other users
- Create and manage groups
- Upload and share files

---

## Local Installation and Setup

### 1. Prerequisites

- PHP >= 8.4
- Composer
- Node.js & NPM
- MySQL/SQLite
- Mailpit (optional for local email testing on Windows)

---

### 2. Quick Installation with Makefile

The project includes a `Makefile` to simplify setup and launching.

**To set up the project (install dependencies, configure, and migrate):**
```bash
make
```

**To launch the development server:**
```bash
make dev
```

**If `make` is not available or you encounter issues, you can run the commands manually:**

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install --legacy-peer-deps
npm run dev
```

---

### 3. Email Configuration

By default, all emails are logged to the log files.

**Windows + Mailpit:**
1. Download the Mailpit executable to `mailpit-windows-amd64/mailpit.exe`
2. Run the executable to capture emails locally
3. Access the web interface at: `http://localhost:8025`
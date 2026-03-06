# 🎬 Cinema Booking System

A fullstack cinema booking system built with:

- **Backend:** Laravel 11 (PHP 8.2)
- **Frontend:** Vue 3 + Vite
- **Database:** MySQL 8
- **Cache:** Redis
- **Web Server:** Nginx
- **Containerization:** Docker + Docker Compose

---

# 📦 Project Structure

```
cinema-booking
│
├── backend/          # Laravel API
├── frontend/         # Vue 3 application
├── docker/
│   └── nginx/
│       └── default.conf
│
├── nginx/
│   └── default.conf
│
├── docker-compose.yml
└── README.md
```

---

# ⚙️ Requirements

Before running the project you need to install:

- Docker
- Docker Compose
- Git

Check installation:

```bash
docker -v
docker compose version
```

---

# 🚀 Installation

Clone project

```bash
git clone <your-repository-url>
cd cinema-booking
```

Start docker containers

```bash
docker compose up -d --build
```

---

# 🐳 Docker Services

The system contains the following services:

| Service | Description | Port |
|------|------|------|
| backend | Laravel PHP-FPM | 9000 |
| frontend | Vue + Vite dev server | 5173 |
| nginx | Reverse proxy | 80 |
| mysql | Database | 3307 |
| redis | Cache | 6379 |

---

# 🌐 Access Application

Frontend

```
http://localhost:5173
```

Backend API (via nginx)

```
http://localhost
```

---

# 🗄 Database Connection

Use this configuration for **TablePlus / MySQL client**

```
Host: 127.0.0.1
Port: 3307
User: root
Password: root
Database: cinema
```

---

# 🔧 Laravel Setup

Enter backend container

```bash
docker exec -it cinema_booking_be_app bash
```

Install dependencies

```bash
composer install
```

Generate app key

```bash
php artisan key:generate
```

Run migrations

```bash
php artisan migrate
```

---

# 🖥 Frontend Setup

Enter frontend container

```bash
docker exec -it cinema_booking_fe_app sh
```

Install packages

```bash
npm install
```

Run dev server

```bash
npm run dev
```

---

# 📜 Useful Docker Commands

Start containers

```bash
docker compose up -d
```

Stop containers

```bash
docker compose down
```

Rebuild containers

```bash
docker compose up -d --build
```

View logs

```bash
docker logs cinema_booking_be_app
```

Enter container

```bash
docker exec -it cinema_booking_be_app bash
```

---

# 🧹 Git Ignore

Important files ignored:

```
backend/vendor
backend/node_modules
backend/.env

frontend/node_modules
frontend/dist

docker volumes
```

---

# 👨‍💻 Author

Developed by **Nguyen Thanh Duong (SugarDev)**

Tech stack:

- Laravel
- Vue 3
- Docker
- MySQL
- Redis

---

# 📄 License

This project is for learning and development purposes.
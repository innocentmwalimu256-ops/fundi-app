# FUNDI APP — Project Architecture & File Organization

Mfumo wa kisasa wa kuunganisha mafundi na wateja Tanzania (On-Demand Artisan Marketplace Platform).

---

## 📁 Muundo wa Mafaili ya Mfumo (Directory Organization)

Mfumo umegawanywa kwa nidhamu ya juu kulingana na viwango vya kimataifa vya Laravel MVC (**Model-View-Controller**), ambapo **Frontend** na **Backend** zimetenganishwa kwenye folda zake mahsusi:

```
fundi app/
├── 🎨 FRONTEND LAYER (Muonekano, Kurasa, CSS, na JS)
│   ├── resources/
│   │   ├── views/                         # Kurasa zote za Blade (HTML/UI)
│   │   │   ├── admin/                     # Kurasa zote za Admin (Dashboard, Subscriptions, Payments, Reports, Profile, n.k.)
│   │   │   ├── client/                    # Kurasa zote za Mteja (Dashboard, Tafuta Fundi, Maombi, Reviews, Chat)
│   │   │   ├── technician/                # Kurasa zote za Fundi (Dashboard, Maombi, Checkout, Mapato, Profile)
│   │   │   ├── auth/                      # Kurasa za Kuingia na Kujisajili (Login, Register, Password Reset)
│   │   │   ├── layouts/                   # Master Layouts (app.blade.php, admin.blade.php)
│   │   │   ├── notifications/             # Kurasa za Taarifa na Notifications
│   │   │   └── welcome.blade.php          # Ukurasa wa Kwanza wa Mfumo (Landing Page)
│   │   ├── css/                           # Tailwind CSS Configuration & Custom Styles
│   │   └── js/                            # Alpine.js & JavaScript Logic
│   └── public/                            # Picha za mfumo (Images, Icons, Uploaded Avatars)
│
├── ⚙️ BACKEND LAYER (Server Logic, Controllers, Services, APIs)
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/               # Controllers zote zinazochakata taarifa za mfumo
│   │   │   │   ├── AdminController.php
│   │   │   │   ├── ClientController.php
│   │   │   │   ├── TechnicianController.php
│   │   │   │   ├── SubscriptionController.php
│   │   │   │   ├── ServiceRequestController.php
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── MessageController.php
│   │   │   │   └── NotificationController.php
│   │   │   └── Middleware/                # Ulinzi wa kurasa (RoleMiddleware, SubscriptionMiddleware)
│   │   ├── Models/                        # Database Models (User, Subscription, ServiceRequest, Review, n.k.)
│   │   └── Services/                      # Business Logic (SubscriptionService, SmartMatchingService)
│   └── routes/
│       ├── web.php                        # Njia na Routes zote za mtandao
│       └── console.php                    # Scheduled Tasks & Cron Jobs
│
├── 🗄️ DATABASE LAYER (Muundo wa Database na Data za Mwanzo)
│   └── database/
│       ├── migrations/                    # Majedwali yote ya database (Users, Requests, Subscriptions, n.k.)
│       └── seeders/                       # Data za mfano na mwanzo (DatabaseSeeder.php)
│
└── 🧪 AUTOMATED TESTS
    └── tests/                             # Majaribio yote ya kiotomatiki (PHPUnit / Pest)
```

---

## 🚀 Jinsi ya Kuendesha Mfumo (Quick Start)

1. **Washa MySQL kwenye XAMPP**.
2. **Kwenye terminal ya mradi**, endesha:
   ```bash
   php artisan serve
   ```
3. Fungua browser kwenye: [`http://127.0.0.1:8000`](http://127.0.0.1:8000)

### Akaunti za Kuingilia (Demo Accounts):
- **Super Admin**: `admin@fundi.co.tz` / Nenosiri: `password`
- **Mteja (Client)**: `client@fundi.co.tz` / Nenosiri: `password`
- **Fundi (Technician)**: `fundi.umeme@fundi.co.tz` / Nenosiri: `password`

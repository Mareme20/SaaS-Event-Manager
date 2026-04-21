# SaaS Event Manager

[![Laravel](https://img.shields.io/badge/Laravel-11-red.svg)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3-green.svg)](https://vuejs.org)
[![Tailwind CSS](https://img.shields.io/badge/TailwindCSS-3-blue.svg)](https://tailwindcss.com)
[![Inertia.js](https://img.shields.io/badge/Inertia.js-TS-orange.svg)](https://inertiajs.com)
[![License](https://img.shields.io/badge/License-MIT-brightgreen.svg)](LICENSE)

## 📖 Description

**SaaS Event Manager** est une application web complète pour la gestion d'événements en mode SaaS (Software as a Service). Elle permet aux utilisateurs d'organiser des événements, de suivre les dépenses, contributions, membres et tâches associées, avec des vues publiques pour partager les événements. 

Fonctionnalités multi-utilisateurs avec rôles et permissions, interface moderne responsive (Vue 3 + Tailwind), génération de rapports PDF.

## ✨ Fonctionnalités principales

- 👥 **Authentification & Profils** : Inscription, connexion, gestion profil, vérification email, reset mot de passe.
- 📅 **Gestion d'événements** : Création, édition, suppression, vue publique partagée, support multi-devises.
- 💰 **Dépenses & Contributions** : Suivi financier des événements.
- 👨‍👩‍👧‍👦 **Membres** : Gestion des participants.
- ✅ **Tâches** : Listes de tâches par événement.
- 🖼️ **Médias** : Upload et gestion de fichiers/images.
- 📊 **Rapports PDF** : Génération de rapports d'événements.
- 🔐 **Rôles & Permissions** : Système avancé (Spatie Laravel Permission).
- 📱 **Interface moderne** : Dashboard, SPA avec Inertia.js + Vue 3 + Tailwind CSS.
- 🧪 **Tests** : Tests unitaires et fonctionnels inclus.
- 🚀 **SaaS-ready** : Prêt pour multi-tenancy (extensible).


| Eloquent ORM | TypeScript | Spatie Permissions | Migrations & Seeders |

Autres : Ziggy (routes JS), PHPUnit.

## 🚀 Installation rapide

### Prérequis
- PHP >= 8.2
- Composer
- Node.js + npm/yarn
- MySQL/PostgreSQL ou SQLite

- Git

```bash
# 1. Cloner le projet
git clone https://github.com/votre-repo/saas-event-manager.git
cd saas-event-manager

# 2. Installer dépendances PHP
composer install --optimize-autoloader --no-dev

# 3. Installer dépendances JS
npm install

# 4. Copier env
cp .env.example .env
php artisan key:generate

# 5. Configurer base de données (.env)
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=saas_event_manager
# DB_USERNAME=root
# DB_PASSWORD=

# 6. Migrer & Seeder (rôles + données exemples)
php artisan migrate --seed

# 7. Compiler assets
npm run build
# ou en dev: npm run dev

# 8. Lancer le serveur
php artisan serve

# 9. Ouvrir
# http://127.0.0.1:8000
# Login test: admin@example.com / password (créé par seeder)
```

## 📚 Utilisation

- **Dashboard** : `/dashboard` (auth requis).
- **Événements privés** : `/events` (liste/création).
- **Événement public** : `/events/{slug}`.
- **Profils** : `/profile`.
- **Admin rôles** : Via seeder ou interface.

Routes principales : Auth Breeze-like + custom controllers (Event, Expense, etc.).

## 🌐 Démo & Captures d'écran

(Ajoutez vos screenshots ici)!


## 🧪 Tests

```bash
composer test
# ou
php artisan test
./vendor/bin/phpunit
npm test
```

## 🤝 Contribution

1. Fork le projet.
2. Créer feature branch (`git checkout -b feature/nouvelle-fonction`).
3. Commit (`git commit -m 'Ajout: nouvelle fonction'`).
4. Push (`git push origin feature/nouvelle-fonction`).
5. Pull Request.

Respectez PSR-12, ajoutez tests.

## 📄 Licence

MIT License - voir [LICENSE](LICENSE).

## 🙏 Remerciements

Construit avec [Laravel](https://laravel.com), [Inertia.js](https://inertiajs.com), [Vue.js](https://vuejs.org).

---

⭐ Star ce repo si utile ! 👨‍💻

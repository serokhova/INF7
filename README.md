# ColocChaleureuse

> Plateforme de gestion de colocation : loyers, charges, tâches ménagères, messagerie et API REST.
> Réponse à l'appel d'offre **colocation.com** — projet de fin de cours INF7 (L3 MIAGE, 2026).

[![Symfony](https://img.shields.io/badge/Symfony-8.0-black?logo=symfony)](https://symfony.com)
[![PHP](https://img.shields.io/badge/PHP-8.4%2B-777BB4?logo=php)](https://www.php.net)
[![Tests](https://img.shields.io/badge/tests-21%20passing-brightgreen)]()
[![Lighthouse](https://img.shields.io/badge/Lighthouse-100%2F100-brightgreen)]()

---

## 🚀 Lancer le projet en local

### Pré-requis
- PHP **8.4+** (`php -v`)
- [Composer](https://getcomposer.org/)
- MySQL 8 — par défaut le projet vise **MAMP** sur `127.0.0.1:8889` (utilisateur `root` / mot de passe `root`).
  Si ta config diffère, modifie `DATABASE_URL` dans `.env` (ou crée un `.env.local`).
- (Optionnel) Node.js 18+ pour relancer l'audit Lighthouse.

### Installation (4 commandes)

```bash
# 1. Dépendances PHP
composer install

# 2. Créer la base + jouer les migrations
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate -n

# 3. Charger les données de démonstration
php bin/console doctrine:fixtures:load -n

# 4. Démarrer le serveur de développement
php -S 127.0.0.1:8000 -t public
```

Ouvre **http://127.0.0.1:8000** — tu es redirigé sur `/fr` (français par défaut).

### Comptes de démonstration

| Rôle           | Email                  | Mot de passe |
|----------------|------------------------|--------------|
| Propriétaire   | `owner@coloc.local`    | `owner123`   |
| Colocataire    | `sophie@coloc.local`   | `tenant123`  |
| Colocataire    | `julien@coloc.local`   | `tenant123`  |

### Lancer les tests

```bash
php bin/phpunit
```

Sortie attendue : `OK (21 tests, 84 assertions)`. Les tests créent automatiquement une base SQLite isolée dans `var/test.db`.

### Tester l'API REST

```bash
# Sans auth → 401
curl -i http://127.0.0.1:8000/fr/api/tasks

# Avec HTTP Basic Auth
curl -u "owner@coloc.local:owner123" http://127.0.0.1:8000/fr/api/tasks
curl -u "sophie@coloc.local:tenant123" http://127.0.0.1:8000/fr/api/chores/summary
curl -u "sophie@coloc.local:tenant123" http://127.0.0.1:8000/fr/api/household/info
```

---

## 📋 Fonctionnalités

Le projet couvre les **5 thématiques** du cahier des charges :

| # | Thématique | Routes principales |
|---|------------|-------------------|
| 1 | **Accueil** — présentation, FAQ, navigation | `/fr`, `/en` |
| 2 | **Espace locataire** — paiement loyer, quittances, tantième, messagerie | `/fr/tenant/*` |
| 3 | **Espace propriétaire** — budget recettes/dépenses (eau, électricité, internet, taxes), admin loyer + tantième, messagerie | `/fr/owner/*` |
| 4 | **Authentification** — login, inscription, changement / oubli de mot de passe | `/fr/login`, `/fr/register`, `/fr/forgot-password` |
| 5 | **Tâches ménagères** — semainier collaboratif (vaisselle, ménage, entretien) | `/fr/chores` |

**API REST** sécurisée (HTTP Basic) — données non sensibles :
- `GET /fr/api/tasks` — catalogue des tâches
- `GET /fr/api/chores/summary` — semainier de la semaine en cours
- `GET /fr/api/household/info` — informations du foyer de l'utilisateur

---

## 🧱 Stack technique

| Composant         | Choix                                |
|-------------------|--------------------------------------|
| Framework         | Symfony **8.0**                      |
| Langage           | PHP **8.4**                          |
| ORM               | Doctrine ORM 3                       |
| Base de données   | MySQL 8 (prod/dev), SQLite (test)    |
| Moteur de vues    | Twig                                 |
| Authentification  | Symfony Security (form login + HTTP Basic pour l'API) |
| Internationalisation | Symfony Translator — FR + EN      |

### Arborescence

```
INF7/
├── public/             # robots.txt, point d'entrée index.php
├── src/
│   ├── Controller/     # 8 contrôleurs (Home, Security, Registration,
│   │                   #   Tenant, Owner, Chore, Message, Sitemap, Api/*)
│   ├── Entity/         # 7 entités Doctrine
│   ├── Form/           # 8 FormTypes
│   ├── Repository/     # 6 repositories
│   └── DataFixtures/   # AppFixtures (données de démo)
├── templates/          # Vues Twig (base + 6 sections)
├── translations/       # messages.fr.yaml, messages.en.yaml
├── migrations/         # Migration Doctrine versionnée
├── tests/Controller/   # 6 fichiers de tests fonctionnels
├── config/             # packages/* + routes/* + bundles.php
└── README.md
```

---

## 🔐 Sécurité

- Mots de passe hashés (Argon/BCrypt, géré par Symfony)
- Protection CSRF sur tous les formulaires (stateless tokens)
- Contrôle d'accès par rôle (`ROLE_USER`, `ROLE_TENANT`, `ROLE_OWNER`) via `access_control` + `#[IsGranted]`
- Vérification d'ownership sur les ressources (un propriétaire ne peut éditer que ses propres foyers, un locataire ne voit que ses propres quittances)
- Token de réinitialisation de mot de passe à durée limitée (1h, à usage unique)
- API protégée par HTTP Basic Auth, séparée du firewall principal

---

## 🌱 Green IT & SEO

- Une seule requête HTTP par page (CSS inliné, **zéro JavaScript**, favicon en data-URI SVG)
- Architecture sobre, pas de polices distantes, pas d'image lourde
- Balises SEO complètes : `<title>`, `<meta description>`, `<link canonical>`, `<link alternate hreflang>` (FR/EN), `og:title`, `og:type`
- **`robots.txt`** + **`sitemap.xml`** (généré dynamiquement avec alternates `hreflang`)
- Données structurées **JSON-LD** (`WebApplication`) sur la page d'accueil
- Accessibilité : skip link, attribut `lang` localisé, contrastes WCAG AA, ordre hiérarchique des headings

### Scores Lighthouse (mode prod, headless Chrome)

| Page          | Performance | Accessibility | Best Practices | SEO |
|---------------|:-----------:|:-------------:|:--------------:|:---:|
| `/fr` (home)  | 100         | 100           | 100            | 100 |
| `/en` (home)  | 100         | 100           | 100            | 100 |
| `/fr/login`   | 100         | 100           | 100            | 100 |

Reproduire l'audit :

```bash
APP_ENV=prod APP_DEBUG=0 php -S 127.0.0.1:8000 -t public &
npx lighthouse http://127.0.0.1:8000/fr \
  --output=html --output-path=./lighthouse-report.html \
  --chrome-flags="--headless --no-sandbox" \
  --only-categories=performance,accessibility,best-practices,seo
```

---

## 🌍 Internationalisation

- Locales actives : **FR** (par défaut) et **EN**
- URLs localisées : `/{_locale}/...`
- Switch de langue dans le header (toutes les pages)
- `hreflang` exposé en `<head>` et dans le sitemap pour les moteurs de recherche

Ajouter une locale : créer `translations/messages.xx.yaml`, étendre la regex `_locale` dans `config/packages/security.yaml` et les contrôleurs.

---

## 🧪 Couverture des tests

Suite **PHPUnit / WebTestCase** — 21 tests, 84 assertions, ~1 seconde :

| Suite                          | Ce qui est couvert |
|--------------------------------|--------------------|
| `HomeControllerTest`           | Redirection root → `/fr`, rendu FR/EN, balises SEO (description, canonical, hreflang, JSON-LD), présence `robots.txt`, `/sitemap.xml` |
| `SecurityControllerTest`       | Pages login/register/forgot-password, oubli mot de passe (génération token), contrôle d'accès cross-rôle (403), redirection anonyme (302) |
| `TenantControllerTest`         | Dashboard, paiement de loyer (formulaire → persistance en DB → quittance) |
| `OwnerControllerTest`          | Dashboard, ajout de dépense (formulaire → persistance) |
| `ChoreControllerTest`          | Semainier (7 jours rendus), accès anonyme refusé |
| `Api/DashboardApiControllerTest` | 401 sans auth, 200 + JSON valide avec HTTP Basic |

---

## 📚 Ressources

- **Cahier des charges** : `15-Projet.pdf` (appel d'offre colocation.com)
- **Doc Symfony 8** : https://symfony.com/doc/current
- **Lighthouse** : https://developer.chrome.com/docs/lighthouse

---

## 📄 Licence

Projet pédagogique — INF7, L3 MIAGE, Université Paris.

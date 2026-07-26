# Vaccipha API — Module Vaccinateurs

API REST du système de gestion de vaccination **Vaccipha**, développée par **Enovpharm**. Ce dépôt couvre le module **Vaccinateurs** : gestion des agents terrain, suivi des commandes de vaccination, positions GPS, et catalogue de vaccins.

## 📋 À propos

Vaccipha est une plateforme de gestion de vaccination qui met en relation officines/institutions, agents vaccinateurs et patients. Ce module API permet aux applications mobiles des agents vaccinateurs de :

- S'authentifier et gérer leur compte
- Consulter et traiter leurs commandes de vaccination (en cours, terminées, transférées, annulées, reportées)
- Enregistrer leur position GPS en temps réel
- Consulter le catalogue de vaccins (PEV et hors PEV)
- Finaliser les commandes (paiement, vaccins administrés)

## 🛠️ Stack technique

| Composant | Technologie |
|---|---|
| Backend | PHP / CodeIgniter (REST_Controller) |
| Base de données | MySQL / MariaDB |
| Format d'échange | `application/x-www-form-urlencoded` (requêtes), JSON (réponses) |
| Documentation API | OpenAPI 3.0 (SwaggerHub) |

## 🔗 Documentation interactive

La documentation complète des endpoints (paramètres, réponses, exemples) est disponible sur SwaggerHub :

👉 **[Voir la documentation Vaccipha API](https://app.swaggerhub.com/apis/ABELYOHOU/Doc/1.0.0)**

Le fichier de spécification OpenAPI est aussi disponible dans ce dépôt : [`vaccipha-api-vaccinateurs.yaml`](./vaccipha-api-vaccinateurs.yaml).

## 🌐 URL de base

```
https://apismobile.vaccipha.net/index.php/api/
```

Tous les endpoints sont accessibles via `POST` ou `GET` selon la méthode, au format :
```
{URL_BASE}/Vaccinateurs/{nomDeLaMethode}
```

## 📦 Format de réponse

Toutes les réponses de l'API suivent une structure JSON commune, avec un code HTTP `200` systématique (le succès ou l'échec fonctionnel est indiqué par le champ `code`) :

```json
{
  "code": 1,
  "data": { },
  "msg": "Message destiné à l'utilisateur"
}
```

| Champ | Type | Description |
|---|---|---|
| `code` | integer | `1` = succès, `0` = échec fonctionnel |
| `data` | mixed | Contenu variable selon l'endpoint (objet, tableau, chaîne vide) |
| `msg` | string | Message à afficher à l'utilisateur |

## 📚 Principaux endpoints

### Authentification & compte
- `POST /Vaccinateurs/login` — Connexion d'un agent
- `POST /Vaccinateurs/majPassword` — Mise à jour du mot de passe (1ère connexion)
- `POST /Vaccinateurs/password` — Changement de mot de passe
- `POST /Vaccinateurs/reinitialize` — Réinitialisation du mot de passe (SMS/email)
- `POST /Vaccinateurs/getMonCompte` — Récupérer les infos du compte
- `POST /Vaccinateurs/majMonCompte` — Mettre à jour les infos du compte

### Gestion des commandes
- `POST /Vaccinateurs/commandeTraitement` — Démarrer le traitement d'une commande
- `POST /Vaccinateurs/FinaliserCommandes` — Finaliser une commande (paiement + vaccins)
- `POST /Vaccinateurs/transfererCommande` — Transférer une commande à un autre agent
- `POST /Vaccinateurs/reporterCommande` — Reporter une commande
- `POST /Vaccinateurs/commandeEffectuee` — Marquer une commande comme terminée
- `POST /Vaccinateurs/getListCommandes` — Lister les commandes sur une période
- `POST /Vaccinateurs/getAllCommandes` — Lister toutes les commandes d'un agent

### Positions GPS
- `POST /Vaccinateurs/insertPosition` — Enregistrer une position GPS
- `POST /Vaccinateurs/getPositionActive` — Récupérer la dernière position active
- `GET /Vaccinateurs/historiqueTrajet` — Historique des positions d'un trajet

### Catalogue de vaccins
- `GET /Vaccinateurs/getCatVaccins` — Catégories de vaccins actives
- `GET /Vaccinateurs/getCatVaccinsPev` / `getCatVaccinsHorsPev` — Catégories PEV / hors PEV
- `POST /Vaccinateurs/getListesSousVaccinsPevByCategories` — Sous-catégories par catégorie
- `POST /Vaccinateurs/getVaccinsBySousCategories` — Vaccins d'une sous-catégorie

> La liste complète des 40 endpoints du module (avec paramètres et exemples) est disponible dans la [documentation SwaggerHub](https://app.swaggerhub.com/apis/ABELYOHOU/Doc/1.0.0).

## 🔒 Sécurité

⚠️ **Avant toute publication ou déploiement**, vérifier qu'aucune information sensible n'est présente en dur dans le code (clés API, tokens d'autorisation, identifiants de services tiers). Ces éléments doivent être déplacés dans un fichier de configuration non versionné (voir `.gitignore`).

## 📁 Structure du projet

```
application/
├── controllers/
│   └── api/
│       ├── Auth.php
│       ├── Vaccinateurs.php
│       ├── Dossiers.php
│       └── AgentPosition.php
├── models/
│   ├── Auth_model.php
│   ├── Vaccinateurs_model.php
│   ├── Agent_position_model.php
│   ├── Dossiers_model.php
│   ├── Global_model.php
│   └── ...
└── libraries/
    ├── REST_Controller.php
    ├── Format.php
    └── mailjet.php
```

## 🚀 Installation locale

1. Cloner le dépôt
   ```bash
   git clone <url-du-depot>
   ```
2. Configurer la base de données dans `application/config/database.php`
3. Configurer les clés/tokens des services tiers (SMS, email) dans un fichier de configuration local (non versionné)
4. Importer le schéma de base de données MySQL/MariaDB
5. Démarrer le serveur (Apache/XAMPP ou équivalent)



---

*Ce README couvre le module Vaccinateurs. D'autres modules (Auth, Vaccins, Dash) existent dans le système global Vaccipha.*

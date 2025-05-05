# 🧰 PIDEV-3A7 - CyberStorm

LinkUp est une application innovante de gestion d’événements et de mise en relation sociale et professionnelle. Elle permet aux organisateurs, utilisateurs et recruteurs d’interagir à travers des fonctionnalités de réservation, de candidature et de matchmaking social.
**Année universitaire : 2024-2025**

---

## 📚 Table des Matières

- [🎯 Présentation](#-présentation)
-  [🎯 Objectifs](#-objectifs)
- [⚙️ Installation](#-installation)
- [💡 Utilisation](#-utilisation)
- [🤝 Contribution](#-contribution)
- [🛡️ Licence](#-licence)

---

## 🎯 Présentation

Ce projet a pour objectif de concevoir une application complète composée de multi clients synchronisés :

- 🌐 Une application **Web** développée avec **Symfony 6.4 **
- 🖥️ Une application **Desktop** en **JavaFX**

Ces deux applications interagissent avec une **base de données commune**, assurant ainsi la cohérence et la synchronisation des données utilisateurs.

---

### 🎯 Objectifs

-Concevoir une application web avec Symfony 4 en respectant l’architecture MVC, incluant à la fois une interface utilisateur et une interface d’administration.

-Structurer la logique métier selon les bonnes pratiques de la programmation orientée objet.

-Manipuler les données via l’ORM Doctrine pour gérer les modèles et leurs relations.

-Personnaliser l’affichage avec le moteur de templates Twig.

-Mettre en place et valider des formulaires complexes avec gestion des contraintes.

-Intégrer des bundles tiers pour enrichir l’application (notations, statistiques, intégration réseaux sociaux, etc.).

### 📆 Planning

![image](https://github.com/user-attachments/assets/7fad9fcc-bef9-46c3-ac91-c8d4bca79c3f)

### 📊 Répartition des séances

![image](https://github.com/user-attachments/assets/55489cd7-12b7-4055-bb1c-81ff98ddd0ae)

### 🙌 Contributions

Nous remercions chaleureusement toutes les personnes ayant contribué à ce projet :
Amal Boussahmine, Nour Berriche, Ahmed Yessine Ben Othmen, Feres Koumanji, Farah Hammemi

---

### 👥 Contributeurs

Farah Hammemi – Responsable du module de gestion des produits

Ahmed Yessine Ben Othmen – Responsable du module de gestion des candidatures

Feres Koumanji – Responsable de la gestion des utilisateurs

Nour Berriche – Responsable de la gestion des rencontres sociales

Amal boussahmine - Responsable de la gestion  des événements
  
 ---

### ⚠️ Contraintes techniques

Le projet se compose des modules suivants :

- Gestion des Utilisateurs

- Gestion des Candidatures

- Gestion des Rencontres Sociales

- Gestion des Services et Produits

- Gestion des Évènements

- Chaque module doit contenir **au minimum deux entités reliées par jointure**.
- Une **seule base de données** doit être utilisée pour tous les clients.
- ❌ Interdiction d’utiliser :
  - FOSUserBundle
  - AdminBundle

---

## ⚙️ Installation

1. **Cloner le repository**

```bash
git clone https://github.com/Hfarah1/linkupweb/tree/main
cd linkupweb

# 🧰 PIDEV-3A7 - CyberStorm

Projet de développement d’une application multi-clients (Web, JavaFX) avec une base de données partagée.
**Année universitaire : 2024-2025**

---

## 📚 Table des Matières

- [🎯 Présentation](#-présentation)
- [🚀 Sprint 1 – Développement Web](#-sprint-1--développement-web)
- [⚙️ Installation](#-installation)
- [💡 Utilisation](#-utilisation)
- [🤝 Contribution](#-contribution)
- [🛡️ Licence](#-licence)

---

## 🎯 Présentation

Ce projet a pour objectif de concevoir une application complète composée de trois clients synchronisés :

- 🌐 Une application **Web** développée avec **Symfony 4**
- 🖥️ Une application **Desktop** en **JavaFX**

Ces deux applications interagissent avec une **base de données commune**, assurant ainsi la cohérence et la synchronisation des données utilisateurs.

---

## 🚀 Sprint 1 – Développement Web

### 🎯 Objectifs

- Développer une application Web Symfony 4 (Front Office + Back Office) en architecture MVC.
- Organiser la couche métier selon les principes de la programmation orientée objet (POO).
- Utiliser Doctrine ORM pour la gestion des entités.
- Intégrer des templates TWIG.
- Gérer des formulaires complexes avec validation.
- Ajouter des bundles externes (notation, statistiques, partage sur les réseaux sociaux…).

### 📆 Planning

![Planning](planning.png)

### 📊 Répartition des séances

![Répartition](repartition.png)

### ⚠️ Contraintes techniques

- Chaque module doit contenir **au minimum deux entités reliées par jointure**.
- Une **seule base de données** doit être utilisée pour tous les clients.
- ❌ Interdiction d’utiliser :
  - FOSUserBundle
  - AdminBundle

---

## ⚙️ Installation

1. **Cloner le repository**

```bash
git clone https://github.com/TonCompte/TonProjet.git
cd TonProjet

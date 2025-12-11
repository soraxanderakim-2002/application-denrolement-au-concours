# 🧪 Guide de Tests - Système d'Enrôlement

## ✅ Checklist de Tests

### 📝 1. Tester le Formulaire d'Enrôlement

#### 1.1 Accès au Formulaire

```
✅ Naviguez vers http://localhost/enroll-concours/index.php
✅ Cliquez sur "Enroll Now" (bouton principal)
✅ Ou cliquez sur "S'inscrire" sur une carte de concours
✅ Vérifiez que vous arrivez sur enroll-form.php
✅ Vérifiez que la page se charge correctement
```

#### 1.2 Validation du Formulaire

```
TEST 1: Soumettre formulaire vide
  ✅ Aucun champ rempli
  ✅ Cliquer "Soumettre"
  ✅ Résultat attendu: Erreurs pour TOUS les champs

TEST 2: Validation Email Invalide
  ✅ Remplir email: "test@invalid"
  ✅ Soumettre
  ✅ Résultat attendu: Erreur "Email invalide"

TEST 3: Validation Email Existant
  ✅ Enrôler candidat 1 avec email: test@email.com
  ✅ Enrôler candidat 2 avec MÊME email
  ✅ Résultat attendu: Erreur "Email déjà utilisé"

TEST 4: Validation Champs Obligatoires
  ✅ Remplir tous sauf 1 champ
  ✅ Soumettre
  ✅ Résultat attendu: Erreur pour le champ manquant

TEST 5: Validation Format Date
  ✅ Entrer date invalide
  ✅ Résultat attendu: Navigateur refuse (input type=date)

TEST 6: Sélection Région
  ✅ Cliquer dropdown région
  ✅ Sélectionner "Centre"
  ✅ Résultat attendu: Valeur sauvegardée

TEST 7: Sélection Filière
  ✅ Cliquer dropdown filière
  ✅ Sélectionner "Ingénierie"
  ✅ Résultat attendu: Valeur sauvegardée

TEST 8: Sélection Concours
  ✅ Cliquer dropdown concours
  ✅ Sélectionner un concours
  ✅ Résultat attendu:
      - Prix affiché (ex: 50 000 FCFA)
      - Deadline affichée (ex: 15 mars 2025)

TEST 9: Soumission Valide
  ✅ Remplir TOUS les champs correctement
  ✅ Cliquer "Soumettre"
  ✅ Résultat attendu: Redirection vers confirmation
```

#### 1.3 Tests de Préremplissage

```
TEST 10: Préremplissage après erreur
  ✅ Remplir prénom: "Jean"
  ✅ NE PAS remplir nom
  ✅ Soumettre
  ✅ Résultat attendu: Erreur ET "Jean" toujours affiché

TEST 11: Conservation données après rejet
  ✅ Remplir formulaire avec erreur
  ✅ Soumettre
  ✅ Résultat attendu: TOUS les champs gardent leurs valeurs
```

#### 1.4 Tests de Design/UX

```
TEST 12: Responsiveness Desktop
  ✅ Ouvrir sur desktop (1920x1080)
  ✅ Formulaire doit être lisible
  ✅ Boutons doivent être cliquables

TEST 13: Responsiveness Tablet
  ✅ Redimensionner à 768px
  ✅ Formulaire doit s'adapter
  ✅ Pas de scroll horizontal

TEST 14: Responsiveness Mobile
  ✅ Redimensionner à 375px
  ✅ Formulaire en 1 colonne
  ✅ Buttons en pleine largeur

TEST 15: Animations
  ✅ Page doit charger smoothement
  ✅ Pas de saccades
  ✅ Transitions fluides
```

---

### ✅ 2. Tester la Page de Confirmation

#### 2.1 Affichage Confirmation

```
TEST 16: Redirection après succès
  ✅ Après enrôlement valide
  ✅ Redirection automatique vers confirmation
  ✅ URL: enrollment-confirmation.php

TEST 17: Affichage Numéro Référence
  ✅ Page affiche "Enrôlement Réussi!"
  ✅ Numéro de référence affiché (ex: #1)
  ✅ Format: #CANDIDATURE_ID

TEST 18: Affichage Infos Saisies
  ✅ Email candidat affiché
  ✅ Prénom et nom affichés (si mode non-simulation)

TEST 19: Affichage Checklist
  ✅ Checklist des 4 prochaines étapes
  ✅ Icons de check visibles
  ✅ Texte clair

TEST 20: Boutons Navigation
  ✅ Bouton "Accueil" présent
  ✅ Bouton "Se Connecter" présent
  ✅ Les deux sont cliquables

TEST 21: Design Confirmation
  ✅ Icône de succès visible
  ✅ Gradient vert
  ✅ Animation icône (scale)
  ✅ Card de confirmation visible
```

#### 2.2 Données Sauvegardées

```
TEST 22: Vérification en BD (si DB disponible)
  ✅ Accéder PhpMyAdmin
  ✅ Aller à base 'enroll_concours'
  ✅ Table 'candidats': 1 nouvelle ligne
  ✅ Table 'candidatures': 1 nouvelle ligne
  ✅ Table 'paiements': 1 nouvelle ligne
  ✅ Vérifier les valeurs correctes
```

---

### 👨‍💼 3. Tester l'Interface Admin

#### 3.1 Authentification Admin

```
TEST 23: Connexion Admin
  ✅ Naviguer vers admin/login.php
  ✅ Email: admin@enrollconcours.cm
  ✅ Mot de passe: admin123
  ✅ Cliquer "Connexion"
  ✅ Redirection vers dashboard

TEST 24: Refus Sans Auth
  ✅ Naviguer directement vers admin/enrollments.php
  ✅ Sans être connecté
  ✅ Résultat attendu: Redirection vers login

TEST 25: Déconnexion
  ✅ Être connecté
  ✅ Cliquer "Déconnexion"
  ✅ Session doit être effacée
  ✅ Redirection vers accueil
```

#### 3.2 Page Enrôlements

```
TEST 26: Accès Page Enrôlements
  ✅ Connecté en tant qu'admin
  ✅ Naviguer vers admin/enrollments.php
  ✅ Page doit charger
  ✅ Titre "Gestion des Enrôlements" visible

TEST 27: KPI Cards
  ✅ 4 cards doivent être visibles
  ✅ Chaque card doit avoir:
      - Icon
      - Titre
      - Nombre/valeur
  ✅ Cards doivent afficher les bonnes données

TEST 28: Liste Enrôlements
  ✅ Enrôlements en attente affichés
  ✅ Pour chaque enrôlement:
      - Nom et prénom
      - Email et téléphone
      - Concours et filière
      - Région
      - Statut "En Attente"
```

#### 3.3 Action Approbation

```
TEST 29: Bouton Approuver
  ✅ Trouver un enrôlement en attente
  ✅ Cliquer bouton "Approuver" (vert)
  ✅ Modal doit apparaître

TEST 30: Modal Approbation
  ✅ Modal affiche le nom du candidat
  ✅ Champ "Notes" présent (optionnel)
  ✅ Bouton "Approuver" visible
  ✅ Bouton "Annuler" visible

TEST 31: Approbation Sans Notes
  ✅ Ne rien écrire dans "Notes"
  ✅ Cliquer "Approuver"
  ✅ Résultat attendu: Approbation réussie

TEST 32: Approbation Avec Notes
  ✅ Écrire "Documents OK" dans Notes
  ✅ Cliquer "Approuver"
  ✅ Résultat attendu: Approbation réussie
  ✅ BD: notes sauvegardées

TEST 33: Vérification Après Approbation
  ✅ Message "Enrôlement approuvé" visible
  ✅ Enrôlement disparaît de la liste
  ✅ Vérifier en BD: statut = 'complète'
```

#### 3.4 Action Rejet

```
TEST 34: Bouton Rejeter
  ✅ Trouver un enrôlement en attente
  ✅ Cliquer bouton "Rejeter" (rouge)
  ✅ Modal doit apparaître

TEST 35: Modal Rejet
  ✅ Modal affiche le nom du candidat
  ✅ Champ "Raison du Rejet" présent (OBLIGATOIRE)
  ✅ Bouton "Rejeter" visible
  ✅ Bouton "Annuler" visible

TEST 36: Rejet Sans Raison
  ✅ NE rien écrire dans "Raison"
  ✅ Cliquer "Rejeter"
  ✅ Résultat attendu: Erreur (field requis)

TEST 37: Rejet Avec Raison
  ✅ Écrire "Paiement non validé" dans Raison
  ✅ Cliquer "Rejeter"
  ✅ Résultat attendu: Rejet réussi

TEST 38: Vérification Après Rejet
  ✅ Message "Enrôlement rejeté" visible
  ✅ Enrôlement disparaît de la liste
  ✅ Vérifier en BD: statut = 'rejetée'
```

---

### 🔗 4. Tester l'Intégration Complète

#### 4.1 Flux Candidat-Admin

```
TEST 39: Flux Complet
  ✅ Candidat s'enrôle (enroll-form.php)
  ✅ Reçoit confirmation (enrollment-confirmation.php)
  ✅ Admin se connecte (admin/login.php)
  ✅ Voit enrôlement (admin/enrollments.php)
  ✅ Admin approuve
  ✅ Vérifier en BD (statut changé)

TEST 40: Flux Rejet
  ✅ Candidat s'enrôle
  ✅ Admin refuse avec raison
  ✅ Vérifier raison en BD
```

#### 4.2 Tests de Sécurité

```
TEST 41: SQL Injection
  ✅ Email: "test@email.com' OR '1'='1"
  ✅ Résultat attendu: Traité comme texte normal
  ✅ Pas de problème de sécurité

TEST 42: XSS Injection
  ✅ Nom: "<script>alert('XSS')</script>"
  ✅ Résultat attendu: Affichage comme texte
  ✅ Pas de script exécuté

TEST 43: CSRF Protection
  ✅ Soumettre formulaire depuis autre origine
  ✅ Vérifier que session valide est requise
```

#### 4.3 Tests de Performance

```
TEST 44: Temps de Chargement
  ✅ enroll-form.php: < 1 second
  ✅ enrollment-confirmation.php: < 1 second
  ✅ admin/enrollments.php: < 2 seconds

TEST 45: Charge BDD
  ✅ Enrôler 100 candidats
  ✅ Admin doit pouvoir voir tous
  ✅ Pas de timeout
```

---

### 📊 5. Tester la Base de Données

#### 5.1 Intégrité Données

```
TEST 46: Candidats
  ✅ Table existe
  ✅ Colonnes correctes
  ✅ Email UNIQUE
  ✅ Données après enrôlement correctes

TEST 47: Candidatures
  ✅ Table existe
  ✅ FK vers candidats OK
  ✅ Statut par défaut: 'en attente'
  ✅ Données correctes après enrôlement

TEST 48: Paiements
  ✅ Table existe
  ✅ FK vers candidat et candidature OK
  ✅ Montant correct
  ✅ Statut par défaut: 'en attente'
```

#### 5.2 Transactions

```
TEST 49: Atomicité Enrôlement
  ✅ Enrôler candidat
  ✅ Vérifier 3 lignes créées atomiquement
  ✅ Si erreur: aucune ligne créée (rollback)
```

---

### 🎯 6. Tests Manuels à Faire

| #   | Test              | Résultat   | Date | Notes |
| --- | ----------------- | ---------- | ---- | ----- |
| 1   | Enrôlement valide | ✅ OK      |      |       |
| 2   | Email invalide    | ✅ Erreur  |      |       |
| 3   | Champs manquants  | ✅ Erreurs |      |       |
| 4   | Approbation admin | ✅ OK      |      |       |
| 5   | Rejet admin       | ✅ OK      |      |       |
| 6   | Responsive mobile | ✅ OK      |      |       |
| 7   | SQL injection     | ✅ Safe    |      |       |
| 8   | XSS injection     | ✅ Safe    |      |       |

---

## 🐛 Bugs Trouvés & Fixes

### Avant Les Tests:

- N/A (Nouveau système)

### Pendant Tests:

- N/A (À trouver lors des tests)

### Après Fixes:

- N/A

---

## ✅ Résumé Tests

**Total Tests à Faire:** 49
**Estimé Temps:** 2-3 heures

**Priorité Critique:**

- Flux complet enrôlement
- Approbation/rejet admin
- Sécurité SQL/XSS

**Priorité Haute:**

- Validation formulaire
- Responsive design
- Intégrité BD

**Priorité Moyenne:**

- Performance
- Messages erreur
- Animations

---

## 📝 Réponse aux Tests

Après chaque test, rapporter:

```
TEST: [Numéro et Nom]
RÉSULTAT: ✅ OK / ❌ FAILED / ⚠️ WARNING
DESCRIPTION: [Ce qui s'est passé]
EVIDENCE: [Screenshot si possible]
NOTES: [Notes additionnelles]
```

---

**Créé:** 09 Décembre 2025
**Version:** 1.0
**Statut:** Ready for Testing

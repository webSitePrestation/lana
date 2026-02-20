# 📧 Configuration Email IONOS - Guide Complet

## 📁 Fichiers fournis

- **send-reservation.php** → Script PHP qui envoie les emails
- **reservation.html** → Page avec formulaire (déjà modifiée pour utiliser send-reservation.php)

---

## ⚙️ Configuration IONOS

### 1️⃣ Ouvrez `send-reservation.php` et modifiez les lignes 22-29 :

```php
// ═══════════════════════════════════════════════════════════════════
// CONFIGURATION IONOS - À PERSONNALISER
// ═══════════════════════════════════════════════════════════════════

// Remplacez par votre email IONOS
$IONOS_EMAIL = 'votre-email@votre-domaine.com';
$IONOS_PASSWORD = 'votre-mot-de-passe';

// Serveur SMTP IONOS
$SMTP_HOST = 'smtp.ionos.fr';  // ou smtp.ionos.com selon votre pays
$SMTP_PORT = 587;               // 587 (TLS) ou 465 (SSL)
$SMTP_SECURE = 'tls';           // 'tls' ou 'ssl'

// Email de destination (où vous recevrez les réservations)
$EMAIL_DESTINATAIRE = 'votre-email@votre-domaine.com';
```

### 📝 Exemple de configuration :

```php
$IONOS_EMAIL = 'contact@maitresselana.fr';
$IONOS_PASSWORD = 'MonMotDePasse123!';
$SMTP_HOST = 'smtp.ionos.fr';
$SMTP_PORT = 587;
$SMTP_SECURE = 'tls';
$EMAIL_DESTINATAIRE = 'contact@maitresselana.fr';
```

---

## 🔧 Paramètres SMTP IONOS selon votre pays

| Pays | Serveur SMTP | Port |
|------|--------------|------|
| 🇫🇷 France | smtp.ionos.fr | 587 (TLS) ou 465 (SSL) |
| 🇩🇪 Allemagne | smtp.ionos.de | 587 (TLS) ou 465 (SSL) |
| 🇬🇧 UK | smtp.ionos.co.uk | 587 (TLS) ou 465 (SSL) |
| 🇺🇸 USA | smtp.ionos.com | 587 (TLS) ou 465 (SSL) |
| 🇪🇸 Espagne | smtp.ionos.es | 587 (TLS) ou 465 (SSL) |

**Si ça ne marche pas avec le port 587, essayez le port 465 avec `$SMTP_SECURE = 'ssl';`**

---

## 📦 Installation de PHPMailer

### Option A : Avec Composer (recommandé)

Dans le dossier racine de votre site :

```bash
composer require phpmailer/phpmailer
```

### Option B : Sans Composer

1. Téléchargez PHPMailer : https://github.com/PHPMailer/PHPMailer/archive/master.zip
2. Décompressez l'archive
3. Créez ce dossier : `vendor/phpmailer/phpmailer/`
4. Copiez le contenu de `PHPMailer-master/src/` dans ce dossier

**Structure finale :**
```
votre-site/
├── send-reservation.php
├── reservation.html
└── vendor/
    └── phpmailer/
        └── phpmailer/
            └── src/
                ├── PHPMailer.php
                ├── SMTP.php
                └── Exception.php
```

---

## 🧪 Test de l'envoi d'email

Créez un fichier `test-email.php` à la racine :

```php
<?php
// Test rapide de l'envoi d'email

// Simuler les données POST
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'prenom' => 'Test',
    'email' => 'test@example.com',
    'telephone' => '0123456789',
    'type_seance' => 'Réel (présentiel)',
    'experience' => 'Débutant(e)',
    'disponibilites' => 'Semaine, soirs',
    'pratiques' => 'Test de pratiques',
    'limites' => 'Test de limites',
    'message' => 'Ceci est un test',
    'rgpd' => 'on'
];

// Inclure le script
include 'send-reservation.php';
?>
```

Puis dans votre navigateur : `https://votre-domaine.com/test-email.php`

**⚠️ N'oubliez pas de supprimer ce fichier après le test !**

---

## 📂 Structure finale de votre site

```
votre-site/
├── index.html
├── apropos.html
├── reservation.html          ← Formulaire (déjà modifié)
├── send-reservation.php      ← Script d'envoi (à configurer)
├── styles.css
├── script.js
├── vendor/
│   └── phpmailer/...         ← PHPMailer
└── logs/                     ← Créé automatiquement
    └── errors.log
```

---

## ❌ Dépannage

### Problème : "Class 'PHPMailer\PHPMailer\PHPMailer' not found"
**Solution :** PHPMailer n'est pas installé. Suivez l'étape "Installation de PHPMailer".

### Problème : "SMTP connect() failed"
**Solutions :**
1. Vérifiez que le serveur SMTP est correct (`smtp.ionos.fr` pour la France)
2. Essayez le port 465 avec SSL : `$SMTP_PORT = 465; $SMTP_SECURE = 'ssl';`
3. Vérifiez que votre hébergement autorise les connexions SMTP sortantes

### Problème : "Could not authenticate"
**Solutions :**
1. Vérifiez votre email et mot de passe IONOS
2. Assurez-vous d'utiliser votre email IONOS (pas un autre)
3. Désactivez temporairement l'authentification 2FA si activée

### Problème : Email non reçu
**Solutions :**
1. Vérifiez vos spams/courrier indésirable
2. Vérifiez que `$EMAIL_DESTINATAIRE` est correct
3. Regardez le fichier `logs/errors.log` pour les erreurs

### Problème : "530 Authentication required"
**Solution :** L'adresse `$IONOS_EMAIL` (FROM) doit être une adresse email IONOS valide.

---

## 🔒 Sécurité en production

Une fois que tout fonctionne, dans `send-reservation.php` ligne 8 :

```php
ini_set('display_errors', 0); // ← Assurez-vous que c'est à 0
```

---

## ✅ Modifications du formulaire HTML

**✅ Le formulaire a déjà été modifié automatiquement.**

Le fichier `reservation.html` pointe maintenant vers `send-reservation.php` au lieu de `reservation-handler.php`.

**Aucune autre modification n'est nécessaire !**

---

## 💡 Conseils

- Testez d'abord en local avant de mettre en ligne
- Gardez une copie de votre mot de passe IONOS en sécurité
- Le fichier `logs/errors.log` enregistre les erreurs PHP
- En cas de problème, contactez le support IONOS pour vérifier que SMTP est bien activé sur votre hébergement

---

## 📞 Support

Si vous avez des questions sur la configuration SMTP, contactez le support IONOS :
- France : https://www.ionos.fr/assistance
- Téléphone : Disponible dans votre espace client IONOS

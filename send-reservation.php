<?php
/**
 * Formulaire de réservation - Compatible IONOS
 * Utilise PHPMailer pour envoyer les emails
 */

// Activer les erreurs en développement (désactiver en production)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Mettre à 0 en production
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/errors.log');

header('Content-Type: application/json; charset=UTF-8');

// Vérifier que c'est bien une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

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

// ═══════════════════════════════════════════════════════════════════

// Charger PHPMailer
require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ───────────────────────────────────────────────────────────────────
// Fonction de nettoyage des données
// ───────────────────────────────────────────────────────────────────
function cleanInput($data) {
    if (is_null($data)) return '';
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// ───────────────────────────────────────────────────────────────────
// Récupération et validation des données
// ───────────────────────────────────────────────────────────────────
$errors = [];

$prenom         = cleanInput($_POST['prenom'] ?? '');
$email          = cleanInput($_POST['email'] ?? '');
$telephone      = cleanInput($_POST['telephone'] ?? '');
$type_seance    = cleanInput($_POST['type_seance'] ?? '');
$experience     = cleanInput($_POST['experience'] ?? '');
$disponibilites = cleanInput($_POST['disponibilites'] ?? '');
$pratiques      = cleanInput($_POST['pratiques'] ?? '');
$limites        = cleanInput($_POST['limites'] ?? '');
$message        = cleanInput($_POST['message'] ?? '');
$rgpd           = isset($_POST['rgpd']);

// Validation
if (empty($prenom)) {
    $errors[] = 'Le prénom est obligatoire';
}

if (empty($email)) {
    $errors[] = "L'email est obligatoire";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "L'email n'est pas valide";
}

if (empty($type_seance)) {
    $errors[] = 'Le type de séance est obligatoire';
}

if (empty($experience)) {
    $errors[] = "Le niveau d'expérience est obligatoire";
}

if (empty($disponibilites)) {
    $errors[] = 'Les disponibilités sont obligatoires';
}

if (empty($pratiques)) {
    $errors[] = 'Les pratiques souhaitées sont obligatoires';
}

if (!$rgpd) {
    $errors[] = 'Vous devez accepter les conditions RGPD';
}

// Si erreurs, retourner
if (!empty($errors)) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => implode('. ', $errors)
    ]);
    exit;
}

// ───────────────────────────────────────────────────────────────────
// Préparation du contenu de l'email
// ───────────────────────────────────────────────────────────────────

$sujet = "🔗 Nouvelle demande de réservation - " . $prenom;

// Version HTML de l'email
$corpsHTML = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; background: #0a0a0a; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #1a1a1a; border-radius: 8px; overflow: hidden; }
        .header { background: linear-gradient(135deg, #8b0000 0%, #4a0000 100%); color: #d4af37; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; text-transform: uppercase; letter-spacing: 3px; }
        .badge { display: inline-block; background: #8b0000; color: #d4af37; padding: 8px 20px; border-radius: 20px; margin-top: 15px; font-size: 14px; letter-spacing: 1px; }
        .section { background: #222; padding: 25px; margin: 15px; border-left: 3px solid #8b0000; }
        .section h2 { color: #d4af37; font-size: 16px; text-transform: uppercase; letter-spacing: 2px; margin: 0 0 15px 0; }
        .field { margin-bottom: 15px; }
        .label { color: #d4af37; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; font-weight: bold; }
        .value { color: #ccc; margin-top: 5px; line-height: 1.6; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>⛓ Nouvelle Réservation</h1>
            <div class='badge'>" . $type_seance . "</div>
        </div>

        <div class='section'>
            <h2>👤 Identité</h2>
            <div class='field'>
                <div class='label'>Prénom</div>
                <div class='value'>" . $prenom . "</div>
            </div>
            <div class='field'>
                <div class='label'>Email</div>
                <div class='value'>" . $email . "</div>
            </div>
            <div class='field'>
                <div class='label'>Téléphone</div>
                <div class='value'>" . ($telephone ?: 'Non renseigné') . "</div>
            </div>
            <div class='field'>
                <div class='label'>Expérience</div>
                <div class='value'>" . $experience . "</div>
            </div>
        </div>

        <div class='section'>
            <h2>📅 Disponibilités</h2>
            <div class='value'>" . nl2br($disponibilites) . "</div>
        </div>

        <div class='section'>
            <h2>🔗 Pratiques souhaitées</h2>
            <div class='value'>" . nl2br($pratiques) . "</div>
        </div>

        " . (!empty($limites) ? "
        <div class='section'>
            <h2>🚫 Limites strictes</h2>
            <div class='value'>" . nl2br($limites) . "</div>
        </div>
        " : "") . "

        " . (!empty($message) ? "
        <div class='section'>
            <h2>💬 Message</h2>
            <div class='value'>" . nl2br($message) . "</div>
        </div>
        " : "") . "

        <div class='footer'>
            Reçu le " . date('d/m/Y à H:i') . "
        </div>
    </div>
</body>
</html>";

// Version texte brut de l'email
$corpsTexte = "
NOUVELLE DEMANDE DE RÉSERVATION
================================

TYPE DE SÉANCE : $type_seance

=== IDENTITÉ ===
Prénom      : $prenom
Email       : $email
Téléphone   : " . ($telephone ?: 'Non renseigné') . "
Expérience  : $experience

=== DISPONIBILITÉS ===
$disponibilites

=== PRATIQUES SOUHAITÉES ===
$pratiques
" . (!empty($limites) ? "

=== LIMITES STRICTES ===
$limites" : "") . (!empty($message) ? "

=== MESSAGE ===
$message" : "") . "

Reçu le " . date('d/m/Y à H:i') . "
";

// ───────────────────────────────────────────────────────────────────
// Envoi de l'email avec PHPMailer
// ───────────────────────────────────────────────────────────────────

$mail = new PHPMailer(true);

try {
    // Configuration du serveur SMTP IONOS
    $mail->isSMTP();
    $mail->Host       = $SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = $IONOS_EMAIL;
    $mail->Password   = $IONOS_PASSWORD;
    $mail->SMTPSecure = $SMTP_SECURE;
    $mail->Port       = $SMTP_PORT;
    $mail->CharSet    = 'UTF-8';

    // Expéditeur (DOIT être votre email IONOS)
    $mail->setFrom($IONOS_EMAIL, 'Maîtresse Lana - Réservations');
    
    // Répondre à (email du visiteur)
    $mail->addReplyTo($email, $prenom);

    // Destinataire
    $mail->addAddress($EMAIL_DESTINATAIRE);

    // Contenu
    $mail->isHTML(true);
    $mail->Subject = $sujet;
    $mail->Body    = $corpsHTML;
    $mail->AltBody = $corpsTexte;

    // Envoi
    $mail->send();

    // Succès
    echo json_encode([
        'success' => true,
        'message' => 'Votre demande a bien été envoyée. Je vous répondrai sous 24 heures.'
    ]);

} catch (Exception $e) {
    // Erreur
    error_log("Erreur PHPMailer: " . $mail->ErrorInfo);
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => "Une erreur est survenue lors de l'envoi. Veuillez réessayer ou me contacter directement."
    ]);
}
?>

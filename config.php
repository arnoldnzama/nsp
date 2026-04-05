<?php
/**
 * Configuration du système d'envoi d'email
 * New Strong Protection
 */

// Configuration de l'email destinataire
define('EMAIL_TO', 'admin@nsp.cd');
define('EMAIL_FROM', 'noreply@forceone.cd');
define('EMAIL_FROM_NAME', 'New Strong Protection');

// Configuration du site
define('SITE_NAME', 'New Strong Protection');
define('SITE_URL', 'https://forceone.cd');
define('SITE_PHONE', '(+243) 898244292 - 81720338');
define('SITE_ADDRESS', 'Haut Uéle/ISIRO - République Démocratique du Congo');

// Configuration de l'email
define('EMAIL_CHARSET', 'UTF-8');
define('EMAIL_PRIORITY', '1'); // 1 = High, 3 = Normal, 5 = Low

// Activer/désactiver l'email de confirmation au client
define('SEND_CONFIRMATION_EMAIL', true);

// Timezone
date_default_timezone_set('Africa/Kinshasa');

// Messages de réponse
define('SUCCESS_MESSAGE', 'Votre demande a été envoyée avec succès !');
define('ERROR_MESSAGE', 'Une erreur est survenue lors de l\'envoi. Veuillez réessayer.');
define('VALIDATION_ERROR', 'Veuillez remplir tous les champs obligatoires.');

// Sécurité - Domaines autorisés pour CORS (optionnel)
$allowedOrigins = [
    'https://forceone.cd',
    'http://localhost',
    'http://127.0.0.1'
];

// Activer le mode debug (à désactiver en production)
define('DEBUG_MODE', false);

?>

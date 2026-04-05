<?php
// Charger la configuration
require_once 'config.php';

// Configuration de sécurité
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Fonction de log pour le debug
function debugLog($message) {
    if (DEBUG_MODE) {
        error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, 'email_debug.log');
    }
}

// Vérifier que c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    debugLog('Erreur: Méthode non autorisée');
    exit;
}

// Récupérer les données JSON
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Validation des données
$errors = [];

if (empty($data['prenom'])) {
    $errors[] = 'Le prénom est requis';
}
if (empty($data['nom'])) {
    $errors[] = 'Le nom est requis';
}
if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Email invalide';
}
if (empty($data['telephone'])) {
    $errors[] = 'Le téléphone est requis';
}
if (empty($data['service'])) {
    $errors[] = 'Le service est requis';
}
if (empty($data['message'])) {
    $errors[] = 'Le message est requis';
}

// Si des erreurs existent
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

// Nettoyer les données
$prenom = htmlspecialchars(strip_tags($data['prenom']));
$nom = htmlspecialchars(strip_tags($data['nom']));
$entreprise = htmlspecialchars(strip_tags($data['entreprise'] ?? 'Non spécifié'));
$email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
$telephone = htmlspecialchars(strip_tags($data['telephone']));
$service = htmlspecialchars(strip_tags($data['service']));
$message = htmlspecialchars(strip_tags($data['message']));

// Configuration de l'email
$to = EMAIL_TO;
$subject = "Demande de devis - $service - $nom";

debugLog("Préparation de l'email pour: $to");

// Corps de l'email en HTML
$emailBody = "
<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #000000, #1a1a1a);
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .header p {
            margin: 10px 0 0;
            font-size: 14px;
            color: #eab308;
        }
        .content {
            padding: 30px;
        }
        .section {
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }
        .section:last-child {
            border-bottom: none;
        }
        .section-title {
            color: #000000;
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
            padding: 8px 0;
        }
        .info-label {
            font-weight: 600;
            color: #666;
            min-width: 120px;
        }
        .info-value {
            color: #333;
            flex: 1;
        }
        .message-box {
            background: #f9fafb;
            border-left: 4px solid #eab308;
            padding: 15px;
            border-radius: 5px;
            margin-top: 10px;
        }
        .footer {
            background: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .badge {
            display: inline-block;
            background: #eab308;
            color: #000;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 10px;
        }
        .highlight {
            color: #eab308;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>🛡️ New Strong Protection</h1>
            <p>Nouvelle Demande de Devis</p>
        </div>
        
        <div class='content'>
            <div class='section'>
                <div class='section-title'>📋 Informations du Client</div>
                <div class='info-row'>
                    <div class='info-label'>Nom complet:</div>
                    <div class='info-value'><strong>$prenom $nom</strong></div>
                </div>
                <div class='info-row'>
                    <div class='info-label'>Entreprise:</div>
                    <div class='info-value'>$entreprise</div>
                </div>
                <div class='info-row'>
                    <div class='info-label'>Email:</div>
                    <div class='info-value'><a href='mailto:$email' style='color: #eab308; text-decoration: none;'>$email</a></div>
                </div>
                <div class='info-row'>
                    <div class='info-label'>Téléphone:</div>
                    <div class='info-value'><a href='tel:$telephone' style='color: #eab308; text-decoration: none;'>$telephone</a></div>
                </div>
            </div>
            
            <div class='section'>
                <div class='section-title'>🔒 Service Demandé</div>
                <div class='badge'>$service</div>
            </div>
            
            <div class='section'>
                <div class='section-title'>💬 Message du Client</div>
                <div class='message-box'>$message</div>
            </div>
            
            <div class='section'>
                <div class='section-title'>⏰ Informations de Soumission</div>
                <div class='info-row'>
                    <div class='info-label'>Date:</div>
                    <div class='info-value'>" . date('d/m/Y à H:i:s') . "</div>
                </div>
                <div class='info-row'>
                    <div class='info-label'>Source:</div>
                    <div class='info-value'>Formulaire de contact - forceone.cd</div>
                </div>
            </div>
        </div>
        
        <div class='footer'>
            <p><strong>New Strong Protection</strong> - La sécurité qui rassure</p>
            <p>Haut Uéle/ISIRO - République Démocratique du Congo</p>
            <p style='margin-top: 10px;'>
                📧 admin@nsp.cd | 📞 (+243) 898244292 - 81720338
            </p>
        </div>
    </div>
</body>
</html>
";

// Version texte brut pour les clients email qui ne supportent pas HTML
$textBody = "
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
NOUVELLE DEMANDE DE DEVIS - New Strong Protection
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

INFORMATIONS DU CLIENT:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Nom complet: $prenom $nom
Entreprise: $entreprise
Email: $email
Téléphone: $telephone

SERVICE DEMANDÉ:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
$service

MESSAGE:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
$message

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Date: " . date('d/m/Y à H:i:s') . "
Source: Formulaire de contact - forceone.cd
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
";

// Headers de l'email
$headers = [
    'MIME-Version: 1.0',
    'Content-Type: text/html; charset=' . EMAIL_CHARSET,
    'From: ' . EMAIL_FROM_NAME . ' <' . EMAIL_FROM . '>',
    'Reply-To: ' . $email,
    'X-Mailer: PHP/' . phpversion(),
    'X-Priority: ' . EMAIL_PRIORITY,
    'Importance: High'
];

// Envoi de l'email
debugLog("Envoi de l'email principal...");
$mailSent = mail($to, $subject, $emailBody, implode("\r\n", $headers));

if ($mailSent) {
    debugLog("Email principal envoyé avec succès");
} else {
    debugLog("Erreur lors de l'envoi de l'email principal");
}

// Email de confirmation au client
if ($mailSent && SEND_CONFIRMATION_EMAIL) {
    debugLog("Envoi de l'email de confirmation au client...");
    $confirmSubject = "Confirmation de votre demande - New Strong Protection";
    $confirmBody = "
    <!DOCTYPE html>
    <html lang='fr'>
    <head>
        <meta charset='UTF-8'>
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                line-height: 1.6;
                color: #333;
                background-color: #f4f4f4;
                margin: 0;
                padding: 0;
            }
            .container {
                max-width: 600px;
                margin: 20px auto;
                background: #ffffff;
                border-radius: 10px;
                overflow: hidden;
                box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            }
            .header {
                background: linear-gradient(135deg, #000000, #1a1a1a);
                color: #ffffff;
                padding: 40px 30px;
                text-align: center;
            }
            .header h1 {
                margin: 0;
                font-size: 28px;
                font-weight: 700;
            }
            .success-icon {
                font-size: 60px;
                margin-bottom: 15px;
            }
            .content {
                padding: 40px 30px;
            }
            .greeting {
                font-size: 18px;
                color: #000;
                margin-bottom: 20px;
            }
            .message {
                font-size: 15px;
                color: #555;
                line-height: 1.8;
                margin-bottom: 15px;
            }
            .info-box {
                background: #f9fafb;
                border-left: 4px solid #eab308;
                padding: 20px;
                border-radius: 5px;
                margin: 25px 0;
            }
            .info-box h3 {
                margin: 0 0 15px;
                color: #000;
                font-size: 16px;
            }
            .info-item {
                display: flex;
                align-items: center;
                margin-bottom: 10px;
                font-size: 14px;
            }
            .info-item span {
                margin-right: 10px;
                font-size: 18px;
            }
            .cta-button {
                display: inline-block;
                background: linear-gradient(135deg, #eab308, #d97706);
                color: #000;
                padding: 15px 40px;
                text-decoration: none;
                border-radius: 50px;
                font-weight: 600;
                margin: 20px 0;
                box-shadow: 0 4px 15px rgba(234, 179, 8, 0.3);
            }
            .footer {
                background: #f9fafb;
                padding: 30px;
                text-align: center;
                font-size: 13px;
                color: #666;
            }
            .social-links {
                margin: 20px 0;
            }
            .social-links a {
                display: inline-block;
                margin: 0 10px;
                color: #eab308;
                text-decoration: none;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <div class='success-icon'>✅</div>
                <h1>Demande Reçue !</h1>
            </div>
            
            <div class='content'>
                <div class='greeting'>
                    Bonjour <strong>$prenom $nom</strong>,
                </div>
                
                <p class='message'>
                    Nous avons bien reçu votre demande de devis pour le service <strong>$service</strong>.
                </p>
                
                <p class='message'>
                    Notre équipe d'experts en sécurité va analyser votre demande et vous contactera dans les plus brefs délais pour vous proposer une solution adaptée à vos besoins.
                </p>
                
                <div class='info-box'>
                    <h3>📋 Récapitulatif de votre demande</h3>
                    <div class='info-item'>
                        <span>🔒</span>
                        <div><strong>Service:</strong> $service</div>
                    </div>
                    <div class='info-item'>
                        <span>📧</span>
                        <div><strong>Email:</strong> $email</div>
                    </div>
                    <div class='info-item'>
                        <span>📞</span>
                        <div><strong>Téléphone:</strong> $telephone</div>
                    </div>
                    <div class='info-item'>
                        <span>📅</span>
                        <div><strong>Date:</strong> " . date('d/m/Y à H:i') . "</div>
                    </div>
                </div>
                
                <div style='text-align: center;'>
                    <a href='https://forceone.cd/' class='cta-button'>Visiter notre site</a>
                </div>
                
                <p class='message' style='margin-top: 30px;'>
                    <strong>Délai de réponse:</strong> Nous nous engageons à vous répondre sous 24 heures ouvrables.
                </p>
                
                <p class='message'>
                    En attendant, n'hésitez pas à nous contacter si vous avez des questions supplémentaires.
                </p>
            </div>
            
            <div class='footer'>
                <p><strong>New Strong Protection</strong></p>
                <p>La sécurité qui rassure</p>
                <p style='margin: 15px 0;'>
                    📍 Haut Uéle/ISIRO - République Démocratique du Congo<br>
                    📧 admin@nsp.cd | 📞 (+243) 898244292 - 81720338
                </p>
                
                <div class='social-links'>
                    <a href='#'>Facebook</a> |
                    <a href='#'>Twitter</a> |
                    <a href='#'>LinkedIn</a> |
                    <a href='#'>Instagram</a>
                </div>
                
                <p style='margin-top: 20px; font-size: 11px; color: #999;'>
                    Cet email a été envoyé automatiquement, merci de ne pas y répondre directement.<br>
                    Pour toute question, contactez-nous à admin@nsp.cd
                </p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $confirmHeaders = [
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=' . EMAIL_CHARSET,
        'From: ' . EMAIL_FROM_NAME . ' <' . EMAIL_FROM . '>',
        'Reply-To: ' . EMAIL_TO,
        'X-Mailer: PHP/' . phpversion()
    ];
    
    $confirmSent = mail($email, $confirmSubject, $confirmBody, implode("\r\n", $confirmHeaders));
    
    if ($confirmSent) {
        debugLog("Email de confirmation envoyé avec succès au client");
    } else {
        debugLog("Erreur lors de l'envoi de l'email de confirmation");
    }
}

// Réponse JSON
if ($mailSent) {
    debugLog("Réponse de succès envoyée au client");
    echo json_encode([
        'success' => true,
        'message' => SUCCESS_MESSAGE,
        'data' => [
            'prenom' => $prenom,
            'nom' => $nom,
            'email' => $email,
            'telephone' => $telephone,
            'service' => $service
        ]
    ]);
} else {
    debugLog("Réponse d'erreur envoyée au client");
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => ERROR_MESSAGE
    ]);
}
?>

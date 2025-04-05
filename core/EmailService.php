<?php
/**
 * Classe EmailService - Gère l'envoi d'emails
 */
class EmailService {
    /**
     * Envoie un email
     * 
     * @param string $to Adresse email du destinataire
     * @param string $subject Sujet de l'email
     * @param string $message Corps de l'email (HTML)
     * @param array $headers En-têtes supplémentaires
     * @return bool Succès ou échec
     */
    public static function send($to, $subject, $message, $headers = []) {
        // En-têtes par défaut
        $defaultHeaders = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=utf-8',
            'From: ' . APP_NAME . ' <noreply@example.com>'
        ];
        
        // Fusionner les en-têtes par défaut avec les en-têtes personnalisés
        $allHeaders = array_merge($defaultHeaders, $headers);
        
        // Convertir le tableau d'en-têtes en chaîne
        $headersString = implode("\r\n", $allHeaders);
        
        // Envoyer l'email
        return mail($to, $subject, $message, $headersString);
    }
    
    /**
     * Envoie un email de confirmation d'inscription
     * 
     * @param string $to Adresse email du destinataire
     * @param string $username Nom d'utilisateur
     * @return bool Succès ou échec
     */
    public static function sendRegistrationConfirmation($to, $username) {
        $subject = 'Bienvenue sur ' . APP_NAME;
        
        $message = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Confirmation d\'inscription</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                    border: 1px solid #ddd;
                    border-radius: 5px;
                }
                .header {
                    background-color: #007bff;
                    color: white;
                    padding: 10px 20px;
                    border-radius: 5px 5px 0 0;
                    text-align: center;
                }
                .content {
                    padding: 20px;
                }
                .footer {
                    text-align: center;
                    margin-top: 20px;
                    font-size: 12px;
                    color: #777;
                }
                .button {
                    display: inline-block;
                    background-color: #007bff;
                    color: white;
                    padding: 10px 20px;
                    text-decoration: none;
                    border-radius: 5px;
                    margin-top: 20px;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>' . APP_NAME . '</h1>
                </div>
                <div class="content">
                    <h2>Bienvenue, ' . htmlspecialchars($username) . ' !</h2>
                    <p>Votre inscription sur ' . APP_NAME . ' a bien été enregistrée.</p>
                    <p>Vous pouvez maintenant vous connecter et profiter de toutes les fonctionnalités de notre plateforme.</p>
                    <p>
                        <a href="' . APP_URL . '" class="button">Accéder au site</a>
                    </p>
                </div>
                <div class="footer">
                    <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
                    <p>&copy; ' . date('Y') . ' ' . APP_NAME . '. Tous droits réservés.</p>
                </div>
            </div>
        </body>
        </html>
        ';
        
        return self::send($to, $subject, $message);
    }
}


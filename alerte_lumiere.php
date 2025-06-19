<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$type = $_GET['type'] ?? null;

if (empty($_SESSION['user_id'])) {
    http_response_code(403);
    echo "Non autorisé";
    exit;
}

$stmt = $pdo->prepare("SELECT email, prenom, notifications_active FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    http_response_code(404);
    echo "Utilisateur non trouvé";
    exit;
}

if (!$user['notifications_active']) {
    echo "🔕 Notifications désactivées pour cet utilisateur. Aucun mail envoyé.";
    exit;
}



$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'huwilliam2601@gmail.com';
    $mail->Password   = 'ahptvtpcwiyvzbqw';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('huwilliam2601@gmail.com', 'Gusteau');
    $mail->addAddress($user['email'], $user['prenom']);

    if ($type === 'on') {
        $mail->Subject = ' Alerte : Luminosite trop basse';
        $mail->Body    = "Bonjour {$user['prenom']},\n\n⚠ La luminosité est inférieure à 350 lux.\nLa lumière a été automatiquement allumée.\n\n– Gusteau's";
    } elseif ($type === 'off') {
        $mail->Subject = ' Alerte : Luminosite trop elevee';
        $mail->Body    = "Bonjour {$user['prenom']},\n\n⚠ La luminosité est supérieure à 1500 lux.\nLa lumière a été automatiquement éteinte.\n\n– Gusteau's";
    } else {
        echo "Type de seuil invalide ou manquant.";
        exit;
    }

    $mail->send();
    echo "✅ Mail envoyé pour type = $type";
} catch (Exception $e) {
    echo "Erreur mail : {$mail->ErrorInfo}";
}

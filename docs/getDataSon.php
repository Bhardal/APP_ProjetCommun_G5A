<?php
require_once './dbConnexion.php';
header('Content-Type: application/json');

try {
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT * FROM Capteur_Son ORDER BY temps DESC LIMIT 1");
    $stmt->execute();

    $allData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // for ($i = 0; $i < count($allData); $i++) {
    //     echo "ID: " . $allData[$i]['id'] . ", Intensité: " . $allData[$i]['valeur_luminosite'] . ", Date: " . $allData[$i]['date_mesure'] . "\n";
    // }
    $data = [];
    foreach ($allData as $row) {
        $data[] = [
            'id' => $row['id'],
            'intensite' => $row['valeur'],
            'date' => $row['temps']
        ];
    }
    echo json_encode($data);
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des categories : " . $e->getMessage();
    exit();
}
?>

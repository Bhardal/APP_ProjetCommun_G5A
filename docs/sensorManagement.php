<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Capteurs</title>
    <link rel="stylesheet" href="../style/styles.css">
    <script>
        function toggleSensor(sensor, state) {
            alert(sensor + " " + (state ? "activé" : "désactivé"));
        }
    </script>
</head>
<body>
    <div class="sensor-container">
        <h2>Gestion des Capteurs</h2>
        <?php
        $sensors = [
            'Lumiere' => 'Lumière',
            'Gaz' => 'Gaz',
            'Son' => 'Son',
            'Temperature' => 'Température',
            'Proximite' => 'Proximité'
        ];
        foreach ($sensors as $key => $label): ?>
            <div class="sensor">
                <span class="sensor-name"><?= htmlspecialchars($label) ?></span>
                <div class="sensor-actions">
                    <button onclick="toggleSensor('<?= $label ?>', true)">On</button>
                    <button class="off" onclick="toggleSensor('<?= $label ?>', false)">Off</button>
                    <a href="./graphView.php?sensor=<?= urlencode($key) ?>">Voir Graphique</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>

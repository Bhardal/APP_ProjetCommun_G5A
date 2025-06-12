<?php
function getConnection() {
    try{
        return new PDO("mysql:host=mysql-gusto.alwaysdata.net;dbname=gusto_g5;charset=utf8mb4", "gusto", "RestoGustoG5");
        //connection a la db
    }catch(PDOException $e){
        echo "Erreur de connexion à la base de donnée : " . $e->getMessage();
    }
}
?>

<?php
function getConnection() {
    try{
        return new PDO("mysql:host=https://phpmyadmin.alwaysdata.com;dbname=gusto_G5", "gusto", "RestoGustoG5");
        //connection a la db
    }catch(PDOExeption $e){
        echo "Erreur de connexion à la base de donnée : " . $e->getMessage();
    }
}
?>

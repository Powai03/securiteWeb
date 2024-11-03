<?php
try
{
    $bdd = new PDO('mysql:host=localhost;dbname=Collegram', 'root', 'root');
}
catch(Exception $e)
{
    die('Erreur : '.$e->getMessage());
}
?>
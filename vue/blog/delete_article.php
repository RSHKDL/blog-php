<?php
if($_POST) {

    // cf. create_article.php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/formation/p5-php/modele/database.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/formation/p5-php/modele/blog/article.php');

    $database = new Database();
    $db = $database->getConnection();

    $article = new Article($db);
 
    // set object id to be deleted then delete
    $article->id = $_POST['object_id'];
    if($article->delete()) {
        echo "L'article a été supprimé.";
    }
    else {
        echo "Erreur lors de la suppression de l'article.";
    }
}
<?php
// Configure pagination variables
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$records_per_page = 3;
$from_record_num = ($records_per_page * $page) - $records_per_page;

// include database and object files (better use sessions for user)
require_once($_SERVER['DOCUMENT_ROOT'] . '/formation/p5-php/modele/database.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/formation/p5-php/modele/blog/article.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/formation/p5-php/modele/blog/category.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/formation/p5-php/modele/blog/user.php');

// instantiate database and objects
// /!\ use a manager to handle the db connections
$database = new Database();
$db = $database->getConnection();
 
$article = new Article($db);
$category = new Category($db);
$user = new User($db);
 
// query articles
$stmt = $article->readAll($from_record_num, $records_per_page);
$num = $stmt->rowCount();

$page_title = "Les derniers billets du blog";
include_once 'header.php';

echo "<a href='create_article.php'>Créer un nouvel article</a>";

// display the articles if there are any
if($num>0) :
    echo "<section>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) :
        extract($row);

        $category->id = $category_id;
        $category->readName();
        $user->id = $user_id;
        $user->readUsername();
        echo "<article>";
            echo "<h3><a href='read_one.php?id={$id}'>{$title}</a></h3>";
            echo "<div class='article-meta'>";
                echo "<i>Date : {$created}, Catégorie : {$category->name}, Auteur : {$user->username}.</i>";
            echo "</div>";
            echo "<div class='article-actions'>";
                echo "<a href='update_article.php?id={$id}'>Éditer</a>";
                echo "<a delete-id='{$id}'>Supprimer</a>";
            echo "</div>";
        echo "</article>";
    endwhile;
    echo "<section>";

    // pagination
    $page_url = "index.php?";
    $total_rows = $article->countAll();
    include_once 'paging.php';

else :
    echo "<div class='alert alert-info'>Aucun article.</div>";
endif;

include_once 'footer.php'; 

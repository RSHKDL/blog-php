<?php
// Ternary to get ID of the article to be edited
$id = isset($_GET['id']) ? $_GET['id'] : die('Erreur: Cet ID n\'existe pas.');
 
// cf. create_article.php
require_once($_SERVER['DOCUMENT_ROOT'] . '/formation/p5-php/modele/database.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/formation/p5-php/modele/blog/article.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/formation/p5-php/modele/blog/category.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/formation/p5-php/modele/blog/user.php');
 
$database = new Database();
$db = $database->getConnection();
 
$article = new Article($db);
$category = new Category($db);
$user = new User($db);
 
$article->id = $id;
$article->readOne();

$category->id = $article->category_id;
$category->readName();
$user->id = $article->user_id;
$user->readUsername();

$page_title = $article->title;
include_once "header.php";

echo "<a href='index.php'>Retour aux articles</a>";
?>
<article>
	<h1><?php echo $article->title; ?></h1>
	<p><b><?php echo $article->header; ?></b></p>
	<p><?php echo $article->content; ?></p>
	<div class='article-meta'>
		<span>Date : <?php echo $article->created; ?></span>
		<span>Catégorie : <?php echo $category->name; ?></span>
		<span>Auteur : <?php echo $user->username; ?></span>
	</div>
	<div class='article-actions'>
	<?php
		echo "<a href='update_article.php?id={$id}'>Éditer</a>";
		echo "<a class='delete-object' delete-id='{$id}'>Supprimer</a>";
	?>
	</div>
</article>
<?php include_once "footer.php"; ?>
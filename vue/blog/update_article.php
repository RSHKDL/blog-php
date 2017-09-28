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

$page_title = "Éditer l'article {$article->title}";
include_once "header.php";

echo "<a href='index.php'>Retour aux articles</a>";
 
?>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id={$id}");?>" method="post">
	<label>Titre</label>
	<input type='text' name='title' value='<?php echo $article->title; ?>' class='form-control' />
	<label>Chapô</label>
	<textarea name='header' class='form-control'><?php echo $article->header; ?></textarea>
	<label>Corps de l'article</label>
	<textarea name='content' class='form-control'><?php echo $article->content; ?></textarea>
	<label>Catégorie</label>
	<?php
		$stmt = $category->read();

		echo "<select class='form-control' name='category_id'>";
			echo "<option>Choisir une catégorie</option>";
			while ($row_category = $stmt->fetch(PDO::FETCH_ASSOC)) {
				extract($row_category);
				if($article->category_id==$id) {
					echo "<option value='$id' selected>";
				}
				else {
					echo "<option value='$id'>";
				}
				echo "$name</option>";
			}
		echo "</select>";
	?>
	<button type="submit" class="btn btn-primary">Mettre à jour</button>
</form>
<?php
// if the form is submitted
if($_POST) {
	// set article property values
	$article->title = $_POST['title'];
	$article->header = $_POST['header'];
	$article->content = $_POST['content'];
	$article->category_id = $_POST['category_id'];
	// update the article or tell the user an error as occured
	if($article->update()) {
		echo "<div class='alert alert-success alert-dismissable'>";
			echo "Article mis à jour.";
		echo "</div>";
	}
	else {
		echo "<div class='alert alert-danger alert-dismissable'>";
			echo "Erreur lors de la mise à jour de l'article.";
		echo "</div>";
	}
}
include_once "footer.php";
?>
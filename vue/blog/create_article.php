<?php
// include database and object files (better use sessions for user)
require_once($_SERVER['DOCUMENT_ROOT'] . '/formation/p5-php/modele/database.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/formation/p5-php/modele/blog/article.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/formation/p5-php/modele/blog/category.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/formation/p5-php/modele/blog/user.php');
 
// get database connection then pass connection to objects
$database = new Database();
$db = $database->getConnection();

$article = new Article($db);
$category = new Category($db);
$user = new User($db);

$page_title = "Création d'un nouvel article";
include_once "header.php";

echo "<a href='index.php'>Retour aux articles</a>";
?>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
	<label>Titre</label>
	<input type='text' name='title' class='form-control' />
	<label>Chapô</label>
	<textarea name='header' class='form-control' /></textarea>
	<label>Corps de l'article</label>
	<textarea name='content' class='form-control'></textarea>
	<label>Catégorie</label>
	<?php
		// read the article categories from the database
		$stmt = $category->read();
		echo "<select class='form-control' name='category_id'>";
		echo "<option>Choisir une catégorie</option>";
	
		while ($row_category = $stmt->fetch(PDO::FETCH_ASSOC)) {
			extract($row_category);
			echo "<option value='{$id}'>{$name}</option>";
		}
		echo "</select>";
	?>
	<label>Auteur</label>
	<?php
		// read the user from the database
		$stmt = $user->read();
		echo "<select class='form-control' name='user_id'>";
		echo "<option>Choisir un auteur</option>";
	
		while ($row_user = $stmt->fetch(PDO::FETCH_ASSOC)) {
			extract($row_user);
			echo "<option value='{$id}'>{$username}</option>";
		}
		echo "</select>";
	?>
	<button type="submit" class="btn btn-primary">Valider</button>
</form>
<?php
// use filter_input instead of $_POST + (http://php.net/manual/fr/function.filter-has-var.php)
if($_POST) {
	$article->title = $_POST['title'];
	$article->header = $_POST['header'];
	$article->content = $_POST['content'];
	$article->category_id = $_POST['category_id'];
	$article->user_id = $_POST['user_id'];

	if($article->create()) {
		echo "<div class='alert alert-success'>Article successfully created.</div>";
	}
	else {
		echo "<div class='alert alert-danger'>Unable to create new article.</div>";
	}
}

include_once "footer.php";
?>
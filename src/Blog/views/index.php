<?= $renderer->render('header') ?>

<h2>tests views</h2>

<p>check ?</p>
<ul>
    <li><a href="<?= $router->generateUri('blog.show', ['slug' => 'test-slug-781']); ?>">test article</a></li>
    <li>test 1</li>
    <li>test 2</li>
</ul>

<?= $renderer->render('footer') ?>
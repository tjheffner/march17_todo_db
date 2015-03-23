<?php
    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__."/../src/Task.php";
    require_once __DIR__."/../src/Category.php";

    $app = new Silex\Application();

    $app['debug']=true;

    use Symfony\Component\HttpFoundation\Request;
      Request::enableHttpMethodParameterOverride();

    $DB = new PDO('pgsql:host=localhost;dbname=to_do');

    $app->register(new Silex\Provider\TwigServiceProvider(), array(
        'twig.path' => __DIR__.'/../views'
    ));

/* 3 pages, view all, view one, edit one
   view all = categories.twig
   view one = category.twig
   edit one = category_edit.twig
*/

//show all categories
    $app->get("/", function() use ($app) {
        return $app['twig']->render('categories.html.twig', array('categories' => Category::getAll()));
    });

//create a new category
    $app->post("/categories", function() use($app) {
        $new_category = new Category($_POST['name']);
        $new_category->save();
        return $app['twig']->render('categories.html.twig', array('categories' => Category::getAll()));
    });

//view a single category + their tasks
    $app->get("/categories/{id}", function($id) use($app) {
        $current_category = Category::find($id);
        return $app['twig']->render('category.html.twig', array('category' => $current_category, 'tasks' => $current_category->getTasks()));
    });

//edit a single category
    $app->get("/categories/{id}/edit", function($id) use($app) {
        $current_category = Category::find($id);
        return $app['twig']->render('category_edit.html.twig', array('category' => $current_category));
    });

//edit form sent as a patch
    $app->patch("/categories/{id}", function($id) use($app) {
        $current_category = Category::find($id);
        $new_name = $_POST['name'];
        $current_category->update($new_name);
        return $app['twig']->render('category.html.twig', array('category' => $current_category, 'tasks' => $current_category->getTasks()));
    });

//delete a single category
    $app->delete("/categories/{id}/delete", function($id) use($app) {
        $current_category = Category::find($id);
        $current_category->delete();
        return $app['twig']->render('categories.html.twig', array('categories' => Category::getAll()));
    });

//delete all categories
    $app->post("/delete_categories", function() use($app) {
        Category::deleteAll();
        return $app['twig']->render('categories.html.twig', array('categories' => Category::getAll()));
    });

    return $app;

?>

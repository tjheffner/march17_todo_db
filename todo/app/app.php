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

// 3 basic pages-- one main page, one for categories, one for tasks
    $app->get("/", function() use($app) {
        return $app['twig']->render('index.html.twig', array('categories' => Category::getAll(), 'tasks' => Task::getAll()));
    });

    $app->get("/tasks", function() use($app) {
        return $app['twig']->render('tasks.html.twig', array('tasks' => Task::getAll()));
    });

    $app->get("/categories", function() use($app) {
        return $app['twig']->render('categories.html.twig', array('categories' => Category::getAll()));
    });

/* 2 pages for each class, view all, view one
   view all: tasks.html.twig
             categories.html.twig
   view one: task.html.twig
             category.html.twig
*/
    $app->post("/tasks", function() use ($app) {
        $description = $_POST['description'];
        $task = new Task($description);
        $task->save();
        return $app['twig']->render('tasks.html.twig', array('tasks' => Task::getAll()));
    });

    $app->get("/tasks/{id}", function($id) use ($app) {
        $task = Task::find($id);
        return $app['twig']->render('task.html.twig', array('task' => $task, 'categories' => $task->getCategories(), 'all_categories' => Category::getAll()));
    });

    $app->post("/categories", function() use($app) {
        $category = new Category($_POST['name']);
        $category->save();
        return $app['twig']->render('categories.html.twig', array('categories' => Category::getAll()));
    });

    $app->get("/categories/{id}", function($id) use($app) {
        $category = Category::find($id);
        return $app['twig']->render('category.html.twig', array('category' => $category, 'tasks' => $category->getTasks(), 'all_tasks' => Task::getAll()));
    });

    /*
    Make 1 add_.. route for each class.
    */

    $app->post("/add_tasks", function() use ($app) {
        $category = Category::find($_POST['category_id']);
        $task = Task::find($_POST['task_id']);
        $category->addTask($task);
        return $app['twig']->render('category.html.twig', array('category' => $category, 'categories' => Category::getAll(), 'tasks' => $category->getTasks(), 'all_tasks' => Task::getAll()));
    });

    $app->post("/add_categories", function() use ($app) {
        $category = Category::find($_POST['category_id']);
        $task = Task::find($_POST['task_id']);
        $task->addCategory($category);
        return $app['twig']->render('task.html.twig', array('task' => $task, 'tasks' => Task::getAll(), 'categories' => $task->getCategories(), 'all_categories' => Category::getAll()));
    });

    //two delete pages per class, singular and class-wide delete functions.

    $app->post("/delete_categories", function() use ($app) {
        Category::deleteAll();
        return $app['twig']->render('categories.html.twig', array('categories' => Category::getAll()));
    });

    $app->post("/delete_tasks", function() use ($app) {
        Task::deleteAll();
        return $app['twig']->render('tasks.html.twig', array('tasks' => Task::getAll()));
    });

    $app->delete("/categories/{id}/delete", function($id) use ($app) {
        $current_category = Category::find($id);
        $current_category->delete();
        return $app['twig']->render('categories.html.twig', array('categories' => Category::getAll()));
    });

    $app->delete("/tasks/{id}/delete", function($id) use ($app) {
        $current_task = Task::find($id);
        $current_task->delete();
        return $app['twig']->render('tasks.html.twig', array('tasks' => Task::getAll()));
    });

    //2 edit routes per class, one to and one from the edit page.

    $app->get("/categories/{id}/edit", function($id) use ($app) {
        $current_category = Category::find($id);
        return $app['twig']->render('category_edit.html.twig', array('category' => $current_category));
    });

    $app->patch("/categories/{id}", function($id) use ($app) {
        $current_category = Category::find($id);
        $new_name = $_POST['new_name'];
        $current_category->update($new_name);
        return $app['twig']->render('category.html.twig', array('category' => $current_category, 'tasks' => $current_category->getTasks(), 'all_tasks' => Task::getAll()));
    });

    $app->get("/tasks/{id}/edit", function($id) use ($app) {
        $current_task = Task::find($id);
        return $app['twig']->render('task_edit.html.twig', array('task' => $current_task));
    });

    $app->patch("/tasks/{id}", function($id) use ($app) {
        $current_task = Task::find($id);
        $new_description = $_POST['new_description'];
        $current_task->update($new_description);
        return $app['twig']->render('task.html.twig', array('task' => $current_task, 'categories' => $current_task->getCategories(), 'all_categories' => Category::getAll()));
        });

    //edit route to update task status
    $app->patch("/tasks/{id}/complete", function($id) use($app) {
        $current_task = Task::find($id);
        $new_status = $_POST['new_status'];
        $current_task->updateStatus($new_status);
        return $app['twig']->render('tasks.html.twig', array('task' => $current_task, 'tasks' => Task::getAll()));
    });

    return $app;

?>

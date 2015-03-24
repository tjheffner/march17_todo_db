<?php
class Task
{
    private $description;
    private $id;
    private $status;
    private $duedate;

//this creates our task with a name (description) and sets the id to a default value of null
    function __construct($description, $id = null, $status = 0, $duedate)
    {
        $this->description = $description;
        $this->id = $id;
        $this->status = $status;
        $this->duedate = $duedate;
    }

//this sets the description as a string
    function setDescription($new_description)
    {
        $this->description = (string) $new_description;
    }

//this is the getter for the description
    function getDescription()
    {
        return $this->description;
    }

//this is the getter for the id
    function getId()
    {
        return $this->id;
    }

//this sets the id as an integer
    function setId($new_id)
    {
        $this->id = (int) $new_id;
    }

    function setStatus($new_status)
    {
        $this->status = (int) $new_status;
    }

    function getStatus()
    {
        return $this->status;
    }

    function setDuedate($new_duedate)
    {
        $this->duedate = (string) $new_duedate;
    }

    function getDuedate()
    {
        return $this->duedate;
    }

//this finds the id of tasks after they have been "transported" from the database into our code
    static function find($search_id)
    {
        $found_task = null;
        $tasks = Task::getAll();
        foreach($tasks as $task) {
            $task_id = $task->getId();
            if ($task_id == $search_id) {
                $found_task = $task;
            }
        }
        return $found_task;
    }

//this saves the entries in our form into the database
    function save()
    {
        $statement = $GLOBALS['DB']->query("INSERT INTO tasks (description, status, duedate) VALUES ('{$this->getDescription()}', {$this->getStatus()}, '{$this->getDuedate()}') RETURNING id;");
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        $this->setId($result['id']);
    }

//this gets everything we've saved in the database and returns it to the array $tasks
//this is the "transporter" function
    static function getAll()
    {
        $returned_tasks = $GLOBALS['DB']->query("SELECT * FROM tasks;");
        $tasks = array();
        foreach($returned_tasks as $task) {
            $description = $task['description'];
            $status = $task['status'];
            $id = $task['id'];
            $duedate = $task['duedate'];
            $new_task = new Task($description, $id, $status, $duedate);
            array_push($tasks, $new_task);
        }
        return $tasks;
    }

//this deletes the entries in the database
    static function deleteAll()
    {
        $GLOBALS['DB']->exec("DELETE FROM tasks *;");
    }

    function update($new_description)
    {
        $GLOBALS['DB']->exec("UPDATE tasks SET description = '{$new_description}' WHERE id = {$this->getId()};");
        $this->setDescription($new_description);
    }

    function updateStatus($new_status)
    {
        $GLOBALS['DB']->exec("UPDATE tasks SET status = {$new_status} WHERE id = {$this->getId()};");
        $this->setStatus($new_status);
    }

    function updateDuedate($new_duedate)
    {
        $GLOBALS['DB']->exec("UPDATE tasks SET duedate = '{$new_duedate}' WHERE id = {$this->getId()};");
        $this->setDuedate($new_duedate);
    }

    function delete()
    {
        $GLOBALS['DB']->exec("DELETE FROM tasks WHERE id = {$this->getId()};");
        $GLOBALS['DB']->exec("DELETE FROM categories_tasks WHERE task_id = {$this->getId()};");
    }

    function addCategory($category)
    {
        $GLOBALS['DB']->exec("INSERT INTO categories_tasks (category_id, task_id) VALUES ({$category->getId()}, {$this->getId()});");
    }

    function getCategories()
    {
        $query = $GLOBALS['DB']->query("SELECT category_id FROM categories_tasks WHERE task_id = {$this->getId()};");
        $category_ids = $query->fetchAll(PDO::FETCH_ASSOC);

        $categories = array();
        foreach($category_ids as $id) {
            $category_id = $id['category_id'];
            $result = $GLOBALS['DB']->query("SELECT * FROM categories WHERE id = {$category_id};");
            $returned_category = $result->fetchAll(PDO::FETCH_ASSOC);

            $name = $returned_category[0]['name'];
            $id = $returned_category[0]['id'];
            $new_category = new Category($name, $id);
            array_push($categories, $new_category);
        }
        return $categories;
    }

}

?>

<?php

    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */

    require_once "src/Task.php";
    require_once "src/Category.php";

    $DB = new PDO('pgsql:host=localhost;dbname=to_do_test');

    class TaskTest extends PHPUnit_Framework_TestCase
    {

        protected function tearDown()
        {
            Task::deleteAll();
            Category::deleteAll();
        }

        function test_getId()
        {
            //Arrange
            $name = "Home stuff";
            $id = null;
            $test_category = new Category($name, $id);
            $test_category->save();

            $description = "Wash the dog";
            $test_Task = new Task($description, $id);
            $test_Task->save();

            //Act
            $result = $test_Task->getId();

            //Assert
            $this->assertEquals(true, is_numeric($result));
        }

        function test_setId()
        {
            //Arrange
            $name = "Home stuff";
            $id = null;
            $test_category = new Category($name, $id);
            $test_category->save();

            $description = "Wash the dog";
            $test_Task = new Task($description, $id);
            $test_Task->save();

            //Act
            $test_Task->setId(2);

            //Assert
            $result = $test_Task->getId();
            $this->assertEquals(2, $result);
        }

        function test_save()
        {
            //Arrange
            $name = "Home stuff";
            $id = null;
            $test_category = new Category($name, $id);
            $test_category->save();

            $description = "Wash the dog";
            $test_Task = new Task($description, $id);

            //Act
            $test_Task->save();

            //Assert
            $result = Task::getAll();
            $this->assertEquals($test_Task, $result[0]);
        }

        function test_getAll()
        {
            //Arrange
            $name = "Home stuff";
            $id = null;
            $test_category = new Category($name, $id);
            $test_category->save();

            $description = "Wash the dog";
            $test_Task = new Task($description, $id);
            $test_Task->save();

            $description2 = "Water the lawn";
            $test_Task2 = new Task($description2, $id);
            $test_Task2->save();

            //Act
            $result = Task::getAll();

            //Assert
            $this->assertEquals([$test_Task, $test_Task2], $result);
        }

        function test_deleteAll()
        {
            //Arrange
            $name = "Home stuff";
            $id = null;
            $test_category = new Category($name, $id);
            $test_category->save();

            $description = "Wash the dog";
            $test_Task = new Task($description, $id);
            $test_Task->save();

            $description2 = "Water the lawn";
            $test_Task2 = new Task($description2, $id);
            $test_Task2->save();

            //Act
            Task::deleteAll();

            //Assert
            $result = Task::getAll();
            $this->assertEquals([], $result);

        }

        function test_find()
        {
            //Arrange
            $name = "Home stuff";
            $id = null;
            $test_category = new Category($name, $id);
            $test_category->save();

            $description = "Wash the dog";
            $test_Task = new Task($description, $id);
            $test_Task->save();

            $description2 = "Water the lawn";
            $test_Task2 = new Task($description2, $id);
            $test_Task2->save();

            //Act
            $result = Task::find($test_Task->getId());

            //Assert
            $this->assertEquals($test_Task, $result);
        }

        function test_update()
        {
            //Arrange
            $description = "Wash the dog";
            $id = 1;
            $test_task = new Task($description, $id);
            $test_task->save();

            $new_description = "Clean the dog";

            //Act
            $test_task->update($new_description);

            //Assert
            $this->assertEquals("Clean the dog", $test_task->getDescription());
        }

        function test_deleteTask()
        {
            //Arrange
            $description = "Wash the dog";
            $id = 1;
            $test_task = new Task($description, $id);
            $test_task->save();

            $description2 = "Water the lawn";
            $id2 = 2;
            $test_task2 = new Task($description2, $id2);
            $test_task2->save();

            //Act
            $test_task->delete();

            //Assert
            $this->assertEquals([$test_task2], Task::getAll());
        }

        function test_addCategory()
        {
            //Arrange
            $name = "Work stuff";
            $id = 1;
            $test_category = new Category($name, $id);
            $test_category->save();

            $description = "File reports";
            $id2 = 2;
            $test_task = new Task($description, $id2);
            $test_task->save();

            //Act
            $test_task->addCategory($test_category);

            //Assert
            $this->assertEquals($test_task->getCategories(), [$test_category]);
        }

        function test_getCategories()
        {
            //Arrange
            $name = "Work stuff";
            $id = 1;
            $test_category = new Category($name, $id);
            $test_category->save();

            $name2 = "Volunteer stuff";
            $id2 = 2;
            $test_category2 = new Category($name2, $id2);
            $test_category2->save();

            $description = "File reports";
            $id3 = 3;
            $test_task = new Task($description, $id3);
            $test_task->save();

            //Act
            $test_task->addCategory($test_category);
            $test_task->addCategory($test_category2);

            //Assert
            $this->assertEquals($test_task->getCategories(), [$test_category, $test_category2]);
        }

    }

 ?>

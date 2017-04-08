<link rel="stylesheet" href="css/to-do-list.css">
<link rel="stylesheet" href="css/jquery-ui.min.css">
<script src="external/jquery/jquery.js"></script>
<script src="js/jquery-ui.min.js"></script>

<script>
    $(function() {
        $('#date').datepicker();
    });
</script>

<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include('inc/functions.php');
require('inc/class/toDoList.php');

session_start([
    'cookie_lifetime' => 90
]);

$loadedListName = "";

if (isset($_POST['selectList'])) {
    if ($_POST['selectList'] === 'newList') {
        unset($_SESSION['displayList']);
        $loadedListName = "New List";
    }
    else {
        $tempDisplayList = new ToDoList();
        $tempDisplayListString = getListByListName($_POST['selectList']);
        $tempDisplayList->getFromDecodedJson(json_decode($tempDisplayListString, true));
        $loadedListName = $tempDisplayList->getName();
        $_SESSION['displayList'] = $tempDisplayList;
    }   
}

echo $loadedListName;
    
switch (returnPost($_POST)) {
    case 'add':
        if (!empty($_POST['task'])) {
            $taskName = filter_var($_POST['task'], FILTER_SANITIZE_STRING);
            if (isset($_POST['date'])) {
                $taskCompleteBy = filter_var($_POST['date'], FILTER_SANITIZE_STRING);
            }
            $task = new toDoItem($taskName, $taskCompleteBy);      
            if (checkObjectOrArray($_SESSION['displayList'])) {     
                $_SESSION['displayList']->addTask($task);
            }
            else {
                $_SESSION['displayList'][] = $task;
            }
            echo "Task Added!";
        }
        else echo '<div class="error">Please enter a task.</div>';
        break;

    case 'clearComplete':
        $updateList = new toDoList();

        if($_SESSION['displayList']) {
            if (checkObjectOrArray($_SESSION['displayList'])) {
                foreach ($_SESSION['displayList']->getList() as $task) {
                    $updateList->addTask($task);
                }
                $updateList->setName($_SESSION['displayList']->getName());
            }
            else {
                foreach ($_SESSION['displayList'] as $task) {
                    $updateList->addTask($task);
                }
            }
        }
        foreach($updateList->getList() as $task) {
            if ($_POST[$task->formatTaskName()]) {
                $task->setStatus(true);
            }
        }
        $updateList->clearComplete();
        $_SESSION['displayList'] = $updateList;
        break;

    case 'save':
        if (!empty($_POST['listName'])) {
            $newList = new toDoList();
            foreach($_SESSION['displayList'] as $task) {
                $newList->addTask($task);
            }
            $newList->setName(filter_var($_POST['listName'], FILTER_SANITIZE_STRING));
            // Check if there is any list in the database. If so, new list ID
            // will be one greater than the highest list ID in database.
            if(getHighestListId()) {
                $id = getHighestListId() + 1;
                $newList->setId($id);
            }
            else {
                $newList->setId(1);
            }
            $newList->saveToDatabase(json_encode($newList->jsonSerialize(), JSON_FORCE_OBJECT));
            echo "List Saved!";
        }
        else echo '<div class="error">Please enter a list name.</div>';   
        break;
    
    case 'update':
        if (checkObjectOrArray($_SESSION['displayList'])) {
            $updateList = new toDoList();
            foreach ($_SESSION['displayList']->getList() as $task) {
                $updateList->addTask($task);
            }
            $updateList->setName($_SESSION['displayList']->getName());
            $updateList->setId($_SESSION['displayList']->getId());
            $updateList->updateInDatabase(json_encode($updateList->jsonSerialize(), JSON_FORCE_OBJECT));
        }
        else {
            echo '<div class="error">No list loaded to update.</div>';
        }
        break;

    case 'deleteList':

        break;
}

?>

<div class="to-do-list">
    <form method="post">

        <?php
            $displayList = new toDoList();
            // Check if a displayList has been selected and set that to the display variable
            if($_SESSION['displayList']) {
                // Check type of displayList
                // Object (saved list) vs Array (unsaved list)
                // Load into display array differently
                if (checkObjectOrArray($_SESSION['displayList'])) {
                    $listArray = $_SESSION['displayList']->getList();
                }
                else {
                    $listArray = $_SESSION['displayList'];
                }
                foreach ($listArray as $task) {
                    $displayList->addTask($task);
                }
            }
            $listHtml = "<select name='selectList' onchange='javascript: submit()'>
            <option>Select To-Do List</option>
            <option value='newList'>New List</option>";
            foreach(getSavedLists() as $list) {
                $newList = new toDoList();
                $newList->getFromDecodedJson(json_decode($list, true));
                    $listHtml .= "<option value='";
                    $listHtml .= $newList->getName();
                    $listHtml .= "'>";
                    $listHtml .= $newList->getName();
                    $listHtml .= "</option>";
            };
            $listHtml .= "</select>";
            echo $listHtml . "</br>";
        ?>
    </form>
    <form method="post">
        <?php                  
            $html = "<ul div='to-do-list'>";
            foreach ($displayList->getList() as $task) {
                $html .= "<li>Task: ";
                $html .= $task->getTask() . "<br>";
                $html .= "Complete By: ";
                $html .= $task->getCompleteBy();
                $html .= "<label><input type='checkbox' class='complete' name='" . $task->getTask() . "' ";
                if ($task->getStatus()) {       // Check for complete status; add checked status if true.
                    $html .= "checked=true";
                }
                $html .= ">Complete</label></li>";
            }
            $html .= "</ul>";
            $html .= "<div class='button'>
                            <button type='submit' name='clearComplete'>Clear Completed</button>
                        </div>";
            echo $html;

            session_write_close();
        ?>

    </form>
</div>

<div class="form">
    <form method="post">
        <div>
            <label for="task">Task: </label>
            <input type="text" id="task" name="task" />
        </div>
        <div>
            <label for="complete_by">Complete By (optional): </label>
            <input type="text" id="date" name="date" />
        </div>
        <div class="button">
            <button type="submit" name="add">Add Task</button>
        </div>
        <div>
            <label for="save">List Name: </label>
            <input type="text" id="listName" name="listName" />
        </div>
        <div class="button">
            <button type="submit" name="save">Save List</button>
        </div>
        <div class="button">
            <button type="submit" name="update">Update List</button>
        </div>
        <div class="button">
            <button type="submit" name="clear">Clear All</button>
        </div>
    </form>
</div>
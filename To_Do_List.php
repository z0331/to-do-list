<link rel="stylesheet" href="css/to-do-list.css">

<?php
require('toDoItem.php');

session_start([
    'cookie_lifetime' => 86400
]);


// Check for POST and that the "task" field isn't empty. If available, add it to session list array.
// Checks if Complete By has been included and adds if so.
// Sanitizes input before setting.
if (isset($_POST)) {
    if (!empty($_POST['task'])) {
        $task = new toDoItem();
        $task->setTask(filter_var($_POST['task'], FILTER_SANITIZE_STRING));
        if (!empty($_POST['complete_by'])) {
            $task->setCompleteBy(filter_var($_POST['complete_by'], FILTER_SANITIZE_STRING));
        }
        $_SESSION['list'][] = $task;
    }
    if (!is_null($_POST['update'])) {
        $_SESSION['list'] = array_values($_SESSION['list']);    // Reset index values of list
        for ($i=0, $size=count($_SESSION['list']); $i<$size; $i++) {
            $task = $_SESSION['list'][$i];
            if ($_POST[$task->formatTaskName()]) {
                unset($_SESSION['list'][$i]);
            }
        }
    }
    if (!is_null($_POST['clear'])) {
        unset($_SESSION['list']);
    }
}
?>

<div class="to-do-list">
    <form method="post">
        <?php
        if ($_SESSION['list']) {
            $html = "<ul div='to-do-list'>";
            foreach ($_SESSION['list'] as $task) {
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
                            <button type='submit' name='update'>Clear Completed</button>
                        </div>";
            echo $html;
        } // End SESSION list check and list display loop
        else {
            echo "Nothing to do";
        }
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
            <input type="text" id="complete_by" name="complete_by" />
        </div>
        <div class="button">
            <button type="submit" name="add">Add Task</button>
        </div>
        <div class="button">
            <button type="submit" name="clear">Clear All</button>
        </div>
    </form>
</div>
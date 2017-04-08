<?php

include('connect.php');
require('toDoItem.php');

class toDoList implements jsonSerializable {
    private $listName;
    private $list = array();
    private $counter;
    private $id = 0;

    public function setName($name) {
        $this->listName = $name;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getName() {
        return htmlspecialchars($this->listName);
    }

    public function getId() {
        return $this->id;
    }

    public function getCount() {
        return $this->counter;
    }

    public function addTask($task, $key = null) {
        $this->list[] = $task;
        $this->counter++;
    }

    /*
    **  Updates internal list parameter to remove those marked with complete status
    */
    public function clearComplete() {
        $updateList = array();
        for ($i=0, $size=count($this->list); $i<$size; $i++) {
            if (!$this->list[$i]->getStatus()) {
                $updateList[] = $this->list[$i];
            }
        }
        $updateList = array_values($updateList);
        $this->list = $updateList;
    }

    public function getList() {
        $returnList = array();
        foreach ($this->list as $task) {
            $returnList[] = $task;
        }
        return $returnList;
    }

    public function deleteTask($key) {

    }

    public function deleteAllTasks() {
        $this->list = array();
    }

    public function jsonSerialize() {
        $tasks = array();
        foreach($this->list as $task) {
            $newTask = array('task' => $task->getTask(),
                'completeBy' => $task->getCompleteBy(),
                'status' => $task->getStatus()
            );
            $tasks[] = $newTask;
        };
        return [
            'id' => $this->id,
            'listName' => $this->listName,
            'tasksList' => $tasks
        ];
    }

    public function getFromDecodedJson($array) {
        $this->id = $array['id'];
        $this->listName = $array['listName'];
        for ($i=0, $size=count($array['tasksList']); $i<$size; $i++) {
            $this->list[] = new toDoItem($array['tasksList'][$i]['task'],
                $array['tasksList'][$i]['completeBy'],
                $array['tasksList'][$i]['status']
            );
        }
    }

    public function saveToDatabase($jsonString) {
        require('connect.php');
        try {
            $query = $dbh->prepare("
                INSERT INTO lists (list, list_id) VALUES (?, ?);
            ");
            $query->bindParam(1, $jsonString);
            $query->bindParam(2, $this->id);
            $query->execute();
        } 
        catch (Exception $e) {
            echo "Couldn't save list.";
            echo "Error: " . $e;
            exit;
        }
    }

    public function updateInDatabase($jsonString) {
        require('connect.php');
        try {
            $query = $dbh->prepare("
                UPDATE lists SET list = ? WHERE JSON_UNQUOTE(list_title) = ?;
            ");
            $query->bindParam(1, $jsonString);
            $query->bindParam(2, $this->listName);
            $query->execute();
        } 
        catch (Exception $e) {
            echo "Couldn't update list.";
            echo "Error: " . $e;
            exit;
        }
    }
}
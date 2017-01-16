<?php

class toDoItem {
    private $task;
    private $complete_by;
    private $status = false;
    
    public static $counter = 0;

    function __construct() {
        self::$counter++;
    }

    // Setters

    public function setTask($newTask) {
        $this->task = $newTask;
    }

    public function setCompleteBy($newCompleteBy) {
        $this->complete_by = $newCompleteBy;
    }

    public function setStatus($newStatus) {
        $this->status = $newStatus;
    }

    // Getters

    public function getTask() {
        return $this->task;
    }

    public function getCompleteBy() {
        return $this->complete_by;
    }

    public function getStatus() {
        return $this->status;
    }

    // Other
    
    // Replace spaces in task name with underscores
    // Used to check for task name in post data
    public function formatTaskName() {
        return str_replace(' ', '_', $this->task);
    }


}
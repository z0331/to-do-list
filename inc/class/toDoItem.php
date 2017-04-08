<?php

class toDoItem {
    private $task;
    private $complete_by;
    private $status = false;

    public function __construct($task, $completeBy="", $status=false) {
        $this->task = $task;
        $this->complete_by = $completeBy;
        $this->status = $status;
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
        return htmlspecialchars($this->task);
    }

    public function getCompleteBy() {
        return htmlspecialchars($this->complete_by);
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
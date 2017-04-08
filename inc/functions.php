<?php


/**
* POST input check.
* Avoids direct manipulation of POST.
*
* @param POST event as string
* @return string
*/
function returnPost($post) {
    if (isset($post['selectList'])) {
        return 'selectList';
    }
    else if (isset($post['add'])) {
        return 'add';
    }
    else if (isset($post['clearComplete'])) {
        return 'clearComplete';
    }
    else if (isset($post['save'])) {
        return 'save';
    }
    else if (isset($post['update'])) {
        return 'update';
    }
    else if (isset($post['deleteList'])) {
        return 'deleteList';
    }
    else {
        return '';
    }
}

/**
* Get number of lists in database.
*
* @return Number of all rows from database.
*/
function getNumLists() {
    include(__DIR__ . '/class/connect.php');
    try {
        $query = $dbh->prepare("
            SELECT * FROM lists;
        ");
        $query->execute();
        $result = $query->rowCount();
    } catch (Exception $e) {
        echo "Couldn't get number of lists." . "<br>";
        echo $e->getMessage();
        exit;
    }
    return $result;
}

/**
* Get highest list ID from database.
* For use in setting new list ids sequentially.
*
* @return The max list_id available in database.
*/
function getHighestListId() {
    include(__DIR__ . '/class/connect.php');
    try {
        $query = $dbh->prepare("
            SELECT MAX(list_id) AS list_id FROM lists;
        ");
        $query->execute();
        $result = $query->fetch();
    } catch (Exception $e) {
        echo "Couldn't get highest list ID." . "<br>";
        echo $e->getMessage();
        exit;
    }
    return $result['list_id'];
}

/**
* Get all lists from database.
*
* @return Array of all saved lists in database.
*/
function getSavedLists() {
    include(__DIR__ . '/class/connect.php');
    try {
        $query = $dbh->prepare("
            SELECT * FROM lists
        ");
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_COLUMN, 1);
    } catch (Exception $e) {
        echo "Failed to retrieve lists." . "<br>";
        echo $e->getMessage();
        exit;
    }
    return $result;
}

/**
* Get list by list name from database.
*
* @param $name as string
* @return List JSON string as array.
*/
function getListByListName($name) {
    include(__DIR__ . '/class/connect.php');
    try {
        $query = $dbh->prepare("
            SELECT list FROM lists WHERE JSON_UNQUOTE(list_title) = ?;
        ");
        $query->bindParam(1, $name);
        $query->execute();
        $result = $query->fetchColumn();
    } catch (Exception $e) {
        echo "Failed to retrieve selected list." . "<br>";
        echo $e->getMessage();
        exit;
    }
    return $result;
}

/**
* Check if is object or array.
* For use in handling whether currently loaded list is from database or not.
*
* @param $_SESSION['displayList'] as Object or Array.
* @return Boolean true if Object, false if Array.
*/
function checkObjectOrArray($list) {
    if (gettype($list) == 'object') {
        return true;
    }
    else {
        return false;
    }
}
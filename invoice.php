<?php
// Sample for querying the database, managing queue of job data information

function addInvoice($db, $content, $footer, $queue_id)
{   
    
    $affected = $db->query("INSERT INTO `Invoices`(content,footer,queue_id) VALUES ('{$content}','{$footer}','{$queue_id}');");
    if (!isset($affected)) {
        http_response_code(500);
    }
}

function delQueue($db, $id)
{
    $affected = $db->query("DELETE FROM `Queues` WHERE `id`='" . $id . "';");

    if (!isset($affected)) {
        http_response_code(500);
    }
}

function resetQueue($db, $id)
{
    $affected = $db->query("UPDATE Queues SET 'position' = 1 WHERE `id`='" . $id . "';");

    if (!isset($affected)) {
        http_response_code(500);
    }
}

function listInvoices($db)
{
    $results = $db->query("SELECT id, name, position FROM Queues");
    $rdata = array();
    $count = 0;

    if (isset($results)) {
        while ($row = $results->fetchArray()) {
            $rdata[$count] = array("id" => strval($row['id']));
            $rdata[$count] += array("name" => $row['name']);
            $rdata[$count] += array("nextPos" => strval($row['position']));
            $count++;
        }

        header("Content-Type: application/json");
        print_r(json_encode($rdata));
    } else {
        http_response_code(500);
    }
}

function handleGETRequest()
{
    $dbname = "simplequeue.sqlite";    // database file name
    $db = new SQLite3($dbname);
    if (!empty($_POST['content']) && !empty($_POST['queue_id'])) {
        $content = $_POST['content'];
        $footer = $_POST['footer'];
        $queue_id = $_POST['queue_id'];
    }


    if (isset($content) && $queue_id) {
        addInvoice($db, $content, $footer, $queue_id);
    } else {
        listInvoices($db);
    }

    $db->close();
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    handleGETRequest();
} else {
    http_response_code(405);
}

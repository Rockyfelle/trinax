<?php
include 'controller.php';

$controller = new Controller();

echo json_encode($controller->sendReport());
<?php
require 'index.php';
header(header: 'Content-Type: application/json');
// header('Content-Type: application/json');
// echo '<pre>'; print_r($temperaturePoints); echo '</pre>';

echo json_encode($temperaturePoints);
?>
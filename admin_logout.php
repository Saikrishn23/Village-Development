<?php
session_start();
session_destroy();
header('Location: index.html');  // Changed from adminstrator.html to index.html
exit();
?>

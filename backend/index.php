<?php
require_once "config/config.php";

// 2. Test if $pdo exists from the config file
if (isset($pdo)) {
    echo "<h1>Library API</h1>";
    echo " Database connection is live.";
} else {
    echo " Connection failed.";
}
<?php

// Keep DB credentials in a separate file
// 1. Easy to exclude this file from SRC managers
// 2. Unique credentials on developments and productions servers
// 3. Unique credentials if working with multiple developers


define("DB_SERVER", "localhost");
define("DB_USER", "webuser");
define("DB_PASSWORD", "secretpassword");
define("DB_NAME", "chain_gang");

?>
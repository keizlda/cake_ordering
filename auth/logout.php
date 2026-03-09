<?php
session_start();
session_unset();
session_destroy();
header("Location: /cake_ordering/auth/login.php");
exit();
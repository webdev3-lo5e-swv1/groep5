<?php
// uitloggen.php — sessie vernietigen en doorsturen
session_start();
session_destroy();
header('Location: login.php');
exit;
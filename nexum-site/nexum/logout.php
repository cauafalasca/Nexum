<?php
require_once __DIR__ . '/php/config.php';

unset($_SESSION['user_id']);
unset($_SESSION['user_nome']);
unset($_SESSION['user_email']);
unset($_SESSION['user_tipo']);

header('Location: login.php');
exit;

<?php
if (!isset($_GET['redirection'])) {
    
    header('Location: /mp.php');
    exit();
}

require_once('../core/account.php');

if (isset($_GET['redirection']) && $_GET['redirection'] == 'mp') {

    deleteSession();

    header('Location: /mp.php');
    exit();

}

if (isset($_GET['redirection']) && $_GET['redirection'] == 'exit') {

    deleteSession();

    header('Location: /index.php');
    exit();
}

<?php
require_once "botlib.php";

$bot = new botTG($_GET["api"]);
$ip = $_SERVER["REMOTE_ADDR"];
if($bot->checkIp($ip)){
    if($bot->is_private()){//funziona solo in chat privata
        $bot->command_simple("{{message_text}}","{{message_text}}");
    }
}
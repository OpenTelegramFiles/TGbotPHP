<?php
require_once ("botlib.php");
$token = $_GET["api"];
$updates = file_get_contents("php://input");
$bot = new botTG($token,$updates, true);//debug activated

$ciaokeyboard = $bot->build_keyboard_of_inline(["altra schermata->"=> "ciao"]);
$bot->command_simple("/start",["text"=>"ciao {{message from first_name}}","keyboard"=>$ciaokeyboard,"photo"=>"start.png"]);
$backkeyboard = $bot->build_keyboard_of_inline(["indietro"=> "back"]);
$bot->simple_callback_response("ciao", ["text"=>"wow, che figata questa schermata! clicca giù per tornare indietro!","keyboard"=>$backkeyboard]);
$bot->simple_callback_response("back", ["text"=>"ciao {{message from first_name}}", "keyboard"=>$ciaokeyboard]);
//bot started, enjoy ;)


<?php
require_once ("botlib.php");
$token = $_GET["api"];
$updates = file_get_contents("php://input");
$bot = new botTG($token,$updates, true);//debug activated
$ciaokeyboard = $bot->build_keyboard_of_inline(["altra schermata->"=> "ciao"]);
$ciaokeyboard2 = $bot->build_keyboard_of_inline(["crea una immagine"=>"makeapicture"]);
$ciaokeyboard = $bot->merge_keyboards($ciaokeyboard, $ciaokeyboard2);//I create two buttons, one above and one below.
$bot->command_simple("/start",["text"=>"ciao {{message from first_name}}","keyboard"=>$ciaokeyboard,"photo"=>"start.png"]);
$backkeyboard = $bot->build_keyboard_of_inline(["indietro"=> "back"]);
$bot->simple_callback_response("makeapicture", ["text"=>"VERSIONE 2.0\nwow, è un esempio di invio di messaggio A PARTE, rispondendo ad un callbackquery, settando un semplice FALSE a fine riga!","keyboard"=>$backkeyboard,"photo"=>"start2.png"], false);
$bot->simple_callback_response("ciao", ["text"=>"VERSIONE 2.0\nwow, che figata questa schermata! clicca giù per tornare indietro!","keyboard"=>$backkeyboard]);
$bot->simple_callback_response("back", ["text"=>"ciao {{message from first_name}}", "keyboard"=>$ciaokeyboard]);
//bot started, enjoy ;)


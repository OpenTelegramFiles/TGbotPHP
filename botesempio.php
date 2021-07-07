<?php
require_once ("botlib.php");
$token = $_GET["api"];
$updates = file_get_contents("php://input");
$bot = new botTG($token,$updates, true);//debug activated
$ciaokeyboard = $bot->build_keyboard_of_inline(["altra schermata->"=> "ciao"]);
$ciaokeyboard2 = $bot->build_keyboard_of_inline(["crea una immagine"=>"makeapicture"]);
$altratastiera_sotto = $bot->build_keyboard_of_inline("LIB", "lib");
$ciaokeyboard = $bot->merge_multiple_keyboards([$ciaokeyboard, $ciaokeyboard2,$altratastiera_sotto]);//I create three buttons, one above, one at center and one below.
$bot->command_simple("/start",["text"=>"ciao {{message from first_name}}","keyboard"=>$ciaokeyboard,"photo"=>"start.png"]);
$backkeyboard = $bot->build_keyboard_of_inline(["indietro"=> "back"]);
$bot->simple_callback_response("makeapicture", ["text"=>"VERSIONE 2.0\nwow, è un esempio di invio di messaggio A PARTE, rispondendo ad un callbackquery, settando un semplice FALSE a fine riga!","keyboard"=>$backkeyboard,"photo"=>"start2.png"], false);
$bot->simple_callback_response("ciao", ["text"=>"VERSIONE 2.0\nwow, che figata questa schermata! clicca giù per tornare indietro!","keyboard"=>$backkeyboard]);
$bot->simple_callback_response("back", ["text"=>"ciao {{message from first_name}}", "keyboard"=>$ciaokeyboard]);
$esempiokeyb = $bot->build_keyboard_of_links(["ESEMPIO"=> "https://github.com/OpenTelegramFiles/TGbotPHP/blob/main/botesempio.php"]);
$keyboardclass = $bot->merge_keyboards($esempiokeyb, $backkeyboard);
$bot->simple_callback_response("lib", ["text"=> "questa è una semplice CLASSE php, per creare bot più che FACILMENTE!, il tutto è stato fatto in meno di 20 righe di codice!(questo bot, **escludendo la classe**!)","keyboard"=>$keyboardclass, "parse_mode"=>"markdown"]);
//bot started, enjoy ;)


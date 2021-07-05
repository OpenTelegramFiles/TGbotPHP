<?php
class botTG{
    public $update;
    protected $token, $debug;
    function __construct($token,$updates, $debug = false){
    $this->token = $token;
    $this->debug = $debug;
        $this->update = json_decode($updates);
    if ($this->debug) {
        if ($this->update->callback_query->data == "nokeyboard") {
           $this->send("editMessageText", array("message_id" => $this->update->callback_query->message->message_id, 'chat_id' => $this->update->callback_query->message->chat->id, 'text' => "📕 why this error?\n\nthis error is causated by no passed value of 'keyboard' in the input array in the method for making simple commands with botlibby @tgceo and @opentelegramfiles."));
        } elseif ($this->update->callback_query->data == "novalidkeyboard") {
           $this->send("editMessageText", array("message_id" => $this->update->callback_query->message->message_id, 'chat_id' => $this->update->callback_query->message->chat->id, 'text' => "📕 why this error?\n\nthis error is causated by no passed value of a VALID 'keyboard' in the input array in the method for making simple commands with botlib by @tgceo and @opentelegramfiles.\n\nNB:you can make keyboards with simple methods.. read the wiki/readme."));
        }
}
        if (isset($this->update->callback_query->id)) {//auto respond to callback query
            $this->send("answerCallbackQuery",array("callback_query_id"=>$this->update->callback_query->id));
        }
    }
    function simple_callback_response($input, $output){
        $parse = "html";
        $text = "text";
        $keyboard = [
            'inline_keyboard' => [ [["text"=>"no keyboard", "callback_data"=>"nokeyboard"]]]];
        if($input == $this->update->callback_query->data){
            if(is_array($output)){
                foreach ($output as $typecmd=>$value) {//not simple text
                    if ($typecmd == "text") {
                        if (is_array($value)) {
                            $text = "you have entered a keyboard or array. not text.";
                        } else {
                            $text = $value;
                            $text = str_replace("{{message text}}", $this->update->callback_query->data, $text);
                            $text = str_replace("{{message from first_name}}", $this->update->callback_query->from->first_name, $text);
                        }

                    } elseif ($typecmd == "parse_mode") {
                        if ($value == "html") {
                            $parse = "html";
                        } elseif ($value == "markdown") {
                            $parse = "markdown";
                        } else {
                            $parse = "html";
                        }
                    }elseif ($typecmd == "keyboard") {
                                $keyboard = $value;
                        }elseif(($typecmd != "start" && $typecmd != "keyboard" )&&($typecmd != "parse_mode" &&  $this->debug) ){
                        $keyboard = [
                            'inline_keyboard' => [ [["text"=>"you need to ask?", "callback_data"=>"needtoask"]]]];}
                }
                if(isset($this->update->callback_query->message->photo)){


                 $this->send("editMessageMedia", array("message_id" => $this->update->callback_query->message->message_id, 'chat_id' => $this->update->callback_query->message->chat->id, "media"=>json_encode(array("type"=>"photo", "media"=>$this->update->callback_query->message->photo[1]->file_id, "caption"=>$text)),"reply_markup" => json_encode($keyboard)));

                }else{
                    $this->send("editMessageText", array("message_id" => $this->update->callback_query->message->message_id, 'chat_id' => $this->update->callback_query->message->chat->id, 'text' => $text,"parse_mode"=>$parse,'resize_keyboard' => "true", "reply_markup" => json_encode($keyboard)));

                }
                       }
            else{
                $this->send("editMessageText",  array("message_id" => $this->update->callback_query->message->message_id, 'chat_id' => $this->update->callback_query->message->chat->id, 'text' => $output,"parse_mode"=>$parse));


                  }
        }
    }
    function command_simple($input, $output){

            $input = str_replace("{{message text}}", $this->update->message->text, $input);
            $input = str_replace("{{message from first_name}}", $this->update->message->from->first_name, $input);
            $parse = "html";
        $photo = null;
            $keyboard = [
                'inline_keyboard' => [[["text" => "no keyboard", "callback_data" => "nokeyboard"]]]];
            $text = "text";
            if ($input == $this->update->message->text) {
                if (is_array($output)) {
                    foreach ($output as $typecmd => $value) {//not simple text
                        if ($typecmd == "text") {
                            if (is_array($value)) {
                                $text = "you have immited a keyboard or array. not text.";
                            } else {
                                   $text = $value;
                                $text = str_replace("{{message text}}", $this->update->message->text, $text);
                                $text = str_replace("{{message from first_name}}", $this->update->message->from->first_name, $text);
                            }
                        } elseif ($typecmd == "parse_mode") {
                            if ($value == "html") {
                                $parse = "html";
                            } elseif ($value == "markdown") {
                                $parse = "markdown";
                            } else {
                                $parse = "html";
                            }
                        }elseif($typecmd == "photo"){
                            $photo = $value;

                        } elseif ($typecmd == "keyboard") {
                            $keyboard = $value;
                        } elseif (($typecmd != "start" && $typecmd != "keyboard") && ($typecmd != "parse_mode" && $this->debug)) {
                            $keyboard = array( 'inline_keyboard' => array(array(array("text" => "you need to ask?", "callback_data" => "needtoask"))));
                        }
                    }
                    if($photo != null){
                        $this->send("sendPhoto", array('chat_id' =>$this->update->message->chat->id, 'photo'     => new CURLFile(realpath($photo)),'caption' => $text, "parse_mode" => $parse, 'resize_keyboard' => "true", "reply_markup" => json_encode($keyboard)));

                    }else{
                        $this->send("sendMessage", array('chat_id' =>$this->update->message->chat->id, 'text' => $text, "parse_mode" => $parse, 'resize_keyboard' => "true", "reply_markup" => json_encode($keyboard)));

                    }
                     } else {
                    if($photo != null){
                        $this->send("sendPhoto", array('chat_id' =>$this->update->message->chat->id, 'photo'     => new CURLFile(realpath($photo)),'caption' => $text, "parse_mode" => $parse));

                    }else {
                        $this->send("sendMessage", array('chat_id' => $this->update->message->chat->id, 'text' => $output, "parse_mode" => $parse));
                    } }
            }
        }
    function build_keyboard_of_links($associativearrayoflinks){
        $urlkeyb = array();
        foreach ($associativearrayoflinks as $Urlbtn_text=>$value){
            $urlkeyb = array_merge($urlkeyb, array(array("text"=>$Urlbtn_text, "callback_data"=>$value)));
        }
        return array('inline_keyboard' => array($urlkeyb));
    }
    function merge_keyboards($keyboard1, $keyboard2){
        return array_merge($keyboard1, $keyboard2);
    }
    function build_keyboard_of_inline($associativearrayofinline){
        $inline = array();
        foreach ($associativearrayofinline as $Inlinebtn_text=>$value){
           $inline = array_merge($inline, array(array("text"=>$Inlinebtn_text, "callback_data"=>$value)));
        }
        return array('inline_keyboard' => array($inline));
    }
  function send($method, $data) {
      $url = "https://api.telegram.org/bot".$this->token. "/" . $method; if (!$curld = curl_init()) { echo "exit"; exit; } curl_setopt($curld, CURLOPT_POST, true); curl_setopt($curld, CURLOPT_POSTFIELDS, $data); curl_setopt($curld, CURLOPT_URL, $url); curl_setopt($curld, CURLOPT_RETURNTRANSFER, true); $output = curl_exec($curld); curl_close($curld); echo json_encode($output); return $output; }
}
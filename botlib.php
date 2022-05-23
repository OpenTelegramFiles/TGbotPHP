<?php
class botTG{
    public $update;
    protected $token, $debug;
    function __construct($token,$updates= false, $debug = false, $debugFile = false){
    $this->token = $token;
    $this->debug = $debug;
    if ($updates!=false){
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
    }


    static function checkIp(string $ip)
    {
        $ranges = [
            [
                'address' => '149.154.160.0',
                'netmask' => '20',
                'netmask_decimal' => -4096,
                'integer_id' => '2509938688',
            ],
            [
                'address' => '91.108.4.0',
                'netmask' => '22',
                'netmask_decimal' => -1024,
                'integer_id' => '1533805568',
            ],
        ];

        $ip = trim($ip);
        $ipDecimal = ip2long($ip);

        if (($ipDecimal & $ranges[0]['netmask_decimal']) == $ranges[0]['integer_id']) {

            return true;
        }

        if (($ipDecimal & $ranges[1]['netmask_decimal']) == $ranges[1]['integer_id']) {

            return true;
        }

        return false;
    }
    function forward_message($chat_id, $message_id, $another_chat_id){
            $this->send("forwardMessage",array("chat_id" => $chat_id,
                                               "from_chat_id" => $another_chat_id,
                                               "message_id"=> $message_id));

    }
    function forward_message_from_reply($chat_id,$FromBot = false){
        if($FromBot){
            if(isset($this->update->message->photo)){
                $this->send_message($chat_id, ["text"=>$this->update->message->text,"photo"=>$this->update->message->photo[1]->file_id]);

            }else{
                $this->send_message($chat_id, ["text"=>$this->update->message->text]);

            }}else{
            $this->send("forwardMessage",array("chat_id" => $chat_id,
                                        "from_chat_id" => $this->update->message->reply_to_message->forward_from->id,
                                        "message_id"=> $this->update->message->reply_to_message->id));
        }

    }
    function simple_callback_response($input, $output, $edit = true){
        $parse = "html";
        $text = "text";
        $photo = null;
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
                    }elseif($typecmd == "photo"){
                        $photo = $value;

                    }elseif ($typecmd == "keyboard") {
                                $keyboard = $value;
                        }
                }
                if($edit){
                    if(isset($this->update->callback_query->message->photo)){
                        if($photo != null){
                            $this->send("editMessageMedia", array("message_id" => $this->update->callback_query->message->message_id, 'chat_id' => $this->update->callback_query->message->chat->id, "media"=>json_encode(array("type"=>"photo", "media"=>$photo, "caption"=>$text)),"reply_markup" => json_encode($keyboard)));
                        }else{
                            $this->send("editMessageMedia", array("message_id" => $this->update->callback_query->message->message_id, 'chat_id' => $this->update->callback_query->message->chat->id, "media"=>json_encode(array("type"=>"photo", "media"=>$this->update->callback_query->message->photo[1]->file_id, "caption"=>$text)),"reply_markup" => json_encode($keyboard)));
                        }

                    }else{
                       return $this->send("editMessageText", array("message_id" => $this->update->callback_query->message->message_id, 'chat_id' => $this->update->callback_query->message->chat->id, 'text' => $text,"parse_mode"=>$parse,'resize_keyboard' => "true", "reply_markup" => json_encode($keyboard)));

                    }
                }else{

                        if($photo != null){
                            $this->send("sendPhoto", array('chat_id' =>$this->update->callback_query->message->chat->id, 'photo'     => new CURLFile(realpath($photo)),'caption' => $text, "parse_mode" => $parse, 'resize_keyboard' => "true", "reply_markup" => json_encode($keyboard)));

                        }else{
                            $this->send("sendMessage", array('chat_id' =>$this->update->callback_query->message->chat->id, 'text' => $text, "parse_mode" => $parse, 'resize_keyboard' => "true", "reply_markup" => json_encode($keyboard)));

                        }


                }

                       }
            else{
                $output = str_replace("{{message text}}", $this->update->callback_query->data, $output);
                $output= str_replace("{{message from first_name}}", $this->update->callback_query->from->first_name, $output);
                if($edit){

                    if(isset($this->update->callback_query->message->photo)){
                        $this->send("editMessageMedia", array("message_id" => $this->update->callback_query->message->message_id, 'chat_id' => $this->update->callback_query->message->chat->id, "media"=>json_encode(array("type"=>"photo", "media"=>$this->update->callback_query->message->photo[1]->file_id, "caption"=>$output)),"reply_markup" => json_encode($keyboard)));
                    }else{
                        $this->send("editMessageText",  array("message_id" => $this->update->callback_query->message->message_id, 'chat_id' => $this->update->callback_query->message->chat->id, 'text' => $output,"parse_mode"=>$parse));
                    }
                }else{

                        $this->send("sendMessage", array('chat_id' => $this->update->message->chat->id, 'text' => $output, "parse_mode" => $parse));

                }

                  }
        }
    }
    function is_private(){
        if($this->update->message->chat->type == "private" or $this->update->callback_query->message->chat->type == "private"){
            return true;
        }else{
            return false;
        }
    }
    function get_message_id(){

            if(isset($this->update->message->id)){

                return $this->update->message->id;
            }else{
                return null;
            }

    }
    function get_chat_id(){
        if(isset($this->update->callback_query->message->chat->id)){
            return $this->update->callback_query->message->chat->id;
        }elseif (isset($this->update->message->chat->id)){
            return $this->update->message->chat->id;
        }else{
            return null;
        }
    }
    function get_text_message(){
        if(isset($this->update->message->text)){

            return $this->update->message->text;
        }else{
return null;
        }
    }
    function get_data(){
        if(isset($this->update->callback_query->data)){

            return $this->update->callback_query->data;
        }else{
            return null;
        }
    }
    function check_text_message($messagetocheck){
        if(empty($this->update->message->text)){
            return false;
        }
        if($messagetocheck == $this->update->message->text){
            return true;
        }else{
            return false;
        }
    }
    function check_callbackquery_data($CallbackQuerydatatocheck){
        if (empty($this->update->callback_query->data)){return false;}
        if($CallbackQuerydatatocheck == $this->update->callback_query->data){
            return true;
        }else{
            return false;
        }
    }
    function edit_message( $output, $edit = true){
        $parse = "html";
        $text = "text";
        $photo = null;
        $keyboard = null;
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
                }elseif($typecmd == "photo"){
                    $photo = $value;

                }elseif ($typecmd == "keyboard") {
                    $keyboard = $value;
                }
            }

                if(isset($this->update->callback_query->message->photo)){
                    if($photo != null){
                        if($keyboard != null){
                            $this->send("editMessageMedia", array("message_id" => $this->update->callback_query->message->message_id, 'chat_id' => $this->update->callback_query->message->chat->id, "media"=>json_encode(array("type"=>"photo", "media"=>$photo, "caption"=>$text)),"reply_markup" => json_encode($keyboard)));

                        }else{
                            $this->send("editMessageMedia", array("message_id" => $this->update->callback_query->message->message_id, 'chat_id' => $this->update->callback_query->message->chat->id, "media"=>json_encode(array("type"=>"photo", "media"=>$photo, "caption"=>$text))));

                        }
                         }else{
                        if ($keyboard != null){
                            $this->send("editMessageMedia", array("message_id" => $this->update->callback_query->message->message_id, 'chat_id' => $this->update->callback_query->message->chat->id, "media"=>json_encode(array("type"=>"photo", "media"=>$this->update->callback_query->message->photo[1]->file_id, "caption"=>$text)),"reply_markup" => json_encode($keyboard)));

                        }else{
                            $this->send("editMessageMedia", array("message_id" => $this->update->callback_query->message->message_id, 'chat_id' => $this->update->callback_query->message->chat->id, "media"=>json_encode(array("type"=>"photo", "media"=>$this->update->callback_query->message->photo[1]->file_id, "caption"=>$text))));

                        }
                         }

                }else{
                    if ($keyboard != null){
                        $this->send("editMessageText", array("message_id" => $this->update->callback_query->message->message_id, 'chat_id' => $this->update->callback_query->message->chat->id, 'text' => $text,"parse_mode"=>$parse,'resize_keyboard' => "true", "reply_markup" => json_encode($keyboard)));

                    }else{
                        $this->send("editMessageText", array("message_id" => $this->update->callback_query->message->message_id, 'chat_id' => $this->update->callback_query->message->chat->id, 'text' => $text,"parse_mode"=>$parse));
                    }

                }


        }
        else{
            $output = str_replace("{{message text}}", $this->update->callback_query->data, $output);
            $output= str_replace("{{message from first_name}}", $this->update->callback_query->from->first_name, $output);


                if(isset($this->update->callback_query->message->photo)){
                    $this->send("editMessageMedia", array("message_id" => $this->update->callback_query->message->message_id, 'chat_id' => $this->update->callback_query->message->chat->id, "media"=>json_encode(array("type"=>"photo", "media"=>$this->update->callback_query->message->photo[1]->file_id, "caption"=>$output)),"reply_markup" => json_encode($keyboard)));
                }else{
                    $this->send("editMessageText",  array("message_id" => $this->update->callback_query->message->message_id, 'chat_id' => $this->update->callback_query->message->chat->id, 'text' => $output,"parse_mode"=>$parse));
                }


        }

    }
    function command_simple($input, $output, $modify_input = true){
            if($modify_input){

                $input = str_replace("{{message text}}", $this->update->message->text, $input);
                $input = str_replace("{{message from first_name}}", $this->update->message->from->first_name, $input);
            }
            if($input == $this->update->message->text){
                $this->send_message($this->update->message->chat->id, $output);
            }


        }
        function send_message($chat_id, $arguments){
            $parse = "html";
            $photo = null;
            $keyboard = null;
            $text = "text";
            if (is_array($arguments)) {
                foreach ($arguments as $typecmd => $value) {//not simple text
                    if ($typecmd == "text") {
                        if (is_array($value)) {
                            $text = "you have immited a keyboard or array. not text.";
                        } else {
                            $text = $value;
                            $text = str_replace("{{message text}}",$this->update->message->text, $text);
                            $text = str_replace("{{message from first_name}}",  $this->update->message->from->first_name, $text);
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
                    if($keyboard != null){
                        $this->send("sendPhoto", array('chat_id' =>$chat_id, 'photo'=> new CURLFile(realpath($photo)),'caption' => $text, "parse_mode" => $parse, 'resize_keyboard' => "true", "reply_markup" => json_encode($keyboard)));
                    }else{
                        $this->send("sendPhoto", array('chat_id' =>$chat_id, 'photo'=> new CURLFile(realpath($photo)),'caption' => $text, "parse_mode" => $parse));
                    }

                }else{
                    if ($keyboard != null){
                        $this->send("sendMessage", array('chat_id' =>$chat_id, 'text' => $text, "parse_mode" => $parse, 'resize_keyboard' => "true", "reply_markup" => json_encode($keyboard)));

                    }else{
                        $this->send("sendMessage", array('chat_id' =>$chat_id, 'text' => $text, "parse_mode" => $parse));

                    }

                }
            } else {

                $arguments = str_replace("{{message text}}",$this->update->message->text, $arguments);
                $arguments = str_replace("{{message from first_name}}", $this->update->message->from->first_name, $arguments);

                $this->send("sendMessage", array('chat_id' => $chat_id, 'text' => $arguments, "parse_mode" => $parse));
                 }
        }
    function build_keyboard_of_links($associativearrayoflinks){
        $urlkeyb = array();
        foreach ($associativearrayoflinks as $Urlbtn_text=>$value){
            $urlkeyb = array_merge($urlkeyb, array(array("text"=>$Urlbtn_text, "url"=>$value)));
        }
        return array('inline_keyboard' => array($urlkeyb));
    }
    function merge_keyboards($keyboard1_first_up, $keyboard2_last_down){
        return array_merge_recursive($keyboard1_first_up, $keyboard2_last_down);
    }
    function merge_multiple_keyboards($keyboards){
        $i = 0;
        $keyboard_complete = null;
        foreach ($keyboards as $keyboard){
            if ($i >0){
$keyboard_complete =array_merge_recursive($keyboard_complete, $keyboard);
            }else{
                $i++;
                $keyboard_complete = $keyboard;
            }

        }
        return $keyboard_complete;
    }
    function build_keyboard_of_inline($associativearrayofinline){
        $inline = array();
        foreach ($associativearrayofinline as $Inlinebtn_text=>$value){
           $inline = array_merge($inline, array(array("text"=>$Inlinebtn_text, "callback_data"=>$value)));
        }
        return array('inline_keyboard' => array($inline));
    }
  function send($method, $data) {
      $url = "https://api.telegram.org/bot".$this->token. "/" . $method; if (!$curld = curl_init()) {exit; } curl_setopt($curld, CURLOPT_POST, true); curl_setopt($curld, CURLOPT_POSTFIELDS, $data); curl_setopt($curld, CURLOPT_URL, $url); curl_setopt($curld, CURLOPT_RETURNTRANSFER, true); $output = curl_exec($curld); curl_close($curld);
        if($this->debug){
            $err = json_decode($output, true);
            if ($err["ok"] == false){
                if($this->debugFile){$fil = fopen("errors.txt", "a+");
                    fwrite($fil, $err["description"]);
                    fclose($err);

                }
                else{
                    $this->send_message($this->get_chat_id(), $err["description"]);
                }

            }
        }
        return $output; }
}
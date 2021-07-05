<?php
class botTG{
    public $message_from_user_chat_id, $callback_query_from_chat_id, $message_group_chat_id, $channel_post_text, $channel_post_chat_id, $channel_post_msg_id, $channel_post_username, $group_username, $msg_text, $callback_query_id, $callback_query_from_user_chat_id, $callquery_from_username, $title_of_chat, $msg_first_name, $msg_from_username, $type_of_chat, $document_type, $callback_query_data, $user_new_members, $callback_query_chat_id, $message_replyed_text, $message_replyed_from_user_chat_id, $message_replyed_from_username,$callbackquery_message_id, $callbackquery_message_from_chat_id, $callbackquery_from_firstname;
    protected $token, $debug;
    function __construct($token,$updates, $debug = false){
    $this->token = $token;
    $this->debug = $debug;
    $update = json_decode($updates, TRUE);
    $this->message_from_user_chat_id = $update['message']['from']['id'];

    $this->message_group_chat_id = $update['message']['chat']['id'];
    $this->channel_post_text = $update['channel_post']['text'];
    $this->channel_post_chat_id = $update['channel_post']['chat']['id'];
    $this->channel_post_msg_id = $update['channel_post']['message_id'];
    $this->channel_post_username = $update['channel_post']['chat']['username'];
    $this->group_username = $update["message"]["chat"]["username"];
    $this->msg_text = $update['message']["text"];
    $this->callback_query_id = $update["callback_query"]["id"];
    $this->callback_query_from_user_chat_id = $update['callback_query']['from']['id'];
$this->callbackquery_from_firstname =  $update['callback_query']['from']['first_name'];
        $this->callback_query_from_chat_id = $update['callback_query']["message"]['chat']['id'];
    $this->callquery_from_username = $update["callback_query"]["from"]["username"];
    $this->title_of_chat = $update["message"]["chat"]["title"];
    $this->msg_first_name = $update['message']['from']['first_name'];
    $this->msg_from_username = $update['message']['from']['username'];
    $this->type_of_chat = $update['message']['chat']['type'];
    $this->document_type = $update["message"]["document"]["file_id"];
    $this->callback_query_data = $update["callback_query"]["data"];
    $this->user_new_members = $update["message"]["new_chat_members"];
    $this->callback_query_chat_id = $update["callback_query"]["chat"]["id"];
    $this->message_replyed_text = $update["message"]["reply_to_message"]["text"];
    $this->message_replyed_from_user_chat_id = $update["message"]["reply_to_message"]["from"]["id"];
    $this->message_replyed_from_username = $update["message"]["reply_to_message"]["from"]["username"];
    $this->callbackquery_message_id = $update['callback_query']["message"]['message_id'];

    if ($this->debug) {
        if ($this->callback_query_data == "nokeyboard") {

           $this->send("editMessageText", array("message_id" => $this->callbackquery_message_id, 'chat_id' => $this->callback_query_from_user_chat_id, 'text' => "📕 why this error?\n\nthis error is causated by no passed value of 'keyboard' in the input array in the method for making simple commands with botlibby @tgceo and @opentelegramfiles."));
        } elseif ($this->callback_query_data == "novalidkeyboard") {
           $this->send("editMessageText", array("message_id" => $this->callbackquery_message_id, 'chat_id' => $this->callback_query_from_user_chat_id, 'text' => "📕 why this error?\n\nthis error is causated by no passed value of a VALID 'keyboard' in the input array in the method for making simple commands with botlib by @tgceo and @opentelegramfiles.\n\nNB:you can make keyboards with simple methods.. read the wiki/readme."));

        }

}

        if (isset($this->callback_query_id)) {//auto respond to callback query
            $this->send("answerCallbackQuery",array("callback_query_id"=>$this->callback_query_id));
        }

    }
    function simple_callback_response($input, $output){
        $parse = "html";
        $text = "text";
        $keyboard = [
            'inline_keyboard' => [ [["text"=>"no keyboard", "callback_data"=>"nokeyboard"]]]];
        if($input == $this->callback_query_data){

            if(is_array($output)){



                foreach ($output as $typecmd=>$value) {//not simple text
                    if ($typecmd == "text") {
                        if (is_array($value)) {
                            $text = "you have immited a keyboard or array. not text.";
                        } else {
                            $text = $value;
                            $text = str_replace("{{message text}}", $this->callback_query_data, $text);
                            $text = str_replace("{{message from first_name}}", $this->callbackquery_from_firstname, $text);

                        }

                    } elseif ($typecmd == "parse_mode") {
                        if ($value == "html") {
                            $parse = "html";
                        } elseif ($value == "markdown") {
                            $parse = "markdown";
                        } else {
                            $value = "html";
                        }
                    } elseif ($typecmd == "keyboard") {



                                $keyboard = $value;

                        }elseif(($typecmd != "start" && $typecmd != "keyboard" )&&($typecmd != "parse_mode" &&  $this->debug) ){
                        $keyboard = [
                            'inline_keyboard' => [ [["text"=>"you need to ask?", "callback_data"=>"needtoask"]]]];}


                }

                $this->send("editMessageText", array("message_id" => $this->callbackquery_message_id, 'chat_id' => $this->callback_query_from_user_chat_id, 'text' => $text,"parse_mode"=>$parse,'resize_keyboard' => "true", "reply_markup" => json_encode($keyboard)));

            }
            else{

                $this->send("editMessageText",  array("message_id" => $this->callbackquery_message_id, 'chat_id' => $this->callback_query_from_user_chat_id, 'text' => $output,"parse_mode"=>$parse));

            }
        }
    }
    function command_simple($input, $output){

            $input = str_replace("{{message text}}", $this->msg_text, $input);
            $input = str_replace("{{message from first_name}}", $this->msg_first_name, $input);
            $parse = "html";
            $keyboard = [
                'inline_keyboard' => [[["text" => "no keyboard", "callback_data" => "nokeyboard"]]]];
            $text = "text";
            if ($input == $this->msg_text) {

                if (is_array($output)) {


                    foreach ($output as $typecmd => $value) {//not simple text
                        if ($typecmd == "text") {
                            if (is_array($value)) {
                                $text = "you have immited a keyboard or array. not text.";
                            } else {
                                   $text = $value;
                                $text = str_replace("{{message text}}", $this->msg_text, $text);
                                $text = str_replace("{{message from first_name}}", $this->msg_first_name, $text);

                            }

                        } elseif ($typecmd == "parse_mode") {
                            if ($value == "html") {
                                $parse = "html";
                            } elseif ($value == "markdown") {
                                $parse = "markdown";
                            } else {
                                $parse = "html";
                            }
                        } elseif ($typecmd == "keyboard") {
                            $keyboard = $value;

                        } elseif (($typecmd != "start" && $typecmd != "keyboard") && ($typecmd != "parse_mode" && $this->debug)) {
                            $keyboard = array( 'inline_keyboard' => array(array(array("text" => "you need to ask?", "callback_data" => "needtoask"))));

                        }


                    }

                   $this->send("sendMessage", array('chat_id' => $this->message_from_user_chat_id, 'text' => $text, "parse_mode" => $parse, 'resize_keyboard' => "true", "reply_markup" => json_encode($keyboard)));
                } else {

                 $this->send("sendMessage", array('chat_id' => $this->message_from_user_chat_id, 'text' => $output, "parse_mode" => $parse));
                }
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

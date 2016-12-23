<?php
ob_start();
define('API_KEY','XXX:XXX');

function makeHTTPRequest($method,$datas=[]){
    $url = "https://api.telegram.org/bot".API_KEY."/".$method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($datas));
    $res = curl_exec($ch);
    if(curl_error($ch)){
        var_dump(curl_error($ch));
    }else{
        return json_decode($res);
    }
}

// Fetching UPDATE
$update = json_decode(file_get_contents('php://input'));

$time = file_get_contents("http://api.bridge-ads.ir/td/?td=time");
$date = file_get_contents("http://api.bridge-ads.ir/td/?td=date");

if(isset($update->callback_query)){
    $callbackMessage = 'آپدیت شد';
    var_dump(makeHTTPRequest('answerCallbackQuery',[
        'callback_query_id'=>$update->callback_query->id,
        'text'=>$callbackMessage
    ]));
    $chat_id = $update->callback_query->message->chat->id;
    $message_id = $update->callback_query->message->message_id;
    $tried = $update->callback_query->data+1;
    var_dump(
        makeHTTPRequest('editMessageText',[
            'chat_id'=>$chat_id,
            'message_id'=>$message_id,
            'text'=>"امروز 📅
$date
ساعت🕕
$time
➖➖➖➖➖
Today is📅
 ".date("Y/M/D")."
The time is🕕
".date("h:i:sa")
      ,
            'reply_markup'=>json_encode([
                'inline_keyboard'=>[
                    [
                        ['text'=>"رفرش زمان",'callback_data'=>"$tried"],
                    ['text'=>"گیتاب اونیکس",'url'=>'https://github.com/onyxtm/tdbot']
                    ]
                ]
            ])
        ])
    );

}else{
    var_dump(makeHTTPRequest('sendMessage',[
        'chat_id'=>$update->message->chat->id,
                  'text'=>"امروز 📅
$date
ساعت🕕
$time
➖➖➖➖➖
Today is📅
 ".date("Y/M/D")."
The time is🕕
".date("h:i:sa"),
        'reply_markup'=>json_encode([
            'inline_keyboard'=>[
                [
                    ['text'=>"رفرش زمان",'callback_data'=>'1']
                ],[
                    ['text'=>"گیتاب اونیکس",'url'=>'https://github.com/onyxtm/tdbot']
                ]
            ]
        ])
    ]));
}

file_put_contents('log',ob_get_clean());

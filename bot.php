<?php

ob_start();
error_reporting(0);
date_default_timezone_set('Asia/Tehran');
// -=-=-=-=-=-=-=-=-=
$Token = '6744043899:AAHukgGe1PboYenxneg6YM72CwAHUcOOXxU'; //
$Admin = 6697495130; // ایدی عددی ادمین
$UsernameBot = "shhdhdhdhdvegbot"; // یوزربات بات
// -=-=-=-=-=-=-=-=-=
define('API_KEY',$Token);
function sheikh($method,$datas=[]){
    $url = "https://api.telegram.org/bot".API_KEY."/".$method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
    $res = curl_exec($ch);
    if(curl_error($ch)){
        var_dump(curl_error($ch));
    }else{
        return json_decode($res);
    }
}
function step($address,$data){
	$user = json_decode(file_get_contents($address),true);	
	$user["step"]="$data";
	$user = json_encode($user,true);
	file_put_contents($address,$user);
}
function is_admin($from_id){
	if($from_id == $Admin){
		return 1;
	}else{
		return 0;
	}
}
//============================================
$update = json_decode(file_get_contents('php://input'));
$message = $update->message; 
$chat_id = $message->chat->id;
$from_id = $message->from->id;
$first_name = $message->from->first_name;
$text = $message->text;
$message_id = $message->message_id;  
$inline = $update->inline_query;
$inline_id = $update->inline_query->id;
$inline_text = $update->inline_query->query;
$inline_from = $update->inline_query->from->id;
$inline_name = $update->inline_query->from->first_name;
$forward_name = $message->forward_from->first_name;
$forward_from = $message->forward_from;
$forward_id = $forward_from->id;
$forward_text = $forward_from->message;
$forward_username = $forward_from->username;
$data = $update->callback_query->data;
$messageid = $update->callback_query->message->message_id;
$chatid = $update->callback_query->message->chat->id;
$fromid = $update->callback_query->from->id;
$firstname = $update->callback_query->from->first_name;
$user = json_decode(file_get_contents("data/$from_id.json"),true);
$step = $user["step"];
$calid = $update->callback_query->id;
$usernameca = $update->callback_query->from->username;
//=============================================
if(!file_exists("data/$from_id.json")){
	step("data/$from_id.json","None");
}
if($text == "/start"){
		sheikh('sendmessage',[
		'chat_id'=>$chat_id,
		'text'=>"سلام $first_name عزیز 🌹
		
به ربات نجوا (ارسال پیام خصوصی) به شخص مورد نظر خوش آمدید !

برای شروع کافیست از دکمه زیر استفاده کنید !

اگر بصورت اینلاین (از راه دور) میخواهید استفاده کنید راهنما را مطالعه کنید✅",
		'reply_markup'=>json_encode([
		'inline_keyboard'=>[
		[['text'=>'📬 ارسال نجوا (پیام خصوصی) 📬','callback_data'=>'send']],
		[['text'=>'🤔 راهنما 🤔','callback_data'=>'help']]
		],
	])
	]);
}
elseif($text == "test"){
	if(is_admin($from_id)){
		sheikh('sendmessage',[
		'chat_id'=>$chat_id,
		'text'=>"Yeah"
		]);
	}else{
		sheikh('sendmessage',[
		'chat_id'=>$chat_id,
		'text'=>"Nope"
		]);
	}
}
elseif($data == 'send'){
	sheikh('editMessageText',[
	'chat_id'=>$chatid,
	'message_id'=>$messageid,
	'text'=>"سلام مجدد $firstname عزیز 🌹
	
برای ارسال نجوا (پیام خصوصی) به شخص مورد نظر شما میتوانید با استفاده از موارد زیر اقدام کنید :

➊ فوروارد یک پیام از کاربر (در صورتی که ربات را استارت کرده باشد)
➋ ارسال آیدی عددی کاربر (اگر نمیدانید ایدی عددی چیست به قسمت راهنما رجوع کنید)
➌ ارسال یوزرنیم کاربر (مخصوص مواقعی که دسترسی به پیام یا ایدی شخص مورد نظر ندارید و یوزرنیم فقط جهت تگ کردن کاربر است)

حال یکی از موارد بالا را انتخاب کنید و ارسال کنید. 
در غیر این صورت گزینه لغو را بزنید 👌",
	'reply_markup'=>json_encode([
		'inline_keyboard'=>[
		[['text'=>'لغو عملیات','callback_data'=>'cancel']]
		],
	])
	]);
	step("data/$fromid.json","send");
}
elseif($data == 'help'){
	sheikh('editMessageText',[
	'chat_id'=>$chatid,
	'message_id'=>$messageid,
	'text'=>"سلام 😁 
به بخش راهنمای ربات خوش آمدید !
برای بدست آوردن ایدی عددی کاربر باید یک پیام او را به ربات @userinfobot بفرستید !
برای استفاده از ربات به صورت از راه دور شما میتوانید از دو روش اقدام کنید 👇

1- از قسمت ( ارسال نجوا ) اقدام کنید.

2- از فرمت زیر استفاده کنید :
برای ارسال نجوا با یوزرنیم :
$UsernameBot
Text
@username 

استفاده کنید. (جای Text متن مورد نظر را وارد کنید)
",
	'parse_mode'=>'HTML',
	'reply_markup'=>json_encode([
		'inline_keyboard'=>[
		[['text'=>'منوی اصلی','callback_data'=>'menu']]
		],
	])
	]);
}
elseif($step == 'send' && isset($forward_from)){
		$Result = sheikh('getChatMember',[
		'chat_id'=>$forward_id,
		'user_id'=>$forward_id
		]);
		$ok = $Result->ok;
		if($ok != 1){
			sheikh('sendmessage',[
			'chat_id'=>$chat_id,
			'text'=>"اوپس 😲
			
اطلاعات کاربر دریافت نشد !
از او بخواهید ربات را استارت بزند سپس مجدد تلاش کنید ☹️"
		]);
			sheikh('sendmessage',[
			'chat_id'=>$chat_id,
			'text'=>"سلام $first_name عزیز 🌹
		
به ربات نجوا (ارسال پیام خصوصی) به شخص مورد نظر خوش آمدید !

برای شروع کافیست ابتدا ایدی ربات را تایپ کنید و یک فاصله بزنید تا راهنمای ارسال پیام خصوصی نمایش داده شود",
			'reply_markup'=>json_encode([
			'inline_keyboard'=>[
			[['text'=>'📬 ارسال نجوا (پیام خصوصی) 📬','callback_data'=>'send']],
			[['text'=>'🤔 راهنما 🤔','callback_data'=>'help']]
			],
		])
	]);
	 step("data/$from_id.json","None");
		}else{
			sheikh('sendmessage',[
			'chat_id'=>$chat_id,
			'text'=>"اطلاعات کاربر به شرح زیر میباشد :

•• آیدی عددی ~> $forward_id
•• یوزرنیم ~> @$forward_username
•• اسم ~> $forward_name",
			'reply_markup'=>json_encode([
			'inline_keyboard'=>[
			[['text'=>"ارسال نجوا به ".$forward_name."",'switch_inline_query'=>"\nپیام شما\n#".$forward_id.""]],
			[['text'=>"منوی اصلی",'callback_data'=>"menu"]]
			],
		])
		]);
		 step("data/$from_id.json","None");
		 
	}
}
elseif(preg_match('/^(\d+)/',$text,$y) && $step == 'send'){
		$Result = sheikh('getChatMember',[
		'chat_id'=>$text,
		'user_id'=>$text
		]);
		$ok = $Result->ok;
		$name = $Result->result->user->first_name;
		$usern = $Result->result->user->username;
		if($ok != 1){
			sheikh('sendmessage',[
			'chat_id'=>$chat_id,
			'text'=>"اوپس 😲
			
اطلاعات کاربر دریافت نشد !
از او بخواهید ربات را استارت بزند سپس مجدد تلاش کنید ☹️"
		]);
			sheikh('sendmessage',[
			'chat_id'=>$chat_id,
			'text'=>"سلام $first_name عزیز 🌹
		
به ربات نجوا (ارسال پیام خصوصی) به شخص مورد نظر خوش آمدید !

برای شروع کافیست ابتدا ایدی ربات را تایپ کنید و یک فاصله بزنید تا راهنمای ارسال پیام خصوصی نمایش داده شود",
			'reply_markup'=>json_encode([
			'inline_keyboard'=>[
			[['text'=>'📬 ارسال نجوا (پیام خصوصی) 📬','callback_data'=>'send']],
			[['text'=>'🤔 راهنما 🤔','callback_data'=>'help']]
			],
		])
	]);
	 step("data/$from_id.json","None");
		}else{
			sheikh('sendmessage',[
			'chat_id'=>$chat_id,
			'text'=>"اطلاعات کاربر به شرح زیر میباشد :

•• آیدی عددی ~> $text
•• یوزرنیم ~> @$usern
•• اسم ~> $name",
			'reply_markup'=>json_encode([
			'inline_keyboard'=>[
			[['text'=>"ارسال نجوا به ".$name."",'switch_inline_query'=>"\nپیام شما\n#".$text.""]],
			[['text'=>"منوی اصلی",'callback_data'=>"menu"]]
			],
		])
		]);
		 step("data/$from_id.json","None");
	}
}
elseif(preg_match('/^@/',$text,$x) && $step == 'send'){
		sheikh('sendmessage',[
			'chat_id'=>$chat_id,
			'text'=>"اطلاعات کاربر به شرح زیر میباشد :

•• یوزرنیم ~> 
$text",
			'reply_markup'=>json_encode([
			'inline_keyboard'=>[
			[['text'=>"ارسال نجوا به ".$text."",'switch_inline_query'=>"\nپیام شما\n".$text.""]],
			[['text'=>"منوی اصلی",'callback_data'=>"menu"]]
			],
		])
		]);
	step("data/$from_id.json","None");
}
elseif($data == 'cancel'){
	step("data/$fromid.json","None");
	sheikh('editMessageText',[
	'chat_id'=>$chatid,
	'message_id'=>$messageid,
	'text'=>"عملیات با موفقیت لغو شد و به منوی اصلی بر میگردیم 🏛"
	]);
	sheikh('sendmessage',[
		'chat_id'=>$chatid,
		'text'=>"سلام $firstname عزیز 🌹
		
به ربات نجوا (ارسال پیام خصوصی) به شخص مورد نظر خوش آمدید !

برای شروع کافیست ابتدا ایدی ربات را تایپ کنید و یک فاصله بزنید تا راهنمای ارسال پیام خصوصی نمایش داده شود",
		'reply_markup'=>json_encode([
		'inline_keyboard'=>[
		[['text'=>'📬 ارسال نجوا (پیام خصوصی) 📬','callback_data'=>'send']],
		[['text'=>'🤔 راهنما 🤔','callback_data'=>'help']]
		],
	])
	]);
}
elseif($data == 'menu'){
	step("data/$fromid.json","None");
	sheikh('editMessageText',[
	'chat_id'=>$chatid,
	'message_id'=>$messageid,
	'text'=>"عملیات با موفقیت لغو شد و به منوی اصلی بر میگردیم 🏛"
	]);
	sheikh('sendmessage',[
		'chat_id'=>$chatid,
		'text'=>"سلام $firstname عزیز 🌹
		
به ربات نجوا (ارسال پیام خصوصی) به شخص مورد نظر خوش آمدید !

برای شروع کافیست ابتدا ایدی ربات را تایپ کنید و یک فاصله بزنید تا راهنمای ارسال پیام خصوصی نمایش داده شود",
		'reply_markup'=>json_encode([
		'inline_keyboard'=>[
		[['text'=>'📬 ارسال نجوا (پیام خصوصی) 📬','callback_data'=>'send']],
		[['text'=>'🤔 راهنما 🤔','callback_data'=>'help']]
		],
	])
	]);
}
elseif(!is_null($inline) && strstr($inline_text,"@")){
	$u = explode("@", $inline_text);
	$user = "@".$u[1]."";
	$txt = $u[0];
	$Tex = base64_encode($txt);
	sheikh('answerInlineQuery', [
        'inline_query_id' =>$inline_id,
		'is_personal' =>true,
		'cache_time' =>"1",
        'results' => json_encode([[
        'type' => 'article',
        'thumb_url'=>"https://www.osucreators.ir/web/1.jpg",
        'id' =>base64_encode(rand(1,999999)),
        'title' => "برای ارسال نجوا اینجا ضربه بزنید ❗️",
		'description' => "ارسال نجوا (پیام مخفی) به $user\nاز @ در متن خود استفاده نکنید !",
		'input_message_content' => [ 'message_text' => "کاربر { $user } شما یک پیام از طرف ( $inline_name ) دارید !"],
		'reply_markup'=>([
		'inline_keyboard'=>[
		[['text'=>"🧐 نمایش پیام",'callback_data'=>"show2_".$user."_".$Tex.""]]
	]
    ])
	]])
	]);
}	
elseif(isset($inline) && strstr($inline_text,"#")){
	$k = explode("#", $inline_text);
	$id = $k[1];
	$txt = $k[0];
	$Tex = base64_encode($txt);
	$Result = sheikh('getChatMember',['chat_id'=>$id,'user_id'=>$id]);
	$name = $Result->result->user->first_name;
	$usern = $Result->result->user->username;
		sheikh('answerInlineQuery', [
        'inline_query_id' =>$inline_id,
		'is_personal' =>true,
		'cache_time' =>"1",
        'results' => json_encode([[
        'type' => 'article',
        'thumb_url'=>"https://www.osucreators.ir/web/1.jpg",
        'id' =>base64_encode(rand(1,999999)),
        'title' => "برای ارسال نجوا اینجا ضربه بزنید ❗️",
		'description' => "ارسال نجوا (پیام مخفی) به $name\nاز # در متن خود استفاده نکنید !",
		'input_message_content' => [ 'message_text' => "کاربر { $name | @$usern } شما یک پیام از طرف ($inline_name) دارید !"],
		'reply_markup'=>([
		'inline_keyboard'=>[
		[['text'=>"🧐 نمایش پیام",'callback_data'=>"show_".$id."_".$Tex.""]]
	]
    ])
	]])
	]);
}
elseif(preg_match('/^show_(\d+)_(.*)/',$data,$nop)){
	$id = $nop[1];
	$txt = $nop[2];
	$text = base64_decode($txt);
	if($fromid == $id){
		sheikh('answercallbackquery', [
            'callback_query_id' =>$calid,
            'text' => "$text",
            'show_alert' =>true
        ]);
	}else{
		sheikh('answercallbackquery', [
            'callback_query_id' =>$calid,
            'text' => "کاربر عزیز ! این نجوا برای شما نیست 🤕",
            'show_alert' =>true
		]);
	}
}
elseif(preg_match('/^show2_(.*)_(.*)/',$data,$nop2)){
	$us = $nop2[1];
	$user = strtolower($us);
	$txt = $nop2[2];
	$text = base64_decode($txt);
	$User = "@".$usernameca."";
	$Username = strtolower($User);
	if($Username == $user){
sheikh('answercallbackquery', [
'callback_query_id' =>$calid,
'text' => "$text",
'show_alert' =>true
]);
}else{
sheikh('answercallbackquery', [
'callback_query_id' =>$calid,
'text' => "کاربر عزیز ! این نجوا برای شما نیست 🤕",
'show_alert' =>true
]);
}
}
?>

<?php

const CHANNEL_ACCESS_TOKEN = '[取得したCHANNEL ACCESS TOKEN]';

$entityBody = file_get_contents(filename:'php://input');
$data       = json_decode($entityBody, assoc:true);

foreach ($data['events'] as $event) {
  switch ($event['type']) {
    case 'message':
      $message = $event['message'];
      $text    = mb_convert_kana($message['text'], option:'KVas');
      $message = array(
        'replyToken' => $event['replyToken'],
        'messages'   => array(
          array(
            'type' => 'text',
            'text' => $text
          )
        )
      );

      $header = array(
        "Content-Type: application/json",
        'Authorization: Bearer' . CHANNEL_ACCESS_TOKEN,
      );

      $context = stream_context_crate(array(
        "http" => array(
          "method"  => "POST",
          "header"  => implode(glue:"\r\n",$header),
          "content" => json_encode($message),
        ),
      ));
      $response = file_get_contents(filename: 'https://api.line.me/v2/bot/message/reply', use_include_path: false, $context);
    default:
      error_log(message:"Unsupproted event type: " . $event['type']);
    break;

  }
}
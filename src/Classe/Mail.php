<?php


namespace App\Classe; 

use Mailjet\Client; 
use Mailjet\Resources;

class Mail
{
    private $api_key = "e4140e6ff2af9701295d3e2b2a3902ea";
    private $api_key_secret = "6b64b608ddd8c1829c3fe63aa64b50a9"; 

    public function send($to_email, $to_name, $subject, $content)
    {
        $mj = new Client($this->api_key, $this->api_key_secret, true,['version' => 'v3.1']); 
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "remi.verhaeghe59@gmail.com",
                        'Name' => "ma_boutique"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' =>  $to_name, 
                        ]
                    ],
                    'TemplateID' => 3720351,
                    'TemplateLanguage' => true,
                    'Subject' => $subject, 
                    'Variables' => [
                        'content' => $content,
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success() && $response->getData();
    }


}

<?php


//token device
$token = "sdXpWQ9yXK62kdwKGrgv";


//send message function
function Kirimfonnte($token, $data)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.fonnte.com/send',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array(
            'target' => $data["target"],
            'message' => $data["message"],
        ),
        CURLOPT_HTTPHEADER => array(
            'Authorization: ' . $token
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    echo $response; //log response fonnte
}

//get user data from database

$data = [
    "target" => "087774487198",
    "message" => "Halo JUSTINE "
];

//send message
Kirimfonnte($token, $data);
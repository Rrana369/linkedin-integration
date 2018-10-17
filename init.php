<?php

session_start();


$client_id = "81iqbg6dhth4aj";
$client_secret = "W6Gio52bFHyiaKdC";
$redirect_uri = "https://rbespoke.000webhostapp.com/callback.php";
$csrf_token = random_int(1111111, 9999999);
$scopes = "r_basicprofile%20r_emailaddress";

function curl($url, $parameters)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
    curl_setopt($ch, CURLOPT_POST, 1);
    $headers = [];
    $headers[] = "Content-Type: application/x-www-form-urlencoded";
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($ch);
    return $result;
}

function getCallback()
{
    $client_id = "81iqbg6dhth4aj";
    $client_secret = "W6Gio52bFHyiaKdC";
    $redirect_uri = "https://rbespoke.000webhostapp.com/callback.php";
    $csrf_token = random_int(1111111, 9999999);
    $scopes = "r_basicprofile%20r_emailaddress";

    if (isset($_REQUEST['code'])) {
        $code = $_REQUEST['code'];
        $url = "https://www.linkedin.com/oauth/v2/accessToken";
        $params = [
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'redirect_uri' => $redirect_uri,
            'code' => $code,
            'grant_type' => 'authorization_code',
        ];
        $accessToken = curl($url,http_build_query($params));
        $accessToken = json_decode($accessToken)->access_token;
        
      

       $url = "https://api.linkedin.com/v1/people/~:(id,email-address,first-name,last-name,headline,location,picture-url,industry,summary,specialties,educations,positions:(id,title,summary,start-date,end-date,is-current,company:(id,name,type,size,industry,ticker)),associations,interests,num-recommenders,date-of-birth,publications:(id,title,publisher:(name),authors:(id,name),date,url,summary),patents:(id,title,summary,number,status:(id,name),office:(name),inventors:(id,name),date,url),languages:(id,language:(name),proficiency:(level,name)),skills:(id,skill:(name)),certifications:(id,name,authority:(name),number,start-date,end-date),courses:(id,name,number),recommendations-received:(id,recommendation-type,recommendation-text,recommender),honors-awards,three-current-positions,three-past-positions,volunteer,)?format=json&oauth2_access_token=" . $accessToken;
        
        // $url="https://api.linkedin.com/v1/people/~:(id,first-name,last-name,headline,picture-url,industry,summary,specialties,positions:(id,title,summary,start-date,end-date,is-current,company:(id,name,type,size,industry,ticker)),educations:(id,school-name,field-of-study,start-date,end-date,degree,activities,notes),associations,interests,num-recommenders,date-of-birth,publications:(id,title,publisher:(name),authors:(id,name),date,url,summary),patents:(id,title,summary,number,status:(id,name),office:(name),inventors:(id,name),date,url),languages:(id,language:(name),proficiency:(level,name)),skills:(id,skill:(name)),certifications:(id,name,authority:(name),number,start-date,end-date),courses:(id,name,number),recommendations-received:(id,recommendation-type,recommendation-text,recommender),honors-awards,three-current-positions,three-past-positions,volunteer)?format=json&oauth2_access_token=" . $accessToken;
        
        
        $user = file_get_contents($url, false);
        return (json_decode($user));

    }
}

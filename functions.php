<?php

function authenticate_and_fetch_diamonds()
{
    $url = 'https://integrations.nivoda.net/graphiql-loupe360';

    // Step 1: Authenticate request
    $authenticate_query = '{"query":"{authenticate{username_and_password(username:\"yourusername\",password:\"yourpassword\"){token}}}"}';
    $token = send_graphql_request($url, $authenticate_query);

    // Step 2: Fetch diamonds using the obtained token
    if ($token) {
        $diamonds_query = '{"query":"query{diamonds_by_query(query:{labgrown:true}){total_count,items{id,price,diamond{video,certificate{certNumber}}}}}"}';
        $diamonds_data = send_authenticated_graphql_request($url, $diamonds_query, $token);

        // Now you can process $diamonds_data as needed
        var_dump($diamonds_data);
    }
}

function send_graphql_request($url, $query)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
    curl_setopt($ch, CURLOPT_POST, 1);

    $headers = array();
    $headers[] = 'Content-Type: application/json';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);

    curl_close($ch);

    $json = json_decode($result, true);

    return $json["data"]["authenticate"]["username_and_password"]["token"] ?? null;
}

function send_authenticated_graphql_request($url, $query, $token)
{
    $headers_q = array();
    $headers_q[] = 'Content-Type: application/json';
    $headers_q[] = 'Authorization: Bearer ' . $token;

    $ch_query = curl_init();
    curl_setopt($ch_query, CURLOPT_URL, $url);
    curl_setopt($ch_query, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch_query, CURLOPT_POSTFIELDS, $query);
    curl_setopt($ch_query, CURLOPT_POST, 1);
    curl_setopt($ch_query, CURLOPT_HTTPHEADER, $headers_q);

    $result_query = curl_exec($ch_query);

    curl_close($ch_query);

    $json = json_decode($result_query, true);

    return $json["data"]["diamonds_by_query"] ?? array();
}

// Call the function to start the process
authenticate_and_fetch_diamonds();

?>
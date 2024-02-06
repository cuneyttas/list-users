<?php
/*
Plugin Name: User Listing API
Description: A project for WPCenter job application.
Version: 1.0
Author: Cüneyt TAŞ
*/


// 1. Tüm User Listesi için Custom endpoint oluşturulacak
add_action('rest_api_init', function () {
  register_rest_route('ulapi/v1', '/users', array(
    'methods' => 'GET',
    'callback' => 'ulapi_get_user_list',
  ));
});

// 1.1 jsonplaceholder'dan user listesini al (https://jsonplaceholder.typicode.com/users)
function ulapi_get_user_list() {
  $result = ulap_send_api_request("/users");
  wp_send_json($result);
}

// 2. User detay datası için custom endpoint oluşturulacak.
add_action('rest_api_init', function () {
  register_rest_route('ulapi/v1', '/users/(?P<id>\d+)', array(
    'methods' => 'GET',
    'callback' => 'ulapi_get_user_detail',
  ));
});

// 2.1 jsonplaceholder'q istek atıp cevabını döndür (https://jsonplaceholder.typicode.com/users/id)
function ulapi_get_user_detail($data) {
  $user_id = $data['id'];
  $result = ulap_send_api_request("/users/$user_id");
  wp_send_json($result);
}



// 3. Admin Settings page'i oluştur. Pages sayfasının listelendiği sayfanın HTML'İni kullan

// 3.1 React'ı o admin sayfasına dahil et



// 3.2 User listesini bu sayfada görüntüle. (Javascript (React veya Ajax) veya PHP)


// Ekstralar

// 1. cache fonskyionları ekleme (wp Transition api)

// 2. BU aşamları Github reposundaki readmeye yaz.

// 3. Unit testler ?

// 4. Örneğin beforeApiRequest gibi do_action fonksiyonları araralar eklenebilir.

function ulap_send_api_request($endpoint) {

  $api_url = "https://jsonplaceholder.typicode.com$endpoint";

  // GET isteği gönder
  $response = wp_remote_get($api_url);

  // Cevapta hata olup olmadığını kontrol et
  if (is_wp_error($response)) {
    return [
      "success" => false,
      "error" => $response->get_error_message()
    ];
  }

  // Cevap verilerini al
  $status_code = wp_remote_retrieve_response_code($response);
  $body = wp_remote_retrieve_body($response);

  if ($status_code !== 200) {
    return [
      "success" => false,
      "error" => "Status code $status_code"
    ];
  }

  $data = json_decode($body, true);

  return [
    "success" => true,
    "data" => $data
  ];

}
<?php
/*
Plugin Name: User Listing API
Description: A project for WPCenter job application.
Version: 1.0
Author: Cüneyt TAŞ
*/

// Tüm user listesi için custom endpoint oluşturuldu
add_action('rest_api_init', function () {
  register_rest_route('ulapi/v1', '/users', array(
    'methods' => 'GET',
    'callback' => 'ulapi_get_user_list',
  ));
});

// User listesi için jsonplaceholder'a istek atıp cevabı döndürüldü (https://jsonplaceholder.typicode.com/users)
function ulapi_get_user_list() {
  // Önbelleğe alınmış veriyi kontrol edin
  $cached_users = get_transient( 'ulapi_user_list' );

  // Önbellekte veri varsa, onu kullan
  if ( false !== $cached_users ) {
      return $cached_users;
  }

  // Önbellekte veri yoksa, API çağrısını yapın ve sonucu alın
  $result = ulapi_send_api_request("/users");

  // API çağrısı başarılıysa, veriyi önbelleğe alın
  if ( $result ) {
      set_transient( 'ulapi_user_list', $result, HOUR_IN_SECONDS ); // Örnek olarak bir saat boyunca önbelleğe alındı
  }

  return $result;
}

// User detay datası için custom endpoint oluşturuldu
add_action('rest_api_init', function () {
  register_rest_route('ulapi/v1', '/users/(?P<id>\d+)', array(
    'methods' => 'GET',
    'callback' => 'ulapi_get_user_detail',
  ));
});

// Detaylar için jsonplaceholder'a istek atıp cevabı döndürüldü (https://jsonplaceholder.typicode.com/users/id)
function ulapi_get_user_detail($data) {

  $user_id = $data['id'];

  // Önbelleğe alınmış veriyi kontrol edin
  $cached_users = get_transient( "ulapi_user_info_$user_id" );

  // Önbellekte veri varsa, onu kullan
  if ( false !== $cached_users ) {
      return $cached_users;
  }

  // Önbellekte veri yoksa, API çağrısını yapın ve sonucu alın
  $result = ulapi_send_api_request("/users/$user_id");

  // API çağrısı başarılıysa, veriyi önbelleğe alın
  if ( $result ) {
      set_transient( 'ulapi_user_info', $result, HOUR_IN_SECONDS ); // Örnek olarak bir saat boyunca önbelleğe alındı
  }

  return $result;
}

// 3. Admin Settings sayfası oluşturuldu
add_action('admin_menu', 'ulapi_register_custom_settings_page');
function ulapi_register_custom_settings_page() {
    add_menu_page(
        'Custom Settings', // Sayfanın Başlığı
        'Custom Settings', // Menünün Adı
        'manage_options', // Kullanıcının Yetkisi
        'custom-settings', // Menü Simgesi
        'ulapi_custom_settings_page_content' // İçerik Fonksiyonu
    );
}

// React projeye dahil edildi
function ulapi_include_react() {
    wp_enqueue_script( 'react', 'https://unpkg.com/react@18/umd/react.development.js');
    wp_enqueue_script( 'react-dom', 'https://unpkg.com/react-dom@18/umd/react-dom.development.js');
    wp_enqueue_script( 'babel', 'https://unpkg.com/@babel/standalone/babel.min.js');
}
add_action( 'admin_init', 'ulapi_include_react' );


// 3.2 User listesini bu sayfada görüntüle.
function ulapi_custom_settings_page_content() {
  ?>
    <div class="wrap" id="users-app"></div>

    <script type="text/babel">
      const apiUrl = "<?= get_rest_url() ?>";
      console.log(apiUrl);

      <?php
        $plugin_dir = dirname(__FILE__);
        require_once("$plugin_dir/script.jsx");
      ?>
    </script>
  <?php
}

function ulapi_send_api_request($endpoint) {

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
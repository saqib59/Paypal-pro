<?php 

include 'lib/paypal-pro/process-credit-card01.php';

add_action( 'wp_enqueue_scripts', 'salient_child_enqueue_styles');
function salient_child_enqueue_styles() {
	
		$nectar_theme_version = nectar_get_theme_version();
		
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css', array('font-awesome'), $nectar_theme_version);
  
 	wp_enqueue_script( 'js6', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyC1cDxNrmPLxUFXAIEp4VNdXEpituJPYWs&libraries=places&callback=initMap', ' ','1.0',  true );

	wp_enqueue_script( 'custom-script', get_template_directory_uri() . '-child/js/custom-script.js', array(), true );//custom js
	
	wp_localize_script('custom-script', 'the_ajax_script', array('ajaxurl' =>admin_url('admin-ajax.php')));

    if ( is_rtl() ) 
   		wp_enqueue_style(  'salient-rtl',  get_template_directory_uri(). '/rtl.css', array(), '1', 'screen' );
}



/* CUSTOM WORK */
/*   POST TYPE  STARTS */
function mybooking() 
{
$supports = array(
'title', // post title
'editor', // post content
'author', // post author
'thumbnail', // featured images
'excerpt', // post excerpt
'custom-fields', // custom fields
'comments', // post comments
'revisions', // post revisions
'post-formats', // post formats
);
$labels = array(
'name' => _x('booking', 'plural'),
'singular_name' => _x('Booking', 'singular'),
'menu_name' => _x('Booking', 'admin menu'),
'name_admin_bar' => _x('Booking', 'admin bar'),
'add_new' => _x('Add New', 'add new'),
'add_new_item' => __('Add New Booking'),
'new_item' => __('New Booing'),
'edit_item' => __('Edit Booking'),
'view_item' => __('View Booking'),
'all_items' => __('All Booking'),
'search_items' => __('Search Booking'),
'not_found' => __('No Booking found.'),
);
$args = array(
'supports' => $supports,
'labels' => $labels,
'public' => true,
'query_var' => true,
'rewrite' => array('slug' => 'booking'),
'has_archive' => true,
'hierarchical' => true,
);
register_post_type('booking', $args);
}
add_action('init', 'mybooking');

add_action('wp_ajax_booking', 'booking');
add_action('wp_ajax_nopriv_booking', 'booking');

function booking()
{
$post = array(
'post_author' => 1,
'post_content' => "Your riding Fair is $".$_POST['price'],
'post_status' => "publish",
'post_title' => $_POST['user_email'],
'post_type' => "booking",
);

$email=$_POST['user_email'];
$date=$_POST['date'];
$time=$_POST['time'];
$picklocation=$_POST['picklocation'];
$dropoff=$_POST['dropoff'];
$km=$_POST['km'];
$price=$_POST['price'];
if (empty($email) || empty($date) || empty($time) || empty($picklocation) || empty($dropoff))
{
$response = array(
"message" =>"Kindly fill all riding credentials",
"type" => "failure",
"error" => true
);
return response_json($response);
}
else{
$client_cardno= $_POST['client_cardno'];
$card_expirydate= $_POST['card_expirydate'];
$card_cvv= $_POST['card_cvv'];
$country_code= $_POST['country_code'];
$card_expirydate = explode('-',$card_expirydate);
$card_year = $card_expirydate[0];
$card_month = $card_expirydate[1];
$card_expirydate = $card_month.$card_year;
if (empty($client_cardno) || empty($card_expirydate) || empty($card_cvv) || empty($country_code)) 
{
$response = array(
"message" =>"Kindly fill all card credentials",
"type" => "failure",
"error" => true
);
return response_json($response);
}

$x=$picklocation;
$y=$dropoff;
$z=strcmp($x,$y);

if($z===0){
    
    $response = array(
	"message" =>"YOur pick up and Droppoff are same",
	"type" => "failure",
	"error" => true);
	return response_json($response);
	
}


	$pick_ca=strpos($picklocation, "CA,");
	$pick_california=strpos($picklocation, "California");

	$drop_ca=strpos($dropoff, "CA,");
	$drop_california=strpos($dropoff, "California");


	if ($pick_ca === false && $pick_california===false) {
	   $response = array(
	"message" =>"Your Location is out of city",
	"type" => "failure",
	"error" => true);
	return response_json($response);
	}
	if ($drop_ca === false && $drop_california===false) {
	$response = array(
	"message" =>"Your Location is out of city",
	"type" => "failure",
	"error" => true);
	return response_json($response);
}
else{
	$res=user_credientials($client_cardno,$country_code,$card_expirydate,$card_cvv);
	if($res['ACK'] == 'Success')
	{
	$response = array(
	"message" =>'Your ride has been booked successfully',
	"type" => "success",
	"redirect_url" => home_url().'/detail',
	"error" => false
	);
		
	}
	else
	{
	$response = array(
	"message" =>'Your ride has not booked due to incorrect card credentials',
	"type" => "success",
	"error" => true
	);
	return response_json($response);
	}
	$post_id = wp_insert_post( $post );
	if ($post_id)
	{
	update_post_meta( $post_id, 'km', $_POST['km']);
	update_post_meta( $post_id, 'date', $_POST['date']);
	update_post_meta( $post_id, 'time', $_POST['time']);
	update_post_meta( $post_id, 'picklocation', $_POST['picklocation']);
	update_post_meta( $post_id, 'dropoff', $_POST['dropoff']);
	update_post_meta( $post_id, 'client_cardno', $_POST['client_cardno']);
	update_post_meta( $post_id, 'card_expirydate', $_POST['card_expirydate']);
	update_post_meta( $post_id, 'card_cvv', $_POST['card_cvv']);
	update_post_meta( $post_id, 'country_code', $_POST['country_code']);
	ob_start();
	include(get_stylesheet_directory() .'/inc/abc.php');
	$email_content = ob_get_contents();
	ob_end_clean();
	$headers = array('Content-Type: text/html; charset=UTF-8');
	wp_mail($email, "My FORM", $email_content, $headers);
		}
	else{
	$response = array(
	"message" =>"Ride has not been booked",
	"type" => "failure",
	"error" => true
	);
	}
}
}
return response_json($response);
}
function response_json($data){
 header('Content-Type: application/json');
 echo json_encode($data);
 wp_die();
}





?>

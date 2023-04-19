<?php
/**
 * Plugin Name: Custom Password Protect
 * Plugin URI: https://example.com
 * Description: A custom password protection plugin for WordPress
 * Version: 1.0
 * Author: Your Name
 * Author URI: https://example.com
 **/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
 
//enquire styles 
// load your plugin css into the website's front-end
class PrivateBlogStyle {
function register(){
    //for backend
    add_action( 'admin_enqueue_scripts', array($this,'backendEnqueue'));
    //for frontend
    add_action( 'wp_enqueue_scripts', array($this,'frontendEnqueue'));
    add_action( 'login_enqueue_scripts', array($this,'frontendEnqueue'));
}
function backendEnqueue(){
    wp_enqueue_style( 'PrivateBlogStyle', plugins_url( '/assets/css/bootstrap.min.css', __FILE__ ));
    wp_enqueue_script( 'PrivateBlogStyle', plugins_url( '/assets/js/bootstrap.min.js', __FILE__ ));
}
function frontendEnqueue(){
    wp_enqueue_style( 'PrivateBlogStyle', plugins_url( '/assets/css/bootstrap.min.css', __FILE__ ));
    wp_enqueue_script( 'PrivateBlogStyle', plugins_url( '/assets/js/bootstrap.min.js', __FILE__ ));
}

}

if(class_exists('PrivateBlogStyle')){
$privateblogstyle=new PrivateBlogStyle();
$privateblogstyle->register();
}




// Create the password protection form

require_once plugin_dir_path( __FILE__ ) . '/dashboard/dashboard.php';



function pb_check_enabled() {
    $enabled = get_option( 'pb_enabled', false);
    return (bool) $enabled;
}


  function custom_password_protect_form() {
    if ( isset( $_POST['password'] ) ) {
        $password = $_POST['password'];
        $name = custom_password_protect_check_password( $password );
        if ( $name ) {
            // Password is correct, allow access and set cookies
            setcookie( 'pb_login', '1', time() + 86400, '/' );
            custom_password_protect_log_entry( $name, $password, true );
            return true;
        } else {
            // Password is incorrect, show error message
            _e( '<div class="error">Invalid password</div>', 'private-blog' );
        }
    }

    // Show the password form
    _e( '<form method="post" class="form-signin">' );
    _e('<img class="mb-4" src="https://getbootstrap.com/assets/brand/bootstrap-solid.svg" alt="" width="72" height="72">')
  ?><h1 class="h3 mb-3 font-weight-normal">Please sign in</h1><?php
    _e( '<label for="password" class="sr-only">Enter password:</label>', 'private-blog' );
    _e( '<input type="password" class="form-control" name="password" id="password" value="' . esc_html( $name ) . '">' );
    _e( '<input  class="btn btn-lg btn-primary btn-block" type="submit" value="Submit">' );
    _e( '</form>' );

    return false;
}

// Check if a given password is valid
function custom_password_protect_check_password( $password ) {
    $passwords = get_option( 'custom_password_protect_passwords', array() );
    foreach ( $passwords as $p ) {
        if ( is_array( $p ) && isset( $p['password'] ) && $p['password'] === $password ) {
            custom_password_protect_log_entry( $p['name'], $password, true );
            return $p['name'];
        }
    }
    // Password is invalid
    custom_password_protect_log_entry( '', $password, false );
    return false;
}


    // Log a password access attempt
    function custom_password_protect_log_entry( $name, $password, $success ) {
        $log = get_option( 'custom_password_protect_log', array() );
        if ( !is_array( $log ) ) {
            $log = array();
        }
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
    preg_match('/(?:\()?\b(\w+)\b(?:\))?/', $user_agent, $matches);
    $browser = isset($matches[1]) ? $matches[1] : '';
        $log_entry = array(
            'name' => $name,
            'password' => $password,
            'success' => $success,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'browser' => $browser,
            'time' => current_time( 'mysql' ),
        );
        $log[] = $log_entry;
        update_option( 'custom_password_protect_log', $log );
    }

    // Add the password protection to the site
    function custom_password_protect() {
        if ( !is_user_logged_in() ) {
            if ( !isset( $_COOKIE['pb_login'] ) || $_COOKIE['pb_login'] !== '1' ) {
                if ( !custom_password_protect_form() ) {
                    // Password form was not submitted or was incorrect, prevent access
                    wp_die("Website is restricted", "login");
                }
            } else {
                // User has already logged in and the cookie is valid, allow access
                return;
            }
        }
    }
    add_action( 'template_redirect', 'custom_password_protect' );


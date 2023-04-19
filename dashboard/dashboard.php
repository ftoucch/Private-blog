<?php
// Add the password options page
function custom_password_protect_options_page() {
   if (isset($_POST['add_password']) && !empty($_POST['name']) && !empty($_POST['password'])) {
    $passwords = get_option('custom_password_protect_passwords', array());
    $passwords[] = array(
        'name' => $_POST['name'],
        'password' => $_POST['password'],
    );
    update_option('custom_password_protect_passwords', $passwords);
}


  _e('<div class="wrap">');
    _e('<h1>Custom Password Protect Options</h1>');

    //enable or disable plugin 




    // Add new password form
    ?>
    <div class="container mb-5 mt-5">
    <?php
    _e('<form method="post">','private-blog');
    _e('<h2>Add Password</h2>');
    _e('<label for="name">Name:</label>','private-blog');
    _e('<input type="text" name="name" id="name" value="' . esc_html_e( $name ) . '">','private-blog');
    _e('<label for="password">Password:</label>','private-blog');
   _e('<input type="text" name="password" id="password" value="' . esc_html_e( $name ) . '">','private-blog');
_e( '<input type="submit" name="add_password" value="Add Password" class="btn btn-primary ml-5">', 'private-blog');
_e( '</form>','private-blog');
?>
</div>
<?php

// Show existing passwords
$passwords = get_option('custom_password_protect_passwords', array());
if (count($passwords) > 0) {
    ?><div class="container mt-5"><?php
    _e('<h2>Existing Passwords</h2>','private-blog');
    echo '<table class="table">';
    _e('<thead class="thead-light"><tr><th>Name</th><th>Password</th></tr><thead>','private-blog');
    foreach ($passwords as $p) {
        if (isset($p['name']) && isset($p['password'])) {
        echo '<tbody><tr><td>' . esc_html($p['name']) . '</td><td>' . esc_html($p['password']) . '</td></tr></tbody>';
    }
}
    echo '</table>';
    ?></div><?php
}

// Show access log
$log = get_option('custom_password_protect_log', array());
if (count($log) > 0) {
    ?>
    <div class="container mt-5">
    <?php
    _e('<h2>Access Log</h2>','private-blog');
    echo '<table class="table">';
   _e('<thead class="thead-light"><tr><th>Name</th><th>Password</th><th>IP Address</th><th>Browser</th><th>Success</th><th>Timestamp</th></tr></thead>','private-blog');
    foreach ($log as $entry) {
        echo '<tbody><tr><td>' . esc_html($entry['name']) . '</td><td>' . esc_html($entry['password']) . '</td><td>' . esc_html($entry['ip']) . '</td><td>' . esc_html($entry['browser']) . '</td><td>' . ($entry['success'] ? 'Yes' : 'No') . '</td><td>' . esc_html($entry['time']) . '</td></tr></tbody>';
    }
    echo '</table>';
}

?></div><?php

}

// Add the password options menu item
function custom_password_protect_options_menu() {
add_menu_page('Custom Password Protect', 'Password Protect', 'manage_options', 'custom-password-protect', 'custom_password_protect_options_page', 'dashicons-lock', 100);
}
add_action('admin_menu', 'custom_password_protect_options_menu');






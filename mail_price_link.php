<?php



/*
Plugin Name: Mail Price Link
Description: Allows admin to add mail, price, and link.
Version: 1.0.3
Author: Md WAHID
*/


register_activation_hook(__FILE__, 'mpl_create_table');
register_deactivation_hook(__FILE__, 'mpl_delete_table');

function mpl_create_table() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $table_name = $wpdb->prefix . 'email_price_link';
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        mail VARCHAR(255) NOT NULL,
        price float NOT NULL,
        link VARCHAR(255) NOT NULL,
        phone VARCHAR(255) NOT NULL,
        username VARCHAR(255) NOT NULL,
        adress VARCHAR(255) NOT NULL,
        created_at DATETIME,

        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

function mpl_delete_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'email_price_link';
    $sql = "DROP TABLE IF EXISTS $table_name;";
    $wpdb->query($sql);
}
function enqueue_global_stylesheet() {
    wp_enqueue_style('my-styles', plugins_url('css/style.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'enqueue_global_stylesheet');


add_action('admin_menu', 'mpl_admin_menu');

function mpl_admin_menu() {
    add_menu_page('Mail Price Link', 'Mail Price Link', 'edit_posts', 'mpl-admin', 'mpl_admin_page', 'dashicons-calendar-alt');
}

function mpl_admin_page() {
    wp_enqueue_style('my-styles', plugins_url('css/style.css', __FILE__));

    global $wpdb;

    // Handling the form submission and data insertion
    if (isset($_POST['submit'])) {
        $mail = sanitize_text_field($_POST['mail']);
        $price = floatval($_POST['price']);
        $link = esc_url($_POST['link']);
        $name = sanitize_text_field($_POST['name']);
        $phone = sanitize_text_field($_POST['phone']);
        $adress = sanitize_text_field($_POST['adress']);
        // Get the current UTC date and time
      $current_datetime_utc = gmdate('Y-m-d H:i:s'); 
       // $current_datetime = current_time('mysql', 1);  // Adjust the timezone if needed
        // $current_datetime = date('Y-m-d H:i:s');


        $table_name = $wpdb->prefix . 'email_price_link';
        $wpdb->insert($table_name, array('mail' => $mail, 'price' => $price, 'link' => $link,'phone'=>$phone,'username'=>$name,'adress'=>$adress,  'created_at' => $current_datetime_utc));
        echo '<p class="success">Added successfully!</p>';
    }

    // Handling data deletion when the Delete button is clicked
    if (isset($_POST['delete'])) {
        $id_to_delete = intval($_POST['delete']);
        $table_name = $wpdb->prefix . 'email_price_link';
        $wpdb->delete($table_name, array('id' => $id_to_delete));
        echo '<p>Data deleted successfully!</p>';
    }

    // Form to add new data
    echo '<form method="post" action="">
        Email: <input type="email" name="mail" required>
        Price: <input type="number" step="0.01" name="price" required>
        Link: <input type="url" name="link" required>
        Name: <input type="text" name="name" required>
        Phone: <input type="text" name="phone" required></br>
        Adress: <input type="text" name="adress" required>
        <input type="submit" name="submit" value="Add" class="add-button">
    </form>';


    echo '<form method="post" action="" name="search_form">
    Search: <input type="text" name="search_mail" placeholder="Search by mail">
    <input type="submit" name="search_submit" value="Search" class="srch-button">
    </form>';

// Fetching stored data and displaying in a table
$table_name = $wpdb->prefix . 'email_price_link';
// $results = array(); // Initialize the $results array
// Handling data search
if (isset($_POST['search_submit'])) {
    $search_mail = sanitize_text_field($_POST['search_mail']);
    if (!empty($search_mail)) {
        $results = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $table_name WHERE mail LIKE %s", '%' . $search_mail . '%'),
            ARRAY_A
        );

        if (!empty($results)) {
            echo '<h2>Search Results</h2>';
            echo '<table border="1" cellspacing="0" cellpadding="5">';
            echo '<thead><tr><th>ID</th><th>Email</th><th>Price</th><th>Name</th><th>Phone</th><th>Address</th><th>Create At</th></tr></thead>';
            echo '<tbody>';

            foreach ($results as $row) {
                echo '<tr>';
                echo '<td>' . esc_html($row['id']) . '</td>';
                echo '<td>' . esc_html($row['mail']) . '</td>';
                echo '<td>' . esc_html($row['price']) . '</td>';
                echo '<td>' . esc_html($row['username']) . '</td>';
                echo '<td>' . esc_html($row['phone']) . '</td>';
                echo '<td>' . esc_html($row['adress']) . '</td>';
                echo '<td>' . esc_html($row['created_at']) . '</td>';
                echo '<td><a href="' . esc_url($row['link']) . '" target="_blank">Link</a></td>';
                echo '<td>';
                echo '<form method="post" action="">';
                echo '<input type="hidden" name="delete" value="' . esc_attr($row['id']) . '">';
                echo '<input type="submit" value="Delete" class="delete-button">';
                echo '</form>';
                echo '</td>';
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<p>No data found for the search query!</p>';
        }
    }
} else {
    // Display all data code here
}
// Display all data
try {
    $query = "SELECT * FROM $table_name";
    $all_results = $wpdb->get_results($query, ARRAY_A);

    if (!empty($all_results)) {
        echo '<h2>All Data</h2>';
        echo '<table  id="all-data-table" class="data-table" border="1" cellspacing="0" cellpadding="5">';
        echo '<thead><tr><th>ID</th><th>Email</th><th>Price</th><th>Name</th><th>Phone</th><th>Address</th><th>Create At</th><th>Link</th><th>Action</th><tr></thead>';
        echo '<tbody>';

        foreach ($all_results as $row) {
            echo '<tr>';
            echo '<td>' . esc_html($row['id']) . '</td>';
            echo '<td>' . esc_html($row['mail']) . '</td>';
            echo '<td>' . esc_html($row['price']) . '</td>';
            echo '<td>' . esc_html($row['username']) . '</td>';
            echo '<td>' . esc_html($row['phone']) . '</td>';
            echo '<td>' . esc_html($row['adress']) . '</td>';
            echo '<td>' . esc_html($row['created_at']) . '</td>';
            echo '<td><a href="' . esc_url($row['link']) . '" target="_blank" class="srch-button">Link</a></td>';
            echo '<td>';
            echo '<form method="post" action="">';
            echo '<input type="hidden" name="delete" value="' . esc_attr($row['id']) . '">';
            echo '<input type="submit" value="Delete" class="delete-button">';
            echo '</form>';
            echo '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>No data found!</p>';
    }
} catch (Exception $e) {
    // Handle the exception here
    echo "An error occurred: " . $e->getMessage();
}
// JavaScript code


// Handling data search


    

}


function mpl_show_user_data() {
    global $wpdb;
    
    // Check if the user is logged in
    if (!is_user_logged_in()) {
        return 'Please login to view your data.';
    }

    $current_user = wp_get_current_user();
    $user_email = $current_user->user_email;

    // Fetch data matching the logged-in user's email
    $table_name = $wpdb->prefix . 'email_price_link';
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE mail = %s", $user_email), ARRAY_A);

    // If matching records found, display in a table. Else, show a message.
    if($results) {
        $output = '<table border="1" cellspacing="0" cellpadding="5">';
        $output .= '<thead><tr><th>Name</th><th>Email</th><th>Price</th>
        <th>Phone</th><th>Adress</th><th>Created At</th><th>Service Link </th> </tr></thead>';
        $output .= '<tbody>';

        foreach($results as $row) {
            $output .= '<tr>';
            $output .= '<td>' . esc_html($row['username']) . '</td>';
            $output .= '<td>' . esc_html($row['mail']) . '</td>';
            $output .= '<td>'. esc_html($row['price']) . '</td>';

            $output .= '<td>' . esc_html($row['phone']) . '</td>';
            $output .= '<td>' . esc_html($row['adress']) . '</td>';
            $output .= '<td>' . esc_html($row['created_at']) . '</td>';
            $output .= '<td><button class="button-1"><a href="' . esc_url($row['link']) . '" target="_blank">Book Appoinment</a></button></td>';
            $output .= '</tr>';
        }

        $output .= '</tbody>';
        $output .= '</table>';
        return $output;
    } else {
        return 'No data found for your email.';
    }
}
add_shortcode('show_user_data', 'mpl_show_user_data');




<?php
/**
* Plugin Name: ilpost
* Plugin URI: https://github.com/ziorufus/work-with-us-plugin
* Description: Il Post esempio di plugin.
* Version: 0.1
* Author: Alessio Palmero Aprosio
* Author URI: https://www.alessiopalmeroaprosio.eu/
**/

define("TAG_SLUG", "governo");
define("NUM_PARAGRAPHS", 4);


// Menu stuff

function ilpost_register_options_page() {
    add_options_page('Il Post', 'Il Post', 'activate_plugins', 'ilpost', 'ilpost_option_page');
}
add_action('admin_menu', 'ilpost_register_options_page');


// Settings page stuff

function display_html_textarea() {
    $saved_text = get_option( 'ilpost_call_to_action_text' );
    wp_editor( $saved_text, 'ilpost_call_to_action_text', array( 
        'media_buttons' => false,
    ) );
}

function ilpost_register_settings() {
    add_settings_section("ilpost_call_to_action", "Opzioni call to action", "display_header_options_content", "ilpost-options");
    add_settings_field(
        'ilpost_call_to_action_text',
        'Testo:',
        'display_html_textarea',
        'ilpost-options',
        'ilpost_call_to_action'
    );
    register_setting("ilpost_call_to_action", "ilpost_call_to_action_text");
}

function ilpost_option_page() {
?>
    <div class="wrap">
    <h1>Impostazioni per plugin "Il Post - Call to action"</h1>
    <form method="post" action="options.php">
        <?php
            settings_fields("ilpost_call_to_action");
            do_settings_sections("ilpost-options");
            submit_button();
        ?>
    </form>
    </div>
<?php
}

add_action('admin_init', 'ilpost_register_settings');


// Posts stuff

function ilpost_content($content) {

    // Post has tag
    if (!has_tag(TAG_SLUG)) {
        return $content;
    }

    // Call to action text exists
    $saved_text = get_option( 'ilpost_call_to_action_text' );
    if (!trim($saved_text)) {
        return $content;
    }

    // Find paragraphs
    preg_match_all("/<\/p>/i", $content, $matches, PREG_OFFSET_CAPTURE);

    // Less than 4 paragraphs
    if (count($matches[0]) < 4) {
        return $content;
    }

    $limit = $matches[0][3][1] + strlen("</p>");
    $ret = substr($content, 0, $limit);
    $ret .= "\n<p class='ilpost-call-to-action'>" . $saved_text . "</p>";
    $ret .= substr($content, $limit);

    return $ret;
}

add_filter('the_content', 'ilpost_content');


// Styles

function ilpost_enqueue_styles() {
    wp_register_style('ilpost-style', plugins_url('style.css', __FILE__));
    wp_enqueue_style('ilpost-style');
}

add_action('wp_enqueue_scripts', 'ilpost_enqueue_styles');

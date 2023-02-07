<?php
/*
Plugin Name: Reddit Auto Poster
Plugin URI: https://geekplaycr.com
Description: Comparte automaticamente las publicaciones a un Subreddit
Version: 1.0
Author: JohnHeatz
*/

function ra_post_to_reddit($post_id) {
    // Get the post details
    $post = get_post($post_id);

    // Check if post is published and not a revision
    if ($post->post_status !== 'publish' || wp_is_post_revision($post_id)) {
        return;
    }

    // Get the subreddit to post to
    $subreddit = get_option('ra_subreddit');

    // Get the featured image of the post
    $thumbnail_id = get_post_thumbnail_id($post_id);
    $thumbnail_url = wp_get_attachment_url($thumbnail_id);

    // Construct the Reddit API post request
    $args = array(
        'title' => $post->post_title,
        'url' => get_permalink($post_id),
        'sr' => $subreddit,
        'kind' => 'link',
        'thumbnail' => $thumbnail_url
    );

    // Reddit API endpoint to post to
    $url = 'https://www.reddit.com/api/submit';

    // Make the API request
    $response = wp_remote_post($url, array(
        'method' => 'POST',
        'body' => $args
    ));

    // Check for successful response
    if (wp_remote_retrieve_response_code($response) === 200) {
        // Success
    } else {
        // Failure
    }
}

add_action('publish_post', 'ra_post_to_reddit');

// Add the plugin settings page
function ra_add_settings_page() {
    add_options_page(
        'Reddit Auto Poster',
        'Reddit Auto Poster',
        'manage_options',
        'ra_settings',
        'ra_settings_page'
    );
}

add_action('admin_menu', 'ra_add_settings_page');

// Display the plugin settings page
function ra_settings_page() {
    if (isset($_POST['ra_subreddit'])) {
        update_option('ra_subreddit', $_POST['ra_subreddit']);
    }
    ?>
    <div class="wrap">
        <h2>Reddit Auto Poster</h2>
        <form method="post">
            <table class="form-table">
                <tr>
                    <th>Subreddit</th>
                    <td>
                        <input type="text" name="ra_subreddit" value="<?php echo get_option('ra_subreddit'); ?>">
                    </td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" class="button-primary" value="Save Changes">
            </p>
        </form>
    </div>
    <?php
}

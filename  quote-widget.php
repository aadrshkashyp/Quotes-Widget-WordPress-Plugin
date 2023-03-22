<?php
/*
Plugin Name: Quote Widget
Description: A simple widget to display quotes from selected category posts.
Author: Aadarsh Kashyap
Website: https://aadarshkashyap.com
Version: 1.0
*/

if (!defined('ABSPATH')) {
    exit; // Prevent direct access to the file.
}

class Quote_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'quote_widget',
            __('Quote Widget', 'text_domain'),
            ['description' => __('A widget to display quotes from selected category posts.', 'text_domain')]
        );
    }

    public function form($instance) {
        $category = !empty($instance['category']) ? $instance['category'] : '';

        ?>
        <p>
        <label for="<?php echo esc_attr($this->get_field_id('category')); ?>"><?php _e('Category:', 'text_domain'); ?></label>
        <input class="widefat" id="<?php echo esc_attr($this->get_field_id('category')); ?>" name="<?php echo esc_attr($this->get_field_name('category')); ?>" type="text" value="<?php echo esc_attr($category); ?>">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['category'] = (!empty($new_instance['category'])) ? strip_tags($new_instance['category']) : '';

        return $instance;
    }

public function widget($args, $instance) {
    $category = !empty($instance['category']) ? $instance['category'] : '';

    $query_args = [
        'category_name' => $category,
        'orderby' => 'rand',
        'posts_per_page' => 1,
    ];

    $quote_query = new WP_Query($query_args);

    if ($quote_query->have_posts()) {
        echo $args['before_widget'];

        while ($quote_query->have_posts()) {
            $quote_query->the_post();

            // Display the custom title "Random Lines"
            echo $args['before_title'] . 'Random Lines' . $args['after_title'];

            // Display a maximum of 200 characters from the middle section without embedded videos and heading tags
            $content = get_the_content();
            $content = preg_replace('/<iframe.*?\/iframe>/i', '', $content); // Remove embedded videos
            $content = preg_replace('/<(h[1-6])[^>]*>.*?<\/\1>/i', '', $content); // Remove heading tags
           $content = preg_replace('/<((?!br)+)>/i', '', $content); // Remove all HTML tags except <br>, </br>, <font>, and </font>


            $start = (int)(strlen($content) / 2) - 100; // Calculate the starting position for the middle section
            $short_content = substr($content, $start, 200); // Extract the middle section

            // Add a space between lines
            $short_content = preg_replace('/([^\s])\s+([^\s])/', "$1 $2", $short_content);



           
        echo '<div class="quote-content">';
        echo '<p>' . $short_content . '</p>';
        echo '</div>';

        // Display a button linking to the post
        echo '<a href="' . get_permalink() . '" class="epcl-button white">Read More</a>';
    }

    echo $args['after_widget'];
}

wp_reset_postdata();

}

}

function register_quote_widget() {
    register_widget('Quote_Widget');
}
add_action('widgets_init', function() {
    register_widget('Quote_Widget');
});

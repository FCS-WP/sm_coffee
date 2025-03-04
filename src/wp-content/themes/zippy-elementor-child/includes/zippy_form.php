<?php 

function enqueue_flatpickr_scripts() {
    wp_enqueue_style('flatpickr-css', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');
    
    wp_enqueue_script('flatpickr-js', 'https://cdn.jsdelivr.net/npm/flatpickr', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_flatpickr_scripts');

<?php
/**
 * Plugin Name: User Entries Lite for Gravity Forms
 * Description: Displays a list of the user's last 5 form entries in the WordPress User Profile, visible to admin only.
 * Version: 1.0
 * Author: Odd Jar (Johnathon Williams)
 * Author URI: https://oddjar.com
 * Text Domain: gf-user-entries-lite
 * Plugin URI: https://oddjar.com
 * License: GPL2
 */

// Check if Gravity Forms is active
if ( class_exists( 'GFForms' ) ) {

    add_action( 'show_user_profile', 'display_user_entries_list' );
    add_action( 'edit_user_profile', 'display_user_entries_list' );

    function display_user_entries_list( $user ) {
        // Check if the current user has the capability to view this
        if ( ! current_user_can( 'administrator' ) ) {
            return;
        }

        // Fetch active entries for the user
        $search_criteria = array(
            'status'        => 'active',
            'field_filters' => array(
                array( 'key' => 'created_by', 'value' => $user->ID ),
            ),
        );
        $sorting = array(
            'key'       => 'date_created',
            'direction' => 'DESC',
        );
        $paging = array(
            'offset'    => 0,
            'page_size' => 5, // Adjust page_size as needed
        );
        $entries = GFAPI::get_entries( 0, $search_criteria, $sorting, $paging );

        if ( empty( $entries ) ) {
            return; // Return nothing if there are no entries
        }

        // Start outputting the entries table
        echo '<h3>User Entries from Gravity Forms</h3>';
        echo '<table class="widefat fixed" cellspacing="0">';
        echo '<thead><tr><th>Form Title</th><th>Entry Date</th><th>View Entry</th></tr></thead>';
        echo '<tbody>';

        foreach ( $entries as $entry ) {
            $form = GFAPI::get_form( $entry['form_id'] );
            $view_entry_url = admin_url( 'admin.php?page=gf_entries&view=entry&id=' . $entry['form_id'] . '&lid=' . $entry['id'] );
            echo '<tr>';
            echo '<td>' . esc_html( $form['title'] ) . '</td>';
            echo '<td>' . esc_html( $entry['date_created'] ) . '</td>';
            echo '<td><a href="' . esc_url( $view_entry_url ) . '" target="_blank">View Entry</a></td>';
            echo '</tr>';
        }

        echo '</tbody></table>';
    }
}

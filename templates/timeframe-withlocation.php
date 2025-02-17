<?php

/**
 * Template: timeframe-withlocation
 * 
 * This template is included in parent template shortcode-items or shortcode-locations
 *
 * $data is set in parent template
 */


use CommonsBooking\Settings\Settings;

$button_label = \CommonsBooking\Settings\Settings::getOption( COMMONSBOOKING_PLUGIN_SLUG . '_options_templates', 'label-booking-button');
$permalink    = add_query_arg ( 'location', $location->ID, get_the_permalink($item->ID) ); // booking link set to item detail page with location ID
?>

<?php echo commonsbooking_sanitizeHTML($location->thumbnail('cb_listing_small')); // div.thumbnail is printed by function ?>



<div class="cb-list-info">
    <h4 class="cb-title cb-item-title"><?php echo commonsbooking_sanitizeHTML($location->post_title); ?></h4>
    <div class="cb-dates cb-timeframe-dates">
        <?php
            if(
                array_key_exists('ranges', $data) &&
                count($data['ranges'])
            ) {

                foreach ($data['ranges'] as $range) {
                    echo commonsbooking_sanitizeHTML( \CommonsBooking\Model\Timeframe::formatBookableDate($range['start_date'], $range['end_date']) ) . '<br>';
                }
            }
        ?>
    </div>
</div>
<div class="cb-action">
    <a href="<?php echo esc_url($permalink); ?>" class="cb-button"><?php echo commonsbooking_sanitizeHTML($button_label); ?></a>
</div>

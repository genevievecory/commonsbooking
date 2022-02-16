<?php
    global $templateData;
    $location =  $templateData['location'];
    echo commonsbooking_sanitizeHTML($location->thumbnail('cb_listing_small')); // div.thumbnail is printed by function
?>
<div class="cb-list-info">
    <h4 class="cb-title cb-location-title"><?php echo commonsbooking_sanitizeHTML($location->post_title); ?></h4>
    <div class="cb-address cb-location-address"><?php echo commonsbooking_sanitizeHTML($location->formattedAddressOneLine()); ?></div>
	<?php
	    if ( $location->hasMap() ) {
            \CommonsBooking\View\Location::renderLocationMap( $location );
        }
	?>
      <div class="cb-address cb-location-pickupinstructions"><?php
      // if pickup instructions are set in location meta
      if ($location->formattedPickupInstructionsOneLine()) {
      ?><strong><?php echo esc_html__('Pickup instructions:', 'commonsbooking'); ?></strong>
<?php echo commonsbooking_sanitizeHTML($location->formattedPickupInstructionsOneLine()); ?>
    <?php
      } // end if pickup instructions
    ?>
    </div>
</div>


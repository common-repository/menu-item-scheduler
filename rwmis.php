<?php
/* RWMIS - Functions */

/**
 * Add custom fields in menu items
 */
function rwmis_add_pluginfields( $item_id ){
	/* Add custom Fields as per settings */?>        
    <!-- Add Fields for Hide Items -->  
    <?php rwpro_mis_add_field( $item_id, 'wide', 'rwmis_hide', __('Hide this Item',RW_MenuItem_TD) ); ?>  
	<div id="<?php echo 'divid_rwmis_hide_'.$item_id; ?>"<?php 
			if( get_post_meta( $item_id, '_menu_item_rwmis_hide', true ) != 'yes' ){ ?> style="display:none;"<?php }  ?>>
		<?php
			rwpro_mis_add_field( $item_id, 'wide', 'rwmis_hide_child', __('Hide Child Items (if any)',RW_MenuItem_TD) );
            rwpro_mis_add_field( $item_id, 'thin', 'rwmis_hide_start', __('Hide From',RW_MenuItem_TD) );
            rwpro_mis_add_field( $item_id, 'thin', 'rwmis_hide_end', __('Hide To', RW_MenuItem_TD) );
        ?>
    </div>
    
    <!-- Add Fields for Redirect Item -->   
    <?php rwpro_mis_add_field( $item_id, 'wide', 'rwmis_redirect', __('Redirect this Item',RW_MenuItem_TD) );?>    
	<div id="<?php echo 'divid_rwmis_redirect_'.$item_id; ?>"<?php 
			if( get_post_meta( $item_id, '_menu_item_rwmis_redirect', true ) != 'yes' ){ ?> style="display:none;"<?php } ?>>
		<?php
            rwpro_mis_add_field( $item_id, 'thin', 'rwmis_redirect_start', __('Redirect From',RW_MenuItem_TD) );
            rwpro_mis_add_field( $item_id, 'thin', 'rwmis_redirect_end', __('Redirect To',RW_MenuItem_TD) );
            rwpro_mis_add_field( $item_id, 'wide', 'rwmis_redirect_label', __('Redirect Label',RW_MenuItem_TD) );
            rwpro_mis_add_field( $item_id, 'wide', 'rwmis_redirect_url', __('Redirect URL',RW_MenuItem_TD) );	
        ?>
    </div>
    <?php
}

function rwpro_mis_add_field($itemid, $desc_class, $fieldname, $label ){
	$fieldtype = 'datetime';
	if($fieldname == 'rwmis_redirect' || $fieldname == 'rwmis_hide' || $fieldname == 'rwmis_hide_child'){ $fieldtype = 'checkbox'; }
	if($fieldname == 'rwmis_redirect_url'){ $fieldtype = 'url'; }
	if($fieldname == 'rwmis_redirect_label'){ $fieldtype = 'text'; }	
	
	if($fieldtype == 'checkbox'){
	?>        
<p class="<?php echo 'field-'.$fieldname; ?> description <?php echo 'description-'.$desc_class; ?>">
	<label for="<?php echo 'edit-menu-item-'. $fieldname .'-'. $itemid; ?>">
		<input type="checkbox" 
                id="<?php echo 'edit-menu-item-'. $fieldname .'-'. $itemid; ?>" class="widefat code <?php echo 'edit-menu-item-'.$fieldname; ?>" 
                name="<?php echo 'menu-item-'. $fieldname .'['. $itemid .']'; ?>" 
				value="yes" <?php checked( get_post_meta( $itemid, '_menu_item_'.$fieldname, true ), 'yes' ); ?> />
		<?php if($fieldname == 'rwmis_hide_child'){ _e( $label ); } else { ?><b><?php _e( $label ); ?></b><?php } ?>
        <br />
	</label> 
</p>    
<?php } else { ?>  
<p class="<?php echo 'field-'.$fieldname; ?> description <?php echo 'description-'.$desc_class; ?>">
	<label for="<?php echo 'edit-menu-item-'. $fieldname .'-'. $itemid; ?>">
    	<?php _e( $label ); ?><br />
		<input type="<?php echo esc_attr($fieldtype); ?>" 
                id="<?php echo 'edit-menu-item-'. $fieldname .'-'. $itemid; ?>" class="widefat code <?php echo 'edit-menu-item-'.$fieldname; ?>" 
                name="<?php echo 'menu-item-'. $fieldname .'['. $itemid .']'; ?>" 
				value="<?php echo get_post_meta( $itemid, '_menu_item_'.$fieldname, true ); ?>" />
	</label> 
</p>
	<?php
	}
}

function rwmis_add_item_script(){
	global $rwmis_ids;
	if(empty($rwmis_ids)){ return; }	
	?>
<script>		
(function( $ ) { 
	$(function() {
<?php for( $id=0; $id < count($rwmis_ids); $id++ ) { ?>        

        /* Hide Item Field Script */
		jQuery("#edit-menu-item-rwmis_hide-<?php echo $rwmis_ids[$id]; ?>").click(function(){
          jQuery('#divid_rwmis_hide_<?php echo $rwmis_ids[$id]; ?>').toggle();
        });
        jQuery('#edit-menu-item-rwmis_hide_start-<?php echo $rwmis_ids[$id]; ?>').datetimepicker();
		jQuery('#edit-menu-item-rwmis_hide_end-<?php echo $rwmis_ids[$id]; ?>').datetimepicker();
		
		/* Redirect Item Field Script */
		jQuery("#edit-menu-item-rwmis_redirect-<?php echo $rwmis_ids[$id]; ?>").click(function(){
          jQuery('#divid_rwmis_redirect_<?php echo $rwmis_ids[$id]; ?>').toggle();
        });
		jQuery('#edit-menu-item-rwmis_redirect_start-<?php echo $rwmis_ids[$id]; ?>').datetimepicker();
		jQuery('#edit-menu-item-rwmis_redirect_end-<?php echo $rwmis_ids[$id]; ?>').datetimepicker();
        
<?php } ?>
	});			 
})( jQuery );
</script>
		<?php	
}


function rwpro_mis_update_field( $mi_bd_id, $field_name ){
	/* Update custom Fields as per settings 		 */
		
	if ( isset($_REQUEST['menu-item-'.$field_name][$mi_bd_id]) ) {
		if($field_name == 'rwmis_redirect_url'){
        	$field_value = esc_url_raw($_REQUEST['menu-item-'.$field_name][$mi_bd_id]);
		} else {
        	$field_value = sanitize_text_field($_REQUEST['menu-item-'.$field_name][$mi_bd_id]);
		}		
    } else {
        if( $field_name == 'rwmis_redirect' || $field_name == 'rwmis_hide' || $field_name == 'rwmis_hide_child' ){
			$field_value = 'no';
		} else{
			$field_value = '';
		}
	}
    update_post_meta( $mi_bd_id , '_menu_item_'.$field_name , $field_value );
	
}



function rwmis_get_start($start_time){
	if ( $start_time != '' ) {
		/* Set current time as start time */
		$start_time = date('Y/m/d H:i:s',current_time( 'timestamp', 0 ) );
	}
	return $start_time;
}
/** 
 *	Functions for Redirect Item 
 **/
function rwmis_changeitem_redirect($items){
	/* To change links of redirect items from items */
			
	foreach ( $items as $key => $item ) {
		if( $item->rwmis_redirect == 'yes' && $item->rwmis_redirect_end != '' ) {
			$redirect_from = rwmis_get_start($item->rwmis_redirect_start);
			$redirect_upto = $item->rwmis_redirect_end;	
			if( $redirect_upto > $redirect_from ){
				if ( $item->rwmis_redirect_label != '' ) {
					$item->title = $item->rwmis_redirect_label;
				}				
				if ( $item->rwmis_redirect_url != '' ) {
					$item->url = $item->rwmis_redirect_url;
				}
			}
		}				
	}
	return $items;
}

/** 
 *	Functions for Hide Item 
 **/
function rwmis_changeitem_hide($items){
	/* To remove hideing items from items */

	foreach ( $items as $key => $item ) {
		if ( $item->rwmis_hide == 'yes' && $item->rwmis_hide_end != '' ) {
			$hide_from = rwmis_get_start($item->rwmis_hide_start);
			$hide_upto = $item->rwmis_hide_end;
			if( $hide_upto > $hide_from ){
				
				unset( $items[$key] );	/* Remove Item Current Item */			
				$items = rwmis_reset_items( $items, $item->ID, $item->menu_item_parent, $item->rwmis_hide_child, $hide_from, $hide_upto );
				
			}
		}
	}
	return $items;
	
}

function rwmis_reset_items($items, $getid, $setid, $hide_child, $hide_from, $hide_upto){

	foreach ( $items as $key => $item ) {
		
		if($item->menu_item_parent == $getid){
			/* assign 'menu_item_parent' of parent item to current item  */	
			
			$item->menu_item_parent = $setid;			
			if( $hide_child == 'yes' ){
				/* rwmis_hide_child set 'yes' to current item */
				$item->rwmis_hide = 'yes';
				$item->rwmis_hide_child = 'yes';
				$item->rwmis_hide_start = $hide_from;
				$item->rwmis_hide_end = $hide_upto;
			}				
		}
	}
	return $items;
}
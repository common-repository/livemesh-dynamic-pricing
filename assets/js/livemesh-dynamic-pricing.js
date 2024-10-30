jQuery( function( $ ) {

	/*****************************
	 * Dynamic Pricing settings
	 *****************************/

	$( '#ldp-settings' ).on( 'change', '#pricing_method', function() {

		var $settings_wrapper = $( '#ldp-dynamic-pricing-settings' );
		$settings_wrapper.block({ message: null, overlayCSS: { background: '#fff', opacity: 0.6 } });

		var data = {
			action:      'ldp_update_pricing_method',
			post_id:     $( this ).parents( '.ldp-settings-meta-box' ).first().attr( 'data-post-id' ),
			pricing_method: $( this ).val(),
			nonce:       ldp.nonce
		};

		$.post( ajaxurl, data, function( response ) {
			$settings_wrapper.html( response );
			$settings_wrapper.unblock();
			init_bulk_row_repeater();
		});

	});

	/** Bulk pricing - repeater */
	function init_bulk_row_repeater() {
		$('.ldp-bulk-pricing-settings').repeater({
			addTrigger: '.ldp-bulk-row-add',
			removeTrigger: '.ldp-bulk-row-delete',
			template: '.ldp-bulk-row-template .ldp-bulk-row',
			elementWrap: '.ldp-bulk-row',
			elementsContainer: '.ldp-bulk-pricing-wrapper',
			removeElement: function (el) {
				el.remove();
			}
		});
	}
	init_bulk_row_repeater();

	/*****************************
	 * Single pricing
	 *****************************/

	// Add new single pricing condition
	$( '#ldp_dynamic_pricing_data' ).on( 'click', '.ldp-add-dynamic-pricing-button', function(){

		var wrapper			= $( this ).closest( '#ldp_dynamic_pricing_data' ).find( '.ldp-product-pricing' );
		var condition_type 	= $( this ).closest( '#ldp_dynamic_pricing_data' ).find( '#ldp-add-dynamic-pricing' ).val();

		var data 			= {
			action:			'ldp_single_pricing_condition',
			condition_type:	condition_type,
			index:			$( '#ldp_dynamic_pricing_data .ldp-dynamic-pricing-condition' ).length,
			nonce:          ldp.nonce,
			post_id:		ldp.post_id
		};

		wrapper.block({ message: null, overlayCSS: { background: '#fff', opacity: 0.6 } });

		$.post( ajaxurl, data, function( response ) {
			$( response ).css( 'display', 'none' ).appendTo( wrapper ).slideDown( 'normal' );
			wrapper.unblock();

			$( document.body ).trigger( 'wc-enhanced-select-init' );
			ldp_single_price_bulk_action_repeater();
			lwc_condition_group_repeater();
			lwc_condition_row_repeater();
		});

	});

	// Remove single pricing action
	$( '#ldp_dynamic_pricing_data' ).on( 'click', '.remove_row', function(e) {
		e.preventDefault();

		if ( confirm( 'Are you sure you want to remove this pricing?' ) ) {
			$( this ).parents( '.ldp-dynamic-pricing-condition.wc-metabox' ).first().slideUp( 'normal', function() { $( this ).remove(); });
		}

	});

	function ldp_single_price_bulk_action_repeater() {
		$( '.ldp-dynamic-pricing-data' ).repeater({
			addTrigger: '.ldp-bulk-row-add',
			removeTrigger: '.ldp-bulk-row-delete',
			template: '.ldp-bulk-row-template .ldp-bulk-row',
			elementWrap: '.ldp-bulk-row',
			elementsContainer: '.ldp-bulk-pricing-wrapper',
			removeElement: function( el ) {
				el.remove();
			}
		});
	}
	ldp_single_price_bulk_action_repeater();

	function lwc_condition_group_repeater() {
		// Condition group repeater
		$( '.lwc-conditions' ).repeater({
			addTrigger: '.lwc-condition-group-add',
			removeTrigger: '.lwc-condition-group .delete',
			template: '.lwc-condition-group-template .lwc-condition-group-wrap',
			elementWrap: '.lwc-condition-group-wrap',
			elementsContainer: '.lwc-condition-groups',
			removeElement: function( el ) {
				el.remove();
			}
		});
	}

	function lwc_condition_row_repeater() {
		// Condition repeater
		$( '.lwc-condition-group' ).repeater({
			addTrigger: '.lwc-condition-add',
			removeTrigger: '.lwc-condition-delete',
			template: '.lwc-condition-template .lwc-condition-wrap',
			elementWrap: '.lwc-condition-wrap',
			elementsContainer: '.lwc-conditions-list',
		});
	}


	// Duplicate condition group
	$( document.body ).on ( 'click', '.lwc-conditions .lwc-duplicate-product-group', function(e) {

		var condition_group_wrap = $( this ).parents( '.lwc-condition-group-wrap' ),
			condition_group_id   = condition_group_wrap.find( '.lwc-condition-group' ).attr( 'data-group' ),
			condition_group_list = $( this ).parents( '.lwc-condition-groups' ),
			new_group            = condition_group_wrap.clone(),
			new_group_id         = Math.floor(Math.random()*899999999+100000000 ),  // Random number sequence of 9 length
			pricing_rule_index   = condition_group_wrap.parents( '.ldp-dynamic-pricing-data.wc-metabox-content' ).attr( 'data-index' );

		// Fix dropdown selected not being cloned properly
		$( condition_group_wrap ).find( 'select' ).each(function(i) {
			$( new_group ).find( 'select' ).eq( i ).val( $( this ).val() );
		});

		// Assign proper names
		new_group.find( '.lwc-condition-group' ).attr( 'data-group', new_group_id );
		new_group.find( 'input[name], select[name]' ).attr( 'name', function( index, name ) {
			return name.replace( 'dynamic_pricing[' + pricing_rule_index + '][condition][' + condition_group_id + ']', 'dynamic_pricing[' + pricing_rule_index + '][condition][' + new_group_id + ']' );
		});

		new_group.find( '.repeater-active' ).removeClass( 'repeater-active' );
		condition_group_list.append( new_group );

		// Enable Select2's
		//$( document.body ).trigger( 'wc-enhanced-select-init' );

		// Init condition repeater
		lwc_condition_row_repeater();

		// Stop autoscroll on manual scrolling
		$( 'html, body' ).on( "scroll mousedown DOMMouseScroll mousewheel keydown touchmove", function( e ) {
			$( 'html, body' ).stop().off('scroll mousedown DOMMouseScroll mousewheel keydown touchmove');
		});

		// Autoscroll to new group
		$( 'body, html' ).animate({ scrollTop: $( new_group ).offset().top - 50 }, 750, function() {
			$( 'html, body' ).off('scroll mousedown DOMMouseScroll mousewheel keydown touchmove');
		});

	});


	/*****************************
	 * Single pricing conditions
	 *****************************/

	// Update condition values
	$( '#ldp_dynamic_pricing_data' ).on( 'change', '.lwc-condition', function () {

		var loading_wrap = '<span style="width: calc( 42.5% - 75px ); border: 1px solid transparent; display: inline-block;">&nbsp;</span>';
		var data = {
			action: 		'ldp_update_single_condition_value',
			index:			$( this ).parents( 'div.lwc-condition-wrap' ).first().attr( 'data-index' ),
			id:				$( this ).attr( 'data-id' ),
			post_id:		ldp.post_id,
			group:			$( this ).parents( '.lwc-condition-group' ).attr( 'data-group' ),
			condition: 		$( this ).val(),
			nonce:          ldp.nonce
		};
		var condition_wrap = $( this ).parents( '.lwc-condition-wrap' ).first();
		var replace = '.lwc-value-field-wrap';

		// Loading icon
		condition_wrap.find( replace ).html( loading_wrap ).block({ message: null, overlayCSS: { background: '', opacity: 0.6 } });

		// Replace value field
		$.post( ajaxurl, data, function( response ) {
			condition_wrap.find( replace ).replaceWith( response );
			$( document.body ).trigger( 'wc-enhanced-select-init' );
		});

		// Update operators
		var operator_value = condition_wrap.find( '.lwc-operator' ).val();
		condition_wrap.find( '.lwc-operator' ).empty().html( function() {
			var operator = $( this );
			var available_operators = lwc.condition_operators[ data.condition] || lwc.condition_operators['default'];

			$.each( available_operators, function( index, value ) {
				operator.append( $('<option/>' ).attr( 'value', index ).text( value ) );
				operator.val( operator_value ).val() || operator.val( operator.find( 'option:first' ).val() );
			});
		});

		// Update condition description
		condition_wrap.find( '.lwc-description' ).html( function() {
			return $( '<span class="woocommerce-help-tip" />' ).attr( 'data-tip', ( lwc.condition_descriptions[ data.condition ] || '' ) );
		});
		$( '.tips, .help_tip, .woocommerce-help-tip' ).tipTip({ 'attribute': 'data-tip', 'fadeIn': 50, 'fadeOut': 50, 'delay': 200 });
		$( '#tiptip_holder' ).removeAttr( 'style' );
		$( '#tiptip_arrow' ).removeAttr( 'style' );

		return false;

	});

});

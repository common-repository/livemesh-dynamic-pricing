jQuery( function( $ ) {

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
    lwc_condition_group_repeater();


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
    lwc_condition_row_repeater();


    // Assign new ID to repeater row + open collapsible + re-enable nested repeater
    jQuery( document.body ).on( 'repeater-added-row', function( e, template, container, $self ) {
        var new_id = Math.floor(Math.random()*899999999+100000000); // Random number sequence of 9 length
        template.find( 'input[name], select[name]' ).attr( 'name', function( index, value ) {
            return ( value.replace( '9999', new_id ) ) || value;
        });
        template.find( '.lwc-condition[data-id]' ).attr( 'data-id', function( index, value ) {
            return ( value.replace( '9999', new_id ) ) || value;
        });
        // Fix #20 - condition IDs being replaced by group IDs
        template.find( '.lwc-condition-template .lwc-condition[data-id]' ).attr( 'data-id', '9999' );

        template.find( '[data-group]' ).attr( 'data-group', function( index, value ) {
            return ( value.replace( '9999', new_id ) ) || value;
        });

        template.find( '.repeater-active' ).removeClass( 'repeater-active' );

        // Init condition group repeater
        lwc_condition_row_repeater();
    });


    // Duplicate condition group
    $( document.body ).on ( 'click', '.lwc-conditions .duplicate', function() {
        var condition_group_wrap = $( this ).parents( '.lwc-condition-group-wrap' ),
            condition_group_id   = condition_group_wrap.find( '.lwc-condition-group' ).attr( 'data-group' ),
            condition_group_list = $( this ).parents( '.lwc-condition-groups' ),
            new_group            = condition_group_wrap.clone(),
            new_group_id         = Math.floor(Math.random()*899999999+100000000); // Random number sequence of 9 length

        // Fix dropdown selected not being cloned properly
        $( condition_group_wrap ).find( 'select' ).each(function(i) {
            $( new_group ).find( 'select' ).eq( i ).val( $( this ).val() );
        });

        // Assign proper names
        new_group.find( '.lwc-condition-group' ).attr( 'data-group', new_group_id );
        new_group.find( 'input[name], select[name]' ).attr( 'name', function( index, name ) {
            return name.replace( 'conditions[' + condition_group_id + ']', 'conditions[' + new_group_id + ']' );
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


    // Update condition values
    $( document.body ).on( 'change', '.lwc-condition', function () {

        var loading_wrap = '<span style="width: calc( 42.5% - 75px ); border: 1px solid transparent; display: inline-block;">&nbsp;</span>';
        var data = {
            action: 	lwc2.action_prefix + 'update_condition_value',
            id:			$( this ).attr( 'data-id' ),
            group:		$( this ).parents( '.lwc-condition-group' ).attr( 'data-group' ),
            condition: 	$( this ).val(),
            nonce: 		lwc.nonce
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

    });


    // Sortable post table
    $( '.lwc-conditions-post-table.lwc-sortable-post-table tbody' ).sortable({
        items:					'tr',
        handle:					'.sort',
        cursor:					'move',
        axis:					'y',
        scrollSensitivity:		40,
        forcePlaceholderSize: 	true,
        helper: 				'clone',
        opacity: 				0.65,
        placeholder: 			'wc-metabox-sortable-placeholder',
        start: function(event,ui){
            ui.item.css( 'background-color','#f6f6f6' );
        },
        stop: function(event,ui){
            ui.item.removeAttr( 'style' );
        },
        update: function(event, ui) {

            $table 	= $( this ).closest( 'table' );
            $table.block({ message: null, overlayCSS: { background: '#fff', opacity: 0.6 } });
            // Update fee order
            var data = {
                action:	'lwc_save_post_order',
                form: 	$( this ).closest( 'form' ).serialize(),
                nonce: 	lwc.nonce
            };

            $.post( ajaxurl, data, function( response ) {
                $( '.lwc-conditions-post-table tbody tr:even' ).addClass( 'alternate' );
                $( '.lwc-conditions-post-table tbody tr:odd' ).removeClass( 'alternate' );
                $table.unblock();
            });
        }
    });

});

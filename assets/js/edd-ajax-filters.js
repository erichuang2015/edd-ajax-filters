(function($) {
    var container; // Used to initial load check

    var animation_interval; // Running animation interval
    var wait_for_animation_interval; // Wait for animation interval

    var cached_filters;
    var cached_atts;

    // Filter taxonomy all
    $('body').on('change.eddAjaxFiltersTaxonomyAll', '.edd-ajax-filter-taxonomy.edd-ajax-filter-multiple input[type="checkbox"]', function(e) {
        if( $(this).is(':checked') ) {
            // On check all option (val == 0), uncheck the rest and on check someone, uncheck all option
            $(this).closest('.edd-ajax-filter-taxonomy').find('input[type="checkbox"]' + ( ( $(this).val() == 0 ) ? ':not([value="0"])' : '[value="0"]' ) ).prop('checked', false);
        }
    });

    // Filter: Button
    $('body').on('click.eddAjaxFiltersButton', '.edd-ajax-filter-button', function (e) {
        e.preventDefault();

        var action = $(this).data('action');

        switch ( action ) {
            case 'submit':
                edd_ajax_filters_apply_filters();
                break;
            case 'order':
                $(this).addClass('edd-ajax-filter-active');

                edd_ajax_filters_apply_filters();
                break;
        }
    });

    // Triggers
    var selectors = [
        // Input
        '.edd-ajax-filter-input',
        // Options
        'select.edd-ajax-filter-options', // Select
        '.edd-ajax-filter-options input', // Block/List
        // Taxonomy
        'select.edd-ajax-filter-taxonomy', // Select
        '.edd-ajax-filter-taxonomy input', // Block/List
    ];

    // Filter: Options
    $('body').on('change.eddAjaxFiltersAutoTrigger', selectors.join(', '), function (e) {
        e.preventDefault();

        edd_ajax_filters_apply_filters();
    });

    // Applies all in screen filters
    function edd_ajax_filters_apply_filters() {
        var shortcode_form = $('#edd-ajax-filters-shortcode-atts');

        var container = $('.edd_downloads_list');

        if( ! container.hasClass('edd-ajax-filters-loading') ) {

            // Class to check if this container is loading
            container.addClass('edd-ajax-filters-loading');

            // Filters
            if( cached_filters === undefined ) {
                // Get all active filters
                var filters = [];

                $('.edd-ajax-filter').each(function () {
                    edd_ajax_filters_pass_filter(filters, $(this));
                });

                cached_filters = filters;
            }

            // Atts
            if( cached_atts === undefined ) {
                // Get shortcode atts
                var shortcode_atts = {};

                shortcode_form.find('input[name^="shortcode_atts"]').each(function () {
                    shortcode_atts[$(this).data('att')] = $(this).val();
                });

                cached_atts = shortcode_atts;
            }

            // Animate results if is set
            if( edd_ajax_filters.out_animation != '' ) {
                container.addClass('edd-ajax-filters-animating');

                if( edd_ajax_filters.in_animation != '' ) {
                    animation_interval = setInterval(
                        edd_ajax_filters_change_animation,                                          // Function
                        edd_ajax_filters.out_animation_delay,                                       // Delay
                        container, edd_ajax_filters.in_animation, edd_ajax_filters.out_animation    // Parameters
                    );
                } else {
                    animation_interval = setInterval(
                        edd_ajax_filters_animate,                     // Function
                        edd_ajax_filters.out_animation_delay,         // Delay
                        container, edd_ajax_filters.out_animation     // Parameters
                    );
                }

            }

            // Apply filters request
            $.ajax({
                url: edd_ajax_filters.ajax_url,
                data: {
                    action: 'edd_ajax_filters',
                    nonce: edd_ajax_filters.nonce,
                    filters: cached_filters,
                    shortcode_atts: cached_atts,
                    paged: shortcode_form.find('input[name="paged"]').val()
                },
                cache: false,
                success: function( response ) {
                    if( response.found_results > 0 ) {
                        // Insert returned html
                        var parsed_response = $(response.html);
                        var content_to_append = parsed_response.filter('.edd_downloads_list').html();

                        if( edd_ajax_filters.out_animation != '' && edd_ajax_filters.in_animation != '' && container.hasClass('edd-ajax-filters-animating') ) {
                            // If is running out animation, then we need to wait to finish it
                            wait_for_animation_interval = setInterval(
                                edd_ajax_filters_wait_for_animate,                                                                  // Function
                                200,                                                                                                // Delay
                                container, content_to_append, edd_ajax_filters.in_animation, edd_ajax_filters.in_animation_delay    // Parameters
                            );
                        } else if( edd_ajax_filters.in_animation != '' ) {
                            container.html(content_to_append);

                            // Animate results if is set
                            container.addClass('edd-ajax-filters-animating');

                            animation_interval = setInterval(
                                edd_ajax_filters_animate,                 // Function
                                edd_ajax_filters.in_animation_delay,      // Delay
                                container, edd_ajax_filters.in_animation  // Parameters
                            );
                        } else {
                            // If no animation, append content directly
                            container.html(content_to_append);
                        }
                    } else {
                        container.html(response.html);
                    }

                    // Reset classes
                    container.removeClass('edd-ajax-filters-loading');
                }
            });
        }
    }

    // Utility to find filter type
    function edd_ajax_filters_get_filter_type( element ) {
        // First check filters based on base filters
        if( element.hasClass('edd-ajax-filter-order') ) {
            return 'order';
        } else if( element.hasClass('edd-ajax-filter-taxonomy') ) {
            return 'taxonomy';
        }

        // Finally check base filters
        if( element.hasClass('edd-ajax-filter-button') ) {
            return 'button';
        } else if( element.hasClass('edd-ajax-filter-input') ) {
            return 'input';
        } else if( element.hasClass('edd-ajax-filter-options') ) {
            return 'options';
        }

        return false;
    }

    // Turn filters input into a javascript object
    function edd_ajax_filters_pass_filter( filters, element ) {
        var filter = edd_ajax_filters_get_filter_type( element );
        var value, values; // Internal used

        if( filter && filter !== 'button' ) {
            switch ( filter ) {
                case 'input':
                    if( element.val() != '' ) {
                        filters.push({
                            field: element.data('field'),
                            value: element.val(),
                            operator: element.data('operator'),
                            filter: filter
                        });
                    }
                    break;
                case 'options':
                case 'taxonomy':
                    value = undefined;

                    if( element.hasClass('edd-ajax-filter-select') ) { // Select
                        value = element.val();
                    } else if( element.hasClass('edd-ajax-filter-multiple') ) { // Block/List multiple
                        values = [];

                        element.find("input:checked").map(function() {
                            values.push($(this).val());
                        });

                        if( values && values.length ) {
                            value = values.join(',');
                        }
                    } else { // Block/List singular
                        value = element.find("input:checked").val();
                    }

                    if( value && value.length ) {
                        filters.push({
                            field: element.data('field'),
                            value: value,
                            operator: element.data('operator'),
                            filter: filter
                        });
                    }
                    break;
                case 'order':
                    if( element.hasClass('edd-ajax-filter-active') ) {
                        // Instead of operator, this filter uses direction
                        filters.push({
                            field: element.data('field'),
                            direction: element.data('direction'),
                            filter: filter
                        });
                    }
                    break;
            }
        }
    }

    // Animate downloads on load if is set
    if( edd_ajax_filters.in_animation != '' && $('.edd_downloads_list').length != 0 ) {
        container = $('.edd_downloads_list');
        container.addClass('edd-ajax-filters-animating');

        animation_interval = setInterval(
            edd_ajax_filters_animate,                   // Function
            edd_ajax_filters.in_animation_delay,            // Delay
            container, edd_ajax_filters.in_animation        // Parameters
        );
    }

    // Apply animation to new loaded downloads
    function edd_ajax_filters_animate( container, animation ) {
        var elements = container.find('.edd_download:not(.animated)');

        // All elements has been animated
        if(elements.length == 0) {
            // Remove animating class
            container.removeClass('edd-ajax-filters-animating');

            // Clear animation interval
            clearInterval(animation_interval);

            return false;
        }

        // Animate the first element
        elements.first().addClass(animation).addClass('animated');
    }

    // Check if container is running an animation to animate content again when finish
    function edd_ajax_filters_wait_for_animate( container, content, animation, animation_delay ) {
        if( container.hasClass('edd-ajax-filters-animating') ) {
            return false;
        }

        container.html(content);

        container.addClass('edd-ajax-filters-animating');

        animation_interval = setInterval(
            edd_ajax_filters_animate, // Function
            animation_delay,                // Delay
            container, animation            // Parameters
        );

        clearInterval(wait_for_animation_interval);
    }

    // Switch between two animations in download list
    function edd_ajax_filters_change_animation( container, old_animation, new_animation ) {
        var elements = container.find('.edd_download.' + old_animation);

        // All elements has been animated
        if(elements.length == 0) {
            // Remove animating class
            container.removeClass('edd-ajax-filters-animating');

            // Clear animation interval
            clearInterval(animation_interval);

            return false;
        }

        // Animate the first element
        elements.first().removeClass(old_animation).addClass(new_animation);
    }
})(jQuery);
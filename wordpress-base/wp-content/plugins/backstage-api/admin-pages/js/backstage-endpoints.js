(function($, _) {
    var templates = {},
        state = {},
        elements = {},
        events = {};

    /**
     * Load config
     */
    BackstageConfig.load('endpoints', function(err, data) {
        if (err) return console.log('error', err);

        console.log(data);

        state.original = data;
        state.current = $.extend(true, {}, state.original);

        $(document).ready(function() {
            // Templates
            templates.tableRow = $('.bs-hook_endpoints-list').html();

            // Elements
            elements.table = $('.bs-hook_endpoints-table');
            elements.disableMessage = $('.bs-hook_endpoints-disabled');
            elements.tableBody = $('.bs-hook_endpoints-list');
            elements.enableEndpoints = $('.bs-hook_enable-endpoints-check');
            elements.toggleAllEndpoints = $('.bs-hook_all-endpoints-check');
            elements.saveButton = $('.bs-hook_save-changes');
            elements.resetButton = $('.bs-hook_reset-changes');
            

            // Bindings
            elements.tableBody.on('click', '.bs-hook_endpoint-check', events.toggleEndpoint);
            elements.toggleAllEndpoints.on('change', events.toggleAllEndpoints);
            elements.enableEndpoints.on('change', events.toggleEnableEndpoints);
            elements.saveButton.on('click', events.saveChanges);
            elements.resetButton.on('click', events.resetChanges);

            render();

            // Removing spinner and showing content
            $('.bs-hook_init-spinner').hide();
            $('.bs-hook_content').fadeIn(200);
        });
    });
    
    /**
     * Enables/disabled endpoints.
     */
    events.toggleEnableEndpoints = function() {
        state.current.enabled = $(this).prop('checked');

        BackstageUtils.statusMessage();
        render();
    };

    /**
     * Toggle endpoint
     */
    events.toggleEndpoint = function() {
        var endpoint = $(this).data('endpoint'),
            exists = false;

        for (var i = 0; i < state.current.exposed.length; i++) {
            if (state.current.exposed[i] === endpoint) {
                state.current.exposed.splice(i, 1);
                i--;
                exists = true;
            }
        }

        if (!exists) {
            state.current.exposed.push(endpoint);
        }

        BackstageUtils.statusMessage();
        render();
    };

    /**
     * Toggle endpoint
     */
    events.toggleAllEndpoints = function() {
        if (state.current.exposed.length === state.current.all.length) {
            state.current.exposed = [];
        } else {
            state.current.exposed = state.current.all.map(function(item) {
                return item.endpoint;
            });
        }

        BackstageUtils.statusMessage();
        render();
    };

    /**
     * Resets changes by resetting state to original state.
     */
    events.resetChanges = function() {
        state.current = $.extend(true, {}, state.original);

        BackstageUtils.statusMessage();
        render();
    };

    /**
     * Saving the new state to WordPress.
     */
    events.saveChanges = function() {
        var interactiveElements = $('.bs-hook_interactive-element'),
            data = {
                enabled: state.current.enabled,
                exposed: state.current.exposed
            };

        BackstageUtils.statusMessage();

        interactiveElements.prop('disabled', true);
        elements.saveButton.prop('disabled', true);
        elements.resetButton.prop('disabled', true);

        BackstageConfig.save('endpoints', data, function(err, res) {
            interactiveElements.prop('disabled', false);
            elements.saveButton.prop('disabled', false);
            elements.resetButton.prop('disabled', false);

            if (err) return console.log('error', err);

            state.original = $.extend(true, {}, state.current);

            BackstageUtils.statusMessage('Endpoints settings has been saved.');
            render();
        });
    };

    /**
     * Render state to view
     */
    var render = function() {
        elements.saveButton.prop('disabled', _.isEqual(state.current, state.original));
        elements.resetButton.prop('disabled', _.isEqual(state.current, state.original));

        elements.tableBody.html('');

        state.current.all.forEach(function(item, i) {
            var row = templates.tableRow.replace(/"(endpoint_check_)"/g, '"$1' + i + '"'),
                row = $(row);

            row.find('.bs-hook_endpoint-label').html(BackstageUtils.escape(item.endpoint));
            row.find('.bs-hook_endpoint-check').data('endpoint', item.endpoint);
            row.find('.bs-hook_endpoint-check').prop('checked', state.current.exposed.indexOf(item.endpoint) >= 0);

            item.methods.forEach(function(method) {
                row.find('.bs-hook_endpoint-methods').append('<span class="bs-endpoints__row__method">' + BackstageUtils.escape(method) + '</span>');
            });

            elements.tableBody.append(row);
        });

        elements.toggleAllEndpoints.prop('checked', state.current.exposed.length === state.current.all.length);

        elements.tableBody.show();

        elements.enableEndpoints.prop('checked', state.current.enabled);

        elements.disableMessage.toggle(!state.current.enabled);

        elements.table.toggle(state.current.enabled);
    };

})(jQuery, _);
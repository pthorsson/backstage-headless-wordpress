(function($, _) {
    var templates = {},
        state = {},
        elements = {},
        events = {};

    /**
     * Load config
     */
    BackstageConfig.load('cors', function(err, data) {
        if (err) return console.log('error', err);

        state.original = data;

        state.original.origins = state.original.origins.map(function(origin) {
            return {
                id: BackstageUtils.incrementId(),
                url: origin
            }
        });

        state.current = $.extend(true, {}, state.original);

        $(document).ready(function() {
            // Templates
            templates.tableRow = $('.bs-hook_added-origins').html();

            // Elements
            elements.table = $('.bs-hook_origins-table');
            elements.disableMessage = $('.bs-hook_cors-disabled');
            elements.tableBody = $('.bs-hook_added-origins');
            elements.checkBox = $('.bs-hook_enable-cors-check');
            elements.saveButton = $('.bs-hook_save-changes');
            elements.resetButton = $('.bs-hook_reset-changes');
            elements.originInput = $('.bs-hook_origin-input');

            // Bindings
            elements.tableBody.on('click', '.bs-hook_remove-origin', events.removeOrigin);
            elements.table.on('click', '.bs-hook_add-origin', events.addOrigin);
            elements.checkBox.on('change', events.toggleCors);
            elements.saveButton.on('click', events.saveChanges);
            elements.resetButton.on('click', events.resetChanges);

            render();

            // Removing spinner and showing content
            $('.bs-hook_init-spinner').hide();
            $('.bs-hook_content').fadeIn(200);
        });
    });

    /**
     * Validates and adds origin to the state.
     */
    events.addOrigin = function() {
        var url = elements.originInput.val();
            isValid = /^https?:\/\/[a-zA-Z0-9-.]{1,}(:[0-9]{1,5}|)$/.test(url),
            exists = state.current.origins.some(function(origin) {
                return origin.url === url;
            });

        BackstageUtils.statusMessage();

        if (isValid && !exists) {
            elements.originInput.val('');

            state.current.origins.push({
                id: BackstageUtils.incrementId(),
                url: url
            });

            render();
        } else {
            if (exists) {
                BackstageUtils.statusMessage('Origin url is already in the list.', 'error');
            } else if (!isValid) {
                BackstageUtils.statusMessage('Origin url is invalid.', 'error');
            }
        }
    };

    /**
     * Removes origin from the state.
     */
    events.removeOrigin = function() {
        var row = $(this).closest('.bs-hook_table-row'),
            id = $(this).closest('.bs-hook_table-row').data('row-id');

        state.current.origins = state.current.origins.filter(function(origin) {
            return origin.id !== id;
        });

        BackstageUtils.statusMessage();
        render();
    };
    
    /**
     * Enables/disabled CORS.
     */
    events.toggleCors = function() {
        state.current.enabled = $(this).prop('checked');

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
                origins: state.current.origins.map(function(origin) {
                    return origin.url;
                })
            };

        BackstageUtils.statusMessage();

        interactiveElements.prop('disabled', true);
        elements.saveButton.prop('disabled', true);
        elements.resetButton.prop('disabled', true);

        BackstageConfig.save('cors', data, function(err, res) {
            interactiveElements.prop('disabled', false);
            elements.saveButton.prop('disabled', true);
            elements.resetButton.prop('disabled', true);

            if (err) return console.log('error', err);

            state.original = $.extend(true, {}, state.current);

            BackstageUtils.statusMessage('CORS settings has been saved.');
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

        state.current.origins.forEach(function(origin) {
            var row = $(templates.tableRow);

            row.find('.bs-hook_origin-label').html(origin.url);
            row.data('row-id', origin.id);

            elements.tableBody.append(row);

            return origin;
        });

        elements.tableBody.show();

        elements.checkBox.prop('checked', state.current.enabled);

        elements.disableMessage.toggle(!state.current.enabled);

        elements.table.toggle(state.current.enabled);
    };

})(jQuery, _);
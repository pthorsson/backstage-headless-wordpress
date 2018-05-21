(function($) {
    var tableRow, state;

    /**
     * Remove origin
     */
    $('.bs-hook_added-origins').on('click', '.bs-hook_remove-origin', function() {
        var row = $(this).closest('.bs-hook_table-row'),
            id = $(this).closest('.bs-hook_table-row').data('row-id');

        state.origins = state.origins.filter(function(origin) {
            return origin.id !== id;
        });

        statusMessage();
        render();
    });

    /**
     * Add origin
     */
    $('.bs-hook_origins-table').on('click', '.bs-hook_add-origin', function() {
        var url = $('.bs-hook_origin-input').val();
            isValid = /^https?:\/\/[a-zA-Z0-9-.]{1,}(:[0-9]{1,5}|)$/.test(url),
            exists = state.origins.some(function(origin) {
                return origin.url === url;
            });

        statusMessage();

        if (isValid && !exists) {
            $('.bs-hook_origin-input').val('');

            state.origins.push({
                url: url,
                id: incrementId()
            });

            render();
        } else {
            if (exists) {
                statusMessage('Origin url is already in the list.', 'error');
            } else if (!isValid) {
                statusMessage('Origin url is invalid.', 'error');
            }
        }
    });

    /**
     * Save data
     */
    $('.bs-hook_save-origins').on('click', function() {
        var button = $(this),
            data = {
                enabled: state.enabled,
                origins: state.origins.map(function(origin) {
                    return origin.url;
                })
            };

        statusMessage();

        $('.bs-hook_interactive-element').prop('disabled', true);
        button.prop('disabled', true);

        BackstageConfig.save('cors', data, function(err, res) {
            $('.bs-hook_interactive-element').prop('disabled', false);
            button.prop('disabled', false);

            if (err) return console.log('error', err);

            statusMessage('CORS settings has been saved.');
        });
    });

    /**
     * On checkbox change
     */
    $('.bs-hook_enable-cors-check').on('change', function() {
        state.enabled = $(this).prop('checked');
        statusMessage();
    });

    /**
     * Load config
     */
    BackstageConfig.load('cors', function(err, data) {
        if (err) return console.log('error', err);

        state = data;

        state.origins = state.origins.map(function(origin) {
            return {
                id: incrementId(),
                url: origin
            }
        });

        $(document).ready(function() {
            tableRow = $('.bs-hook_added-origins').html();
            render();
        });
    });

    /**
     * Render state to view
     */
    var render = function() {
        var table = $('.bs-hook_added-origins'),
            checkBox = $('.bs-hook_enable-cors-check');

        table.html('');

        state.origins.forEach(function(origin) {
            var row = $(tableRow);

            row.find('.bs-hook_origin-label').html(origin.url);
            row.data('row-id', origin.id);

            table.append(row);

            return origin;
        });

        table.show();

        checkBox.prop('checked', state.enabled);
    };

    /**
     * Render status message
     */
    var statusMessage = function(message, type) {
        if (type === 'error') {
            $('.bs-hook_status-message').removeClass('bs-cors__status-message_success').addClass('bs-cors__status-message_error');
        } else {
            $('.bs-hook_status-message').removeClass('bs-cors__status-message_error').addClass('bs-cors__status-message_success');
        }

        $('.bs-hook_status-message').html(message || '');
    };

    /**
     * Increment ID function
     */
    var incrementId = (function() {
        var id = 0;

        return function() {
            return 'cors_' + (id++);
        }
    })();

})(jQuery);
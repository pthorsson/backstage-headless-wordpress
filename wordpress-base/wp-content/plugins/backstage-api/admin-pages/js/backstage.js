
/**
 * BackstageConfig - Used for loading and saving configuration made in the Backstage API.
 */
(function($) {
    var API_URL = '/wp-admin/admin-ajax.php?action=backstage_';
    var config = {};
    
    var BackstageConfig = {
        load: function(name, callback) {
            if (config[name]) {
                callback(null, $.extend({}, config[name]));
            } else {
                $.ajax({
                    method: 'GET',
                    url: API_URL + name,
                    success: function(res) {
                        config[name] = res;
                        callback(null, $.extend({}, config[name]));
                    },
                    error: function(res) {
                        callback(res);
                    }
                });
            }
        },
        save: function(name, data, callback) {
            $.ajax({
                method: 'POST',
                url: API_URL + name,
                data: data,
                success: function(res) {
                    callback(null, res);
                },
                error: function(res) {
                    callback(res);
                }
            });
        }
    };

    window.BackstageConfig = BackstageConfig;

    var BackstageUtils = {

        incrementId: (function() {
            var id = 0;
    
            return function() {
                return 'bsid_' + (id++);
            }
        })(),

        escape: function (unsafe) {
            return unsafe
                 .replace(/&/g, "&amp;")
                 .replace(/</g, "&lt;")
                 .replace(/>/g, "&gt;")
                 .replace(/"/g, "&quot;")
                 .replace(/'/g, "&#039;");
        },

        statusMessage: function(message, type) {
            if (type === 'error') {
                $('.bs-hook_status-message').removeClass('bs__status-message_success').addClass('bs__status-message_error');
            } else {
                $('.bs-hook_status-message').removeClass('bs__status-message_error').addClass('bs__status-message_success');
            }
    
            $('.bs-hook_status-message').html(message || '');
        }

    };

    window.BackstageUtils = BackstageUtils;

})(jQuery);
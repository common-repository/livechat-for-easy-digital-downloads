(function($){
    $(document).ready(function () {
        window.LiveChatEdd = {
            slug: 'livechat-for-easy-digital-downloads',
            buttonLoaderHtml:
                '<div class="lc-loader-wrapper lc-btn__loader"><div class="lc-loader-spinner-wrapper lc-loader-spinner-wrapper--small"><div class="lc-loader-spinner lc-loader-spinner--thin" /></div></div>',
            init: function () {
                this.signInWithLiveChat();
                this.bindDisconnect();
                this.updateSettingsHandler();
                this.installedNotificationHandler();
                this.connectNoticeButtonHandler();
                this.deactivationModalOpenHandler();
                this.deactivationModalCloseHandler();
                this.deactivationFormOptionSelectHandler();
                this.deactivationFormSkipHandler();
                this.deactivationFormSubmitHandler();
            },
            sanitize: function (str) {
                var tmpDiv = document.createElement('div');
                tmpDiv.textContent = str;
                return tmpDiv.innerHTML;
            },
            bindEvent: function(element, eventName, eventHandler) {
                if (element.addEventListener){
                    element.addEventListener(eventName, eventHandler, false);
                } else if (element.attachEvent) {
                    element.attachEvent('on' + eventName, eventHandler);
                }
            },
            signInWithLiveChat: function () {
                var logoutButton = document.getElementById('resetAccount'),
                    iframeEl = document.getElementById('login-with-livechat');

                LiveChatEdd.bindEvent(window, 'message', function (e) {
                    if (e.origin !== 'https://addons.livechatinc.com') {
                        return false;
                    }

                    try {
                        var lcDetails = JSON.parse(e.data);
                        if (lcDetails.type === 'logged-in') {
                                var licenseForm = $('#licenseForm');
                                if (licenseForm.length) {
                                    $('#licenseEmail').val(lcDetails.email);
                                    $('#licenseNumber').val(lcDetails.license);
                                    LiveChatEdd.sendEvent(
                                        'Integrations: User authorized the app',
                                        lcDetails.license,
                                        lcDetails.email,
                                        function () {
                                            licenseForm.submit();
                                        }
                                    );
                                }
                        }
                    } catch (e) {
                        console.warn(e);
                    }
                });

                if (logoutButton) {
                    LiveChatEdd.bindEvent(logoutButton, 'click', function () {
                        sendMessage('logout');
                    });
                }

                var sendMessage = function(msg) {
                    iframeEl.contentWindow.postMessage(msg, '*');
                };
            },
            bindDisconnect: function() {
                $('#resetAccount').click(function (e) {
                    e.preventDefault();
                    LiveChatEdd.sendEvent(
                        'Integrations: User unauthorized the app',
                        lcDetails.license,
                        lcDetails.email,
                        function () {
                            $('#livechatReset').submit();
                        }
                    );
                });
            },
            sendEvent: function(eventName, license, email, callback) {
                var amplitudeURL = 'https://queue.livechatinc.com/app_event/';
                var data = {
                    "e" : JSON.stringify(
                        [{
                            "event_type": eventName,
                            "user_id": email,
                            "user_properties": {
                                "license": license
                            },
                            "product_name": "livechat",
                            "event_properties": {
                                "integration name": "livechat-for-easy-digital-downloads"
                            }
                        }]
                    )
                };
                $.ajax({
                    url: amplitudeURL,
                    type: 'GET',
                    crossOrigin: true,
                    data: data
                }).always(function () {
                    if (callback) callback();
                });
            },
            getSettings: function () {
                var values;
                $.each($('#lc-settings').serializeArray(), function(i, field) {
                    values[field.name] = field.value;
                });
            },
            updateSettingsHandler: function () {
                $('.switch input[type=checkbox]').change(function () {
                    var livechatSettings = $('#lc-settings');

                    $.ajax({
                        type     : "POST",
                        url      : livechatSettings.attr('action'),
                        data     : livechatSettings.serialize()
                    });
                });
            },
            installedNotificationHandler: function() {
                var that = this;
                var notificationElement = $('.updated.installed');
                $('#installed-close').click(function () {
                    that.hideNotification(notificationElement);
                });
                setTimeout(function () {
                    that.hideNotification(notificationElement);
                }, 3000);
            },
            hideNotification: function (el) {
                if (el.is(":visible")) {
                    el.slideToggle();
                }
            },
            connectNoticeButtonHandler: function () {
                $('#lc-connect-notice-button').click(function () {
                    window.location.replace('admin.php?page=livechat-easydigitaldownloads');
                })
            },
            deactivationFormHelpers: {
                hideErrors: function () {
                    $('.lc-field-error').hide();
                },
                toggleModal: function () {
                    $('#lc-deactivation-feedback-modal-overlay').toggleClass('lc-modal-base__overlay--visible').show();
                },
                showError: function (errorType) {
                    $('#lc-deactivation-feedback-form-' + errorType + '-error').show();
                }
            },
            deactivationModalOpenHandler: function() {
                var that = this;
                $('table.plugins tr[data-slug=' + that.slug + '] span.deactivate a').click(function (e) {
                    if ($('#lc-deactivation-feedback-modal-container').length < 1) {
                        return;
                    }
                    e.preventDefault();
                    that.deactivationFormHelpers.toggleModal();
                })
            },
            deactivationModalCloseHandler: function() {
                var that = this;
                var modalOverlay = $('#lc-deactivation-feedback-modal-overlay');
                modalOverlay.click(function (e) {
                    if (
                        modalOverlay.hasClass('lc-modal-base__overlay--visible') &&
                        (
                            !$(e.target).closest('#lc-deactivation-feedback-modal-container').length ||
                            $(e.target).closest('.lc-modal-base__close').length
                        )
                    ) {
                        that.deactivationFormHelpers.toggleModal();
                    }
                });
            },
            deactivationFormOptionSelectHandler: function () {
                var that = this;
                $('.lc-radio').click(function () {
                    that.deactivationFormHelpers.hideErrors();
                    var otherTextField = $('#lc-deactivation-feedback-other-field');
                    $('.lc-radio').removeClass('lc-radio--selected');
                    $(this).addClass('lc-radio--selected');
                    if ($(this).find('#lc-deactivation-feedback-option-other').length > 0) {
                        otherTextField.show();
                        otherTextField.find('textarea').focus();
                    } else {
                        otherTextField.hide();
                    }
                })
            },
            sendFeedback: function(response, comment) {
                var that = this;
                response = response ? this.sanitize(response) : 'skipped';
                comment = comment ? this.sanitize(comment) : '';
                $.ajax({
                    method: 'POST',
                    url: 'https://script.google.com/macros/s/AKfycbxqXkuWGYrjhWBQ1pfkJuaQ8o3d2uOrGdNiQdYGIBODL5OvOsI/exec',
                    data: $.param({
                        plugin: that.slug,
                        url: window.location.href.replace(/(.*)wp-admin.*/, '$1'),
                        license: window.deactivationDetails.license,
                        name: window.deactivationDetails.name,
                        wpEmail: window.deactivationDetails.wpEmail,
                        response,
                        comment
                    }),
                    dataType: 'jsonp',
                    complete: function () {
                        window.location.replace(
                            $('table.plugins tr[data-slug=' + that.slug + '] span.deactivate a').attr('href')
                        );
                    }
                });
            },
            deactivationFormSkipHandler: function() {
                var that = this;
                $('#lc-deactivation-feedback-modal-skip-btn').click(function () {
                    $(this).addClass('lc-btn--loading lc-btn--disabled').html(
                        $(this).html() + that.buttonLoaderHtml
                    );
                    $('#lc-deactivation-feedback-modal-submit-btn')
                        .attr('disabled', true)
                        .addClass('lc-btn--disabled');
                    that.sendFeedback();
                });
            },
            deactivationFormSubmitHandler: function () {
                var that = this;
                $('#lc-deactivation-feedback-modal-submit-btn').click(function (e) {
                    e.preventDefault();
                    that.deactivationFormHelpers.hideErrors();
                    var response = $('.lc-radio.lc-radio--selected .lc-radio__input').val();
                    if (!response) {
                        that.deactivationFormHelpers.showError('option');
                        return;
                    }
                    var comment = $('#lc-deactivation-feedback-other-field .lc-textarea').val();
                    if (response.toLowerCase() === 'other' && !comment) {
                        that.deactivationFormHelpers.showError('other');
                        return;
                    }
                    $(this).addClass('lc-btn--loading lc-btn--disabled').html(
                        $(this).html() + that.buttonLoaderHtml
                    );
                    $('#lc-deactivation-feedback-modal-skip-btn')
                        .attr('disabled', true)
                        .addClass('lc-btn--disabled');
                    that.sendFeedback(response, comment);
                })
            }
        };

        LiveChatEdd.init();
    });
})(jQuery);

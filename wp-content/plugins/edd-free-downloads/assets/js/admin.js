/*global jQuery, document*/
/*jslint newcap: true*/
jQuery(document).ready(function ($) {
    'use strict';

    var EDD_Free_Downloads_Settings, EDD_Free_Downloads_Meta_Box;

    /**
     * Settings
     */
    EDD_Free_Downloads_Settings = {
        init : function () {
            this.general();
        },

        general : function () {
            $('input[name="edd_settings[edd_free_downloads_get_name]"]').change(function () {
                var require_name_target = $('input[name="edd_settings[edd_free_downloads_require_name]"]').closest('tr');

                if ($(this).prop('checked')) {
                    require_name_target.fadeIn('fast').css('display', 'table-row');
                } else {
                    require_name_target.fadeOut('fast', function () {
                        $(this).css('display', 'none');
                    });
                }
            }).change();

            $('input[name="edd_settings[edd_free_downloads_show_notes]"]').change(function () {
                var global_notes_target = $('input[name="edd_settings[edd_free_downloads_disable_global_notes]"]').closest('tr');
                var notes_title_target  = $('input[name="edd_settings[edd_free_downloads_notes_title]"]').closest('tr');
                var notes_target        = $('textarea[name="edd_settings[edd_free_downloads_notes]"]').closest('tr');
                if ($(this).prop('checked')) {
                    global_notes_target.fadeIn('fast').css('display', 'table-row');

                    $('input[name="edd_settings[edd_free_downloads_disable_global_notes]"]').change(function () {
                        if ($(this).prop('checked')) {
                            notes_title_target.fadeOut('fast', function () {
                                $(this).css('display', 'none');
                            });
                            notes_target.fadeOut('fast', function () {
                                $(this).css('display', 'none');
                            });
                        } else {
                            notes_title_target.fadeIn('fast').css('display', 'table-row');
                            notes_target.fadeIn('fast').css('display', 'table-row');
                        }
                    }).change();
                } else {
                    global_notes_target.fadeOut('fast', function () {
                        $(this).css('display', 'none');
                    });
                    notes_title_target.fadeOut('fast', function () {
                        $(this).css('display', 'none');
                    });
                    notes_target.fadeOut('fast', function () {
                        $(this).css('display', 'none');
                    });
                }
            }).change();

            $('select[name="edd_settings[edd_free_downloads_on_complete]"]').change(function () {
                var selectedItem   = $('select[name="edd_settings[edd_free_downloads_on_complete]"] option:selected');
                var directDownload = $('input[name="edd_settings[edd_free_downloads_direct_download]"]').is(':checked');

                var redirect_target = $('input[name="edd_settings[edd_free_downloads_redirect]"]').closest('tr');
                var zip_target      = $('.edd-free-downloads-zip-status-available').closest('tr');
                if (selectedItem.val() === 'redirect' ) {
                    redirect_target.fadeIn('fast').css('display', 'table-row');

                    if (! directDownload) {
                        zip_target.fadeOut('fast', function () {
                            $(this).css('display', 'none');
                        });
                    }
                } else if(selectedItem.val() === 'default') {
                    redirect_target.fadeOut('fast', function () {
                        $(this).css('display', 'none');
                    });

                    if (! directDownload) {
                        zip_target.fadeOut('fast', function () {
                            $(this).css('display', 'none');
                        });
                    }
                } else {
                    redirect_target.fadeOut('fast', function () {
                        $(this).css('display', 'none');
                    });

                    if (! directDownload) {
                        zip_target.fadeIn('fast').css('display', 'table-row');
                    }
                }
            }).change();

            $('select[name="edd_settings[edd_free_downloads_mobile_on_complete]"]').change(function () {
                var selectedItem = $('select[name="edd_settings[edd_free_downloads_mobile_on_complete]"] option:selected');

                var apple_target = $('select[name="edd_settings[edd_free_downloads_apple_on_complete]"]').closest('tr');
                if (selectedItem.val() === 'auto-download' ) {
                    apple_target.fadeIn('fast').css('display', 'table-row');
                } else {
                    apple_target.fadeOut('fast', function () {
                        $(this).css('display', 'none');
                    });
                }

                var mobile_redirect_target = $('input[name="edd_settings[edd_free_downloads_mobile_redirect]"]').closest('tr');
                if (selectedItem.val() === 'redirect' ) {
                    mobile_redirect_target.fadeIn('fast').css('display', 'table-row');
                } else {
                    mobile_redirect_target.fadeOut('fast', function () {
                        $(this).css('display', 'none');
                    });
                }
            }).change();

            $('select[name="edd_settings[edd_free_downloads_apple_on_complete]"]').change(function () {
                var selectedItem = $('select[name="edd_settings[edd_free_downloads_apple_on_complete]"] option:selected');

                var target = $('input[name="edd_settings[edd_free_downloads_apple_redirect]"]').closest('tr');
                if (selectedItem.val() === 'redirect' ) {
                    target.fadeIn('fast').css('display', 'table-row');
                } else {
                    target.fadeOut('fast', function () {
                        $(this).css('display', 'none');
                    });
                }
            }).change();

            $('input[name="edd_settings[edd_free_downloads_direct_download]"]').change(function () {
                var onComplete = $('select[name="edd_settings[edd_free_downloads_on_complete]"] option:selected');

                var target_input = $('input[name="edd_settings[edd_free_downloads_direct_download_label]"]').closest('tr');
                var target_label = $('.edd-free-downloads-zip-status-available').closest('tr');
                if ($(this).prop('checked')) {
                    target_input.fadeIn('fast').css('display', 'table-row');

                    if (onComplete.val() !== 'auto-download' ) {
                        target_label.fadeIn('fast').css('display', 'table-row');
                    }
                } else {
                    target_input.fadeOut('fast', function () {
                        $(this).css('display', 'none');
                    });

                    if (onComplete.val() !== 'auto-download' ) {
                        target_label.fadeOut('fast', function () {
                            $(this).css('display', 'none');
                        });
                    }
                }
            }).change();

            $('input[name="edd_settings[edd_free_downloads_disable_cache]"]').change(function () {
                var target = $('a.edd-free-downloads-purge-cache').closest('tr');
                if ($(this).prop('checked')) {
                    target.fadeOut('fast', function () {
                        $(this).css('display', 'none');
                    });
                } else {
                    target.fadeIn('fast').css('display', 'table-row');
                }
            }).change();

            $('input[name="edd_settings[edd_free_downloads_require_verification]"]').change(function () {
                var message_target = $('input[name="edd_settings[edd_free_downloads_require_verification_message]"]').closest('tr');
                var subject_target = $('input[name="edd_settings[edd_free_downloads_verification_email_subject]"]').closest('tr');
                var email_target   = $('textarea[name="edd_settings[edd_free_downloads_verification_email]"]').closest('tr');
                if ($(this).prop('checked')) {
                    message_target.fadeIn('fast').css('display', 'table-row');
                    subject_target.fadeIn('fast').css('display', 'table-row');
                    email_target.fadeIn('fast').css('display', 'table-row');
                } else {
                    message_target.fadeOut('fast', function() {
                       $(this).css('display', 'none');
                    });
                    subject_target.fadeOut('fast', function() {
                        $(this).css('display', 'none');
                    });
                    email_target.fadeOut('fast', function () {
                        $(this).css('display', 'none');
                    });
                }
            }).change();
        }
    };
    EDD_Free_Downloads_Settings.init();

    /**
     * Download Meta Box
     */
    EDD_Free_Downloads_Meta_Box = {
        init : function () {
            this.general();
        },

        general : function () {
            $('select[name="_edd_product_type"]').change(function () {
                if ($(this).val() === 'bundle') {
                    $('.edd-free-downloads-bundle-wrap').fadeIn('fast').css('display', 'block');
                } else {
                    $('.edd-free-downloads-bundle-wrap').fadeOut('fast', function() {
                        $(this).css('display', 'none');
                    });
                }
            }).change();
        }
    };
    EDD_Free_Downloads_Meta_Box.init();
});

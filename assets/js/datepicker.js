// // Datepicker initialization and configuration
// (function($) {
//     'use strict';
    
//     // Only initialize if not already initialized by header.php
//     if (typeof window.datepickerInitialized === 'undefined') {
//         window.datepickerInitialized = true;
        
//         // Wait for document ready
//         $(document).ready(function() {
//             // If datepicker is already initialized by header.php, don't initialize again
//             if ($('.datepicker').first().data('datepicker')) {
//                 console.log('Datepicker already initialized by header.php');
//                 return;
//             }
            
//             console.log('Document ready - checking datepicker dependencies...');
            
//             // Check jQuery version
//             console.log('jQuery version:', $.fn.jquery);
            
//             // Check if Bootstrap is loaded
//             console.log('Bootstrap loaded:', typeof bootstrap !== 'undefined');
            
//             // Check if datepicker plugin is loaded
//             if (typeof $.fn.datepicker === 'undefined') {
//                 console.error('Bootstrap datepicker plugin is not loaded. Please include bootstrap-datepicker.min.js');
//                 return;
//             }
            
//             console.log('Datepicker plugin loaded:', true);
            
//             // Initialize datepicker with diagnostic options
//             $('.datepicker').each(function() {
//                 var $input = $(this);
//                 if ($input.data('datepicker')) {
//                     console.log('Datepicker already initialized for:', $input.attr('id') || $input.attr('name') || 'unnamed input');
//                     return;
//                 }
                
//                 console.log('Initializing datepicker for:', $input.attr('id') || $input.attr('name') || 'unnamed input');
                
//                 try {
//                     $input.datepicker({
//                         format: 'yyyy-mm-dd',
//                         autoclose: true,
//                         todayHighlight: true,
//                         orientation: 'bottom auto',
//                         beforeShow: function(input, inst) {
//                             console.log('Datepicker showing for input:', input.id || input.name);
//                         },
//                         beforeHide: function(input, inst) {
//                             console.log('Datepicker hiding for input:', input.id || input.name);
//                         }
//                     }).on('show', function(e) {
//                         console.log('Datepicker show event triggered');
//                     }).on('hide', function(e) {
//                         console.log('Datepicker hide event triggered');
//                     }).on('changeDate', function(e) {
//                         console.log('Datepicker date changed:', e.date);
//                     });
//                 } catch (error) {
//                     console.error('Error initializing datepicker:', error);
//                 }
//             });
//         });
//     } else {
//         console.log('Datepicker initialization handled by header.php');
//     }
// })(jQuery);

// // Datepicker initialization and configuration
// document.addEventListener('DOMContentLoaded', function() {
//     // Check if datepicker plugin is available
//     if (typeof $.fn.datepicker === 'undefined') {
//         console.error('Bootstrap datepicker plugin is not loaded. Please include bootstrap-datepicker.min.js');
//         return;
//     }

//     try {
//         // Initialize datepicker with custom options
//         $('.datepicker').datepicker({
//             format: 'yyyy-mm-dd',
//             autoclose: true,
//             todayHighlight: true,
//             startDate: new Date(),
//             endDate: '+1y',
//             clearBtn: true,
//             language: 'en',
//             // Bootstrap 5 specific options
//             container: 'body',
//             zIndex: 1050
//         });

//         // Custom datepicker event handlers
//         $('.datepicker').on('changeDate', function(e) {
//             // Handle date change events
//             console.log('Date selected:', e.date);
//             // You can add custom logic here, such as:
//             // - Updating related fields
//             // - Triggering form validation
//             // - Making AJAX calls
//         });

//         // Add Bootstrap 5 specific styling
//         $('.datepicker').addClass('bootstrap-5-datepicker');
//     } catch (error) {
//         console.error('Error initializing datepicker:', error);
//     }

//     // Add any custom datepicker methods here
//     $.fn.customDatepicker = function(options) {
//         // Extend the default options with custom ones
//         var settings = $.extend({
//             // Custom default options
//             customOption: true
//         }, options);

//         // Add custom datepicker functionality
//         return this.each(function() {
//             // Custom datepicker implementation
//             console.log('Custom datepicker initialized with options:', settings);
//         });
//     };
// }); 




// datepicker.js
(function($) {
    'use strict';

    // Prevent multiple initializations
    if (typeof window.datepickerInitialized === 'undefined') {
        window.datepickerInitialized = true;

        $(document).ready(function() {
            // Check if the Bootstrap datepicker plugin is loaded
            if (typeof $.fn.datepicker === 'undefined') {
                console.error('❌ Bootstrap datepicker plugin is not loaded. Please include bootstrap-datepicker.min.js');
                return;
            }

            console.log('✅ Bootstrap datepicker plugin detected.');

            // Initialize datepickers
            $('.datepicker').each(function() {
                const $input = $(this);

                // Avoid re-initializing
                if ($input.data('datepicker')) {
                    console.log('ℹ️ Datepicker already initialized for:', $input.attr('id') || $input.attr('name') || 'unnamed input');
                    return;
                }

                console.log('📅 Initializing datepicker for:', $input.attr('id') || $input.attr('name') || 'unnamed input');

                // Initialize with options
                try {
                    $input.datepicker({
                        format: 'yyyy-mm-dd',
                        autoclose: true,
                        todayHighlight: true,
                        clearBtn: true,
                        startDate: new Date(),
                        endDate: '+1y',
                        orientation: 'bottom auto',
                        container: 'body',
                        zIndexOffset: 1050,
                        language: 'en'
                    }).on('changeDate', function(e) {
                        console.log('📆 Date selected:', e.date);
                    });
                } catch (error) {
                    console.error('🚫 Error initializing datepicker:', error);
                }
            });
        });
    } else {
        console.log('ℹ️ Datepicker already initialized elsewhere.');
    }
})(jQuery);

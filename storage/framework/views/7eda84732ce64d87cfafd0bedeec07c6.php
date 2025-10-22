<!-- REQUIRED JS SCRIPTS -->

<!-- Custom JS script -->
<script src="<?php echo e(asset('/js/all.js')); ?>" type="text/javascript"></script>

<!-- Enhanced UX JavaScript -->
<script src="<?php echo e(asset('/js/enhanced-ux.js')); ?>" type="text/javascript"></script>

<!-- AdminLTE Initialization Script -->
<script type="text/javascript">
$(document).ready(function() {
    // Initialize AdminLTE
    if (typeof $.AdminLTE !== 'undefined') {
        // Activate layout features
        $.AdminLTE.layout.activate();
        
        // Activate push menu (sidebar toggle)
        $.AdminLTE.pushMenu.activate('[data-toggle="offcanvas"]');
        
        // Activate tree menu for sidebar with proper configuration
        if ($.AdminLTE.tree) {
            $.AdminLTE.tree('.sidebar-menu');
        }
        
        // Fix layout on window resize
        $(window).resize(function(){
            $.AdminLTE.layout.fix();
            $.AdminLTE.layout.fixSidebar();
        });
        
        // Handle responsive behavior for different screen sizes  
        function handleResponsiveLayout() {
            var windowWidth = $(window).width();
            
            // Mobile/Tablet breakpoint (768px)
            if (windowWidth <= 767) {
                $('body').removeClass('sidebar-mini sidebar-collapse')
                        .addClass('sidebar-collapse');
            } else if (windowWidth >= 768 && windowWidth <= 991) {
                // Tablet landscape
                $('body').addClass('sidebar-mini sidebar-collapse');
            } else {
                // Desktop - maintain current state but ensure proper classes
                if (!$('body').hasClass('sidebar-mini')) {
                    $('body').addClass('sidebar-mini');
                }
            }
        }
        
        // Run on page load
        handleResponsiveLayout();
        
        // Run on window resize
        $(window).resize(function(){
            handleResponsiveLayout();
        });
        
        // Handle sidebar toggle button
        $('[data-toggle="offcanvas"]').click(function(e) {
            e.preventDefault();
            
            var windowWidth = $(window).width();
            
            if (windowWidth > 767) {
                // Desktop behavior
                if ($('body').hasClass('sidebar-collapse')) {
                    $('body').removeClass('sidebar-collapse').trigger('expanded.pushMenu');
                } else {
                    $('body').addClass('sidebar-collapse').trigger('collapsed.pushMenu');
                }
            } else {
                // Mobile behavior
                if ($('body').hasClass('sidebar-open')) {
                    $('body').removeClass('sidebar-open').trigger('collapsed.pushMenu');
                } else {
                    $('body').addClass('sidebar-open').trigger('expanded.pushMenu');
                }
            }
        });
        
        // Close sidebar when clicking content on mobile
        $('.content-wrapper').click(function() {
            if ($(window).width() <= 767 && $('body').hasClass('sidebar-open')) {
                $('body').removeClass('sidebar-open');
            }
        });
        
        // Fix treeview menu auto-cascade issue
        // Remove existing AdminLTE tree handlers first
        $('.sidebar-menu').off('click', 'li a');
        
        // Add custom treeview handler
        $('.sidebar-menu').on('click', '.treeview > a', function(e) {
            var $this = $(this);
            var $parent = $this.parent();
            var $menu = $this.next('.treeview-menu');
            
            // If this link has a real URL (not just #), allow normal navigation
            if ($this.attr('href') && $this.attr('href') !== '#' && $this.attr('href').indexOf('javascript:') !== 0) {
                return true;
            }
            
            e.preventDefault();
            e.stopPropagation();
            
            // Check if sidebar is collapsed
            var isCollapsed = $('body').hasClass('sidebar-collapse');
            
            if (!isCollapsed) {
                // Normal sidebar behavior
                if ($menu.is(':visible')) {
                    // Close this menu
                    $menu.slideUp(300, function() {
                        $menu.removeClass('menu-open');
                    });
                    $parent.removeClass('active');
                } else {
                    // Close all other menus first
                    var $siblings = $parent.siblings('.treeview');
                    $siblings.find('.treeview-menu:visible').slideUp(300);
                    $siblings.find('.treeview-menu').removeClass('menu-open');
                    $siblings.removeClass('active');
                    
                    // Open this menu
                    $menu.slideDown(300, function() {
                        $menu.addClass('menu-open');
                    });
                    $parent.addClass('active');
                }
            } else {
                // Collapsed sidebar - show menu as overlay
                if ($parent.hasClass('active')) {
                    $parent.removeClass('active');
                    $menu.hide();
                } else {
                    $('.sidebar-menu .treeview').removeClass('active');
                    $('.sidebar-menu .treeview-menu').hide();
                    $parent.addClass('active');
                    $menu.show();
                }
            }
        });
        
        // Handle clicks outside to close overlay menus in collapsed mode
        $(document).on('click', function(e) {
            if ($('body').hasClass('sidebar-collapse')) {
                if (!$(e.target).closest('.treeview').length) {
                    $('.sidebar-menu .treeview').removeClass('active');
                    $('.sidebar-menu .treeview-menu').hide();
                }
            }
        });
    }
});
</script>

<!-- Optionally, you can add Slimscroll and FastClick plugins.
      Both of these plugins are recommended to enhance the
      user experience. Slimscroll is required when using the
      fixed layout. -->
<?php /**PATH D:\Project\ITQuty\quty2\resources\views/layouts/partials/scripts.blade.php ENDPATH**/ ?>
<header class="main-header">

    <a href="<?php echo e(url('/home')); ?>" class="logo">
        <span class="logo-mini">QUTY Assets</span>
        <span class="logo-lg"><b>QUTY</b>Assets </span>
    </a>

    <nav class="navbar navbar-static-top" role="navigation">
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button" aria-label="Toggle navigation">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </a>
        
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <?php if(auth()->guard()->check()): ?>
                <li class="nav-search-item">
                    <form class="navbar-form">
                        <div class="enhanced-search">
                            <div class="input-group">
                                <input type="text" id="global-search" class="form-control" placeholder="Search (Ctrl+K)...">
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-flat" aria-label="Search">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </form>
                </li>
                <?php endif; ?>

                <li class="notification-bell-container">
                    <a href="#" id="notification-bell" class="notification-bell">
                        <i class="fa fa-bell"></i>
                        <span id="notification-badge" class="notification-badge hidden">0</span>
                    </a>
                </li>
                
                <?php if(Auth::guest()): ?>
                    <li><a href="<?php echo e(url('/login')); ?>">Login</a></li>
                <?php else: ?>
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="hidden-xs"><?php echo e(Auth::user()->name); ?></span>
                            <i class="fa fa-angle-down" style="margin-left: 5px;"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="user-footer">
                                <form method="POST" action="<?php echo e(route('logout')); ?>" style="display: block; width: 100%;">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-flat btn-logout">
                                        Sign out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
</header><?php /**PATH D:\Project\ITQuty\quty2\resources\views/layouts/partials/mainheader.blade.php ENDPATH**/ ?>
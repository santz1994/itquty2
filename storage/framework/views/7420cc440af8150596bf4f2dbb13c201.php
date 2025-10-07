<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu">
            <li class="header">Navigation</li>
            
            <!-- 🏠 Home (Admin=2, SuperAdmin=3, Management=4) -->
            <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', ['admin', 'super-admin', 'management'])): ?>
              <li><a href="<?php echo e(url('home')); ?>"><i class='fa fa-home'></i> <span>Home</span></a></li>
            <?php endif; ?>
            
            <!-- 🏷️ Assets (Admin=2, SuperAdmin=3, Management=4 view-only) -->
            <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', ['admin', 'super-admin', 'management'])): ?>
              <li class="treeview">
                  <a href="#"><i class='fa fa-tags'></i> <span>Assets</span> <i class="fa fa-angle-left pull-right"></i></a>
                  <ul class="treeview-menu">
                      <li><a href="<?php echo e(url('/assets')); ?>">All Assets</a></li>
                      <li><a href="<?php echo e(url('/asset-maintenance')); ?>">Asset Maintenance</a></li>
                      <li><a href="<?php echo e(url('/spares')); ?>">Spares</a></li>
                  </ul>
              </li>
            <?php endif; ?>
            
            <!-- 🎫 Tickets (All roles: User=1, Admin=2, SuperAdmin=3, Management=4) -->
            <li class="treeview">
                <a href="#"><i class='fa fa-ticket'></i> <span>Tickets</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <li><a href="<?php echo e(url('/tickets')); ?>">All Tickets</a></li>
                    <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', ['admin', 'super-admin'])): ?>
                    <li><a href="<?php echo e(url('/tickets/unassigned')); ?>">Unassigned Tickets</a></li>
                    <?php endif; ?>
                </ul>
            </li>
            
            <!-- 📅 Daily Activities (Admin=2/SuperAdmin=3 full, Management=4 view-only) -->
            <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', ['admin', 'super-admin', 'management'])): ?>
            <li class="treeview">
                <a href="#"><i class='fa fa-calendar-check-o'></i> <span>Daily Activities</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <li><a href="<?php echo e(url('/daily-activities')); ?>">Activity List</a></li>
                    <li><a href="<?php echo e(url('/daily-activities/calendar')); ?>">Calendar View</a></li>
                    <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', ['admin', 'super-admin'])): ?>
                    <li><a href="<?php echo e(url('/daily-activities/create')); ?>">Add Activity</a></li>
                    <?php endif; ?>
                </ul>
            </li>
            <?php endif; ?>
            
            <!-- 💻 Models (SuperAdmin=3 only) -->
            <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', ['super-admin'])): ?>
              <li class="treeview">
                  <a href="#"><i class='fa fa-desktop'></i> <span>Models</span> <i class="fa fa-angle-left pull-right"></i></a>
                  <ul class="treeview-menu">
                      <li><a href="<?php echo e(url('/models')); ?>">Models</a></li>
                      <li><a href="<?php echo e(url('/pcspecs')); ?>">PC Specifications</a></li>
                      <li><a href="<?php echo e(url('/manufacturers')); ?>">Manufacturers</a></li>
                      <li><a href="<?php echo e(url('/asset-types')); ?>">Asset Types</a></li>
                  </ul>
              </li>
            <?php endif; ?>
            
            <!-- 🛒 Suppliers (SuperAdmin=3 only) -->
            <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', ['super-admin'])): ?>
              <li><a href="<?php echo e(url('/suppliers')); ?>"><i class='fa fa-shopping-cart'></i> <span>Suppliers</span></a></li>
            <?php endif; ?>
            
            <!-- 🏢 Locations (SuperAdmin=3 only) -->
            <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', ['super-admin'])): ?>
              <li><a href="<?php echo e(url('/locations')); ?>"><i class='fa fa-building'></i> <span>Locations</span></a></li>
            <?php endif; ?>
            
            <!-- 👥 Divisions (SuperAdmin=3 only) -->
            <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', ['super-admin'])): ?>
              <li><a href="<?php echo e(url('/divisions')); ?>"><i class='fa fa-group'></i> <span>Divisions</span></a></li>
            <?php endif; ?>
            
            <!-- 💰 Invoices and Budgets (SuperAdmin=3 only) -->
            <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', ['super-admin'])): ?>
              <li class="treeview">
                  <a href="#"><i class='fa fa-usd'></i> <span>Invoices and Budgets</span> <i class="fa fa-angle-left pull-right"></i></a>
                  <ul class="treeview-menu">
                      <li><a href="<?php echo e(url('/invoices')); ?>">Invoices</a></li>
                      <li><a href="<?php echo e(url('/budgets')); ?>">Budgets</a></li>
                  </ul>
              </li>
            <?php endif; ?>
            
            <!-- ⚙️ Admin (SuperAdmin=3 only) -->
            <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', ['super-admin'])): ?>
              <li><a href="<?php echo e(url('/admin')); ?>"><i class='fa fa-gear'></i> <span>Admin</span></a></li>
            <?php endif; ?>
        </ul><!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>
<?php /**PATH D:\Project\ITQuty\Quty1\resources\views/layouts/partials/sidebar.blade.php ENDPATH**/ ?>
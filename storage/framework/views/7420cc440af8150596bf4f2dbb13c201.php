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
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-assets')): ?>
              <li class="treeview">
                  <a href="#"><i class='fa fa-tags'></i> <span>Assets</span> <i class="fa fa-angle-left pull-right"></i></a>
                  <ul class="treeview-menu">
                      <li><a href="<?php echo e(url('/assets')); ?>">All Assets</a></li>
                      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create-assets')): ?>
                      <li><a href="<?php echo e(url('/asset-maintenance')); ?>">Asset Maintenance</a></li>
                      <li><a href="<?php echo e(url('/spares')); ?>">Spares</a></li>
                      <?php endif; ?>
                      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('export-assets')): ?>
                      <li><a href="<?php echo e(route('assets.export')); ?>">Export Assets</a></li>
                      <?php endif; ?>
                      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('import-assets')): ?>
                      <li><a href="<?php echo e(route('assets.import-form')); ?>">Import Assets</a></li>
                      <?php endif; ?>
                  </ul>
              </li>
            <?php endif; ?>
            
            <!-- 🎫 Tickets (All roles: User=1, Admin=2, SuperAdmin=3, Management=4) -->
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-tickets')): ?>
            <li class="treeview">
                <a href="#"><i class='fa fa-ticket'></i> <span>Tickets</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <li><a href="<?php echo e(url('/tickets')); ?>">All Tickets</a></li>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('assign-tickets')): ?>
                    <li><a href="<?php echo e(url('/tickets/unassigned')); ?>">Unassigned Tickets</a></li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create-tickets')): ?>
                    <li><a href="<?php echo e(url('/tickets/create')); ?>">Create Ticket</a></li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('export-tickets')): ?>
                    <li><a href="<?php echo e(route('tickets.export')); ?>">Export Tickets</a></li>
                    <?php endif; ?>
                </ul>
            </li>
            <?php endif; ?>
            
      <!-- 📅 Daily Activity (Admin=2/SuperAdmin=3 full, Management=4 view-only) -->
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-daily-activities')): ?>
      <li class="treeview">
        <a href="#"><i class='fa fa-calendar'></i> <span>Daily Activity</span> <i class="fa fa-angle-left pull-right"></i></a>
        <ul class="treeview-menu">
          <li><a href="<?php echo e(url('/daily-activities')); ?>">Activity List</a></li>
          <li><a href="<?php echo e(url('/daily-activities/calendar')); ?>">Calendar View</a></li>
          <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create-daily-activities')): ?>
          <li><a href="<?php echo e(url('/daily-activities/create')); ?>">Add Activity</a></li>
          <?php endif; ?>
        </ul>
      </li>
      <?php endif; ?>
      
      <!-- 📊 KPI Dashboard (management, admin, super-admin only) -->
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-kpi-dashboard')): ?>
      <li>
        <a href="<?php echo e(route('kpi.dashboard')); ?>">
          <i class='fa fa-dashboard'></i> 
          <span>KPI Dashboard</span>
        </a>
      </li>
      <?php endif; ?>
      
      <!-- 📋 Reports (management, admin, super-admin) -->
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-reports')): ?>
      <li class="treeview">
        <a href="#"><i class='fa fa-bar-chart'></i> <span>Reports</span> <i class="fa fa-angle-left pull-right"></i></a>
        <ul class="treeview-menu">
          <li><a href="<?php echo e(route('kpi.dashboard')); ?>">KPI Dashboard</a></li>
          <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', 'management|admin|super-admin')): ?>
          <li><a href="<?php echo e(url('/management/dashboard')); ?>">Management Dashboard</a></li>
          <li><a href="<?php echo e(url('/management/admin-performance')); ?>">Admin Performance</a></li>
          <?php endif; ?>
        </ul>
      </li>
      <?php endif; ?>
            
            <!-- 💻 Models (SuperAdmin=3 only) -->
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-models')): ?>
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
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-suppliers')): ?>
              <li><a href="<?php echo e(url('/suppliers')); ?>"><i class='fa fa-shopping-cart'></i> <span>Suppliers</span></a></li>
            <?php endif; ?>
            
            <!-- 🏢 Locations (SuperAdmin=3 only) -->
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-locations')): ?>
              <li><a href="<?php echo e(url('/locations')); ?>"><i class='fa fa-building'></i> <span>Locations</span></a></li>
            <?php endif; ?>
            
            <!-- 👥 Divisions (SuperAdmin=3 only) -->
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-divisions')): ?>
              <li><a href="<?php echo e(url('/divisions')); ?>"><i class='fa fa-group'></i> <span>Divisions</span></a></li>
            <?php endif; ?>
            
            <!-- 💰 Invoices and Budgets (SuperAdmin=3 only) -->
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-invoices')): ?>
              <li class="treeview">
                  <a href="#"><i class='fa fa-usd'></i> <span>Invoices and Budgets</span> <i class="fa fa-angle-left pull-right"></i></a>
                  <ul class="treeview-menu">
                      <li><a href="<?php echo e(url('/invoices')); ?>">Invoices</a></li>
                      <li><a href="<?php echo e(url('/budgets')); ?>">Budgets</a></li>
                  </ul>
              </li>
            <?php endif; ?>
            
            <!-- 📥📤 Import/Export (admin & super-admin) -->
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('export-data')): ?>
            <li class="treeview">
              <a href="#"><i class='fa fa-exchange'></i> <span>Import/Export</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('export-data')): ?>
                <li><a href="<?php echo e(url('/exports')); ?>">Export Data</a></li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('import-data')): ?>
                <li><a href="<?php echo e(url('/imports')); ?>">Import Data</a></li>
                <?php endif; ?>
                <li><a href="<?php echo e(url('/exports/templates')); ?>">Download Templates</a></li>
              </ul>
            </li>
            <?php endif; ?>
            
            <!-- 👥 User Management (admin & super-admin) -->
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-users')): ?>
            <li class="treeview">
                <a href="#"><i class='fa fa-users'></i> <span>User Management</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <li><a href="<?php echo e(url('/users')); ?>">All Users</a></li>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create-users')): ?>
                    <li><a href="<?php echo e(url('/users/create')); ?>">Add User</a></li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-users')): ?>
                    <li><a href="<?php echo e(url('/users/roles')); ?>">User Roles</a></li>
                    <?php endif; ?>
                </ul>
            </li>
            <?php endif; ?>
            
            <!-- ⚙️ System Settings (super-admin only) -->
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-system-settings')): ?>
            <li class="treeview">
                <a href="#"><i class='fa fa-cogs'></i> <span>System Settings</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <li><a href="<?php echo e(url('/system/settings')); ?>">General Settings</a></li>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit-system-settings')): ?>
                    <li><a href="<?php echo e(url('/system/permissions')); ?>">Permissions</a></li>
                    <li><a href="<?php echo e(url('/system/roles')); ?>">Roles Management</a></li>
                    <li><a href="<?php echo e(url('/system/maintenance')); ?>">System Maintenance</a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo e(url('/system/logs')); ?>">System Logs</a></li>
                </ul>
            </li>
            <?php endif; ?>
            
            <!-- 🔧 Admin Tools (super-admin only) -->
            <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', 'super-admin')): ?>
            <li class="treeview">
                <a href="#"><i class='fa fa-wrench'></i> <span>Admin Tools</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <li><a href="<?php echo e(url('/admin/dashboard')); ?>">Admin Dashboard</a></li>
                    <li><a href="<?php echo e(url('/admin/database')); ?>">Database Management</a></li>
                    <li><a href="<?php echo e(url('/admin/cache')); ?>">Cache Management</a></li>
                    <li><a href="<?php echo e(url('/admin/backup')); ?>">Backup & Restore</a></li>
                </ul>
            </li>
            <?php endif; ?>
        </ul><!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>
<?php /**PATH D:\Project\ITQuty\Quty1\resources\views/layouts/partials/sidebar.blade.php ENDPATH**/ ?>
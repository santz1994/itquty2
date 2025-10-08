<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu">
            <li class="header">Navigation</li>
            
            <!-- 🏠 Home (Admin=2, SuperAdmin=3, Management=4) -->
            @role(['admin', 'super-admin', 'management'])
              <li><a href="{{ url('home') }}"><i class='fa fa-home'></i> <span>Home</span></a></li>
            @endrole
            
            <!-- 🏷️ Assets (Admin=2, SuperAdmin=3, Management=4 view-only) -->
            @can('view-assets')
              <li class="treeview">
                  <a href="#"><i class='fa fa-tags'></i> <span>Assets</span> <i class="fa fa-angle-left pull-right"></i></a>
                  <ul class="treeview-menu">
                      <li><a href="{{ url('/assets')}}">All Assets</a></li>
                      @can('create-assets')
                      <li><a href="{{ url('/asset-maintenance')}}">Asset Maintenance</a></li>
                      <li><a href="{{ url('/spares')}}">Spares</a></li>
                      @endcan
                      @can('export-assets')
                      <li><a href="{{ route('assets.export') }}">Export Assets</a></li>
                      @endcan
                      @can('import-assets')
                      <li><a href="{{ route('assets.import-form') }}">Import Assets</a></li>
                      @endcan
                  </ul>
              </li>
            @endcan
            
            <!-- 🎫 Tickets (All roles: User=1, Admin=2, SuperAdmin=3, Management=4) -->
            @can('view-tickets')
            <li class="treeview">
                <a href="#"><i class='fa fa-ticket'></i> <span>Tickets</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <li><a href="{{ url('/tickets')}}">All Tickets</a></li>
                    @can('assign-tickets')
                    <li><a href="{{ url('/tickets/unassigned')}}">Unassigned Tickets</a></li>
                    @endcan
                    @can('create-tickets')
                    <li><a href="{{ url('/tickets/create')}}">Create Ticket</a></li>
                    @endcan
                    @can('export-tickets')
                    <li><a href="{{ route('tickets.export') }}">Export Tickets</a></li>
                    @endcan
                </ul>
            </li>
            @endcan
            
      <!-- 📅 Daily Activity (Admin=2/SuperAdmin=3 full, Management=4 view-only) -->
      @can('view-daily-activities')
      <li class="treeview">
        <a href="#"><i class='fa fa-calendar'></i> <span>Daily Activity</span> <i class="fa fa-angle-left pull-right"></i></a>
        <ul class="treeview-menu">
          <li><a href="{{ url('/daily-activities')}}">Activity List</a></li>
          <li><a href="{{ url('/daily-activities/calendar')}}">Calendar View</a></li>
          @can('create-daily-activities')
          <li><a href="{{ url('/daily-activities/create')}}">Add Activity</a></li>
          @endcan
        </ul>
      </li>
      @endcan
      
      <!-- 📊 KPI Dashboard (management, admin, super-admin only) -->
      @can('view-kpi-dashboard')
      <li>
        <a href="{{ route('kpi.dashboard') }}">
          <i class='fa fa-dashboard'></i> 
          <span>KPI Dashboard</span>
        </a>
      </li>
      @endcan
      
      <!-- 📋 Reports (management, admin, super-admin) -->
      @can('view-reports')
      <li class="treeview">
        <a href="#"><i class='fa fa-bar-chart'></i> <span>Reports</span> <i class="fa fa-angle-left pull-right"></i></a>
        <ul class="treeview-menu">
          <li><a href="{{ route('kpi.dashboard') }}">KPI Dashboard</a></li>
          @hasrole('management|admin|super-admin')
          <li><a href="{{ url('/management/dashboard')}}">Management Dashboard</a></li>
          <li><a href="{{ url('/management/admin-performance')}}">Admin Performance</a></li>
          @endhasrole
        </ul>
      </li>
      @endcan
            
            <!-- 💻 Models (SuperAdmin=3 only) -->
            @can('view-models')
              <li class="treeview">
                  <a href="#"><i class='fa fa-desktop'></i> <span>Models</span> <i class="fa fa-angle-left pull-right"></i></a>
                  <ul class="treeview-menu">
                      <li><a href="{{ url('/models')}}">Models</a></li>
                      <li><a href="{{ url('/pcspecs')}}">PC Specifications</a></li>
                      <li><a href="{{ url('/manufacturers')}}">Manufacturers</a></li>
                      <li><a href="{{ url('/asset-types')}}">Asset Types</a></li>
                  </ul>
              </li>
            @endcan
            
            <!-- 🛒 Suppliers (SuperAdmin=3 only) -->
            @can('view-suppliers')
              <li><a href="{{ url('/suppliers')}}"><i class='fa fa-shopping-cart'></i> <span>Suppliers</span></a></li>
            @endcan
            
            <!-- 🏢 Locations (SuperAdmin=3 only) -->
            @can('view-locations')
              <li><a href="{{ url('/locations')}}"><i class='fa fa-building'></i> <span>Locations</span></a></li>
            @endcan
            
            <!-- 👥 Divisions (SuperAdmin=3 only) -->
            @can('view-divisions')
              <li><a href="{{ url('/divisions')}}"><i class='fa fa-group'></i> <span>Divisions</span></a></li>
            @endcan
            
            <!-- 💰 Invoices and Budgets (SuperAdmin=3 only) -->
            @can('view-invoices')
              <li class="treeview">
                  <a href="#"><i class='fa fa-usd'></i> <span>Invoices and Budgets</span> <i class="fa fa-angle-left pull-right"></i></a>
                  <ul class="treeview-menu">
                      <li><a href="{{ url('/invoices')}}">Invoices</a></li>
                      <li><a href="{{ url('/budgets')}}">Budgets</a></li>
                  </ul>
              </li>
            @endcan
            
            <!-- 📥📤 Import/Export (admin & super-admin) -->
            @can('export-data')
            <li class="treeview">
              <a href="#"><i class='fa fa-exchange'></i> <span>Import/Export</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                @can('export-data')
                <li><a href="{{ url('/exports')}}">Export Data</a></li>
                @endcan
                @can('import-data')
                <li><a href="{{ url('/imports')}}">Import Data</a></li>
                @endcan
                <li><a href="{{ url('/exports/templates')}}">Download Templates</a></li>
              </ul>
            </li>
            @endcan
            
            <!-- 👥 User Management (admin & super-admin) -->
            @can('view-users')
            <li class="treeview">
                <a href="#"><i class='fa fa-users'></i> <span>User Management</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <li><a href="{{ url('/users')}}">All Users</a></li>
                    @can('create-users')
                    <li><a href="{{ url('/users/create')}}">Add User</a></li>
                    @endcan
                    @can('view-users')
                    <li><a href="{{ url('/users/roles')}}">User Roles</a></li>
                    @endcan
                </ul>
            </li>
            @endcan
            
            <!-- ⚙️ System Settings (super-admin only) -->
            @can('view-system-settings')
            <li class="treeview">
                <a href="#"><i class='fa fa-cogs'></i> <span>System Settings</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <li><a href="{{ url('/system/settings')}}">General Settings</a></li>
                    @can('edit-system-settings')
                    <li><a href="{{ url('/system/permissions')}}">Permissions</a></li>
                    <li><a href="{{ url('/system/roles')}}">Roles Management</a></li>
                    <li><a href="{{ url('/system/maintenance')}}">System Maintenance</a></li>
                    @endcan
                    <li><a href="{{ url('/system/logs')}}">System Logs</a></li>
                </ul>
            </li>
            @endcan
            
            <!-- 🔧 Admin Tools (super-admin only) -->
            @role('super-admin')
            <li class="treeview">
                <a href="#"><i class='fa fa-wrench'></i> <span>Admin Tools</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <li><a href="{{ url('/admin/dashboard')}}">Admin Dashboard</a></li>
                    <li><a href="{{ url('/admin/database')}}">Database Management</a></li>
                    <li><a href="{{ url('/admin/cache')}}">Cache Management</a></li>
                    <li><a href="{{ url('/admin/backup')}}">Backup & Restore</a></li>
                </ul>
            </li>
            @endrole
        </ul><!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>

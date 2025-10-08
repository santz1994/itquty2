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
            @role(['admin', 'super-admin', 'management'])
              <li class="treeview">
                  <a href="#"><i class='fa fa-tags'></i> <span>Assets</span> <i class="fa fa-angle-left pull-right"></i></a>
                  <ul class="treeview-menu">
                      <li><a href="{{ url('/assets')}}">All Assets</a></li>
                      <li><a href="{{ url('/asset-maintenance')}}">Asset Maintenance</a></li>
                      <li><a href="{{ url('/spares')}}">Spares</a></li>
                  </ul>
              </li>
            @endrole
            
            <!-- 🎫 Tickets (All roles: User=1, Admin=2, SuperAdmin=3, Management=4) -->
            <li class="treeview">
                <a href="#"><i class='fa fa-ticket'></i> <span>Tickets</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <li><a href="{{ url('/tickets')}}">All Tickets</a></li>
                    @role(['admin', 'super-admin'])
                    <li><a href="{{ url('/tickets/unassigned')}}">Unassigned Tickets</a></li>
                    @endrole
                </ul>
            </li>
            
      <!-- 📅 Daily Activity (Admin=2/SuperAdmin=3 full, Management=4 view-only) -->
      @role(['admin', 'super-admin', 'management'])
      <li class="treeview">
        <a href="#"><i class='fa fa-calendar'></i> <span>Daily Activity</span> <i class="fa fa-angle-left pull-right"></i></a>
        <ul class="treeview-menu">
          <li><a href="{{ url('/daily-activities')}}">Activity List</a></li>
          <li><a href="{{ url('/daily-activities/calendar')}}">Calendar View</a></li>
          @role(['admin', 'super-admin'])
          <li><a href="{{ url('/daily-activities/create')}}">Add Activity</a></li>
          @endrole
        </ul>
      </li>
      @endrole
            
            <!-- 💻 Models (SuperAdmin=3 only) -->
            @role(['super-admin'])
              <li class="treeview">
                  <a href="#"><i class='fa fa-desktop'></i> <span>Models</span> <i class="fa fa-angle-left pull-right"></i></a>
                  <ul class="treeview-menu">
                      <li><a href="{{ url('/models')}}">Models</a></li>
                      <li><a href="{{ url('/pcspecs')}}">PC Specifications</a></li>
                      <li><a href="{{ url('/manufacturers')}}">Manufacturers</a></li>
                      <li><a href="{{ url('/asset-types')}}">Asset Types</a></li>
                  </ul>
              </li>
            @endrole
            
            <!-- 🛒 Suppliers (SuperAdmin=3 only) -->
            @role(['super-admin'])
              <li><a href="{{ url('/suppliers')}}"><i class='fa fa-shopping-cart'></i> <span>Suppliers</span></a></li>
            @endrole
            
            <!-- 🏢 Locations (SuperAdmin=3 only) -->
            @role(['super-admin'])
              <li><a href="{{ url('/locations')}}"><i class='fa fa-building'></i> <span>Locations</span></a></li>
            @endrole
            
            <!-- 👥 Divisions (SuperAdmin=3 only) -->
            @role(['super-admin'])
              <li><a href="{{ url('/divisions')}}"><i class='fa fa-group'></i> <span>Divisions</span></a></li>
            @endrole
            
            <!-- 💰 Invoices and Budgets (SuperAdmin=3 only) -->
            @role(['super-admin'])
              <li class="treeview">
                  <a href="#"><i class='fa fa-usd'></i> <span>Invoices and Budgets</span> <i class="fa fa-angle-left pull-right"></i></a>
                  <ul class="treeview-menu">
                      <li><a href="{{ url('/invoices')}}">Invoices</a></li>
                      <li><a href="{{ url('/budgets')}}">Budgets</a></li>
                  </ul>
              </li>
            @endrole
            
            <!-- ⚙️ Admin (SuperAdmin=3 only) -->
            @role(['super-admin'])
              <li><a href="{{ url('/admin')}}"><i class='fa fa-gear'></i> <span>Admin</span></a></li>
            @endrole
        </ul><!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>

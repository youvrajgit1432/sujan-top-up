 
  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
      <img src="../assets/img/logo11.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">Sujan Topup</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
    
<div class="user-panel mt-3 pb-3 mb-3 d-flex">
 
    <div class="info">
        <!-- Dynamically pass user_id as a URL parameter -->
        <a href="profile.php" style="padding-left:8px;font-size: 20px;color:white;" class="d-block"> 
        <i class="nav-icon fas fa-user-circle" style="padding-left:5px;font-size: 40px;color:white;"><span style="font-size: 20px;">Account</span></i>    </a>
 </div>
</div>
      <!-- SidebarSearch Form -->
 
<!-- Sidebar Menu -->
<nav class="mt-2">
  <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <!-- Dashboard -->
    <li class="nav-item">
      <a href="index.php" class="nav-link">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>Dashboard</p>
      </a>
    </li>
    <li class="nav-item">
  <a href="imgpay.php" class="nav-link">
    <i class="nav-icon fas fa-image"></i>
    <p>Pay Images</p>
  </a>
</li>
    <li class="nav-item">
  <a href="imagedis.php" class="nav-link">
    <i class="nav-icon fas fa-image"></i>
    <p>Topup Images</p>
  </a>
</li>
<li class="nav-item">
  <a href="gal.php" class="nav-link">
    <i class="nav-icon fas fa-image"></i>
    <p>Gallery Images</p>
  </a>
</li>

    <!-- Admin Details -->
    <li class="nav-item">
      <a href="admin_details.php" class="nav-link">
        <i class="fas fa-user-shield nav-icon"></i>
        <p>Admin Details</p>
      </a>
    </li>

    <!-- Users -->
    <li class="nav-item">
      <a href="users.php" class="nav-link">
        <i class="fas fa-users nav-icon"></i>
        <p>Users</p>
      </a>
    </li>

    <!-- Reviews -->
    <li class="nav-item">
      <a href="reviews.php" class="nav-link">
        <i class="fas fa-comments nav-icon"></i>
        <p>Reviews</p>
      </a>
    </li>



<!-- Website Orders Dropdown -->
<li class="nav-item">
  <a href="#" class="nav-link">
    <i class="nav-icon fas fa-globe"></i>
    <p>
      Website Orders
      <i class="fas fa-angle-left right"></i>
      <span class="badge badge-info right">5</span>
    </p>
  </a>
  <ul class="nav nav-treeview" style="padding-left: 20px;">
    
  <li class="nav-item">
      <a href="webtoday.php" class="nav-link">
      <i class="fas fa-calendar-day nav-icon"></i> 
        <p>Todays Orders</p>
      </a>
    </li><li class="nav-item">
      <a href="website_orders_all.php" class="nav-link">
        <i class="fas fa-list nav-icon"></i>
        <p>All Orders</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="website_orders_pending.php" class="nav-link">
        <i class="fas fa-clock nav-icon"></i>
        <p>Pending Orders</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="website_orders_confirmed.php" class="nav-link">
        <i class="fas fa-check-circle nav-icon"></i>
        <p>Confirmed Orders</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="website_orders_completed.php" class="nav-link">
        <i class="fas fa-check nav-icon"></i>
        <p>Completed Orders</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="website_orders_rejected.php" class="nav-link">
        <i class="fas fa-times-circle nav-icon"></i>
        <p>Rejected Orders</p>
      </a>
    </li>
    
  </ul>
</li>

<!-- Whatsapp Orders Dropdown -->
<li class="nav-item">
  <a href="#" class="nav-link">
    <i class="nav-icon fas fa-comment-dots"></i>
    <p>
      Whatsapp Orders
      <i class="fas fa-angle-left right"></i>
      <span class="badge badge-info right">5</span>
    </p>
  </a>
  <ul class="nav nav-treeview" style="padding-left: 20px;">
  <li class="nav-item">
    <a href="whattoday.php" class="nav-link">
        <i class="fas fa-calendar-day nav-icon"></i> <!-- 'Today' Icon -->
        <p>Today's Orders</p>
    </a>
</li>

    <li class="nav-item">
      <a href="whatsapp_orders_all.php" class="nav-link">
        <i class="fas fa-list nav-icon"></i>
        <p>All Orders</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="whatsapp_orders_pending.php" class="nav-link">
        <i class="fas fa-clock nav-icon"></i>
        <p>Pending Orders</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="whatsapp_orders_completed.php" class="nav-link">
        <i class="fas fa-check-circle nav-icon"></i>
        <p>Completed Orders</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="whatsapp_orders_confirmed.php" class="nav-link">
        <i class="fas fa-check nav-icon"></i>
        <p>Confirmed Orders</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="whatsapp_orders_rejected.php" class="nav-link">
        <i class="fas fa-times-circle nav-icon"></i>
        <p>Rejected Orders</p>
      </a>
    </li>
  </ul>
</li>


    
    <!-- Games Dropdown -->
    <li class="nav-item">
      <a href="#" class="nav-link">
        <i class="nav-icon fas fa-gamepad"></i>
        <p>
          Games
          <i class="fas fa-angle-left right"></i>
          <span class="badge badge-info right">10</span>
        </p>
      </a>
      <ul class="nav nav-treeview active" style="padding-left: 20px;">
      <li class="nav-item">
          <a href="all.php" class="nav-link">
          <i class="fas fa-th-list nav-icon"></i>

            <p>All</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="pubg.php" class="nav-link">
            <i class="fas fa-mobile-alt nav-icon"></i>
            <p>PUBG</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="freefire.php" class="nav-link">
            <i class="fas fa-fire nav-icon"></i>
            <p>Free Fire</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="clash_of_clans.php" class="nav-link">
            <i class="fas fa-crown nav-icon"></i>
            <p>Clash of Clans</p>
          </a>
        </li>

        <li class="nav-item">
  <a href="unpin.php" class="nav-link">
    <i class="fas fa-ticket-alt nav-icon"></i>
    <p>Unpin Voucher</p>
  </a>
</li>
    
        <li class="nav-item">
  <a href="netflix.php" class="nav-link">
    <i class="fab fa-netflix nav-icon"></i>
    <p>Netflix</p>
  </a>
</li>
<li class="nav-item">
  <a href="spotify.php" class="nav-link">
    <i class="fab fa-spotify nav-icon"></i>
    <p>Spotify</p>
  </a>
</li>
<li class="nav-item">
  <a href="prime.php" class="nav-link">
    <i class="fas fa-video nav-icon"></i>
    <p>Prime Video</p>
  </a>
</li>


        <li class="nav-item">
          <a href="mobile_legends.php" class="nav-link">
            <i class="fas fa-dragon nav-icon"></i>
            <p>Mobile Legends</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="efootball_android.php" class="nav-link">
            <i class="fas fa-futbol nav-icon"></i>
            <p>eFootball Android</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="efootball_ios.php" class="nav-link">
          <i class="fas fa-futbol nav-icon"></i>
            <p>eFootball iOS</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="tiktok.php" class="nav-link">
            <i class="fab fa-tiktok nav-icon"></i>
            <p>TikTok</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="mlbb.php" class="nav-link">
            <i class="fas fa-shield-alt nav-icon"></i>
            <p>MLBB</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="pubg_global.php" class="nav-link">
            <i class="fas fa-globe nav-icon"></i>
            <p>PUBG Global</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="freefire_indonesia.php" class="nav-link">
            <i class="fas fa-flag nav-icon"></i>
            <p>Free Fire Indonesia</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="mobilelegend_indonesia.php" class="nav-link">
            <i class="fas fa-dragon nav-icon"></i>
            <p>Mobile Legends Indonesia</p>
          </a>
        </li>
      </ul>
    </li>
<br><br><br>
    <!-- Logout -->
    <li class="nav-item">
      <a href="logout.php" class="nav-link">
        <i class="fas fa-sign-out-alt nav-icon"></i>
        <p>Logout</p>
      </a>
    </li>
  </ul>
</nav>

      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

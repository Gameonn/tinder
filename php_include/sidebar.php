<!--sidebar start-->
<aside>
    <div id="sidebar" class="nav-collapse">
        <!-- sidebar menu start-->
        <div class="leftside-navigation">
            <ul class="sidebar-menu" id="nav-accordion">
                <li <?php if(stripos($_SERVER['REQUEST_URI'],"dashboard.php")) echo 'class="active"'; ?>>
                    <a href="dashboard.php">
                        <i class="fa fa-dashboard"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                  <li class="sub-menu">
                    <a <?php if(stripos($_SERVER['REQUEST_URI'],"users.php") ) echo 'class="active"'; ?> href="users.php">
                        <i class="fa fa-list-alt"></i>
                        <span>Users</span>
                    </a>
                   
                </li>

                <li class="sub-menu">
                    <a <?php if(stripos($_SERVER['REQUEST_URI'],"matches.php") ) echo 'class="active"'; ?> href="matches.php">
                        <i class="fa fa-list-alt"></i>
                        <span>Matches</span>
                    </a>
                   
                </li>
				
            </ul>            
          </div>
          </div>
          </aside>
        
        <!-- sidebar menu end-->
   
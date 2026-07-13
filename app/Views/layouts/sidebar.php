<body>

<div class="preloader">
    <img src="<?= base_url('assets/images/logo.png') ?>"
         alt="loader"
         class="lds-ripple img-fluid" />
</div>

<div id="main-wrapper">
<aside class="left-sidebar modern-sidebar">

    <!-- Logo -->
    <div class="sidebar-brand">

        <div class="brand-icon">
            G
        </div>

        <div>
            <h5 class="mb-0 fw-bold">GatePass</h5>
            <small class="text-muted">Management System</small>
        </div>

    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">

        <div class="sidebar-title">
            MAIN MENU
        </div>

        <ul class="sidebar-menu">

            <li>
                <a href="<?= base_url('dashboard') ?>" class="sidebar-link <?= (uri_string() == 'dashboard') ? 'active' : '' ?>">
                    <iconify-icon icon="solar:home-2-bold-duotone"></iconify-icon>
                    <span>Dashboard</span>
                </a>
            </li>

           <li>
                <a href="<?= base_url('gatepass') ?>" class="sidebar-link <?= (uri_string() == 'gatepass') ? 'active' : '' ?>">
                    <iconify-icon icon="solar:clipboard-list-bold-duotone"></iconify-icon>
                    <span> Request</span>
                </a>
            </li>

            <li>
                <a href="<?= base_url('accessories') ?>" class="sidebar-link <?= (uri_string() == 'accessories') ? 'active' : '' ?>">
                    <iconify-icon icon="solar:box-bold-duotone"></iconify-icon>
                    <span>Accessories</span>
                </a>
            </li>

            <li>
                <a href="#" class="sidebar-link">
                    <iconify-icon icon="solar:checklist-bold-duotone"></iconify-icon>
                    <span>Approvals</span>
                </a>
            </li>

            <li>
                <a href="#" class="sidebar-link">
                    <iconify-icon icon="solar:chart-bold-duotone"></iconify-icon>
                    <span>Reports</span>
                </a>
            </li>

             <li nav-item dropdown>
    <a class="sidebar-link " data-bs-toggle="dropdown" href="#setupMenu" role="button" >
        <iconify-icon icon="solar:settings-minimalistic-bold-duotone"></iconify-icon>
        <span>Setup</span>
        <iconify-icon icon="solar:alt-arrow-down-bold" class="ms-auto collapse-arrow"></iconify-icon>
    </a>
    <div class="collapse" id="setupMenu">
<<<<<<< Updated upstream
         <ul class="list-unstyled ps-4 mt-1">
=======
    <ul class="list-unstyled ps-4 mt-1">
>>>>>>> Stashed changes
        <li>
            <a href="#" class="sidebar-link py-2">
                <iconify-icon icon="solar:document-text-bold-duotone"></iconify-icon>
                <span>Template</span>
            </a>
        </li>
        <li>
            <a href="<?= base_url('user-management') ?>" class="sidebar-link <?= (uri_string() == 'user-management') ? 'active' : '' ?>">
                <iconify-icon icon="solar:users-group-two-rounded-bold-duotone"></iconify-icon>
                <span>User Management</span>
            </a>
        </li>
        <li>
            <a href="#" class="sidebar-link py-2">
                <iconify-icon icon="solar:clipboard-check-bold-duotone"></iconify-icon>
                <span>Approving Officers</span>
            </a>
        </li>
    </ul>
<<<<<<< Updated upstream
    </div>
</li>
        </ul>
</div>


        <div class="sidebar-title mt-4">
            ACCOUNT
        </div>

        <ul class="sidebar-menu">

            <li>
                <a href="#" class="sidebar-link">
                    <iconify-icon icon="solar:settings-bold-duotone"></iconify-icon>
                    <span>Settings</span>
                </a>
            </li>

            <li>
                <a href="<?= base_url('logout') ?>" class="sidebar-link text-danger">
                    <iconify-icon icon="solar:logout-2-bold-duotone"></iconify-icon>
                    <span>Sign Out</span>
                </a>
            </li>

        </ul>

    </nav>

</aside>
    <!--  Sidebar End -->
    <div class="page-wrapper">
      <!--  Header Start -->
      <header class="topbar rounded-0 border-0 bg-primary">
        <div class="with-vertical">
          <nav class="navbar navbar-expand-lg px-lg-0 px-3 py-0">
            <div class="d-none d-lg-block">
                <div class="brand-logo d-flex align-items-center justify-content-between">
                    <a href="#" class="text-nowrap logo-img d-flex align-items-center gap-2">
                        <!-- Logo Icon -->
                        <div class="logo-icon bg-primary text-white d-flex align-items-center justify-content-center rounded-circle fw-bold"
                            style="width:45px;height:45px;font-size:24px;">
                            G
                        </div>
                        <!-- Logo Text -->
                        <span class="logo-text text-white fw-bold fs-5">
                            GATEPASS SYSTEM
                        </span>

                    </a>
                </div>
            </div>

            <ul class="navbar-nav gap-2">

              <li class="nav-item nav-icon-hover-bg rounded-circle">
                <a class="nav-link nav-icon-hover sidebartoggler" id="headerCollapse" href="javascript:void(0)">
                  <iconify-icon icon="solar:list-bold"></iconify-icon>
                </a>
              </li>
           

            </ul>

            <div class="d-block d-lg-none">
              <div class="brand-logo d-flex align-items-center justify-content-between">
                <a href="#" class="text-nowrap logo-img d-flex align-items-center gap-2">
                  <span class="logo-text">
                    GATEPASS SYSTEM
                  </span>
                </a>
              </div>
            </div>
            <ul class="navbar-nav flex-row  gap-2 align-items-center justify-content-center d-flex d-lg-none">
              <li class="nav-item dropdown nav-icon-hover-bg rounded-circle">
                <a class="navbar-toggler nav-link text-white nav-icon-hover border-0" href="javascript:void(0)" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                  <span class="">
                    <i class="ti ti-dots fs-7"></i>
                  </span>
                </a>
              </li>
            </ul>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
              <div class="d-flex align-items-center justify-content-between py-2 py-lg-0">
               
                <ul class="navbar-nav gap-2 flex-row ms-auto align-items-center justify-content-center">
             

                  <li class="nav-item hover-dd dropdown nav-icon-hover-bg rounded-circle d-none d-lg-block">
                    <a class="nav-link nav-icon-hover waves-effect waves-dark" href="javascript:void(0)" id="drop2" aria-expanded="false">
                      <iconify-icon icon="solar:bell-bing-line-duotone"></iconify-icon>
                      <div class="notify">
                        <span class="heartbit"></span>
                        <span class="point"></span>
                      </div>
                    </a>
                    <div class="dropdown-menu py-0 content-dd  dropdown-menu-animate-up overflow-hidden dropdown-menu-end" aria-labelledby="drop2">

                      <div class="py-3 px-4 bg-primary">
                        <div class="mb-0 fs-6 fw-medium text-white">Notifications</div>
                        <div class="mb-0 fs-2 fw-medium text-white">You have 0 Notifications</div>
                      </div>
                      <div class="message-body" data-simplebar>
                        <a href="javascript:void(0)" class="p-3 d-flex align-items-center  dropdown-item gap-3   border-bottom">
                          <span class="flex-shrink-0 bg-primary-subtle rounded-circle round-40 d-flex align-items-center justify-content-center fs-6 text-primary">
                            <iconify-icon icon="solar:widget-3-line-duotone"></iconify-icon>
                          </span>
                          <div class="w-80">
                            <div class="d-flex align-items-center justify-content-between">
                              <h6 class="mb-1">Launch Admin</h6>
                              <span class="fs-2 d-block text-muted ">9:30 AM</span>
                            </div>
                            <span class="fs-2 d-block text-truncate text-muted">Just see the my new admin!</span>
                          </div>
                        </a>
 
                      </div>
                      <div class="p-3">
                        <a class="d-flex btn btn-primary  align-items-center justify-content-center gap-2" href="javascript:void(0);">
                          <span>Check all Notifications</span>
                          <iconify-icon icon="solar:alt-arrow-right-outline" class="iconify-sm"></iconify-icon>
                        </a>
                      </div>

                    </div>
                  </li>

                  <li class="nav-item hover-dd dropdown">
                    <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2" aria-expanded="false">
                      <img src="<?= base_url('assets/images/default.png') ?>" alt="user" class="profile-pic rounded-circle round-30" />
                    </a>
                    <div class="dropdown-menu pt-0 content-dd overflow-hidden pt-0 dropdown-menu-end user-dd" aria-labelledby="drop2">
                      <div class="profile-dropdown position-relative" data-simplebar>
                        <div class=" py-3 border-bottom">
                          <div class="d-flex align-items-center px-3">
                            <img src="<?= base_url('assets/images/default.png') ?>" class="rounded-circle round-50" alt="" />
                            <div class="ms-3">
                              <h5 class="mb-1 fs-4"><?= session()->get('logged_in')['full_name'] ?? '' ?></h5>
                            </div>
                          </div>
                        </div>
                        <div class="message-body pb-3">
                          <div class="px-3 pt-3">
                            <div class="h6 mb-0 dropdown-item py-8 px-3 rounded-2 link">
                              <a href="#" class=" d-flex  align-items-center ">
                                My Profile
                              </a>
                            </div>
                         
                          </div>
                          <hr>
                          <div class="px-3">
                            <div class="py-8 px-3 d-flex justify-content-between dropdown-item align-items-center h6 mb-0  rounded-2 link">
                              <a href="javascript:void(0)" class="">
                                Mode
                              </a>
                              <div>
                                <a class="moon dark-layout" href="javascript:void(0)">
                                  <iconify-icon icon="solar:moon-line-duotone" class="moon"></iconify-icon>
                                </a>
                                <a class="sun light-layout" href="javascript:void(0)">
                                  <iconify-icon icon="solar:sun-2-line-duotone" class="sun"></iconify-icon>
                                </a>
                              </div>
                            </div>
                        
                            <div class="h6 mb-0 dropdown-item py-8 px-3 rounded-2 link">
                              <a href="<?= base_url('logout') ?>" class=" d-flex  align-items-center ">
                                Sign Out
                              </a>
                            </div>
                          </div>
                        </div>

                      </div>
                    </div>
                  </li>


                </ul>
              </div>
            </div>
          </nav>

        </div>
    
      </header>
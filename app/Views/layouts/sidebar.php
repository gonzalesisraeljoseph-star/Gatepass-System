<?php
/**
 * Sidebar nav - fully DB-driven off `modules` / `sub_modules` / `role_module`
 * / `role_submodule`. Nothing here names a specific module: whatever
 * role_ids are in the session get whatever rows role_module/role_submodule
 * grant them.
 *
 * role_ids comes from session('logged_in')['role_ids'], set at login in
 * Auth::login() via UserRoleModel::roleIdsForUser($refEmp).
 *
 * There's no module_url/module_icon column in the DB, so:
 *  - the route is derived from the label via module_slug() (see
 *    app/Helpers/menu_helper.php) - fix the $overrides map there if a
 *    module's real route doesn't match its name
 *  - no icon is rendered; add an <iconify-icon> back in once/if an icon
 *    column exists
 */
helper('menu');

$roleIds = session()->get('logged_in')['role_ids'] ?? [];
$menu    = (new \App\Models\ModuleModel())->menuForRoles($roleIds);
?>

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
          <?php foreach ($menu as $module): ?>
    <?php $slug = module_slug($module['module_name']); ?>
    <?php if (empty($module['sub_modules'])): ?>
      <li>
        <a href="<?= base_url($slug) ?>"
           class="sidebar-link <?= (uri_string() == $slug) ? 'active' : '' ?>">
          <span><?= esc($module['module_name']) ?></span>
        </a>
      </li>
    <?php else: ?>
      <li nav-item dropdown>
        <a class="sidebar-link" data-bs-toggle="dropdown" href="#module<?= $module['module_id'] ?>" role="button">
          <span><?= esc($module['module_name']) ?></span>
          <iconify-icon icon="solar:alt-arrow-down-bold" class="ms-auto collapse-arrow"></iconify-icon>
        </a>
        <div class="collapse" id="module<?= $module['module_id'] ?>">
          <ul class="list-unstyled ps-4 mt-1">
            <?php foreach ($module['sub_modules'] as $sub): ?>
              <?php $subSlug = submodule_slug($sub['sub_module_desc']); ?>   <!-- ← replace this line -->
              <li>
                <a href="<?= base_url($subSlug) ?>"
                   class="sidebar-link <?= (uri_string() == $subSlug) ? 'active' : '' ?>">
                  <span><?= esc($sub['sub_module_desc']) ?></span>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </li>
    <?php endif; ?>
<?php endforeach; ?>
        </ul>

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
                        <div class="logo-icon bg-primary text-white d-flex align-items-center justify-content-center rounded-circle fw-bold"
                            style="width:45px;height:45px;font-size:24px;">
                            G
                        </div>
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
<aside class="sidebar d-flex flex-column" id="sidebar">
    <div class="sidebar-header d-flex align-items-center justify-content-between">
        <a href="<?= base_url('/') ?>" class="logo">
            <div class="logo-icon">
                <i class="fa-solid fa-star" aria-hidden="true"></i>
            </div>
            <span class="logo-text">DayNight</span>
        </a>
        <button class="sidebar-close" onclick="closeSidebar()" aria-label="Close sidebar">
            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
        </button>
    </div>

    <nav class="sidebar-nav">
      

        <a href="<?= base_url('/') ?>" class="nav-link active">
            <span class="nav-icon"><i class="fa-solid fa-grid" aria-hidden="true"></i></span>
            Dashboard
        </a>

        <a href="<?= base_url('projects') ?>" class="nav-link">
            <span class="nav-icon "><i class="fa-solid fa-folder-open" aria-hidden="true"></i></span>
            Requests
            <span class="badge">4</span>
        </a>

        <a href="<?= base_url('inbox') ?>" class="nav-link">
            <span class="nav-icon"><i class="fa-solid fa-envelope" aria-hidden="true"></i></span>
            Inbox
            <span class="badge badge-accent">12</span>
        </a>

        <a href="<?= base_url('analytics') ?>" class="nav-link">
            <span class="nav-icon"><i class="fa-solid fa-chart-line" aria-hidden="true"></i></span>
            Analytics
        </a>


        <a href="<?= base_url('settings') ?>" class="nav-link">
            <span class="nav-icon"><i class="fa-solid fa-gear" aria-hidden="true"></i></span>
            Settings
        </a>

    </nav>

    <div class="sidebar-footer">
       
        <div class="user-profile">
            <div class="user-avatar">A</div>
            <div class="user-info">
                <span class="user-name">Alex Johnson</span>
                <span class="user-role">Administrator</span>
            </div>
            <a href="<?= base_url('login') ?>" class="logout-btn" title="Logout" aria-label="Logout">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
            </a>
        </div>
    </div>
</aside>
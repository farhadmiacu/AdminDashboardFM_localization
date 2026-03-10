 <!-- ========== App Menu ========== -->
 <div class="app-menu navbar-menu">
     <!-- LOGO -->
     <div class="navbar-brand-box">
         <!-- Dark Logo-->
         <a href="{{ route('admin.dashboard') }}" class="logo logo-dark">
             <span class="logo-sm">
                 @if (!empty($adminSetting->mini_logo))
                     <img src="{{ asset($adminSetting->mini_logo) }}" alt="Logo" height="22">
                 @endif
             </span>
             <span class="logo-lg">
                 @if (!empty($adminSetting->logo))
                     <img src="{{ asset($adminSetting->logo) }}" alt="Logo" height="35">
                 @endif
             </span>
         </a>
         <!-- Light Logo-->
         <a href="{{ route('admin.dashboard') }}" class="logo logo-light">
             <span class="logo-sm">
                 @if (!empty($adminSetting->mini_logo))
                     <img src="{{ asset($adminSetting->mini_logo) }}" alt="Logo" height="22">
                 @endif
             </span>
             <span class="logo-lg">
                 @if (!empty($adminSetting->logo))
                     <img src="{{ asset($adminSetting->logo) }}" alt="Logo" height="35">
                 @endif
             </span>
         </a>
         <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
             <i class="ri-record-circle-line"></i>
         </button>
     </div>

     <!-- sidebar-user -->
     <div class="dropdown sidebar-user m-1 rounded">
         <button type="button" class="btn material-shadow-none" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
             <span class="d-flex align-items-center gap-2">
                 <img class="rounded header-profile-user" src="{{ auth()->user()->avatar ? asset(auth()->user()->avatar) : asset('backend/assets/images/users/avatar-1.jpg') }}" alt="Header Avatar">
                 <span class="text-start">
                     <span class="d-block fw-medium sidebar-user-name-text">{{ auth()->user()->name }}</span>
                     <span class="d-block fs-14 sidebar-user-name-sub-text"><i class="ri ri-circle-fill fs-10 text-success align-baseline"></i> <span class="align-middle">Online</span></span>
                 </span>
             </span>
         </button>
         <div class="dropdown-menu dropdown-menu-end">
             <!-- item-->
             <h6 class="dropdown-header">Welcome {{ auth()->user()->name }}!</h6>
             <a class="dropdown-item" href="{{ route('admin.profile-settings.edit') }}"><i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span
                     class="align-middle">Profile</span></a>
             <!-- Logout -->
             <form method="POST" action="{{ route('logout') }}">
                 @csrf
                 <button type="submit" class="dropdown-item">
                     <i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i>
                     <span class="align-middle" data-key="t-logout">Logout</span>
                 </button>
             </form>
         </div>
     </div>

     <!-- sidebar -->
     <div id="scrollbar">
         <div class="container-fluid">

             <div id="two-column-menu">
             </div>
             <ul class="navbar-nav" id="navbar-nav">

                 <!--  Menu -->
                 <li class="menu-title"><span data-key="t-menu">Menu</span></li>

                 <!-- Dashboard -->
                 <li class="nav-item">
                     <a class="nav-link menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                         <i class="ri-dashboard-2-line"></i> <span data-key="t-dashboards">Dashboards</span>
                     </a>
                 </li>

                 {{-- Brand Menu --}}
                 <li class="nav-item">
                     <a class="nav-link menu-link {{ request()->routeIs('admin.brands.*') ? '' : 'collapsed' }}" href="#sidebarBrand" data-bs-toggle="collapse" role="button"
                         aria-expanded="{{ request()->routeIs('admin.brands.*') ? 'true' : 'false' }}" aria-controls="sidebarBrand">
                         <i class="ri-award-line"></i> <span>Brand</span>
                     </a>
                     <div class="collapse menu-dropdown {{ request()->routeIs('admin.brands.*') ? 'show' : '' }}" id="sidebarBrand">
                         <ul class="nav nav-sm flex-column">
                             <li class="nav-item">
                                 <a href="{{ route('admin.brands.create') }}" class="nav-link {{ request()->routeIs('admin.brands.create') ? 'active' : '' }}">
                                     Add Brand
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="{{ route('admin.brands.index') }}" class="nav-link {{ request()->routeIs('admin.brands.index') ? 'active' : '' }}">
                                     All Brands
                                 </a>
                             </li>
                         </ul>
                     </div>
                 </li>

                 {{-- Category Menu --}}
                 <li class="nav-item">
                     <a class="nav-link menu-link {{ request()->routeIs('admin.categories.*') ? '' : 'collapsed' }}" href="#sidebarCategory" data-bs-toggle="collapse" role="button"
                         aria-expanded="{{ request()->routeIs('admin.categories.*') ? 'true' : 'false' }}" aria-controls="sidebarCategory">
                         <i class="ri-folder-line"></i> <span>Category</span>
                     </a>
                     <div class="collapse menu-dropdown {{ request()->routeIs('admin.categories.*') ? 'show' : '' }}" id="sidebarCategory">
                         <ul class="nav nav-sm flex-column">
                             <li class="nav-item">
                                 <a href="{{ route('admin.categories.create') }}" class="nav-link {{ request()->routeIs('admin.categories.create') ? 'active' : '' }}">
                                     Add Category
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.index') ? 'active' : '' }}">
                                     All Categories
                                 </a>
                             </li>
                         </ul>
                     </div>
                 </li>

                 {{-- Product Menu --}}
                 <li class="nav-item">
                     <a class="nav-link menu-link {{ request()->routeIs('admin.products.*') ? '' : 'collapsed' }}" href="#sidebarProduct" data-bs-toggle="collapse" role="button"
                         aria-expanded="{{ request()->routeIs('admin.products.*') ? 'true' : 'false' }}" aria-controls="sidebarProduct">
                         <i class="ri-shopping-bag-3-line"></i> <span>Product</span>
                     </a>
                     <div class="collapse menu-dropdown {{ request()->routeIs('admin.products.*') ? 'show' : '' }}" id="sidebarProduct">
                         <ul class="nav nav-sm flex-column">
                             <li class="nav-item">
                                 <a href="{{ route('admin.products.create') }}" class="nav-link {{ request()->routeIs('admin.products.create') ? 'active' : '' }}">
                                     Add Product
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.index') ? 'active' : '' }}">
                                     All Products
                                 </a>
                             </li>
                         </ul>
                     </div>
                 </li>

                 {{-- <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-pages">Pages</span></li> --}}

                 {{-- nested drop down menu  --}}
                 {{-- <li class="nav-item">
                     <a class="nav-link menu-link" href="#sidebarAuth" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarAuth">
                         <i class="ri-account-circle-line"></i> <span data-key="t-authentication">Authentication</span>
                     </a>
                     <div class="collapse menu-dropdown" id="sidebarAuth">
                         <ul class="nav nav-sm flex-column">
                             <li class="nav-item">
                                 <a href="#sidebarSignIn" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarSignIn" data-key="t-signin"> Sign
                                     In
                                 </a>
                                 <div class="collapse menu-dropdown" id="sidebarSignIn">
                                     <ul class="nav nav-sm flex-column">
                                         <li class="nav-item">
                                             <a href="auth-signin-basic.html" class="nav-link" data-key="t-basic"> Basic
                                             </a>
                                         </li>
                                         <li class="nav-item">
                                             <a href="auth-signin-cover.html" class="nav-link" data-key="t-cover"> Cover
                                             </a>
                                         </li>
                                     </ul>
                                 </div>
                             </li>
                         </ul>
                     </div>
                 </li> --}}

                 {{-- Package Menu --}}
                 <li class="nav-item">
                     <a class="nav-link menu-link {{ request()->routeIs('admin.packages.*') ? '' : 'collapsed' }}" href="#sidebarPackage" data-bs-toggle="collapse" role="button"
                         aria-expanded="{{ request()->routeIs('admin.packages.*') ? 'true' : 'false' }}" aria-controls="sidebarPackage">
                         <i class="ri-gift-line"></i> <span>Packages</span>
                     </a>
                     <div class="collapse menu-dropdown {{ request()->routeIs('admin.packages.*') ? 'show' : '' }}" id="sidebarPackage">
                         <ul class="nav nav-sm flex-column">
                             <li class="nav-item">
                                 <a href="{{ route('admin.packages.create') }}" class="nav-link {{ request()->routeIs('admin.packages.create') ? 'active' : '' }}">
                                     Add Package
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="{{ route('admin.packages.index') }}" class="nav-link {{ request()->routeIs('admin.packages.index') ? 'active' : '' }}">
                                     All Packages
                                 </a>
                             </li>
                         </ul>
                     </div>
                 </li>

                 {{-- Settings --}}
                 <li class="menu-title"><span data-key="t-menu">Settings</span></li>

                 {{-- Settings Section --}}
                 <li class="nav-item">
                     <a class="nav-link menu-link {{ request()->routeIs('admin.profile-settings.*') || request()->routeIs('admin.managers.*') || request()->routeIs('admin.social-settings.*') || request()->routeIs('admin.stripe-settings.*') || request()->routeIs('admin.admin-settings.*') || request()->routeIs('admin.system-settings.*') || request()->routeIs('admin.mail-settings.*') ? '' : 'collapsed' }}"
                         href="#sidebarSettings" data-bs-toggle="collapse" role="button"
                         aria-expanded="{{ request()->routeIs('admin.profile-settings.*') || request()->routeIs('admin.managers.*') || request()->routeIs('admin.social-settings.*') || request()->routeIs('admin.stripe-settings.*') || request()->routeIs('admin.admin-settings.*') || request()->routeIs('admin.system-settings.*') || request()->routeIs('admin.mail-settings.*') ? 'true' : 'false' }}"
                         aria-controls="sidebarSettings">
                         <i class="ri-settings-3-line"></i> <span>Settings</span>
                     </a>

                     <div class="collapse menu-dropdown {{ request()->routeIs('admin.profile-settings.*') || request()->routeIs('admin.managers.*') || request()->routeIs('admin.social-settings.*') || request()->routeIs('admin.stripe-settings.*') || request()->routeIs('admin.admin-settings.*') || request()->routeIs('admin.system-settings.*') || request()->routeIs('admin.mail-settings.*') ? 'show' : '' }}"
                         id="sidebarSettings">

                         <ul class="nav nav-sm flex-column">
                             {{-- Profile Settings --}}
                             <li class="nav-item">
                                 <a href="{{ route('admin.profile-settings.edit') }}" class="nav-link {{ request()->routeIs('admin.profile-settings.*') ? 'active' : '' }}">
                                     <i class="ri-user-settings-line"></i> <span>Profile Settings</span>
                                 </a>
                             </li>

                             {{-- Manage Managers --}}
                             @if (auth()->user()->role == 'admin')
                                 <li class="nav-item">
                                     <a href="{{ route('admin.managers.index') }}" class="nav-link {{ request()->routeIs('admin.managers.*') ? 'active' : '' }}">
                                         <i class="ri-group-line"></i> <span>Manage Managers</span>
                                     </a>
                                 </li>
                             @endif

                             {{-- Social Settings --}}
                             <li class="nav-item">
                                 <a href="{{ route('admin.social-settings.edit') }}" class="nav-link {{ request()->routeIs('admin.social-settings.*') ? 'active' : '' }}">
                                     <i class="ri-share-line"></i> <span>Social Settings</span>
                                 </a>
                             </li>

                             {{-- Stripe Settings --}}
                             <li class="nav-item">
                                 <a href="{{ route('admin.stripe-settings.edit') }}" class="nav-link {{ request()->routeIs('admin.stripe-settings.*') ? 'active' : '' }}">
                                     <i class="ri-mail-settings-line"></i> <span>Stripe Settings</span>
                                 </a>
                             </li>

                             {{-- System Settings --}}
                             <li class="nav-item">
                                 <a href="{{ route('admin.system-settings.edit') }}" class="nav-link {{ request()->routeIs('admin.system-settings.*') ? 'active' : '' }}">
                                     <i class="ri-settings-3-line"></i> <span>System Settings</span>
                                 </a>
                             </li>

                             {{-- Admin Settings --}}
                             <li class="nav-item">
                                 <a href="{{ route('admin.admin-settings.edit') }}" class="nav-link {{ request()->routeIs('admin.admin-settings.*') ? 'active' : '' }}">
                                     <i class="ri-settings-3-line"></i> <span>Admin Settings</span>
                                 </a>
                             </li>

                             {{-- Mail Settings --}}
                             <li class="nav-item">
                                 <a href="{{ route('admin.mail-settings.edit') }}" class="nav-link {{ request()->routeIs('admin.mail-settings.*') ? 'active' : '' }}">
                                     <i class="ri-mail-settings-line"></i> <span>Mail Settings</span>
                                 </a>
                             </li>
                         </ul>
                     </div>
                 </li>

             </ul>
         </div>
         <!-- Sidebar -->
     </div>

     <div class="sidebar-background"></div>
 </div>

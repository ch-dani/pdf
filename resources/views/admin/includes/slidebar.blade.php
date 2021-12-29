<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar" style="height: auto;">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                {{--<img src="{{ asset(\Auth::user()->avatar) }}" class="img-circle" alt="User Image">--}}
                <img src="{{ asset('favicon.ico') }}" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>{{ \Auth::user()->name }}</p>
                <a href="#">
                    <i class="fa fa-circle text-success"></i>
                    Online
                </a>
            </div>
        </div>
        <!-- search form -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search...">
                <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat">
                    <i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>
        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu tree" data-widget="tree">
            <li class="header">MAIN NAVIGATION</li>
            <li>
                <a href="{{ route('admin-dashboard') }}">
                    <i class="fa fa-dashboard"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-files-o"></i>
                    <span>Pages</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu" style="display: none;">
                    <li>
                        <a href="{{ route('admin-pages') }}">
                            <i class="fa fa-circle-o"></i>
                            All Pages
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin-add-page') }}">
                            <i class="fa fa-circle-o"></i>
                            Add New Page
                        </a>
                    </li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-users"></i>
                    <span>Users</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu" style="display: none;">
                    <li>
                        <a href="{{ route('admin-users') }}">
                            <i class="fa fa-circle-o"></i>
                            All Users
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin-add-user') }}">
                            <i class="fa fa-circle-o"></i>
                            Add New User
                        </a>
                    </li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-money"></i>
                    <span>Subscriptions</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu" style="display: none;">
                    <li>
                        <a href="{{ route('admin-subscriptions') }}">
                            <i class="fa fa-circle-o"></i>
                            All Subscriptions
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin-add-subscription') }}">
                            <i class="fa fa-circle-o"></i>
                            Add New Subscription
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="{{ route('admin-documents') }}">
                    <i class="fa fa-file-text"></i>
                    <span>Documents</span>
                </a>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-navicon"></i>
                    <span>Menu</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu" style="display: none;">
                    <li>
                        <a href="{{ route('admin-menu') }}">
                            <i class="fa fa-circle-o"></i>
                            Header Menu
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin-footer-menu') }}">
                            <i class="fa fa-circle-o"></i>
                            Footer Menu
                        </a>
                    </li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-book"></i>
                    <span>How-To Guides</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu" style="display: none;">
                    <li>
                        <a href="{{ route('admin-guides') }}">
                            <i class="fa fa-circle-o"></i>
                            All Guides
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin-add-guide') }}">
                            <i class="fa fa-circle-o"></i>
                            Add New Guide
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="{{ route('admin-contacts') }}">
                    <i class="fa fa-envelope-o"></i>
                    <span>Contacts</span>
                </a>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-rss" aria-hidden="true"></i>
                    <span>Blog</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu" style="display: none;">
                    <li>
                        <a href="{{ route('admin-articles') }}">
                            <i class="fa fa-circle-o"></i>
                            All Articles
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin-add-article') }}">
                            <i class="fa fa-circle-o"></i>
                            Add New Article
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin-blog-cats') }}">
                            <i class="fa fa-circle-o"></i>
                            Blog categories
                        </a>
                    </li>


                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-question-circle-o"></i>
                    <span>Home FAQ</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu" style="display: none;">
                    <li>
                        <a href="{{ route('admin-faq') }}">
                            <i class="fa fa-circle-o"></i>
                            All FAQ
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin-add-faq') }}">
                            <i class="fa fa-circle-o"></i>
                            Add New FAQ
                        </a>
                    </li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-gears"></i>
                    <span>Settings</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu" style="display: none;">
                    <li>
                        <a href="{{ route('admin-setting-seo') }}">
                            <i class="fa fa-circle-o"></i>
                            <span>SEO Global</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin-setting-contact') }}">
                            <i class="fa fa-circle-o"></i>
                            Contacts
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin-setting-social') }}">
                            <i class="fa fa-circle-o"></i>
                            Socials
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin-setting-payment') }}">
                            <i class="fa fa-circle-o"></i>
                            Payment
                        </a>
                    </li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-flag"></i>
                    <span>Languages</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu" style="display: none;">
                    <li>
                        <a href="{{ route('admin-languages') }}">
                            <i class="fa fa-circle-o"></i>
                            All Languages
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin-add-language') }}">
                            <i class="fa fa-circle-o"></i>
                            Add New Language
                        </a>
                    </li>
                </ul>
            </li>
            @if (\Auth::user()->role == 'superadmin')
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-gears"></i>
                        <span>Administrators</span>
                        <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                    </a>
                    <ul class="treeview-menu" style="display: none;">
                        <li>
                            <a href="{{ route('admin-administrators') }}">
                                <i class="fa fa-circle-o"></i>
                                <span>All Administrators</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin-add-administrator') }}">
                                <i class="fa fa-circle-o"></i>
                                Add Administrator
                            </a>
                        </li>
                    </ul>
                </li>





            @endif
            <li>
                <a href="{{ route('logout') }}">
                    <i class="fa fa-sign-out"></i>
                    <span>Sign out</span>
                </a>
            </li>

    </section>
    <!-- /.sidebar -->
</aside>

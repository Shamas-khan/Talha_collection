<div class="col-md-3 left_col">
    
    <div class="left_col scroll-view">
        
        <div class="navbar nav_title" style="border: 0;">
            <a href="{{ route('home') }}" class="site_title"><span>Admin</span></a>
        </div>

        <div class="clearfix"></div>






        <!-- sidebar menu -->
        <!-- sidebar menu -->
        <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
            <div class="menu_section">

                <ul class="nav side-menu">
                    <li><a href="{{ route('home') }}"><i class="fa fa-home"></i></i>Dashboard </a></li>
                    
                    <li><a href="{{ route('suppliers.index') }}"><i class="fa fa-sitemap"></i>Supplier </a></li>
                    <li>
                                <a><i class="fa fa-user-plus"></i>Vendor <span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                    <li><a href="{{ route('karahivendor.index') }}"><i class="fa fa-user-plus"></i>Karhai </a>
                                    </li>
                                    <li><a href="{{ route('vendors.index') }}"><i class="fa fa-user-plus"></i>Kariger </a></li>
        
        
        
                                </ul>
                            </li>
                    <li>
                    <li><a href="{{ route('customers.index') }}"><i class="fa fa-users"></i>Customer </a></li>
                    <li><a href="{{ route('parties.index') }}"><i class="fa fa-users"></i>Direct Party</a></li>

                        
                        
                    </li>
                    <li>
                        <a><i class="fa fa-user-plus"></i>Human Resource HR<span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                           
                            <li><a href="{{ route('employee.index') }}"><i class="fa fa-sitemap"></i>Employees </a></li>
                            <li><a href="{{ route('attendence.index') }}"><i class="fa fa-sitemap"></i>Attendence </a></li>
                            <li><a href="{{ route('payroll.index') }}"><i class="fa fa-sitemap"></i>Payroll </a></li>
                           
                           

                           



                        </ul>
                    </li>
                    <li><a href="{{ route('raw_material.index') }}"><i class="fa fa-tasks"></i>Raw Materials </a></li>
                    <li><a href="{{ route('purchase.index') }}"><i class="fa fa-shopping-cart"></i>Purchase </a></li>
                    <li><a href="{{ route('finishproduct.index') }}"><i class="fa fa-tasks"></i>Finish Product </a></li>
                    <li><a href="{{ route('issue.index') }}"><i class="fa fa-spinner"></i>Issue </a></li>
                    <li><a href="{{ route('stock') }}"><i class="fa fa-stack-overflow"></i>Stock </a></li>
                    <li>
                        <a><i class="fa fa-sliders"></i>Finance<span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            
                            <li>
                                <a href="{{ route('roznamcha.index') }}"><i class="fa fa-tags"></i>Roznamcha  </a>
                            </li>
                            <li>
                                <a href="{{ route('payment.index') }}"><i class="fa fa-tags"></i>Payment Voucher  </a>
                            </li>
                            <li>
                                <a href="{{ route('recieve.index') }}"><i class="fa fa-tags"></i>Recipt Voucher</a>
                            </li>
                            <li>
                              <a href="{{ route('banks.index') }}"><i class="fa fa-tags"></i>Accounts </a>
                          </li>
                           
                        </ul>
                    </li>
                    <li>
                        <a><i class="fa fa-sliders"></i>Sell <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li>
                                <a href="{{ route('sell.createlist') }}"><i class="fa fa-tags"></i>Sell List </a>
                            </li>
                            <li>
                                <a href="{{ route('sell.index') }}"><i class="fa fa-tags"></i>Sell Order List </a>
                            </li>
                        </ul>
                    </li>
                    <li><a href="{{ route('expense.index') }}"><i class="fa fa-money"></i>Expenses </a></li>
                    <li><a href="absc.php"><i class="fa fa-user"></i></i>User </a></li>
                    <li><a href="{{ route('forcasting.index') }}"><i class="fa fa-user"></i></i>Forecasting </a></li>

                    <li>

                        <a><i class="fa fa-sliders"></i>Setup <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href="{{ route('units.index') }}"><i
                                        class="fa fa-balance-scale icon-custom"></i>Unit </a></li>
                            <li>
                                <a href="{{ route('expensecategory.index') }}"><i
                                        class="fa fa-list-ol icon-custom"></i>
                                    Expense Category </a>
                            </li>
                            <li>
                                <a href="{{ route('machine.index') }}"><i class="fa fa-list-ol icon-custom"></i>
                                    Machine </a>
                            </li>
                            <li>
                                <a href="{{ route('design.index') }}"><i class="fa fa-list-ol icon-custom"></i>
                                    Design </a>
                            </li>
                            <li>
                                <a href="{{ route('packing.index') }}"><i class="fa fa-list-ol icon-custom"></i>
                                    Packing </a>
                            </li>
                        </ul>
                    </li>

                    <li><a><i class="fa fa-sliders"></i>Report <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href="{{ route('report.index') }}"><i class="fa fa-sitemap icon-custom"></i>Profit and loss </a></li>
                            <li><a href="{{ route('summaryreport') }}"><i class="fa fa-sitemap icon-custom"></i>Summary </a></li>
                           
                        </ul>
                    </li>






                </ul>
            </div>

        </div>
        <!-- /sidebar menu -->
        <!-- /sidebar menu -->

        <!-- /menu footer buttons -->
        {{-- <div class="sidebar-footer hidden-small">
              <a data-toggle="tooltip" data-placement="top" title="Settings">
                <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="FullScreen">
                <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="Lock">
                <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="Logout" href="login.html">
                <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
              </a>
            </div> --}}
        <!-- /menu footer buttons -->
    </div>
</div>

<!-- top navigation -->
<div class="top_nav">
    <div class="nav_menu">
        <div class="nav toggle">
            <a id="menu_toggle"><i class="fa fa-bars"></i></a>
        </div>
        <nav class="nav navbar-nav">
            <ul class=" navbar-right">
                <li class="nav-item dropdown open" style="padding-left: 15px;">
                    <a href="javascript:;" class="user-profile dropdown-toggle" aria-haspopup="true" id="navbarDropdown"
                        data-toggle="dropdown" aria-expanded="false">
                        admin name
                    </a>
                    <div class="dropdown-menu dropdown-usermenu pull-right" aria-labelledby="navbarDropdown">



                        <a class="dropdown-item" href="login.html"><i class="fa fa-sign-out pull-right"></i> Log Out</a>
                    </div>
                </li>


            </ul>
        </nav>
    </div>
</div>
<!-- /top navigation -->

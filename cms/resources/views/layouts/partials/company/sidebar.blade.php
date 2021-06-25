<aside class="main-sidebar">
    <section class="sidebar">
        <ul class="sidebar-menu" data-widget="tree">
            @if(config('settings.livetracking')==1)
                <li>
                    <a href="#" class="livetracker" data-ename="Delta Tech">
                        <i class="fa fa-map-marker"></i> <span>Live Tracking</span>
                    </a>
                </li>
            @endif
            <li class="{{ (request()->is('admin/') || request()->is('admin/todayattendancereport')) ? 'active' : '' }}">
                <a href="{{ domain_route('company.admin.home') }}">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                </a>
            </li> 


            @if(Auth::user()->can('employee-view'))
                <li class="treeview {{ (request()->is('admin/employee*') ) ? 'active' : '' }}">
                    <a href="#">
                        <i class="fa fa-user"></i>
                        <span>Employees</span>
                        <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
                    </a>
                    <ul class="treeview-menu">
                        <li class="{{ (request()->is('admin/employee') || request()->is('admin/employee/*') ) ? 'active' : '' }}">
                            <a href="{{ domain_route('company.admin.employee') }}"><i class="fa fa-circle-o"></i>
                                Employee List</a>
                        </li>
                        @if(Auth::user()->can('settings-view') || Auth::user()->employee()->first()->role()->first()->name=="Full Access")
                            <li class="{{ (request()->is('admin/employeegroup') || request()->is('admin/employeegroup/*') ) ? 'active' : '' }}">
                                <a href="{{ domain_route('company.admin.employeegroup') }}"><i
                                            class="fa fa-circle-o"></i> Employee Groups</a>
                            </li>
                        @endif
                    </ul>
            @endif

            @if(Auth::user()->can('product-view'))
                @if(config('settings.product')==1)
                    <li class="treeview {{ ((request()->is('admin/product*') || request()->is('admin/brand*')) || (request()->is('admin/category*') || request()->is('admin/unit*') || request()->is('admin/custom-rate-setup*') || request()->is('admin/scheme*'))) ? 'active' : '' }}">
                        <a href="#">
                            <i class="fa fa-th-large"></i>
                            <span>Products</span>
                            <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                             </span>
                        </a>
                        <ul class="treeview-menu">
                            <li class="{{ (request()->is('admin/product*')) ? 'active' : '' }}"><a
                                        href="{{ domain_route('company.admin.product') }}"><i
                                            class="fa fa-circle-o"></i> Product List</a></li>
                            @if(config('settings.party_wise_rate_setup')==1 && Auth::user()->can('party_wise_rate_setup-view'))
                                <li class="{{ (request()->is('admin/custom-rate-setup*')) ? 'active' : '' }}">
                                    <a
                                            href="{{ domain_route('company.admin.setup_rates') }}"><i
                                                class="fa fa-circle-o"></i> Custom Rate Setup</a></li>
                            @endif
                            <!-- @if(config('settings.category_wise_rate_setup') == 1)
                            <li class="{{ (request()->is('admin/category-rate-setup*')) ? 'active' : '' }}">
                                    <a href="{{ domain_route('company.admin.category.rates.index') }}"><i
                                                class="fa fa-circle-o"></i> Category Rate Setup</a></li>
                            @endif -->
                            @if(Auth::user()->can('settings-view') || Auth::user()->employee()->first()->role()->first()->name=="Full Access")
                                <li class="{{ (request()->is('admin/brand*')) ? 'active' : '' }}"><a
                                            href="{{ domain_route('company.admin.brand') }}"><i
                                                class="fa fa-circle-o"></i> Brands</a></li>
                                <li class="{{ (request()->is('admin/category*')) ? 'active' : '' }}"><a
                                            href="{{ domain_route('company.admin.category') }}"><i
                                                class="fa fa-circle-o"></i> Categories</a></li>
                                <li class="{{ (request()->is('admin/unit/*')|| request()->is('admin/unit')) ? 'active' : '' }}">
                                    <a href="{{ domain_route('company.admin.unit') }}"><i
                                                class="fa fa-circle-o"></i>
                                        Units</a></li>
                            @endif
                            @if(Auth::user()->can('settings-view') || Auth::user()->employee()->first()->role()->first()->name=="Full Access")
                                @if(getClientSetting()->unit_conversion==1)
                                    <li class="{{ (request()->is('admin/unitsconversion')) ? 'active' : '' }}">
                                        <a
                                                href="{{ domain_route('company.admin.unit.conversion') }}"><i
                                                    class="fa fa-circle-o"></i> Units Conversion</a></li>
                                @endif
                            @endif
                            @if(Auth::user()->can('settings-view') || Auth::user()->employee()->first()->role()->first()->name=="Full Access")
                                @if(getClientSetting()->schemes==1 )
                                    <li class="{{ (request()->is('admin/scheme*')) ? 'active' : '' }}">
                                        <a
                                                href="{{ domain_route('company.admin.scheme') }}"><i
                                                    class="fa fa-circle-o"></i> Schemes</a>
                                    </li>
                                @endif
                            @endif
                        </ul>
                    </li>
                @endif
            @endif

            @if(Auth::user()->can('party-view'))
                @if(config('settings.party')==1)
                    @if(config('settings.company_id')==55)
                        <li class="treeview {{ (request()->is('admin/client/*') ) ? 'active' : '' }}">
                            <a href="#">
                                <i class="fa fa-user-secret"></i>
                                <span>Parties</span>
                                <span class="pull-right-container">
                                                <i class="fa fa-angle-left pull-right"></i>
                                            </span>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="{{ domain_route('company.admin.client.subclients', [1]) }}"><i
                                                class="fa fa-circle-o"></i> Distrubutors</a>
                                </li>
                                <li>
                                    <a href="{{ domain_route('company.admin.client.subclients', ['wholeseller-retailer']) }}"><i
                                                class="fa fa-circle-o"></i>
                                        Wholeseller/Retailers</a></li>
                            </ul>
                            <!-- <a href="#">
                                <i class="fa fa-user-secret"></i>
                                <span class="fa fa-circle-o">Party Type</span>
                            </a> -->
                        </li>
                    @else
                        <li id="partytypes"
                            class="{{ (request()->is('admin/client/*') ) ? 'active' : '' }}">
                            <a href="#">
                                <i class="fa fa-user-secret"></i><span>Parties</span></a>
                        </li>
                    @endif
                @endif
            @endif

            @if(Auth::user()->can('activity-view'))
                @if(config('settings.activities')==1)
                    <li class="treeview {{ (request()->is('admin/activities*') ) ? 'active' : '' }}">
                        <a href="#">
                            <i class="fa fa-file"></i>
                            <span>Activities</span>
                            <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                        </span>
                        </a>
                        <ul class="treeview-menu">
                            <li class="{{ (request()->is('admin/activities') || request()->is('admin/activities/*')) ? 'active' : '' }}">
                                <a href="{{ domain_route('company.admin.activities.index') }}"><i
                                            class="fa fa-file"></i> Activities List</a>
                            </li>
                            @if(Auth::user()->can('settings-view') && config('settings.activities')==1)
                                <li class="{{ (request()->is('admin/activities-t*') ) ? 'active' : '' }}"><a
                                            href="{{ domain_route('company.admin.activities-type.index') }}"><i
                                                class="fa fa-file"></i> Activities
                                        Types</a></li>
                                <li class="{{ (request()->is('admin/activities-p*') ) ? 'active' : '' }}"><a
                                            href="{{ domain_route('company.admin.activities-priority.index') }}"><i
                                                class="fa fa-file"></i>
                                        Activities Priority</a></li>
                            @endif
                        </ul>
                    </li>
                @endif
            @endif

            @if(Auth::user()->can('order-view'))
                @if(config('settings.orders')==1)
                    <li class="{{ (request()->is('admin/order*') ) ? 'active' : '' }}">
                        <a href="{{ domain_route('company.admin.order') }}">
                            <i class="fa fa-cart-plus"></i> <span>Orders</span> <span
                                    class="pull-right-container"><span
                                        class=" badge label bg-teal pull-right"
                                        id="ordercount"></span></span>
                        </a>
                    </li>
                @endif
            @endif

            @if(Auth::user()->can('zeroorder-view'))
                @if(config('settings.zero_orders')==1)
                    <li class="{{ (request()->is('admin/zeroorders') ) ? 'active' : '' }}">
                        <a href="{{ domain_route('company.admin.zeroorders') }}">
                            <i class="fa fa-cart-plus"></i> <span>Zero Orders</span> <span
                                    class="pull-right-container"><span
                                        class=" badge label bg-teal pull-right"
                                        id="ordercount"></span></span>
                        </a>
                    </li>
                @endif
            @endif

            @if(Auth::user()->can('collection-view'))
                @if(config('settings.collections')==1)
                    <li class="treeview {{ (request()->is('admin/collection*') || request()->is('admin/cheque*')) ? 'active' : '' }}">
                        <a href="#">
                            <i class="fa fa-money"></i>
                            <span>Collections</span>
                            <span class="pull-right-container">
                                         <i class="fa fa-angle-left pull-right"></i>
                                        </span>
                        </a>
                        <ul class="treeview-menu">
                            <li class="{{ (request()->is('admin/collection*')) ? 'active' : '' }}">
                                <a href="{{ domain_route('company.admin.collection') }}">
                                    <i class="fa fa-circle-o"></i> <span>Collections List</span>
                                </a>
                            </li>
                            <li class="{{ (request()->is('admin/cheque*')) ? 'active' : '' }}">
                                <a href="{{ domain_route('company.admin.cheque.index') }}">
                                    <i class="fa fa-circle-o"></i> <span>PDC Management</span><span
                                            class="pull-right-container"></span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
            @endif

            @if(Auth::user()->can('note-view') && config('settings.notes')==1)
              
            <li class="{{ (request()->is('admin/notes*')) ? 'active' : '' }}">
                <a href="{{ domain_route('company.admin.notes') }}">
                    <i class="fa fa-sticky-note"></i> <span>Notes</span>
                </a>
            </li>
            @endif

            @if(Auth::user()->can('expense-view'))
                @if(config('settings.expenses')==1)
                    <li class="{{ (request()->is('admin/expense*')) ? 'active' : '' }}">
                        <a href="{{ domain_route('company.admin.expense') }}">
                            <i class="fa fa-delicious"></i> <span>Expenses</span><span
                                    class="pull-right-container"><span
                                        class=" badge label bg-teal pull-right"
                                        id="expensecount"></span></span>
                        </a>
                    </li>
                @endif
            @endif

            @if(Auth::user()->can('announcement-view'))
                @if(config('settings.announcement')==1)
                    <li class="{{ (request()->is('admin/announcement*')) ? 'active' : '' }}">
                        <a href="{{ domain_route('company.admin.announcement') }}">
                            <i class="fa fa-volume-up"></i> <span>Announcements</span>
                        </a>
                    </li>
                @endif
            @endif

            @if(Auth::user()->can('leave-view'))
                @if(config('settings.leaves')==1)
                    <li class="{{ (request()->is('admin/leave*')) ? 'active' : '' }}">
                        <a href="{{ domain_route('company.admin.leave') }}">
                            <i class="fa fa-tag"></i> <span>Employee Leave</span><span
                                    class="pull-right-container"><span
                                        class=" badge label bg-teal pull-right"
                                        id="leavecount"></span></span>
                        </a>
                    </li>
                @endif
            @endif

            @if(Auth::user()->can('tourplan-view'))
                @if(config('settings.tour_plans')==1)
                    <li class="{{ (request()->is('admin/tourplans*')) ? 'active' : '' }}">
                        <a href="{{ domain_route('company.admin.tours') }}">
                            <i class="fa fa-location-arrow"></i> <span>Tour Plans</span><span
                                    class="pull-right-container">
                            </span>
                        </a>
                    </li>
                @endif
            @endif

            @if(Auth::user()->can('PartyVisit-view'))
                @if(config('settings.visit_module')==1)
                    <li class="{{ (request()->is('admin/client-visit*')) ? 'active' : '' }}">
                        <a href="{{ domain_route('company.admin.clientvisit.index') }}">
                            <i class="fa fa-handshake-o"></i> <span>Party Visits</span><span
                                    class="pull-right-container">
                            </span>
                        </a>
                    </li>
                @endif
            @endif

            @if(Auth::user()->can('dayremark-view'))
                @if(config('settings.remarks')==1)
                    <li class="{{ (request()->is('admin/dayremarks*')) ? 'active' : '' }}">
                        <a href="{{ domain_route('company.admin.dayremarks') }}">
                            <i class="fa fa-book"></i> <span>Day Remarks</span><span
                                    class="pull-right-container">
                            </span>
                        </a>
                    </li>
                @endif
            @endif

            @if(Auth::user()->can('beat-plan-view'))
                @if(config('settings.beat')==1)
                    @if(config('settings.beat')==1)
                        <li class="{{ (request()->is('admin/beatplan*')) ? 'active' : '' }}">
                            <a href="{{ domain_route('company.admin.beatplan') }}">
                                <i class="fa fa-tasks"></i> <span>Beat Plan</span><span
                                        class="pull-right-container">
                                </span>
                            </a>
                        </li>
                    @endif
                @endif
            @endif


            @if(Auth::user()->can('targets-view') && config('settings.targets'))
                <li class="treeview {{ (request()->is('admin/salesmantarget*') ) ? 'active' : '' }}">
                    <a href="#">
                        <i class="fa fa-user"></i>
                        <span>Targets</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                          </span>
                    </a>
                    <ul class="treeview-menu">
                        <li class="{{ (request()->is('admin/salesmantarget') || request()->is('admin/salesmantarget/*') ) ? 'active' : '' }}">
                            <a href="{{ domain_route('company.admin.salesmantarget') }}"><i class="fa fa-circle-o"></i>
                                Salesman Target</a>
                        </li>
                    <!-- <li class="{{ (request()->is('admin/salesmantargetassign') || request()->is('admin/salesmantarget/*') ) ? 'active' : '' }}">
                      <a href="{{ domain_route('company.admin.salesmantarget.set') }}"><i class="fa fa-circle-o"></i>
                          Assign Target</a>
                  </li> -->
                        <li class="{{ (request()->is('admin/salesmantargetshow') || request()->is('admin/salesmantarget/*') ) ? 'active' : '' }}">
                            <a href="{{ domain_route('company.admin.salesmantargetlist.show') }}"><i
                                        class="fa fa-circle-o"></i>
                                Target List</a>
                        </li>
                        @if(Auth::user()->can('targets_rep-view') && config('settings.targets_rep'))
                            <li class="{{ (request()->is('admin/salesmantargetreport') || request()->is('admin/salesmantargetreport/*') ) ? 'active' : '' }}">
                                <a href="{{ domain_route('company.admin.salesmantargetreport') }}"><i
                                            class="fa fa-circle-o"></i>Target Report</a>
                            </li>
                        @endif
                    </ul>
            @endif

            @if(config('settings.custom_module') == 1)
                <li id="customModules"
                    class="treeview {{ request()->is('admin/custom-modules*') ? 'active' : '' }}">
                </li>
            @endif



            @if(Auth::user()->can('report-view') && (config('settings.livetracking')==1 || config('settings.attendance')==1 || config('settings.cincout')==1 || config('settings.dsobyunit')==1 || config('settings.dso')==1 || config('settings.ordersreport')==1 || config('settings.psoreport')==1 || config('settings.spwise')==1 || config('settings.dpartyreport')==1 || config('settings.dempreport')==1 || config('settings.beat')==1 || config('settings.stock_report')==1 || config('settings.returns')==1 || config('settings.odometer_report')==1 || config('settings.party')==1))
                <li class="treeview {{ (request()->is('admin/reports/*')) ? 'active' : '' }}">
                    <a href="#">
                        <i class="fa fa-pie-chart"></i>
                        <span>Reports</span>
                        <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                    </a>
                    <ul class="treeview-menu" style="padding-left: 25px;">
                        @if(config('settings.party')==1)
                          @if(Auth::user()->can('partydet_rep-view'))
                              <li class="{{ (request()->is('admin/reports/partydetailsreport')) ? 'active' : '' }}">
                              <a href="{{ domain_route('company.admin.partydetailsreport') }}"><i
                                      class="fa fa-circle-o"></i>
                              Party Report</a></li>
                          @endif
                        @endif

                        @if(Auth::user()->can('employee-view')) 
                            @if(Auth::user()->can('employeedet_rep-view'))
                            <li class="{{ (request()->is('admin/reports/employeedetailsreport')) ? 'active' : '' }}">
                                <a
                                        href="{{ domain_route('company.admin.employeedetailsreport') }}"><i
                                            class="fa fa-circle-o"></i>
                                    Employee Report</a></li>
                            @endif
                        @endif
                        @if(config('settings.gpsreports')==1 && Auth::user()->can('salesmangps-view'))
                            <li class="{{ (request()->is('admin/reports/salesmangpspath')) ? 'active' : '' }}">
                                <a
                                        href="{{ domain_route('company.admin.employeerattendancegps') }}"><i
                                            class="fa fa-circle-o"></i>
                                    Salesman GPS Path</a></li>
                        @endif
                        @if(config('settings.monthly_attendance')==1 && Auth::user()->can('monthly-attendance-view'))
                            <li class="{{ (request()->is('admin/reports/monthlyattendancereport')) ? 'active' : '' }}">
                                <a href="{{ domain_route('company.admin.getmonthlyattendancereport') }}"><i
                                            class="fa fa-circle-o"></i>
                                    Monthly Attendance Report</a></li>
                        @endif
                        @if(config('settings.cincout')==1 && Auth::user()->can('cincout-view'))
                            <li class="{{ (request()->is('admin/reports/checkin-checkoutlocationreport')) ? 'active' : '' }}">
                                <a href="{{ domain_route('company.admin.viewattendancereport') }}"><i
                                            class="fa fa-circle-o"></i>
                                    Checkin-Checkout Location Report</a></li>
                        @endif
                        @if(config('settings.dsobyunit')==1 && Auth::user()->can('dsorbunit-view'))
                            <li class="{{ (request()->is('admin/reports/dailysalesorderreport')) ? 'active' : '' }}">
                                <a
                                        href="{{ domain_route('company.admin.dailysalesreport') }}"><i
                                            class="fa fa-circle-o"></i>Daily Sales Order Report (by
                                    unit)</a></li>
                        @endif
                        @if(config('settings.dso')==1 && Auth::user()->can('dsor-view'))
                            <li class="{{ (request()->is('admin/reports/dailysalesmanorderreport')) ? 'active' : '' }}">
                                <a href="{{ domain_route('company.admin.dailysalesreportbysalesman') }}"><i
                                            class="fa fa-circle-o"></i>Daily Salesman Order Report</a></li>
                        @endif

                        @if(config('settings.ordersreport')==1 && Auth::user()->can('orderr-view'))
                            <li class="{{ (request()->is('admin/reports/orderreports')) ? 'active' : '' }}">
                                <a
                                        href="{{ domain_route('company.admin.variantreports') }}"><i
                                            class="fa fa-circle-o"></i>Order Reports</a></li>
                        @endif

                        
                        @if(config('settings.orders') == 1 && Auth::user()->can('product_order_detail_report-view') )
                            <li class="{{ (request()->is('admin/reports/order-breakdown-report')) ? 'active' : '' }}">
                                    <a href="{{ domain_route('company.admin.report.product-order-details') }}"><i class="fa fa-circle-o"></i>Order Breakdown Report</a>
                            </li>
                        @endif

                        @if(config('settings.psoreport')==1 && Auth::user()->can('psor-view'))
                            <li class="{{ (request()->is('admin/reports/productsalesorderreport')) ? 'active' : '' }}">
                                <a href="{{ domain_route('company.admin.viewproductsalesreports') }}"><i
                                            class="fa fa-circle-o"></i>Product Sales Order Report</a></li>
                        @endif

                        @if(config('settings.spwise')==1 && Auth::user()->can('spartywiser-view'))
                            <li class="{{ (request()->is('admin/reports/salesmanparty-wisereports')) ? 'active' : '' }}">
                                <a href="{{ domain_route('company.admin.salesreports') }}"><i
                                            class="fa fa-circle-o"></i>Salesman Party-wise Reports</a></li>
                        @endif

                        @if(config('settings.dpartyreport')==1 && Auth::user()->can('dailypr-view'))
                            <li class="{{ (request()->is('admin/reports/dailypartyreport')) ? 'active' : '' }}">
                                <a
                                        href="{{ domain_route('company.admin.dailyordercollectionreport') }}"><i
                                            class="fa fa-circle-o"></i>
                                    Daily Party Report</a></li>
                        @endif

                        @if(config('settings.dempreport')==1 && Auth::user()->can('dempr-view'))
                            <li class="{{ (request()->is('admin/reports/dailyemployeereport')) ? 'active' : '' }}">
                                <a
                                        href="{{ domain_route('company.admin.dailyemployeereport') }}"><i
                                            class="fa fa-circle-o"></i> Daily
                                    Employee Report</a></li>
                        @endif

                        @if(config('settings.beat')==1 && Auth::user()->can('beatplanreport-view'))
                            <li class="{{ (request()->is('admin/reports/beatreport')) ? 'active' : '' }}"><a
                                        href="{{ domain_route('company.admin.beatroutereports') }}"><i
                                            class="fa fa-circle-o"></i>Beat Report</a></li>
                        @endif
                        @if(config('settings.stock_report')==1 && Auth::user()->can('stock-report-view'))
                            <li class="{{ (request()->is('admin/reports/stockreport')) ? 'active' : '' }}">
                                <a
                                        href="{{ domain_route('company.admin.stockreport') }}"><i
                                            class="fa fa-circle-o"></i>Stock Report</a></li>
                        @endif
                        @if(config('settings.returns')==1 && Auth::user()->can('return-report-view'))
                            <li class="{{ (request()->is('admin/reports/returnsreport')) ? 'active' : '' }}">
                                <a
                                        href="{{ domain_route('company.admin.returnsReport') }}"><i
                                            class="fa fa-circle-o"></i>Return Report</a></li>
                        @endif
                        @if(config('settings.ageing')==1 && Auth::user()->can('ageing-view') && !getCompanyPartyTypeLevel(config('settings.company_id')) && config('settings.order_with_amt') ==0 && config('settings.accounting')==1 && Auth::user()->can('Accounting-view'))
                            <li class="{{ (request()->is('admin/reports/ageingreport')) ? 'active' : '' }}">
                                <a
                                        href="{{ domain_route('company.admin.ageingReport') }}"><i
                                            class="fa fa-circle-o"></i>Ageing Payment Report</a></li>

                            <li class="{{ (request()->is('admin/reports/ageingbreakdownreport')) ? 'active' : '' }}">
                                <a
                                        href="{{ domain_route('company.admin.ageingBreakdownReport') }}"><i
                                            class="fa fa-circle-o"></i>Ageing Payment Breakdown Report</a>
                            </li>
                        @endif

                        @if(config('settings.odometer_report')==1 && Auth::user()->can('odometer-report-view'))
                            <li class="{{ (request()->is('admin/odometer-report')) ? 'active' : '' }}">
                                <a
                                        href="{{ domain_route('company.admin.odometer.report.index') }}"><i
                                            class="fa fa-circle-o"></i>
                                    Odometer Report</a></li>
                        @endif

                    </ul>
                </li>
            @endif

            @if(config('settings.analytics_new')==1)
                @if(Auth::user()->employee->is_admin==1)
                    <li class="{{ (request()->is('admin/analytics*')) ? 'active' : '' }}">
                        <a
                            href="{{ domain_route('company.admin.analytics') }}"><i
                                class="fa fa-circle-o"></i>
                        Analytics</a></li>
                @endif
            @endif

            @if(config('settings.retailer_app')==1 && Auth::user()->can('outlet-view'))
                <li
                        class="treeview {{ request()->is('admin/outlet*') ? 'active' : '' }}">
                    <a href="#">
                        <i class="fa fa-th-large"></i>
                        <span>Outlet Settings</span>
                        <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                    </a>
                    <ul class="treeview-menu">
                        <li class="{{ (request()->is('admin/outlets-connection*')) ? 'active' : '' }}"><a
                                    href="{{ domain_route('company.admin.outlets.connection') }}"><i
                                        class="fa fa-circle-o"></i> Outlet Connection</a></li>
                        <li class="{{ (request()->is('admin/ptr-setup*')) ? 'active' : '' }}"><a
                                    href="{{ domain_route('company.admin.outlets.ptr.setup') }}"><i
                                        class="fa fa-circle-o"></i> Order Setup</a>
                        </li>
                    </ul>
                </li>
        @endif


    </section>
    <!-- /.sidebar -->
</aside>

<div class="modal fade livetrack" id="livetrackmodal">
    <div class="modal-dialog" style="width: 100%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Most Recent Locations </h4>
                <p style="color:red; font-size: 14px">*It may take up to 20 minutes for data to be displayed here.</p>
                <span id="nouser"></span>
            </div>
            <div class="modal-body">
                <div id="map" style="height:550px; width: 100%;"></div>
            </div>
        </div>
    </div>
</div>

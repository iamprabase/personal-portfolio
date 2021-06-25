<?php

namespace App\Http\Controllers\Company\Admin;

use DB;
use App;
use Auth;
use Mail;
use View;
use Excel;
use Session;
use App\Beat;
use App\Color;
use App\Order;
use App\Client;
use App\Outlet;
use App\Scheme;
use App\Company;
use App\Product;
use App\TaxType;
use App\Employee;
use App\PartyType;
use App\UnitTypes;
use Carbon\Carbon;
use App\RateDetail;
use App\OrderScheme;
use App\OrderDetails;
use App\ClientSetting;
use App\ProductVariant;
use App\UnitConversion;
use App\ModuleAttribute;
use Illuminate\Http\Request;
use App\CategoryRateTypeRate;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:order-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:order-view');
        $this->middleware('permission:order-update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:order-delete', ['only' => ['destroy']]);
        $this->middleware('permission:order-status', ['only' => ['changeDeliveryStatus']]);
    }

    private function getTimeZone($company_id)
    {
        try {
            $setting = ClientSetting::whereCompanyId($company_id)->first();
            if ($setting->time_zone) $timezone = $setting->time_zone;
            else $timezone = 'Asia/Kathmandu';

            return $timezone;
        } catch (\Exception $e) {
          Log::info(array("getTimeZone", $e->getMessage()));
            return 'Asia/Kathmandu';
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company_id = config('settings.company_id');
        // DB::statement("ALTER TABLE `orders` ADD `order_to` INT NULL AFTER `client_id`");


        $partyTypeLevel = getCompanyPartyTypeLevel($company_id);
        $nCal = config('settings.ncal');
        $allOrders = Auth::user()->handleQuery('order')->get();

        $ordersCount = $allOrders->count();
        if ($ordersCount > 0) {
            $employee_ids = $allOrders->unique('employee_id')
                ->pluck('employee_id')
                ->toArray();

            $employeesWithOrders = Auth::user()->handleQuery('employee')
                ->whereIn('id', $employee_ids)
                ->orderBy('name', 'asc')
                ->pluck('name', 'id')
                ->toArray();

            $party_ids = $allOrders->unique('client_id')
                ->pluck('client_id')
                ->toArray();

            $order_to_ids = $allOrders->unique('order_to')
                ->pluck('order_to')
                ->toArray();
            $client_handles = array();
            if (!Auth::user()->employee->is_admin == 1) {

                $client_handles = Auth::user()->handleQuery('client')->pluck('id')->toArray();
                $retailerOrders = Order::whereCompanyId($company_id)->whereIn('client_id', $client_handles)->whereEmployeeId(0)->pluck('orders.client_id')->toArray();
                if (!empty($retailerOrders)) {
                    foreach ($retailerOrders as $retailerOrder) {
                        array_push($party_ids, $retailerOrder);
                    }
                }
            }
            $partiesWithOrders = Client::whereIn('id', $party_ids)
                ->orderBy('company_name', 'asc')
                ->pluck('company_name', 'id')
                ->toArray();
            $orderToParties = Client::whereIn('id', $order_to_ids)
                ->orderBy('company_name', 'asc')
                ->pluck('company_name', 'id')
                ->toArray();
            $beatIdsWithOrders = DB::table('beat_client')->whereIn('client_id', array_keys($partiesWithOrders))->pluck('beat_id')->toArray();
            $beatsWithOrders = Beat::whereIn('id', $beatIdsWithOrders)->orderby('name', 'asc')->distinct('client_id')->pluck('name', 'id')->toArray();
            $beatsWithOrders[0] = "Unspecified";
            $order_by_outlet = Order::where('company_id', $company_id)->where('employee_id', 0)->where(function ($query) use ($client_handles) {
                if (!Auth::user()->employee->is_admin == 1) {
                    $query->whereIn('client_id', $client_handles);
                }
            })->pluck('outlet_id')->toArray();

            if (!empty($order_by_outlet)) {
                $outlet_contacts = Outlet::whereIn('id', $order_by_outlet)->pluck('contact_person', 'id')->toArray();
            } else {
                $outlet_contacts = array();
            }
        } else {
            $employee_ids = array();
            $employeesWithOrders = 0;
            $party_ids = 0;
            $partiesWithOrders = array();
            $outlet_contacts = array();
            $beatsWithOrders = array();
            $orderToParties = array();

        }

        $orderStatus = ModuleAttribute::where('company_id', $company_id)
            ->where('module_id', 1)->get();
        return view('company.orders.index', compact('company_id', 'ordersCount', 'employeesWithOrders', 'partiesWithOrders', 'orderStatus', 'outlet_contacts', 'beatsWithOrders', 'nCal', 'partyTypeLevel', 'orderToParties'));
    }

    public function updatetaxonorders()
    {
        $companies = Company::get(['id']);
        $allTaxes = TaxType::get(['id', 'company_id', 'name', 'percent']);
        foreach ($companies as $company) {
            $orders = $company->orders;
            if ($orders->count() > 0) {
                $companyId = $company->id;
                $ordersIds = $orders->pluck('id')->toArray();
                $taxes = (clone $allTaxes)->where('company_id', $companyId);
                foreach ($taxes as $tax) {
                    DB::table('tax_on_orders')->whereIn('order_id', $ordersIds)->where('tax_name', $tax->name)->where('tax_percent', $tax->percent)->update([
                        'tax_type_id' => $tax->id
                    ]);
                }
            }
        }
        die("DONE");
    }

    public function ajaxDatatable(Request $request)
    {

        // if (getClientSetting()->order_with_amt == 0) {
        //     $columns = array(
        //         0 => 'id',
        //         1 => 'order_no',
        //         2 => 'order_date',
        //         3 => 'client_id',
        //         4 => 'employee_id',
        //         5 => 'grand_total',
        //         6 => 'delivery_status',
        //         7 => 'action',
        //     );
        // } else {
        $columns = array(
            'id',
            'order_no',
            'order_date',
            'client_id',
            'employee_id',
            'orderstatus',
            'delivery_status',
            'ordered_to'
        );
        // }

        $company_id = config('settings.company_id');
        $companyName = Auth::user()->companyName($company_id)->company_name;
        $totalData = $request->orderCount;
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if ($request->input('search.value')) {
            $search = $request->input('search.value');
        }
        if ($request->empVal) {
            $empFilter = $request->input('empVal');
        }
        if ($request->partyVal) {
            $partyFilter = $request->input('partyVal');
        }
        if (!empty($request->orderTo) || $request->orderTo === '0') {
            $orderToFilter = $request->input('orderTo');
        }
        if ($request->startDate) {
            $startDateFilter = $request->input('startDate');
        }
        if ($request->endDate) {
            $endDateFilter = $request->input('endDate');
        }
        $selIds = $request->selIds;
        $statuses = $request->has('stsFilters') ? $request->stsFilters : [];
        $beats = $request->has('beat_ids') ? $request->beat_ids : [];
        if (in_array(0, $beats)) {
            $beats = array_diff($beats, array(0));
            $all_clients = Client::whereCompanyId($company_id)->pluck('id')->toArray();
            $all_beat_clients = DB::table('beat_client')->whereIn('client_id', $all_clients)->pluck('client_id')->toArray();
            $no_beat_clients = Client::whereNotIn('clients.id', $all_beat_clients)->pluck('id')->toArray();
        } else {
            $no_beat_clients = array();
        }
        if (!empty($beats)) {
            $beat_clients = DB::table('beat_client')->whereIn('beat_id', $beats)->pluck('client_id')->toArray();
        } else {
            $beat_clients = array();
        }
        if (!empty($no_beat_clients)) $beat_clients = array_merge($beat_clients, $no_beat_clients);
        if (Auth::user()->employee->is_admin == 1) {
            $query = Auth::user()->handleQuery('order');
        } else {
            $allOrders = Auth::user()->handleQuery('order')->get(['orders.id']);
            $employeeHandledOrders = $allOrders
                ->pluck('id')
                ->toArray();
            $client_handles = Auth::user()->handleQuery('client')->pluck('id')->toArray();
            $retailerOrders = Order::whereCompanyId($company_id)->whereIn('client_id', $client_handles)->whereEmployeeId(0)->pluck('orders.id')->toArray();
            if (!empty($retailerOrders)) {
                foreach ($retailerOrders as $retailerOrder) {
                    array_push($employeeHandledOrders, $retailerOrder);
                }
            }
            $query = Order::whereIn('orders.id', $employeeHandledOrders);
        }

        $getOrders = $query
            ->leftJoin('employees', 'orders.employee_id', 'employees.id')
            ->leftJoin('clients', 'orders.client_id', 'clients.id')
            ->leftJoin('clients as c', 'orders.order_to', 'c.id')
            ->leftJoin('client_settings', 'orders.company_id', 'client_settings.company_id')
            ->leftJoin('module_attributes', 'orders.delivery_status_id', 'module_attributes.id')
            ->whereIn('orders.delivery_status_id', $statuses)
            ->where(function ($query) use ($beat_clients) {
                if (!empty($beat_clients)) $query->whereIn('orders.client_id', $beat_clients);
            })
            ->select('orders.*', 'employees.name as employee_name', 'clients.company_name as client_name', 'client_settings.order_prefix', 'clients.opening_balance', 'clients.credit_limit', 'module_attributes.id as moduleattributesId', 'module_attributes.title as status_name', 'module_attributes.color', 'module_attributes.order_amt_flag', 'module_attributes.order_edit_flag', 'module_attributes.order_delete_flag', 'c.company_name as order_to_company_name');
        if ($request->input('search.value')) {
            $getOrders = $getOrders->where(function ($query) use ($search) {
                $query->orWhere('employees.name', 'LIKE', "%{$search}%");
                $query->orWhere('clients.company_name', 'LIKE', "%{$search}%");
                $query->orWhere('module_attributes.title', 'LIKE', "%{$search}%");
                $query->orWhere('orders.grand_total', 'LIKE', "%{$search}%");
                $query->orWhere('orders.order_date', 'LIKE', "%{$search}%");
                $query->orWhere('orders.order_no', 'LIKE', "%{$search}%");
                $query->orWhere(\DB::raw("Concat(client_settings.order_prefix ,orders.order_no)"), 'LIKE', "%{$search}%");
            });
        }
        if ($request->empVal) {
            // $getOrders = $getOrders->where('orders.employee_id',$empFilter);
            if ($request->outlet_search == 1) {
                $getOrders = $getOrders->where('orders.employee_id', $empFilter);
            } elseif ($request->outlet_search == 0) {
                $getOrders = $getOrders->where('orders.employee_id', 0)->where('orders.outlet_id', $empFilter);
            }
        }
        if ($request->partyVal) {
            $getOrders = $getOrders->where('orders.client_id', $partyFilter);
        }
        if (!empty($request->orderTo) || $request->orderTo === '0') {
            if ($request->orderTo) $getOrders = $getOrders->where('orders.order_to', $orderToFilter);
            else $getOrders = $getOrders->whereNULL('orders.order_to');
        }
        $getOrders = $getOrders->whereBetween('orders.order_date', [$startDateFilter, $endDateFilter]);

        $totalFiltered = (clone $getOrders)->count();
        if ($order == 'order_date') $getOrders = $getOrders->orderBy('orders.id', $dir);
        $orders = (clone $getOrders)->orderBy($order, $dir)->offset($start)
            ->limit($limit)->get();
        $total = (clone $getOrders)->sum('grand_total');
        $data = array();
        $selectThisPageCheckBox = true;
        if ($orders->first()) {
            $i = $start;
            $viewable_clients = Auth::user()->handleQuery('client')->pluck('id')->toArray();
            foreach ($orders as $order) {
                $show = domain_route('company.admin.order.show', [$order->id]);
                $edit = domain_route('company.admin.order.edit', [$order->id]);
                $delete = domain_route('company.admin.order.destroy', [$order->id]);
                $clientShow = in_array($order->client_id, $viewable_clients) && Auth::user()->can('party-view') ? domain_route('company.admin.client.show', [$order->client_id]) : null;
                $order_to_show = in_array($order->order_to, $viewable_clients) && Auth::user()->can('party-view') ? domain_route('company.admin.client.show', [$order->order_to]) : null;
                $partyName = $order->client_name;
                $orderToName = $order->order_to ? $order->order_to_company_name : $companyName;
                $checked = "";
                if (in_array($order->id, explode(',', $selIds))) $checked = "checked";
                else $selectThisPageCheckBox = false;

                if ($order->employee_id != 0) {
                    $empShow = domain_route('company.admin.employee.show', [$order->employee_id]);
                    $empName = ucfirst(strtolower(getEmployee($order->employee_id)['name']));
                    $empSection = "<a href='{$empShow}' datasalesman='{$empName}'> {$empName}</a>";
                } else {
                    $outlet_details = $order->outlets()->withTrashed()->first();
                    $empName = $outlet_details ? ucfirst($outlet_details->contact_person) : NULL;
                    $imgSrc = URL::asset('assets/dist/img/ret_logo.png');
                    $empSection = "<span><img src=$imgSrc></img>&nbsp;{$empName}</span>";
                }
                $orderNo = getClientSetting()->order_prefix . $order->order_no;
                $orderIdSpanTag = "<input type='checkbox' class='orderStatusCheckBox' name='update_order_status' value='{$order->id}' data-order_no='{$orderNo}' {$checked}>";
                $nestedData['id'] = $orderIdSpanTag . ++$i . ".";
                $nestedData['orderno'] = "<a href='{$show}'>$orderNo</a>";
                $nestedData['orderdate'] = getDeltaDate($order->order_date);

                if (config('settings.accounting') == 1 && Auth::user()->can('Accounting-view')) {
                    // code for tooltip party order outstanding amount
                    $getOrderStatusFlag = ModuleAttribute::where('company_id', $company_id)->where('module_id', 1)->where('order_amt_flag', 1)->pluck('id')->toArray();
                    if (!empty($getOrderStatusFlag)) {
                        $partyOrders = Order::select('id', 'delivery_status_id', 'grand_total')
                            ->where('client_id', $order->client_id)
                            ->orderBy('created_at', 'desc')
                            ->get();
                        $tot_order_amount = $partyOrders->whereIn('delivery_status_id', $getOrderStatusFlag)->sum('grand_total');
                    } else {
                        $tot_order_amount = 0;
                    }
                    $collections = Auth::user()->handleQuery('collection')->where('client_id', $order->client_id)
                        ->orderBy('created_at', 'desc')
                        ->get();
                    $cheque_collection_amount = $collections->where('payment_method', 'Cheque')->where('payment_status', 'Cleared')->sum('payment_received');
                    $cash_collection_amount = $collections->where('payment_method', 'Cash')->sum('payment_received');
                    $bank_collection_amount = $collections->where('payment_method', 'Bank Transfer')->sum('payment_received');
                    $tot_collection_amount = $cheque_collection_amount + $cash_collection_amount + $bank_collection_amount;

                    $tooltipData = 'Current Outstanding Amount: ' . config('settings.currency_symbol') . ' ' . number_format(($order->opening_balance + $tot_order_amount - $tot_collection_amount), 2);
                    if (isset($order->credit_limit)) {
                        $tooltipData = $tooltipData . ", Credit Limit: " . config('settings.currency_symbol') . ' ' . $order->credit_limit;
                    } else {
                        $tooltipData = $tooltipData . ", Credit Limit: " . config('settings.currency_symbol') . ' 0';

                    }
                    // End tooltip party order outstanding amount section

                    $nestedData['partyname'] = "<a class='tips clientLinks' href='{$clientShow}' data-viewable='{$clientShow}' data-html='true' data-toggle='tooltip' title='{$tooltipData}' dataparty='{$partyName}'>{$partyName}</a>";
                    $nestedData['ordered_to'] = $order->order_to ? "<a class='tips clientLinks' href='{$order_to_show}' data-viewable='{$order_to_show}' data-html='true' data-toggle='tooltip' title='' dataparty='{$orderToName}'>{$orderToName}</a>" : "<span>{$orderToName}</span>";
                } else {
                    $nestedData['partyname'] = "<a class='clientLinks' href='{$clientShow}' data-viewable='{$clientShow}' dataparty='{$partyName}'>{$partyName}</a>";
                    $nestedData['ordered_to'] = $order->order_to ? "<a class='clientLinks' href='{$order_to_show}' data-viewable='{$order_to_show}' dataparty='{$orderToName}'>{$orderToName}</a>" : "<span>{$orderToName}</span>";
                }

                $nestedData['createdby'] = $empSection;

                if (getClientSetting()->order_with_amt == 0) {
                    $nestedData['grandtotal'] = config('settings.currency_symbol') . ' ' . number_format((float)$order->grand_total, 2);
                }

                $spanTag = NULL;
                if ($order->status_name) {
                    $orderStatus = $order->status_name;
                    $statusColor = $order->color;
                } else {
                    $orderStatus = $order->delivery_status;
                    $statusColor = '#5555ee';
                }
                $spanTag = "<span class='label' style='background: {$statusColor};'>{$orderStatus}</span>";
                $delivery_date = isset($order->delivery_date) ? getDeltaDateFormat($order->delivery_date) : null;
                if (Auth::user()->can('order-status'))
                    $nestedData['orderstatus'] = "<a href='#' class='edit-modal' data-id='{$order->id}'
          data-status='{$order->delivery_status_id}' data-orderdate='{$delivery_date}' data-nodate='{$delivery_date}' data-note='{$order->delivery_note}' data-place='{$order->delivery_place}' data-transport_name='{$order->transport_name}' data-transport_number='{$order->transport_number}' data-billty_number='{$order->billty_number}' data-include_delivery_details='{$order->include_delivery_details}'>$spanTag";
                else
                    $nestedData['orderstatus'] = "<a href='#' class='alert-modal' data-id='{$order->id}'
          data-status='{$order->delivery_status_id}' data-orderdate='{$delivery_date}' data-nodate='{$delivery_date}' data-note='{$order->delivery_note}' data-place='{$order->delivery_place}' data-transport_name='{$order->transport_name}' data-transport_number='{$order->transport_number}' data-billty_number='{$order->billty_number}'>$spanTag";
                $nestedData['action'] = "<a href='{$show}' class='btn btn-success btn-sm'
            style='padding: 3px 6px;'><i class='fa fa-eye'></i></a>";

                if ($order->order_edit_flag == 1 && Auth::user()->can('order-update')) {
                    $nestedData['action'] = $nestedData['action'] . "<a href='{$edit}' class='btn btn-warning btn-sm'
            style='padding: 3px 6px;'><i class='fa fa-edit'></i></a>";
                }
                if ($order->order_delete_flag == 1 && Auth::user()->can('order-delete')) {
                    $nestedData['action'] = $nestedData['action'] . "<a class='btn btn-danger btn-sm delete' data-mid='{ $order->id }' data-url='{$delete}' data-toggle='modal' data-target='#delete' style='padding: 3px 6px;'><i class='fa fa-trash-o'></i></a>";
                }

                $data[] = $nestedData;
            }
        } else {
            $selectThisPageCheckBox = false;
        }

        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
            "total" => config('settings.currency_symbol') . ' ' . number_format((float)$total, 2),
            "prevSelVal" => $selIds,
            "selectThisPageCheckBox" => $selectThisPageCheckBox
        );

        return json_encode($json_data);
    }

    public function customPdfExport(Request $request)
    {
        $orderwithQTYAMT = config('settings.order_with_amt');
        ini_set('max_execution_time', 300);
        $partyTypeLevel = getCompanyPartyTypeLevel(config('settings.company_id'));
        $getExportData = json_decode($request->exportedData)->data;
        $pageTitle = $request->pageTitle;
        $columns = json_decode($request->columns);
        $properties = json_decode($request->properties);
        set_time_limit(300);
        ini_set("memory_limit", "256M");
        $pdf = PDF::loadView('company.orders.exportpdf', compact('getExportData', 'pageTitle', 'partyTypeLevel', 'orderwithQTYAMT', 'properties', 'columns'))->setPaper('a4', 'portrait');
        unset($getExportData);
        return $pdf->download($pageTitle . '.pdf');
    }

    public function getEmployeeRecords($emp, $start, $limit, $order, $dir)
    {
        $company_id = config('settings.company_id');
        // $fetchOrders = Order::where('company_id', $company_id)
        //                 ->where('employee_id', $emp)->orderBy($order,$dir);
        $fetchOrders = Auth::user()->handleQuery('order')
            ->where('employee_id', $emp)->orderBy($order, $dir);

        $empRecords['filtered'] = $fetchOrders->count();
        $empRecords['records'] = $fetchOrders->offset($start)
            ->limit($limit)
            ->get();
        $empRecords['total'] = $fetchOrders->sum('grand_total');

        return $empRecords;
    }

    public function getPartyRecords($party, $start, $limit, $order, $dir)
    {
        $company_id = config('settings.company_id');
        // $fetchOrders = Order::where('company_id', $company_id)
        //                 ->where('client_id', $party)->orderBy($order,$dir);
        $fetchOrders = Auth::user()->handleQuery('order')
            ->where('client_id', $party)->orderBy($order, $dir);

        $partyRecords['filtered'] = $fetchOrders->count();
        $partyRecords['records'] = $fetchOrders
            ->offset($start)
            ->limit($limit)
            ->get();

        $partyRecords['total'] = $fetchOrders->sum('grand_total');

        return $partyRecords;
    }

    public function getEmpPartyRecords($emp, $party, $start, $limit, $order, $dir)
    {
        $company_id = config('settings.company_id');

        $empPartyOrders = Auth::user()->handleQuery('order')
            ->where('employee_id', $emp)
            ->where('client_id', $party)
            ->orderBy($order, $dir);

        $empPartyRecords['filtered'] = $empPartyOrders->count();
        $empPartyRecords['records'] = $empPartyOrders
            ->offset($start)
            ->limit($limit)
            ->get();
        $empPartyRecords['total'] = $empPartyOrders->sum('grand_total');

        return $empPartyRecords;
    }

    public function getOrderRecordsByDate($startDate, $endDate)
    {
        $company_id = config('settings.company_id');
        if ($startDate == $endDate) {
            $empDateRecords = Auth::user()->handleQuery('order')
                ->where('order_date', '=', $startDate);
        } else {
            $empDateRecords = Auth::user()->handleQuery('order')
                ->whereBetween('order_date', [$startDate, $endDate]);
        }
        // Log::info('info', array("empDateRecords" => print_r($empDateRecords, true)));
        return $empDateRecords;
    }

    public function create()
    {
        $company_id = config('settings.company_id');
        $partyTypeLevel = getCompanyPartyTypeLevel($company_id);

        $getClientSetting = getClientSetting();
        $allClients = Client::where('status', 'Active')->whereCompanyId($company_id)->orderBy('company_name', 'asc')->get(['company_name', 'id', 'rate_id', 'client_type', 'superior'])->toArray();
        $clients = Auth::user()->handleQuery('client')->where('status', 'Active')->orderBy('company_name', 'asc')->get(['company_name', 'id', 'rate_id', 'client_type', 'superior']);

        if(config('settings.category_wise_rate_setup') == 1) {
          $clients->load(['appliedcategoryrates'=> function($query){
            $query->select('id', 'category_id');
          }]);
        }

        $clients = $clients->toArray();

        $products = Product::with('product_variants')->where('products.company_id', $company_id)
            ->where('products.status', 'Active')
            ->orderBy('products.star_product', 'desc')
            ->orderBy('product_name', 'asc')
            ->get(['products.*']);
        
          $colors = Color::pluck('name', 'value')->toArray();

        $taxes = TaxType::where('company_id', $company_id)->select(DB::raw("CONCAT(name,' (',percent,'%)') as name, id, percent, default_flag"))->get();
        if ($taxes->first()) {
            $tax_types = $taxes->pluck('name', 'id')->toArray();
            $tax_percents = $taxes->pluck('percent', 'id')->toArray();
            $default_taxes = $taxes->where('default_flag', 1)->pluck('id')->toArray();
            $getAllDefault_taxes = $taxes->toArray();
            return view('company.orders.create', compact('allClients', 'clients', 'getClientSetting', 'products', 'taxes', 'tax_types', 'tax_percents', 'default_taxes', 'getAllDefault_taxes', 'partyTypeLevel', 'colors'));
        } else {
            $tax_types = array();
            $tax_percents = array();
            $default_taxes = array();
            $getAllDefault_taxes = array();
            return view('company.orders.create', compact('allClients', 'clients', 'getClientSetting', 'products', 'taxes', 'tax_types', 'tax_percents', 'default_taxes', 'getAllDefault_taxes', 'partyTypeLevel', 'colors'));
        }
    }

    public function orderTo(Request $request)
    {
        $client_type = $request->client_type;
        try {
            $getParentPartyTypes = PartyType::partyTypeParents($client_type, array());
            if (count($getParentPartyTypes) > 0) $parties = Client::with('partytypes')->whereStatus('Active')->whereIn('client_type', $getParentPartyTypes)->orWhere(function ($query) use ($request) {
                if ($request->has('order_to')) {
                    $query->where('clients.id', $request->order_to);
                }
            })->orderBy('client_type', 'asc')->orderBy('company_name', 'asc')->get(['company_name', 'id', 'rate_id', 'client_type', 'superior'])->toArray();
            else $parties = array();
            $supParties = array();

            foreach ($parties as $party) {
                if (!isset($supParties[$party['partytypes']['name']])) $supParties[$party['partytypes']['name']] = array();
                array_push($supParties[$party['partytypes']['name']], $party);
            }
            return response()->json([
                'data' => $supParties,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'data' => $e->getMessage(),
            ], 400);
        }
        // $client = Client::find($request->client_id);
        // $partySuper = null;
        // $partySuper = PartyType::find($client->client_type);
        // $data = array();
        // $pt = null;
        // $select=null;
        // $c = null;
        // $af = null;
        // $twoLevel = null;
        // if($client->superior)
        // {
        //   $checkSup = Client::where('id',$client->superior)->get();
        //   foreach ($checkSup as $item) {
        //       $pt = '"<option value="'. $item->id .'" selected>' . $item->company_name . '</option>"';
        //   }
        //   $pt .='<option disabled>Select Order To</option>';
        // }else{
        //   $pt .= '<option disabled>Select Order To</option>';
        // }

        // if($client->superior!=null && $partySuper->parent_id!=0) {
        //   if ($client->client_type != 0) {
        //     $getParentPartyTypes = PartyType::partyTypeParents($client->client_type, array());
        //     $c = $getParentPartyTypes;
        //     $twoLevel = array_slice($getParentPartyTypes, 0, 3);
        //     $af = $partySuper->parent_id;
        //     if ($getParentPartyTypes){
        //       $data = Client::whereCompanyId($client->company_id)
        //           ->whereIn('client_type', $twoLevel)
        //           ->get();
        //       foreach ($data as $item) {
        //         $pt .= '"<option value="' . $item->id . '" ' . $select . '>' . $item->company_name . '</option>"';
        //       }
        //     } else {
        //       $data = Client::whereCompanyId($client->company_id)
        //           ->where('client_type', 0)
        //           ->get();
        //       foreach ($data as $item) {
        //         $pt .= '"<option value="' . $item->id . '">' . $item->company_name . '</option>"';
        //       }
        //     }
        //   } else {
        //     $pt = '<option selected value="" disabled>Sona tech</option>';
        //   }
        // }else{
        //   $pt = '<option selected value="" disabled>Sona tech</option>';
        // }
        // return response()->json([
        //     'data' => $data, 'pt' => $pt,'c' => $partySuper,'af' =>$af
        // ], 200);
    }

    public function getRelatedUnits(Request $request)
    {
        $unit_id = (int)$request->unit_id;
        $visited_nodes = array($unit_id);
        $product_id = $request->product_id;
        try {
            $instance = Product::find($product_id)->conversions;
            $allInstances = $instance->toArray();
            $conversions = array();
            $instances = array();
            foreach ($allInstances as $conversion) {
                if ($conversion['unit_type_id'] == $unit_id) {
                    array_push($conversions, $conversion['converted_unit_type_id']);
                } elseif ($conversion['converted_unit_type_id'] == $unit_id) {
                    array_push($conversions, $conversion['unit_type_id']);
                }
                array_push($instances, $conversion['id']);
            }

            // $instances = $instance->pluck('id')->toArray();
            // $conversions = UnitRelation::whereIn('unit_conversion_id', $instances)->where('unit_type_id', $unit_id)->where('related_unit_type_id', '<>', $unit_id)->pluck('related_unit_type_id')->toArray();
            if (!empty($conversions)) {
                $get_all_related_conversion = $this->getrelatedConversion($conversions, $visited_nodes, $instances);
                $unit_options = UnitTypes::whereIn('id', $get_all_related_conversion)->pluck('name', 'id')->toArray();
            } else {
                $unit_options = UnitTypes::whereIn('id', $visited_nodes)->pluck('name', 'id')->toArray();
            }
        } catch (\Exception $e) {
            Log::error(array("Conversion Function:- ", $e->getMessage()));
            $unit_options = UnitTypes::whereIn('id', $visited_nodes)->pluck('name', 'id')->toArray();
        }

        return json_encode($unit_options, true);
    }

    private function getrelatedConversion($conversions, $nodes_visited, $relations)
    {
        $visited_nodes = $nodes_visited;
        foreach ($conversions as $conversion) {
            if (!in_array($conversion, $visited_nodes)) {
                array_push($visited_nodes, $conversion);
                $get_related_conversions = array();
                // $get_related_conversions = UnitRelation::where('unit_type_id', $conversion)
                // ->whereIn('unit_conversion_id', $relations)
                // ->where('related_unit_type_id', '<>', $conversion)
                // ->whereNotIn('related_unit_type_id', $visited_nodes)
                // ->pluck('related_unit_type_id')
                // ->toArray();

                $allRelatedConversions = UnitConversion::whereIn('id', $relations)
                    ->where(function ($query) use ($conversion) {
                        $query->orWhere('unit_type_id', $conversion);
                        $query->orWhere('converted_unit_type_id', $conversion);
                    })
                    ->get(['unit_type_id', 'converted_unit_type_id'])
                    ->toArray();
                if (!empty($allRelatedConversions)) {
                    foreach ($allRelatedConversions as $allRelatedConversion) {
                        if (!in_array($allRelatedConversion['unit_type_id'], $get_related_conversions) && !in_array($allRelatedConversion['unit_type_id'], $visited_nodes)) {
                            array_push($get_related_conversions, $allRelatedConversion['unit_type_id']);
                        }
                        if (!in_array($allRelatedConversion['converted_unit_type_id'], $get_related_conversions) && !in_array($allRelatedConversion['converted_unit_type_id'], $visited_nodes)) {
                            array_push($get_related_conversions, $allRelatedConversion['converted_unit_type_id']);
                        }
                    }
                }


                if (!empty($get_related_conversions))
                    $visited_nodes = $this->getrelatedConversion($get_related_conversions, $visited_nodes, $relations);
            }
        }
        return $visited_nodes;
    }

    public function getUnitMrp(Request $request)
    {
        $company_id = config('settings.company_id');
        $product_id = (int)$request->product_id;
        $prod_unit = (int)$request->originalUnit;
        $unit_id = (int)$request->selUnit;
        $instance = Product::findOrFail($product_id)->conversions->pluck('id')->toArray();
        $conversion_instances = UnitConversion::where('company_id', $company_id)->whereIn('id', $instance)->whereNull('deleted_at')->orderBy('unit_type_id', 'asc')->get(['quantity', 'unit_type_id', 'converted_quantity', 'converted_unit_type_id'])->toArray();
        $returnVal = 1;
        if (!empty($conversion_instances)) {
            foreach ($conversion_instances as $conversion_instance) {
                if ($conversion_instance['unit_type_id'] == $prod_unit && $conversion_instance['converted_unit_type_id'] == $unit_id) {
                    $multiplication_factor = floatval($conversion_instance['quantity'] / $conversion_instance['converted_quantity']);
                    $returnVal = $multiplication_factor;

                    return $returnVal;
                } elseif ($conversion_instance['unit_type_id'] == $unit_id && $conversion_instance['converted_unit_type_id'] == $prod_unit) {
                    $multiplication_factor = floatval($conversion_instance['converted_quantity'] / $conversion_instance['quantity']);
                    $returnVal = $multiplication_factor;

                    return $returnVal;
                }
            }

            $this_unit_conversions = array_filter($conversion_instances, function ($instance) use ($prod_unit, $unit_id) {
                return $instance['unit_type_id'] == $unit_id || $instance['converted_unit_type_id'] == $unit_id;
            });
            if (!empty($this_unit_conversions)) {
                foreach ($this_unit_conversions as $key => $this_unit_conversion) {
                    $factor = 1;
                    $foundVal = array();
                    $foundVal['found'] = false;
                    $foundVal['factor'] = 1;
                    $returnFactor = $this->getMultiplicationFactor($prod_unit, $unit_id, $conversion_instances, $factor, $foundVal);
                    if ($returnFactor['found']) {
                        return floatval($returnFactor['factor']);
                    }
                    unset($conversion_instances[$key]);
                }
            }
            // foreach($conversion_instances as $conversion_instance){
            // if($conversion_instance['unit_type_id']==$prod_unit && $conversion_instance['converted_unit_type_id']==$unit_id){
            //   $multiplication_factor = floatval($conversion_instance['converted_quantity']/$conversion_instance['quantity']);
            //   $returnVal = $multiplication_factor;

            //   return $returnVal;
            // }elseif($conversion_instance['unit_type_id']==$unit_id && $conversion_instance['converted_unit_type_id']==$prod_unit){
            //   $multiplication_factor = floatval($conversion_instance['quantity']/$conversion_instance['converted_quantity']);
            //   $returnVal = $multiplication_factor;

            //   return $returnVal;
            // }else
            // if($conversion_instance['unit_type_id']==$unit_id && $conversion_instance['converted_unit_type_id']!=$prod_unit || $conversion_instance['unit_type_id']==$prod_unit && $conversion_instance['converted_unit_type_id']!=$unit_id || $conversion_instance['unit_type_id']!=$unit_id && $conversion_instance['converted_unit_type_id']==$prod_unit || $conversion_instance['unit_type_id']!=$prod_unit && $conversion_instance['converted_unit_type_id']==$unit_id){
            // $multiplication_factor = $this->getMultiplicationFactor($prod_unit, $unit_id, $conversion_instances);
            // return $multiplication_factor;
            // }
            // }
        }
    }

    private function getMultiplicationFactor($prod_unit, $unit_id, $conversion_instances, $factor, $foundVal)
    {
        foreach ($conversion_instances as $key => $conversion_instance) {
            if ($conversion_instance['unit_type_id'] == $unit_id) {
                $conversion = floatval($factor * ($conversion_instance['converted_quantity'] / $conversion_instance['quantity']));
                $related_unit = $conversion_instance['converted_unit_type_id'];
                if ($related_unit == $prod_unit) {
                    $foundVal['found'] = true;
                    $foundVal['factor'] = $conversion;
                    return $foundVal;
                }
                unset($conversion_instances[$key]);
                $this_unit_conversions = array_filter($conversion_instances, function ($instance) use ($related_unit) {
                    return $instance['unit_type_id'] == $related_unit || $instance['converted_unit_type_id'] == $related_unit;
                });
                // Log::info($this_unit_conversions);

                foreach ($this_unit_conversions as $this_unit_conversion) {
                    return $this->getMultiplicationFactor($prod_unit, $related_unit, $conversion_instances, $conversion, $foundVal);
                }
                // return $this->getMultiplicationFactor($prod_unit, $related_unit, $conversion_instances, $conversion, $foundVal);
            } elseif ($conversion_instance['converted_unit_type_id'] == $unit_id) {
                $conversion = floatval($factor * ($conversion_instance['quantity'] / $conversion_instance['converted_quantity']));
                $related_unit = $conversion_instance['unit_type_id'];
                if ($related_unit == $prod_unit) {
                    $foundVal['found'] = true;
                    $foundVal['factor'] = $conversion;
                    return $foundVal;
                }
                unset($conversion_instances[$key]);
                $this_unit_conversions = array_filter($conversion_instances, function ($instance) use ($related_unit) {
                    return $instance['unit_type_id'] == $related_unit || $instance['converted_unit_type_id'] == $related_unit;
                });
                // Log::info($this_unit_conversions);
                foreach ($this_unit_conversions as $this_unit_conversion) {
                    return $this->getMultiplicationFactor($prod_unit, $related_unit, $conversion_instances, $conversion, $foundVal);
                }
            }
        }

        return $foundVal;
    }

    private function getChildrenFactor($factor, $children_unit, $prod_unit, $unit_id, $conversion_instances)
    {

        foreach ($conversion_instances as $key => $children_conversion) {
            if ($children_conversion['unit_type_id'] == $children_unit) {

                $factor = floatval($children_conversion['quantity'] / $children_conversion['converted_quantity']) * floatval(1 / $factor);
                $children_unit = $children_conversion['converted_unit_type_id'];
                unset($conversion_instances[$key]);

                if ($children_conversion['converted_unit_type_id'] == $unit_id) return $factor;
                else $this->getChildrenFactor($factor, $children_unit, $prod_unit, $unit_id, $conversion_instances);

            } elseif ($children_conversion['converted_unit_type_id'] == $children_unit) {

                $factor = floatval($children_conversion['converted_quantity'] / $children_conversion['quantity']) * floatval(1 / $factor);
                $children_unit = $children_conversion['unit_type_id'];
                unset($conversion_instances[$key]);

                if ($children_conversion['unit_type_id'] == $unit_id) return $factor;
                else $this->getChildrenFactor($factor, $children_unit, $prod_unit, $unit_id, $conversion_instances);
            }
        }

    }

    public function suffCreditLimit(Request $request)
    {
        $customMessages = [
            'id.required' => 'The Party Name field is required.',
        ];
        $this->validate($request, [
            'id' => 'required',
        ], $customMessages);
        $client = Client::find($request->id);
        $grand_total = $request->grand_total;
        $order_id = $request->order_id;
        $order_with_qty_amt_settings = config('settings.order_with_amt');
        $order_with_qty_amt = $order_with_qty_amt_settings ? $order_with_qty_amt_settings : 0;
        $accounting_settings = config('settings.accounting');
        $accounting = $accounting_settings ? $accounting_settings : 0;
        $allow_order_above_credit_limit = config('settings.order_above_credit_limit');
        try {
            $outstading_amount = $this->getOutstandingAmount($client, $order_id);
            $client->credit_limit = $client->credit_limit ? $client->credit_limit : 0;

            if ($client->credit_limit && $client->credit_limit > 0 && $allow_order_above_credit_limit == 0) {
                if (($client->credit_limit - ($outstading_amount + $grand_total)) < 0 && $order_with_qty_amt != 1 && $accounting == 1) {
                    $response = array("success" => false, "msg" => $request->msg);
                    return response()->json($response);
                    // redirect()->route('company.admin.order', ['domain' => domain()])->with('warning', 'Insufficient credit limit. Orders cannot be placed.');
                } else {
                    $response = array("success" => true, "msg" => "Success");
                    return response()->json($response);
                }
            } else {
                $response = array("success" => true, "msg" => "Success");
                return response()->json($response);
            }
        } catch (\Exception $e) {
            Log::error(array("suffCreditLimit Func", $e->getMessage(), $e->getCode()));
            $response = array("success" => false, "msg" => "Some Error occured.");
            return response()->json($response);
        }
    }

    private function getOutstandingAmount($client, $orderId = null)
    {
        try {
            $outstanding_amt_calculation = DB::table('client_settings')->whereCompanyId($client->company_id)->first()->outstanding_amt_calculation;
            if($outstanding_amt_calculation == 1){
              $outstading_amount = $client->due_amount;
            }else{

              $orders = $client->orders;
              $getOrderStatusFlag = ModuleAttribute::where('company_id', $client->company_id)->where('module_id', 1)->where('order_amt_flag', 1)->pluck('id')->toArray();
              if (!empty($getOrderStatusFlag)) {
                  if ($orderId) {
                      $tot_order_amount = $orders->whereIn('delivery_status_id', $getOrderStatusFlag)->where('id', '<>', $orderId)->sum('grand_total');
                  } else {
                      $tot_order_amount = $orders->whereIn('delivery_status_id', $getOrderStatusFlag)->sum('grand_total');
                  }
              } else {
                  $tot_order_amount = 0;
              }

              $collections = $client->collections;
              if ($collections) {
                  $cheque_collection_amount = $collections->where('payment_method', 'Cheque')->where('payment_status', 'Cleared')->sum('payment_received');
                  $cash_collection_amount = $collections->where('payment_method', 'Cash')->sum('payment_received');
                  $bank_collection_amount = $collections->where('payment_method', 'Bank Transfer')->sum('payment_received');
              } else {
                  $cheque_collection_amount = 0;
                  $cash_collection_amount = 0;
                  $bank_collection_amount = 0;
              }
              $tot_collection_amount = $cheque_collection_amount + $cash_collection_amount + $bank_collection_amount;

              $outstading_amount = $client->opening_balance + $tot_order_amount - $tot_collection_amount;
            }

            return $outstading_amount;
        } catch (\Exception $e) {
          Log::error(array("getOutstandingAmount Func", $e->getMessage, $e->getCode()));
            return 0;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $customMessages = [
            'client_id.required' => 'The Party Name field is required.',
            'order_date.required' => 'Order Date Field is required',
            'product_id.*.required' => 'Product Name field is required.',
            'discount.not_regex' => 'Discount cannot be zero.',
            'uid.*.required' => "Unit is required"
        ];

        $this->validate($request, [
            'client_id' => 'required',
            'order_date' => 'required',
            'product_id.*' => 'required',
            'discount' => (getClientSetting()->non_zero_discount == 1) ? 'sometimes|required|not_regex:/^[0]+$/' : 'nullable',
            'uid.*' => 'required'
        ], $customMessages);
        $company_id = config('settings.company_id');

        try {
            DB::beginTransaction();
            $order = new Order;
            $order->company_id = $company_id;
            $order->employee_id = Auth::user()->EmployeeId();
            $order->client_id = $request->get('client_id');
            $order->order_to = $request->get('orderTo');
            if (isset($request->englishDate)) {
                $order->order_date = $request->get('englishDate');
            } else {
                $order->order_date = $request->get('order_date');
            }
            $credit_days = Client::find($request->client_id)->credit_days ?? config('settings.credit_days');
            $order->due_date = Carbon::parse($order->order_date)->addDays($credit_days)->format('Y-m-d');
            $order->order_datetime = date('Y-m-d H:i:s');
            $order->order_note = $request->get('order_note');
            if (getClientSetting()->product_level_tax == 1) {
                $order->tax = $request->get('tax');
                $order->product_level_tax_flag = 1;
            } else {
                $order->tax = $request->has('tax') ? array_sum($request->get('tax')) : null;
            }
            $order->tot_amount = (getClientSetting()->order_with_amt == 0) ? $request->get('subtotal') : array_sum($request->get('amount'));
            $order->discount = $request->get('discount');
            if (getClientSetting()->product_level_discount == 1) {
                $order->product_level_discount_flag = 1;
                $order->discount_type = "Amt";
            } else {
                $order->discount_type = $request->get('discount_type');
            }
            $order->grand_total = (getClientSetting()->order_with_amt == 0) ? $request->get('grand_total') : array_sum($request->get('amount'));

            $module_attribute = ModuleAttribute::where('company_id', $company_id)->where('title', 'Pending')->first();
            $order->delivery_status_id = $module_attribute ? $module_attribute->id : NULL;
            $o_no = getOrderNo($company_id);
            $order->order_no = $o_no;
            $order_location = null;//$this->getOrderLocation();
            if (isset($order_location)) {
                $latLong = explode(',', $order_location->loc);
                $latitude = $latLong[0];
                $longitude = $latLong[1];
                if (isset($latitude)) $order->latitude = $latitude;
                if (isset($longitude)) $order->longitude = $longitude;
            }

            if ($order->client_id) {
                $client = Client::find($order->client_id);

                if ($client) {
                    $credit_days = $client->credit_days;

                }
            }
            $saved = $order->save();

            $schemeResponse = array();

            if (isset($request->scheme_id)) {
                $scheme_id = explode(',', $request->scheme_id);
                $scheme_discount = explode(',', $request->scheme_discount);
                $scheme_freeItems = explode(',', $request->scheme_freeItems);

                foreach ($scheme_id as $key => $id) {
                    $order_scheme = new OrderScheme;
                    $order_scheme->company_id = $company_id;
                    $order_scheme->scheme_id = $id;
                    $order_scheme->is_amount = ((empty($scheme_freeItems[$key]))) ? 1 : 0;
                    $order_scheme->discount_amount = $scheme_discount[$key];
                    $order_scheme->free_item = $scheme_freeItems[$key];
                    $order_scheme->order_id = $order->id;
                    $scheme = Scheme::find($id);
                    $order_scheme->product_id = isset($scheme->offered_product) ? $scheme->offered_product : null;
                    $order_scheme->variant_id = isset($scheme->offered_product_variant) ? $scheme->offered_product_variant : null;
                    $order_scheme->save();
                    $schemeResponse[] = $order_scheme->refresh();
                }
            }
            if ($saved && getClientSetting()->product_level_tax == 0) {
                if ($request->has('tax')) {
                    $tax_percents = $request->get('tax_percents');
                    $tax_type_ids = $request->get('tax_type_id');
                    $taxes = [];
                    if (!empty($tax_type_ids)) {
                        foreach ($tax_type_ids as $index => $tax_type) {
                            $taxes[] = [
                                'order_id' => $order->id,
                                'tax_type_id' => $tax_type,
                                'tax_percent' => $tax_percents[$index]
                            ];
                        }
                    }
                    DB::table('tax_on_orders')->insert($taxes);
                }
            }

            $orderProducts = [];
            $products = $request->get('product_id');
            $orderproductsIds = $request->get('orderproductsId');
            $product_name = $request->get('product_name');
            $product_variant = $request->has('product_variant') ? $request->get('product_variant') : [];
            $product_variant_name = $request->get('product_variant_name');
            $product_variant_colors = $request->has('variant_colors') ? $request->get('variant_colors') : [];
            $brand = $request->get('brand');
            $mrp = $request->get('mrp');
            $quantity = $request->get('quantity');
            $rate = $request->get('rate');
            if (getClientSetting()->product_level_discount == 1) {
                $discounts = $request->get('product_discount');
                $discount_types = $request->get('product_discount_type');
            } else {
                $discounts = array();
                $discount_types = array();
            }
            if (getClientSetting()->product_level_tax == 1) {
                $taxes = $request->get('product_tax');
            } else {
                $taxes = array();
            }
            $amount = $request->get('amount');
            $uid = $request->get('uid');
            $short_desc = $request->get('short_desc');
            foreach ($orderproductsIds as $index) {
                $orderProducts[$index] = [
                    'order_id' => $order->id,
                    'product_id' => $products[$index],
                    'product_name' => $product_name[$index],
                    'mrp' => $mrp[$index],
                    'brand' => $brand[$index],
                    'unit' => $uid[$index],
                    'unit_name' => getUnitName($uid[$index]),
                    'rate' => $rate[$index],
                    'quantity' => $quantity[$index],
                    'pdiscount' => !empty($discounts) ? $discounts[$index] : null,
                    'pdiscount_type' => !empty($discount_types) ? $discount_types[$index] : "Amt",
                    'amount' => $amount[$index],
                    'short_desc' => $short_desc[$index],
                    'product_variant_id' => array_key_exists($index, $product_variant) ? $product_variant[$index] : null,
                    'product_variant_name' => $product_variant_name[$index],
                    'variant_colors' => array_key_exists($index, $product_variant_colors) ? $product_variant_colors[$index] : null,
                ];
                $inserted = OrderDetails::insertGetId($orderProducts[$index]);
                if (getClientSetting()->product_level_tax == 1) {
                    if ($inserted) {
                        $insertedId = $inserted;
                        $thisProductTax = $taxes[$index];
                        if (!empty($thisProductTax)) {
                            foreach ($thisProductTax as $tax) {
                                DB::table('tax_on_orderproducts')->insert([
                                    "orderproduct_id" => $insertedId,
                                    "tax_type_id" => $tax,
                                    "product_id" => $products[$index],
                                ]);
                            }
                        }
                    }
                }
            }
            DB::commit();
            // if(Auth::user()->employees->first()->is_admin==0) $employeeInstance = Employee::employeeParents($order->employee_id, array());
            // else $employeeInstance = DB::table('handles')->where('client_id', $order->client_id )->pluck('employee_id')->toArray();

            // $fbIDs = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereIn('id', $employeeInstance)->whereNotNull('firebase_token')->pluck('firebase_token', 'id');

            // $this->orderNotification($company_id, $order->employee_id, $fbIDs, $order->id, "add");
            $superiors = Employee::employeeParents($order->employee_id, array());


            $orderData['id'] = $order->id;
            saveAdminNotification($company_id, Auth::user()->EmployeeId(), date("Y-m-d H:i:s"), "Added Order", "order", $orderData);

            $partyHandles = DB::table('handles')->where('client_id', $order->client_id)->whereNotIn('employee_id', $superiors)->pluck('employee_id')->toArray();
            $superiors = array_merge($partyHandles, $superiors);



            $this->orderNotification($company_id, $superiors, $order->id, $schemeResponse, "add");
            return redirect()->route('company.admin.order', ['domain' => domain()])->with('success', 'Information has been  Added');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('company.admin.order', ['domain' => domain()])->with('error', 'Some Error Occured. Please Try again');
        }
    }

    private function getOrderLocation()
    {
        $getLocation = null;
        try {
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                //ip from share internet
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                //ip pass from proxy
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
            if (isset($ip)) $getLocation = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));

            return $getLocation;
        } catch (\Exception $e) {
            Log::info(array("getOrderLocation", $e->getMessage()));
            return $getLocation;
        }
    }

    // private function orderNotification($companyID, $employeeID, $fbIds, $orderID, $action)
    private function orderNotification($companyID, $employeeIDs, $orderID, $schemeResponse, $action)
    {
        $orders = Order::where('orders.id', $orderID)
            ->where('orders.company_id', $companyID)
            // ->leftJoin('employees', 'orders.employee_id', 'employees.id')
            // ->leftJoin('clients', 'orders.client_id', 'clients.id')
            ->leftJoin('client_settings', 'orders.company_id', 'client_settings.company_id')
            ->leftJoin('module_attributes', 'orders.delivery_status_id', 'module_attributes.id')
            ->select('orders.*', 'client_settings.order_prefix', 'module_attributes.id as moduleattributesId', 'module_attributes.title as delivery_status', 'module_attributes.color', 'module_attributes.order_amt_flag', 'module_attributes.order_edit_flag', 'module_attributes.order_delete_flag')
            ->first();

        // $moduleAttributes = ModuleAttribute::where('company_id', $companyID)->get();

        // $product_level_tax_flag = $orders->product_level_tax_flag;
        // $productLines = $this->getProductLines($orders->id, $product_level_tax_flag);
        // if ($product_level_tax_flag == 1) {
        //     $orders->taxes = null;
        // } else {
        //     $orders->taxes = $this->getTaxes($orders->id);
        // }
        // $orders->orderproducts = $productLines;
        // if ($orders->delivery_status) {
        //     $delivery_status_color = $moduleAttributes->where('title', '=', $orders->delivery_status)->first();
        //     if ($delivery_status_color) {
        //         $orders->delivery_status_color = $delivery_status_color->color;
        //     } else {
        //         $orders->delivery_status_color = null;
        //     }
        // } else {
        //     $orders->delivery_status_color = null;
        // }

        // if ($orders->employee_id != 0 && !$orders->outlet_id) {
        //     $orders->employee_name = $orders->employee()->withTrashed()->first() ? $orders->employee()->withTrashed()->first()->name : "";
        // } elseif ($orders->outlet_id && $orders->employee_id == 0) {
        //     $orders->employee_name = $orders->outlets()->withTrashed()->first() ? $orders->outlets()->withTrashed()->first()->contact_person . " (O)" : "";
        // } else {
        //     $orders->employee_name = "";
        // }
        $dataPayload = array("data_type" => "order", "order" => $orders->id, 'scheme_response' => array(), "action" => $action);
        $fbIDs = DB::table('employees')->where(array(array('company_id', $companyID), array('status', 'Active')))->whereNotNull('firebase_token')->whereIn('id', $employeeIDs)->where('id', '<>', Auth::user()->EmployeeId())->pluck('firebase_token');

        $notificationAlert = array();
        $orderPrefix = getClientSetting()->order_prefix;
        switch ($action) {
            case "add":
                $title = "Order Created";
                $description = "Order {$orderPrefix}{$orders->order_no} has been added.";
                break;
            case "update":
                $title = "Order Updated";
                $description = "Order {$orderPrefix}{$orders->order_no} has been updated.";
                break;
            case "update_status":
                $title = "Order " . $orders->delivery_status;
                $description = "Order {$orderPrefix}{$orders->order_no} status has been changed to {$orders->delivery_status}.";
                break;
            default:
                $title = "Order Notification";
                $description = "Order {$orderPrefix}{$orders->order_no}'s.";
                break;
        }
        $time = time();
        $notificationAlert[] = array(
            "company_id" => $companyID,
            "employee_id" => $orders->employee_id,
            "data_type" => "order",
            "data" => "",
            "action_id" => $orders->id,
            "title" => $title,
            "description" => $description,
            "created_at" => date('Y-m-d H:i:s'),
            "status" => 1,
            "to" => 1,
            "unix_timestamp" => "$time"
        );

        $msgID = sendPushNotification_($fbIDs, 1, $notificationAlert, $dataPayload);

        $selfApp = DB::table('employees')->where(array(array('company_id', $companyID), array('status', 'Active')))->whereNotNull('firebase_token')->where('id', Auth::user()->EmployeeId())->pluck('firebase_token');
        sendPushNotification_($selfApp, 1, null, $dataPayload);
    }

    private function getProductLines($orderID, $tax_flag)
    {
        if (empty($orderID)) {
            return null;
        }

        $orderProducts = OrderDetails::select('orderproducts.*', 'products.product_name', 'products.short_desc')
            ->join('products', 'products.id', '=', 'orderproducts.product_id')
            ->where('order_id', $orderID)
            ->get();
        $discount_flag = DB::table('orders')->where('id', $orderID)->first();
        if ($tax_flag == 1 || $discount_flag->product_level_discount_flag == 0) {
            foreach ($orderProducts as $orderProduct) {
                if ($tax_flag == 1) {
                    $taxes = $orderProduct->taxes()->withTrashed()->get();
                    if ($taxes->count() == 0) {
                        $orderProduct->total_tax = null;
                    } else {
                        $orderProduct->total_tax = json_encode($taxes->toArray());
                    }
                }

                if ($discount_flag->product_level_discount_flag == 0) {
                    $orderProduct->mrp = $orderProduct->rate;
                }
            }
        }
        $orderProducts = $orderProducts->toArray();
        return empty($orderProducts) ? null : json_encode($orderProducts);
    }

    private function getTaxes($orderID)
    {
        if (empty($orderID)) {
            return null;
        }
        $order_instance = Order::findOrFail($orderID);
        $taxes = $order_instance->taxes()->withTrashed()->get()->toArray();
        return empty($taxes) ? null : json_encode($taxes);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Order $order
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $company_id = config('settings.company_id');
        $partyTypeLevel = getCompanyPartyTypeLevel($company_id);
        $order = Order::with('orderScheme')->findOrFail($request->id);
        
       // $order = Auth::user()->handleQuery('order')->with('orderScheme')->where('id', $request->id)->firstOrFail();
        $scheme_off_value = 0;
        $free_schemes = [];

        $schemeNames = $order->orderScheme->map(function ($orderScheme) {
            return $orderScheme->scheme->name;
        })->toArray();


        if (!$order->orderScheme->isEmpty()) {
            foreach ($order->orderScheme as $scheme) {
                if ($scheme->is_amount) {
                    $scheme_off_value += $scheme->discount_amount;
                } else {
                    $getScheme = App\Scheme::find($scheme->scheme_id);
                    $product = Product::withTrashed()->find($getScheme->offered_product);
                    $variant = ProductVariant::withTrashed()->where('product_id', $getScheme->offered_product)->find($getScheme->offered_product_variant);
                    $unit = isset($variant) ? UnitTypes::withTrashed()->find($variant->unit) : UnitTypes::withTrashed()->find($product->unit);
                    $free_schemes[] = array(
                        'id' => $scheme->scheme_id,
                        'freeItem' => $scheme->free_item,
                        'product_name' => isset($getScheme->offered_product) ? Product::find($getScheme->offered_product)->product_name : ' ',
                        'product_variant' => isset($getScheme->offered_product_variant) ? ProductVariant::where('product_id', $getScheme->offered_product)->find($getScheme->offered_product_variant)->variant : ' ',
                        'unit_name' => isset($unit) ? $unit->symbol : ''
                    );
                }
            }
        }

        $getClientSetting = getClientSetting();
        if (getClientSetting()->order_with_amt == 0) {
            $colspan = 5;
            if ($order->product_level_tax_flag == 1)
                $colspan += 1;
            if ($order->product_level_discount_flag == 1)
                $colspan += 1;
        } else {
            $colspan = 3;
        }
        $orderdetails = $order->orderdetails;
        if ($order->discount_type == '%') {
            $tot_amount = 0;
            if ($order->product_level_tax_flag == 1) {
                $taxesTotal = [];
                foreach ($orderdetails as $orderdetail) {
                    foreach ($orderdetail->taxes()->withTrashed()->get() as $tax) {
                        $rate = $orderdetail->rate;
                        $taxAmount = ($rate * $tax->percent) / 100;
                        if (array_key_exists($tax->id, $taxesTotal)) {
                            $taxesTotal[$tax->id]["amount"] = $taxesTotal[$tax->id]["amount"] + $taxAmount;
                        } else {
                            $taxesTotal[$tax->id]["amount"] = $taxAmount;
                        }
                        $taxesTotal[$tax->id]["name"] = $tax->name . " (" . $tax->percent . "%)";
                    }
                    $qtyRate = $orderdetail->quantity * $orderdetail->rate;
                    $tot_amount += $qtyRate;
                    $orderdetail->qtyRate = $qtyRate;
                }
                $order->applicable_taxes = $taxesTotal;
            } else {
                foreach ($orderdetails as $orderdetail) {
                    $qtyRate = $orderdetail->quantity * $orderdetail->rate;
                    $tot_amount += $qtyRate;
                    $orderdetail->qtyRate = $qtyRate;
                }
            }
            $order->discount_amount = isset($order->discount) ? ($tot_amount * $order->discount) / 100 : 0;
        } else {
            $tot_amount = 0;

            if ($order->product_level_tax_flag == 1) {
                $taxesTotal = [];
                foreach ($orderdetails as $orderdetail) {
                    foreach ($orderdetail->taxes()->withTrashed()->get() as $tax) {
                        if ($orderdetail->pdiscount_type == "oAmt") {
                            $pdiscount = $orderdetail->pdiscount ? $orderdetail->pdiscount : 0;
                            $rate = $orderdetail->rate * $orderdetail->quantity - $pdiscount;
                        } else {
                            $rate = $orderdetail->rate * $orderdetail->quantity;
                        }
                        $taxAmount = ($rate * $tax->percent) / 100;
                        if (array_key_exists($tax->id, $taxesTotal)) {
                            $taxesTotal[$tax->id]["amount"] = $taxesTotal[$tax->id]["amount"] + $taxAmount;
                        } else {
                            $taxesTotal[$tax->id]["amount"] = $taxAmount;
                        }
                        $taxesTotal[$tax->id]["name"] = $tax->name . " (" . $tax->percent . "%)";
                    }
                    $qtyRate = $orderdetail->quantity * $orderdetail->rate;
                    $tot_amount += $qtyRate;
                    $orderdetail->qtyRate = $qtyRate;
                }
                $order->applicable_taxes = $taxesTotal;
            }
            $order->discount_amount = isset($order->discount) ? $order->discount : 0;
        }
        //Total
        $order->total = $order->tot_amount - $order->discount_amount;
        // Sub Total
        $order->tot_amount = $order->tot_amount;
        $module_attribute = ModuleAttribute::where('id', $order->delivery_status_id)->first();
        if ($module_attribute)
            $order->delivery_status = $module_attribute->title;
        if ($order->employee_id == 0) {
            $outlet = $order->outlets()->withTrashed()->first();
            $outlet_name = $outlet->contact_person . " (" . $outlet->outlet_name . ")";
            $order->outlet_name = $outlet_name;

        }

        $action = null;

        if ($order->module_status->order_edit_flag == 1 && (Auth::user()->can('order-update'))) {
            $edit_link = domain_route('company.admin.order.edit', [$order->id]);
            $action = "<a class='btn btn-warning btn-sm edit' href='{$edit_link}' style='padding: 7px 6px;'><i class='fa fa-edit'></i>Edit</a>";
        }

        if ($order->module_status->order_delete_flag == 1 && Auth::user()->can('order-delete')) {
            $delete = domain_route('company.admin.order.destroy', [$order->id]);
            $action = $action . "<a class='btn btn-danger btn-sm delete' data-mid='{ $order->id }' data-url='{$delete}' data-toggle='modal' data-target='#delete' style='padding: 7px 6px;'><i class='fa fa-trash-o'></i>Delete</a>";
        }

        return view('company.orders.show', compact('order', 'orderdetails', 'colspan', 'getClientSetting', 'action', 'scheme_off_value', 'free_schemes', 'partyTypeLevel', 'schemeNames'));
    }

    public function download(Request $request)
    {
        //download this with schemes
        $company_id = config('settings.company_id');
        $partyTypeLevel = getCompanyPartyTypeLevel($company_id);
        $order = Order::findOrFail($request->id);
        $scheme_off_value = 0;
        $free_schemes = [];

        if (!$order->orderScheme->isEmpty()) {
            foreach ($order->orderScheme as $scheme) {
                if ($scheme->is_amount) {
                    $scheme_off_value += $scheme->discount_amount;
                } else {
                    $getScheme = App\Scheme::find($scheme->scheme_id);
                    $productIds = json_decode($getScheme->product_id);
                    $unit_name = OrderDetails::where('order_id', $order->id)->whereIn('product_id', $productIds)->value('unit_name');
                    $free_schemes[] = array(
                        'id' => $scheme->scheme_id,
                        'freeItem' => $scheme->free_item,
                        'product_name' => isset($getScheme->offered_product) ? Product::find($getScheme->offered_product)->product_name : ' ',
                        'product_variant' => isset($getScheme->offered_product_variant) ? ProductVariant::where('product_id', $getScheme->offered_product)->find($getScheme->offered_product_variant)->variant : ' ',
                        'unit_name' => isset($unit_name) ? $unit_name : ''
                    );
                }
            }
        }

        $module_attribute = ModuleAttribute::where('id', $order->delivery_status_id)->first();
        if ($module_attribute)
            $order->delivery_status = $module_attribute->title;
        $getClientSetting = getClientSetting();
        if (getClientSetting()->order_with_amt == 0) {
            $colspan = 5;
            if ($order->product_level_tax_flag == 1) {
                $colspan += 1;
            }
            if ($order->product_level_discount_flag == 1) {
                $colspan += 1;
            }
        } else {
            $colspan = 3;
        }
        $orderdetails = $order->orderdetails;
        if ($order->discount_type == '%') {
            $tot_amount = 0;
            if ($order->product_level_tax_flag == 1) {
                $taxesTotal = [];
                foreach ($orderdetails as $orderdetail) {
                    foreach ($orderdetail->taxes()->withTrashed()->get() as $tax) {
                        $rate = $orderdetail->rate;
                        $taxAmount = ($rate * $tax->percent) / 100;
                        if (array_key_exists($tax->id, $taxesTotal)) {
                            $taxesTotal[$tax->id]["amount"] = $taxesTotal[$tax->id]["amount"] + $taxAmount;
                        } else {
                            $taxesTotal[$tax->id]["amount"] = $taxAmount;
                        }
                        $taxesTotal[$tax->id]["name"] = $tax->name . " (" . $tax->percent . "%)";
                    }
                    $qtyRate = $orderdetail->quantity * $orderdetail->rate;
                    $tot_amount += $qtyRate;
                    $orderdetail->qtyRate = $qtyRate;
                }
                $order->applicable_taxes = $taxesTotal;
            } else {
                foreach ($orderdetails as $orderdetail) {
                    $qtyRate = $orderdetail->quantity * $orderdetail->rate;
                    $tot_amount += $qtyRate;
                    $orderdetail->qtyRate = $qtyRate;
                }
            }
            $order->discount_amount = isset($order->discount) ? ($tot_amount * $order->discount) / 100 : 0;
        } else {
            $tot_amount = 0;

            if ($order->product_level_tax_flag == 1) {
                $taxesTotal = [];
                foreach ($orderdetails as $orderdetail) {
                    foreach ($orderdetail->taxes()->withTrashed()->get() as $tax) {
                        $rate = $orderdetail->rate * $orderdetail->quantity;
                        $taxAmount = ($rate * $tax->percent) / 100;
                        if (array_key_exists($tax->id, $taxesTotal)) {
                            $taxesTotal[$tax->id]["amount"] = $taxesTotal[$tax->id]["amount"] + $taxAmount;
                        } else {
                            $taxesTotal[$tax->id]["amount"] = $taxAmount;
                        }
                        $taxesTotal[$tax->id]["name"] = $tax->name . " (" . $tax->percent . "%)";
                    }
                    $qtyRate = $orderdetail->quantity * $orderdetail->rate;
                    $tot_amount += $qtyRate;
                    $orderdetail->qtyRate = $qtyRate;
                }
                $order->applicable_taxes = $taxesTotal;
            }
            $order->discount_amount = isset($order->discount) ? $order->discount : 0;
        }
        //Total
        $order->total = $order->tot_amount - $order->discount_amount;
        // Sub Total
        $order->tot_amount = $order->tot_amount;

        $pdf = App::make('dompdf.wrapper');
        if ($order->employee_id == 0) {
            $outlet = $order->outlets()->withTrashed()->first();
            $outlet_name = $outlet->contact_person . " (" . $outlet->outlet_name . ")";
            $order->outlet_name = $outlet_name;

        }

        //this is testing new invoice
//        return view('company.orders.invoice', compact('order', 'orderdetails', 'colspan', 'getClientSetting', 'partyTypeLevel', 'free_schemes', 'scheme_off_value'));

//        $pdf =  PDF::loadView('company.orders.invoice', compact('order', 'orderdetails', 'colspan', 'getClientSetting', 'partyTypeLevel', 'free_schemes', 'scheme_off_value'));


        $pdf = PDF::loadView('company.orders.download', compact('order', 'orderdetails', 'colspan', 'getClientSetting', 'partyTypeLevel', 'free_schemes', 'scheme_off_value'));
        // return $pdf->inline();
        if ((getClientSetting()->ncal == 0)) {
            $pdf_name = getClient($order->client_id)['company_name'] . '_' . date('d M Y', strtotime($order->order_date));
        } else {
            $pdf_name = getClient($order->client_id)['company_name'] . '_' . getDeltaDate(date('Y-m-d', strtotime($order->order_date)));
        }
        return $pdf->download($pdf_name . '_orderstatus.pdf');
    }

    public function newInvoice(Request $request)
    {
        //download this with schemes
        $company_id = config('settings.company_id');
        $partyTypeLevel = getCompanyPartyTypeLevel($company_id);
        $order = Order::findOrFail($request->id);
        $scheme_off_value = 0;
        $free_schemes = [];

        if (!$order->orderScheme->isEmpty()) {
            foreach ($order->orderScheme as $scheme) {
                if ($scheme->is_amount) {
                    $scheme_off_value += $scheme->discount_amount;
                } else {
                    $getScheme = App\Scheme::find($scheme->scheme_id);
                    $product = Product::withTrashed()->find($getScheme->offered_product);
                    $variant = ProductVariant::withTrashed()->where('product_id', $getScheme->offered_product)->find($getScheme->offered_product_variant);
                    $unit = isset($variant) ? UnitTypes::withTrashed()->find($variant->unit) : UnitTypes::withTrashed()->find($product->unit);
                    $free_schemes[] = array(
                        'id' => $scheme->scheme_id,
                        'freeItem' => $scheme->free_item,
                        'product_name' => isset($getScheme->offered_product) ? Product::find($getScheme->offered_product)->product_name : ' ',
                        'product_variant' => isset($getScheme->offered_product_variant) ? ProductVariant::where('product_id', $getScheme->offered_product)->find($getScheme->offered_product_variant)->variant : ' ',
                        'unit_name' => isset($unit) ? $unit->symbol : ''
                    );
                }
            }
        }

        $module_attribute = ModuleAttribute::where('id', $order->delivery_status_id)->first();
        if ($module_attribute)
            $order->delivery_status = $module_attribute->title;
        $getClientSetting = getClientSetting();
        if (getClientSetting()->order_with_amt == 0) {
            $colspan = 5;
            if ($order->product_level_tax_flag == 1) {
                $colspan += 1;
            }
            if ($order->product_level_discount_flag == 1) {
                $colspan += 1;
            }
        } else {
            $colspan = 3;
        }
        $orderdetails = $order->orderdetails;
        if ($order->discount_type == '%') {
            $tot_amount = 0;
            if ($order->product_level_tax_flag == 1) {
                $taxesTotal = [];
                foreach ($orderdetails as $orderdetail) {
                    foreach ($orderdetail->taxes()->withTrashed()->get() as $tax) {
                        $rate = $orderdetail->rate;
                        $taxAmount = ($rate * $tax->percent) / 100;
                        if (array_key_exists($tax->id, $taxesTotal)) {
                            $taxesTotal[$tax->id]["amount"] = $taxesTotal[$tax->id]["amount"] + $taxAmount;
                        } else {
                            $taxesTotal[$tax->id]["amount"] = $taxAmount;
                        }
                        $taxesTotal[$tax->id]["name"] = $tax->name . " (" . $tax->percent . "%)";
                    }
                    $qtyRate = $orderdetail->quantity * $orderdetail->rate;
                    $tot_amount += $qtyRate;
                    $orderdetail->qtyRate = $qtyRate;
                }
                $order->applicable_taxes = $taxesTotal;
            } else {
                foreach ($orderdetails as $orderdetail) {
                    $qtyRate = $orderdetail->quantity * $orderdetail->rate;
                    $tot_amount += $qtyRate;
                    $orderdetail->qtyRate = $qtyRate;
                }
            }
            $order->discount_amount = isset($order->discount) ? ($tot_amount * $order->discount) / 100 : 0;
        } else {
            $tot_amount = 0;

            if ($order->product_level_tax_flag == 1) {
                $taxesTotal = [];
                foreach ($orderdetails as $orderdetail) {
                    foreach ($orderdetail->taxes()->withTrashed()->get() as $tax) {
                        $rate = $orderdetail->rate * $orderdetail->quantity;
                        $taxAmount = ($rate * $tax->percent) / 100;
                        if (array_key_exists($tax->id, $taxesTotal)) {
                            $taxesTotal[$tax->id]["amount"] = $taxesTotal[$tax->id]["amount"] + $taxAmount;
                        } else {
                            $taxesTotal[$tax->id]["amount"] = $taxAmount;
                        }
                        $taxesTotal[$tax->id]["name"] = $tax->name . " (" . $tax->percent . "%)";
                    }
                    $qtyRate = $orderdetail->quantity * $orderdetail->rate;
                    $tot_amount += $qtyRate;
                    $orderdetail->qtyRate = $qtyRate;
                }
                $order->applicable_taxes = $taxesTotal;
            }
            $order->discount_amount = isset($order->discount) ? $order->discount : 0;
        }
        //Total
        $order->total = $order->tot_amount - $order->discount_amount;
        // Sub Total
        $order->tot_amount = $order->tot_amount;

        $pdf = App::make('dompdf.wrapper');
        if ($order->employee_id == 0) {
            $outlet = $order->outlets()->withTrashed()->first();
            $outlet_name = $outlet->contact_person . " (" . $outlet->outlet_name . ")";
            $order->outlet_name = $outlet_name;

        }

        $pdf =  PDF::loadView('company.orders.invoice', compact('order', 'orderdetails', 'colspan', 'getClientSetting', 'partyTypeLevel', 'free_schemes', 'scheme_off_value'));

        if ((getClientSetting()->ncal == 0)) {
            $pdf_name = getClient($order->client_id)['company_name'] . '_' . date('d M Y', strtotime($order->order_date));
        } else {
            $pdf_name = getClient($order->client_id)['company_name'] . '_' . getDeltaDate(date('Y-m-d', strtotime($order->order_date)));
        }
        return $pdf->download($pdf_name . '_orderstatus.pdf');
    }

    public function mail(Request $request)
    {
        //make this working with mail
        $customMessages = [
            'email.required' => 'E-mail is a required field.',

        ];

        $this->validate($request, [
            'email' => 'required|email',

        ], $customMessages);

        $company_email = Auth::user()->isCompanyManager()->contact_email;
        $report_mail = array(
            'email' => $request->input('email'),
            'semail' => $company_email,
        );
        $company_id = config('settings.company_id');
        $partyTypeLevel = getCompanyPartyTypeLevel($company_id);
        $company_name = config('settings.title');
        $order = Order::with('orderScheme')->findOrFail($request->id);
        $scheme_off_value = 0;
        $free_schemes = [];

        if (!$order->orderScheme->isEmpty()) {
            foreach ($order->orderScheme as $scheme) {
                if ($scheme->is_amount) {
                    $scheme_off_value += $scheme->discount_amount;
                } else {
                    $getScheme = App\Scheme::find($scheme->scheme_id);
                    $product = Product::withTrashed()->find($getScheme->offered_product);
                    $variant = ProductVariant::withTrashed()->where('product_id', $getScheme->offered_product)->find($getScheme->offered_product_variant);
                    $unit = isset($variant) ? UnitTypes::withTrashed()->find($variant->unit) : UnitTypes::withTrashed()->find($product->unit);
                    $free_schemes[] = array(
                        'id' => $scheme->scheme_id,
                        'freeItem' => $scheme->free_item,
                        'product_name' => isset($getScheme->offered_product) ? Product::find($getScheme->offered_product)->product_name : ' ',
                        'product_variant' => isset($getScheme->offered_product_variant) ? ProductVariant::where('product_id', $getScheme->offered_product)->find($getScheme->offered_product_variant)->variant : ' ',
                        'unit_name' => isset($unit) ? $unit->symbol : ''
                    );
                }
            }
        }

        $module_attribute = ModuleAttribute::where('id', $order->delivery_status_id)->first();
        if ($module_attribute)
            $order->delivery_status = $module_attribute->title;
        $getClientSetting = getClientSetting();
        if (getClientSetting()->order_with_amt == 0) {
            $colspan = 5;
            if ($order->product_level_tax_flag == 1) {
                $colspan += 1;
            }
            if ($order->product_level_discount_flag == 1) {
                $colspan += 1;
            }
        } else {
            $colspan = 3;
        }
        $orderdetails = $order->orderdetails;
        if ($order->discount_type == '%') {
            $tot_amount = 0;
            if ($order->product_level_tax_flag == 1) {
                $taxesTotal = [];
                foreach ($orderdetails as $orderdetail) {
                    foreach ($orderdetail->taxes as $tax) {
                        $rate = $orderdetail->rate;
                        $taxAmount = ($rate * $tax->percent) / 100;
                        if (array_key_exists($tax->id, $taxesTotal)) {
                            $taxesTotal[$tax->id]["amount"] = $taxesTotal[$tax->id]["amount"] + $taxAmount;
                        } else {
                            $taxesTotal[$tax->id]["amount"] = $taxAmount;
                        }
                        $taxesTotal[$tax->id]["name"] = $tax->name . " (" . $tax->percent . "%)";
                    }
                    $qtyRate = $orderdetail->quantity * $orderdetail->rate;
                    $tot_amount += $qtyRate;
                    $orderdetail->qtyRate = $qtyRate;
                }
                $order->applicable_taxes = $taxesTotal;
            } else {
                foreach ($orderdetails as $orderdetail) {
                    $qtyRate = $orderdetail->quantity * $orderdetail->rate;
                    $tot_amount += $qtyRate;
                    $orderdetail->qtyRate = $qtyRate;
                }
            }
            $order->discount_amount = isset($order->discount) ? ($tot_amount * $order->discount) / 100 : 0;
        } else {
            $tot_amount = 0;

            if ($order->product_level_tax_flag == 1) {
                $taxesTotal = [];
                foreach ($orderdetails as $orderdetail) {
                    foreach ($orderdetail->taxes as $tax) {
                        $rate = $orderdetail->rate;
                        $taxAmount = ($rate * $tax->percent) / 100;
                        if (array_key_exists($tax->id, $taxesTotal)) {
                            $taxesTotal[$tax->id]["amount"] = $taxesTotal[$tax->id]["amount"] + $taxAmount;
                        } else {
                            $taxesTotal[$tax->id]["amount"] = $taxAmount;
                        }
                        $taxesTotal[$tax->id]["name"] = $tax->name . " (" . $tax->percent . "%)";
                    }
                    $qtyRate = $orderdetail->quantity * $orderdetail->rate;
                    $tot_amount += $qtyRate;
                    $orderdetail->qtyRate = $qtyRate;
                }
                $order->applicable_taxes = $taxesTotal;
            }
            $order->discount_amount = isset($order->discount) ? $order->discount : 0;
        }
        //Total
        $order->total = $order->tot_amount - $order->discount_amount;
        // Sub Total
        $order->tot_amount = $order->tot_amount;
        //Total
        $order->total = $order->tot_amount - $order->discount_amount;
        // Sub Total
        $order->tot_amount = $order->tot_amount;

        $pdf = App::make('dompdf.wrapper');
        $pdf = PDF::loadView('company.orders.download', compact('order', 'orderdetails', 'colspan', 'getClientSetting', 'partyTypeLevel', 'free_schemes', 'scheme_off_value'));
        if ((getClientSetting()->ncal == 0)) {
            $pdf_name = getClient($order->client_id)['company_name'] . '_' . date('d M Y', strtotime($order->order_date));
        } else {
            $pdf_name = getClient($order->client_id)['company_name'] . '_' . getDeltaDate(date('Y-m-d', strtotime($order->order_date)));
        }
        // $pdf_name = getClient($order->client_id)['company_name'] . '_' . date('d M Y', strtotime($order->order_date));
        Mail::send([], ['url' => '', 'company_name' => ''], function ($message) use ($report_mail, $pdf, $pdf_name, $company_name) {
            $message->attachData($pdf->output(), $pdf_name . "_orderstatus.pdf");
            $message->from($report_mail['email'], $company_name);
            $message->to($report_mail['email'])->subject('Order Status');
        });

        return response()->json(
            ['message' => 'Mail is Sent.',
                'url' => domain_route('company.admin.order'),
                'id' => $request->id,
            ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Order $order
     * @return \Illuminate\Http\Response
     */

    public function editOrder($domain, $id)
    {

        $company_id = config('settings.company_id');
        $partyTypeLevel = getCompanyPartyTypeLevel($company_id);
        $getClientSetting = getClientSetting();
        $order = Order::with('orderScheme')->findOrFail($id);
        $scheme_off_value = 0;
        $free_schemes = [];
        $used_schemes = [];
        $old_values = [];

        if (!$order->orderScheme->isEmpty()) {
            foreach ($order->orderScheme as $scheme) {
                $getScheme = App\Scheme::find($scheme->scheme_id);
                if ($scheme->is_amount) {
                    $scheme_off_value += $scheme->discount_amount;
                } else {
                    $getScheme = App\Scheme::find($scheme->scheme_id);
                    $product = Product::withTrashed()->find($getScheme->offered_product);
                    $variant = ProductVariant::withTrashed()->where('product_id', $getScheme->offered_product)->find($getScheme->offered_product_variant);
                    $unit = isset($variant) ? UnitTypes::withTrashed()->find($variant->unit) : UnitTypes::withTrashed()->find($product->unit);
                    $free_schemes[] = array(
                        'id' => $scheme->scheme_id,
                        'freeItem' => $scheme->free_item,
                        'product_name' => isset($getScheme->offered_product) ? Product::find($getScheme->offered_product)->product_name : ' ',
                        'product_variant' => isset($getScheme->offered_product_variant) ? ProductVariant::where('product_id', $getScheme->offered_product)->find($getScheme->offered_product_variant)->variant : ' ',
                        'unit' => isset($unit) ? $unit->symbol : ''
                    );
                }
                $used_schemes[] = $scheme->scheme_id;
                $old_values[] = array(
                    'scheme_id' => $scheme->scheme_id,
                    'discounts' => $scheme->discount_amount,
                    'free_items' => $scheme->free_item
                );
            }
        }
        $orderdetails = $order->orderdetails;
        if ($order->order_to) $orderTo = Client::where('id', $order->order_to)->withTrashed()->first();
        else $orderTo = null;

        $allClients = Client::where('status', 'Active')->orderBy('company_name', 'asc')->get(['company_name', 'id', 'rate_id', 'client_type', 'superior'])->toArray();
        $clients = Auth::user()->handleQuery('client')->where('status', 'Active')->orWhere('clients.id', $order->client_id)->orderBy('company_name', 'asc')->get(['company_name', 'id', 'rate_id', 'client_type', 'superior']);
        
        if(config('settings.category_wise_rate_setup') == 1) {
          $clients->load(['appliedcategoryrates'=> function($query){
            $query->select('id', 'category_id');
          }]);
        }

        $clients = $clients->toArray();
        
        $products = Product::where('products.company_id', $company_id)
            ->orderBy('products.star_product', 'desc')
            ->orderBy('product_name', 'asc')
            ->get(['products.*']);
        $variants = ProductVariant::where('company_id', $company_id)->whereIn('product_id', (clone $products)->pluck('id')->toArray())->get();
        $colors = Color::pluck('name', 'value')->toArray();
        $companyDefaultTaxes = TaxType::where('company_id', $company_id)->get();

        $subTotal = (clone $orderdetails)->sum('mrp');
        if ($order->discount_type == '%') {
            $tot_amount = 0;
            foreach ($orderdetails as $orderdetail) {
                $qtyRate = $orderdetail->quantity * $orderdetail->rate;
                $tot_amount += $qtyRate;
                $orderdetail->qtyRate = $qtyRate;
                if ($orderdetail->product->taxes->count() == 0)
                    $getDefaultTaxes = $companyDefaultTaxes;//TaxType::where('company_id', $company_id)->where('default_flag', 1)->get();
                else
                    $getDefaultTaxes = $orderdetail->product->taxes->merge($companyDefaultTaxes);
                $orderdetail->product->taxes = $getDefaultTaxes->merge($orderdetail->taxes()->withTrashed()->get());
            }
            $order->discount_amount = isset($order->discount) ? ($tot_amount * $order->discount) / 100 : 0;
        } else {
            foreach ($orderdetails as $orderdetail) {
                if ($orderdetail->product->taxes->count() == 0)
                    $getDefaultTaxes = $companyDefaultTaxes;//TaxType::where('company_id', $company_id)->where('default_flag', 1)->get();
                else
                    $getDefaultTaxes = $orderdetail->product->taxes->merge($companyDefaultTaxes);
                $orderdetail->product->taxes = $getDefaultTaxes->merge($orderdetail->taxes()->withTrashed()->get());
            }
            $order->discount_amount = isset($order->discount) ? $order->discount : 0;
        }
        //Total
        $order->total = $order->tot_amount - $order->discount_amount;
        // Sub Total
        $order->tot_amount = $order->tot_amount;
        $taxes = TaxType::where('company_id', $company_id)->select(DB::raw("CONCAT(name,' (',percent,'%)') as name, id, percent, default_flag"))->get();
        $order_taxes = $order->taxes()->withTrashed()->pluck('tax_types.id')->toArray();
        if ($order->product_level_tax_flag == 0) {
            $taxes = $taxes->merge($order->taxes()->withTrashed()->select(DB::raw("CONCAT(tax_types.name,' (',tax_types.percent,'%)') as name, tax_types.id, tax_types.percent, tax_types.default_flag"))->get());
        }

        if ($taxes->first()) {
            $tax_types = $taxes->pluck('name', 'id')->toArray();
            $tax_percents = $taxes->pluck('percent', 'id')->toArray();
            $default_taxes = $taxes->where('default_flag', 1)->pluck('id')->toArray();
            $getAllDefault_taxes = $taxes->toArray();
        } else {
            $tax_types = array();
            $tax_percents = array();
            $default_taxes = array();
            $getAllDefault_taxes = array();
        }
        return view('company.orders.edit', compact('allClients', 'clients', 'products', 'variants', 'order', 'orderdetails', 'colors', 'subTotal', 'taxes', 'tax_types', 'default_taxes', 'getAllDefault_taxes', 'order_taxes', 'tax_percents', 'getClientSetting', 'scheme_off_value', 'free_schemes', 'used_schemes', 'old_values', 'orderTo', 'partyTypeLevel'));
    }

    public function updateOrder($domain, $id, Request $request)
    {

        $customMessages = [
            'client_id.required' => 'The Party Name field is required.',
            // 'order_date.required' => 'Order Date Field is required',
            'product_id.*.required' => 'Product Name field is required.',
            'discount.not_regex' => 'Discount cannot be zero.',
            'uid.*.required' => "Unit is required"
        ];

        $this->validate($request, [
            'client_id' => 'required',
            // 'order_date' => 'required',
            'product_id.*' => 'required',
            'discount' => (getClientSetting()->non_zero_discount == 1) ? 'sometimes|required|not_regex:/^[0]+$/' : 'nullable',
            'uid.*' => 'required'
        ], $customMessages);
        $company_id = config('settings.company_id');

        try {
            Session::flash('DT_Ord_filters', $request->DT_Ord_FILTER);
            DB::beginTransaction();
            $order = Order::with('orderScheme')->findOrFail($request->id);

            if (isset($order->outlet_id)) {
                if ($order->client_id != $request->get('client_id')) {
                    $order->employee_id = Auth::user()->employee->id;
                    $order->outlet_id = NULL;
                }
            }
            $order->client_id = $request->get('client_id');
            $order->order_to = $request->get('orderTo');
            // if(isset($request->englishDate)){
            //   $order->order_date = $request->get('englishDate');
            // }else{
            $order->order_date = $order->order_date;
            // }
            $credit_days = Client::find($request->client_id)->credit_days ?? config('settings.credit_days');
            $order->due_date = Carbon::parse($order->order_date)->addDays($credit_days)->format('Y-m-d');
            $order->order_note = $request->get('order_note');
            if ($order->product_level_tax_flag == 1) {
                $order->tax = $request->get('tax');
            } else {
                $order->tax = $request->has('tax') ? array_sum($request->get('tax')) : null;
            }
            $order->discount = $request->get('discount');
            if ($order->product_level_discount_flag == 0) {
                $order->discount_type = $request->get('discount_type');
            }
            // $order->grand_total = $request->get('grand_total');
            // $order->tot_amount = $request->get('subtotal');
            $order->grand_total = (getClientSetting()->order_with_amt == 0) ? $request->get('grand_total') : array_sum($request->get('amount'));
            $order->tot_amount = (getClientSetting()->order_with_amt == 0) ? $request->get('subtotal') : array_sum($request->get('amount'));

            if ($order->client_id) {
                $client = Client::find($order->client_id);

                if ($client) {
                    $credit_days = $client->credit_days;
                    // $order_with_qty_amt_settings = config('settings.order_with_amt');
                    // $order_with_qty_amt = $order_with_qty_amt_settings?$order_with_qty_amt_settings:0;
                    // $accounting_settings = config('settings.accounting');
                    // $accounting = $accounting_settings?$accounting_settings:0;

                    // if(isset($client->credit_limit)){
                    //   $outstading_amount = $this->getOutstandingAmount($client);
                    //   if(($client->credit_limit-($outstading_amount+$order->grand_total))<0 && $order_with_qty_amt!=1 && $accounting==1) {
                    //     return redirect()->route('company.admin.order', ['domain' => domain()])->with('warning', 'Insufficient credit limit. Orders cannot be updated.');
                    //   }
                    // }
                }
            }


            $orderProducts = [];
            if ($request->has('orderproductsId')) {
                $oldOrdersIds = $request->get('orderproductsId');
                if (count($oldOrdersIds) > 0) {
                    $products = $request->get('product_id');
                    $product_names = $request->get('product_name');
                    $product_variant = ($request->has('product_variant')) ? $request->get('product_variant') : [];
                    $product_variant_name = $request->get('product_variant_name');
                    if (getClientSetting()->var_colors == 1) {
                        $variant_colors = $request->has('variant_colors') ? $request->get('variant_colors') : [];
                    }
                    $mrp = $request->get('mrp');
                    $rate = $request->get('rate');
                    $quantity = $request->get('quantity');
                    $uid = $request->get('uid');
                    if ($order->product_level_discount_flag == 1) {
                        $discounts = $request->get('product_discount');
                        $discount_types = $request->get('product_discount_type');
                    } else {
                        $discounts = array();
                        $discount_types = array();
                    }
                    if ($order->product_level_tax_flag == 1) {
                        $taxes = $request->get('product_tax');
                    } else {
                        $taxes = array();
                    }
                    $amount = $request->get('amount');
                    $brand = $request->get('brand');
                    $short_desc = $request->get('short_desc');

                    foreach ($oldOrdersIds as $oldOrdersId) {
                        // $orderInstance = OrderDetails::where('order_id', $request->id)->where('id',$oldOrdersId)->first();
                        $orderInstance = OrderDetails::findOrFail($oldOrdersId);

                        $orderInstance->product_id = $products[$oldOrdersId];
                        $orderInstance->product_name = $product_names[$oldOrdersId];
                        $orderInstance->mrp = $mrp[$oldOrdersId];
                        $orderInstance->brand = $brand[$oldOrdersId];
                        $orderInstance->unit = $uid[$oldOrdersId];
                        $orderInstance->unit_name = getUnitName($uid[$oldOrdersId]);
                        $orderInstance->rate = $rate[$oldOrdersId];
                        $orderInstance->quantity = $quantity[$oldOrdersId];
                        $orderInstance->pdiscount = !empty($discounts) ? $discounts[$oldOrdersId] : null;
                        $orderInstance->pdiscount_type = !empty($discount_types) ? $discount_types[$oldOrdersId] : "Amt";
                        $orderInstance->amount = $amount[$oldOrdersId];
                        $orderInstance->short_desc = $short_desc[$oldOrdersId];
                        $orderInstance->product_variant_id = array_key_exists($oldOrdersId, $product_variant) ? $product_variant[$oldOrdersId] : null;
                        $orderInstance->product_variant_name = $product_variant_name[$oldOrdersId];
                        if (getClientSetting()->var_colors == 1) {
                            $orderInstance->variant_colors = array_key_exists($oldOrdersId, $variant_colors) ? $variant_colors[$oldOrdersId] : null;
                        }
                        $inserted = $orderInstance->update();
                        if ($order->product_level_tax_flag == 1) {
                            $orderInstance->taxes()->detach();
                            if ($inserted) {
                                $insertedId = $orderInstance->id;
                                $thisProductTax = $taxes[$oldOrdersId];
                                if (!empty($thisProductTax)) {
                                    foreach ($thisProductTax as $tax) {
                                        DB::table('tax_on_orderproducts')->insert([
                                            "orderproduct_id" => $insertedId,
                                            "tax_type_id" => $tax,
                                            "product_id" => $products[$insertedId],
                                        ]);
                                        // $orderInstance->taxes()->attach([
                                        //   "orderproduct_id" => $insertedId,
                                        //   "tax_type_id" => $tax,
                                        //   "product_id" => $products[$insertedId],
                                        // ]);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if (!$order->orderScheme->IsEmpty()) {
                foreach ($order->orderScheme as $scheme) {
                    $scheme->delete();
                }
            }

            $schemeResponse = array();

            if (isset($request->scheme_id)) {
                $scheme_id = explode(',', $request->scheme_id);
                $scheme_discount = explode(',', $request->scheme_discount);
                $scheme_freeItems = explode(',', $request->scheme_freeItems);

                foreach ($scheme_id as $key => $id) {

                    $scheme = App\Scheme::find($id);
                    $order_scheme = new OrderScheme;
                    $order_scheme->company_id = $company_id;
                    $order_scheme->scheme_id = $id;
                    $order_scheme->is_amount = ((empty($scheme_freeItems[$key]))) ? 1 : 0;
                    $order_scheme->discount_amount = $scheme_discount[$key];
                    $order_scheme->free_item = $scheme_freeItems[$key];
                    $order_scheme->order_id = $order->id;
                    $order_scheme->product_id = isset($scheme->offered_product) ? $scheme->offered_product : null;
                    $order_scheme->variant_id = isset($scheme->offered_product_variant) ? $scheme->offered_product_variant : null;
                    $order_scheme->save();
                    $schemeResponse[] = $order_scheme->refresh();
                }
            }

            if ($request->has('newrow_orderproductsId')) {
                $newOrdersIds = $request->get('newrow_orderproductsId');
                if (count($newOrdersIds) > 0) {
                    $products = $request->get('newrow_product_id');
                    $product_names = $request->get('newrow_product_name');
                    $product_variant = $request->has('newrow_product_variant') ? $request->get('newrow_product_variant') : [];
                    $product_variant_name = $request->get('newrow_product_variant_name');
                    if (getClientSetting()->var_colors == 1) {
                        $variant_colors = ($request->has('newrow_variant_colors')) ? $request->get('newrow_variant_colors') : [];
                    }
                    $mrp = $request->get('newrow_mrp');
                    $rate = $request->get('newrow_rate');
                    $quantity = $request->get('newrow_quantity');
                    $uid = $request->get('newrow_uid');
                    if ($order->product_level_discount_flag == 1) {
                        $discounts = $request->get('newrow_product_discount');
                        $discount_types = $request->get('newrow_product_discount_type');
                    } else {
                        $discounts = array();
                        $discount_types = array();
                    }
                    if ($order->product_level_tax_flag == 1) {
                        $taxes = $request->get('newrow_product_tax');
                    } else {
                        $taxes = array();
                    }
                    $amount = $request->get('newrow_amount');
                    $brand = $request->get('newrow_brand');
                    $short_desc = $request->get('newrow_short_desc');

                    foreach ($newOrdersIds as $newOrdersId) {
                        $orderInstance = new OrderDetails;
                        if ($orderInstance) {
                            $orderInstance->order_id = $order->id;
                            $orderInstance->product_id = $products[$newOrdersId];
                            $orderInstance->product_name = $product_names[$newOrdersId];
                            $orderInstance->mrp = $mrp[$newOrdersId];
                            $orderInstance->brand = $brand[$newOrdersId];
                            $orderInstance->unit = $uid[$newOrdersId];
                            $orderInstance->unit_name = getUnitName($uid[$newOrdersId]);
                            $orderInstance->rate = $rate[$newOrdersId];
                            $orderInstance->quantity = $quantity[$newOrdersId];
                            $orderInstance->pdiscount = !empty($discounts) ? $discounts[$newOrdersId] : null;
                            $orderInstance->pdiscount_type = !empty($discount_types) ? $discount_types[$newOrdersId] : "Amt";
                            $orderInstance->amount = $amount[$newOrdersId];
                            $orderInstance->short_desc = $short_desc[$newOrdersId];
                            $orderInstance->product_variant_id = array_key_exists($newOrdersId, $product_variant) ? $product_variant[$newOrdersId] : null;
                            $orderInstance->product_variant_name = $product_variant_name[$newOrdersId];
                            if (getClientSetting()->var_colors == 1) {
                                $orderInstance->variant_colors = array_key_exists($newOrdersId, $variant_colors) ? $variant_colors[$newOrdersId] : null;
                            }
                            $inserted = $orderInstance->save();
                            if ($order->product_level_tax_flag == 1) {
                                if ($inserted) {
                                    $insertedId = $orderInstance->id;
                                    $thisTax = $taxes[$newOrdersId];
                                    if (!empty($thisTax)) {
                                        foreach ($taxes[$newOrdersId] as $tax) {
                                            // $orderInstance->taxes()->attach([
                                            //   "orderproduct_id" => $insertedId,
                                            //   "tax_type_id" => $tax,
                                            //   "product_id" => $products[$newOrdersId],
                                            // ]);
                                            DB::table('tax_on_orderproducts')->insert([
                                                "orderproduct_id" => $insertedId,
                                                "tax_type_id" => $tax,
                                                "product_id" => $products[$newOrdersId],
                                            ]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            date_default_timezone_set($this->getTimeZone($company_id));
            $order->updated_at = date('Y-m-d H:i:s');
            $saved = $order->update();

            $updated = $order->update();
            if ($updated && $order->product_level_tax_flag == 0) {
                $order->taxes()->detach();
                $tax_percents = $request->get('tax_percents');
                $tax_type_ids = $request->get('tax_type_id');
                $taxes = [];
                if (!empty($tax_type_ids)) {
                    foreach ($tax_type_ids as $index => $tax_type) {
                        $taxes[] = [
                            'order_id' => $order->id,
                            'tax_type_id' => $tax_type,
                            'tax_percent' => $tax_percents[$index]
                        ];
                    }
                }
                DB::table('tax_on_orders')->insert($taxes);
            }
            DB::commit();

            // if(Auth::user()->employees->first()->is_admin==0) $employeeInstance = Employee::employeeParents($order->employee_id, array());
            // else $employeeInstance = DB::table('handles')->where('client_id', $order->client_id )->pluck('employee_id')->toArray();

            // $fbIDs = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereIn('id', $employeeInstance)->whereNotNull('firebase_token')->pluck('firebase_token', 'id');

            // $this->orderNotification($company_id, $order->employee_id, $fbIDs, $order->id, "update");

            if ($order->employee_id == 0 && $order->outlet_id) {
                $superiors = DB::table('handles')->where('client_id', $order->client_id)->pluck('employee_id')->toArray();
            } else {
                $superiors = Employee::employeeParents((int)$order->employee_id, array());
            }
            $orderData['id'] = $order->id;
            saveAdminNotification($company_id, Auth::user()->EmployeeId(), date("Y-m-d H:i:s"), "Updated Order", "order", $orderData);

            $partyHandles = DB::table('handles')->where('client_id', $order->client_id)->whereNotIn('employee_id', $superiors)->pluck('employee_id')->toArray();
            $superiors = array_merge($partyHandles, $superiors);

            $this->orderNotification($company_id, $superiors, $order->id, $schemeResponse, "update");
            $newprevious = explode('/', $request->previous_url);
            if ($newprevious[4] == 'order' || $newprevious[4] == 'employee' || $newprevious[4] == 'client') {
                $previous = $request->previous_url;
            } else {
                $previous = route('company.admin.order', ['domain' => domain()]);
            }
            return redirect($previous)->with('success', 'Information has been  Updated.');
        } catch (Exception $e) {
            DB::rollback();

            return back();
        }
    }

    public function remove(Request $request, $domain, $id)
    {
        $company_id = config('settings.company_id');
        $order = Order::select('orders.*', 'module_attributes.id as module_attributesID', 'module_attributes.title', 'module_attributes.color', 'module_attributes.order_amt_flag', 'module_attributes.order_edit_flag', 'module_attributes.order_delete_flag')->leftJoin('module_attributes', 'orders.delivery_status_id', 'module_attributes.id')->where('orders.company_id', $company_id)->where('module_attributes.order_delete_flag', '1')->where('orders.id', $id)->first();
        $employeeId = $order->employee_id;
        $clientId = $order->client_id;
        $finalArray = $order;
        if ($order) {
            Session::flash('DT_Ord_filters', $request->DT_Ord_FILTER);
            DB::beginTransaction();
            if ($order->product_level_tax_flag == 1) {
                foreach ($order->orderdetails as $orderdetail) {
                    $orderdetail->taxes()->detach();
                }
            } else {
                $order->taxes()->detach();
            }
            $orderdetails = OrderDetails::where('order_id', $id)->delete();
            // $orderdetails->delete();
            $deleted = $order->delete();
            if ($deleted) {
                DB::commit();
                $dataPayload = array("data_type" => "order", "order" => $id, "action" => "delete");

                // if(Auth::user()->employees->first()->is_admin==0) $employeeInstance = Employee::employeeParents($order->employee_id, array());
                // else $employeeInstance = DB::table('handles')->where('client_id', $order->client_id )->pluck('employee_id')->toArray();
                if ($employeeId == 0) {
                    $superiors = DB::table('handles')->where('client_id', $clientId)->pluck('employee_id')->toArray();
                } else {
                    $superiors = Employee::employeeParents($employeeId, array());
                }
                $partyHandles = DB::table('handles')->where('client_id', $clientId)->whereNotIn('employee_id', $superiors)->pluck('employee_id')->toArray();
                $superiors = array_merge($partyHandles, $superiors);
                $fbIDs = DB::table('employees')->where(array(array('company_id', $finalArray->company_id), array('status', 'Active')))->whereIn('id', $superiors)->where('id', '<>', Auth::user()->EmployeeId())->whereNotNull('firebase_token')->pluck('firebase_token');
                $orderData['id'] = $id;
                saveAdminNotification($company_id, Auth::user()->EmployeeId(), date("Y-m-d H:i:s"), "Deleted Order", "order", $orderData);

                $notificationAlert = array();
                $orderPrefix = getClientSetting()->order_prefix;
                $time = time();
                $notificationAlert[] = array(
                    "company_id" => $company_id,
                    "employee_id" => $employeeId,
                    "data_type" => "order",
                    "data" => "",
                    "action_id" => $id,
                    "title" => "Order Deleted",
                    "description" => "Order {$orderPrefix}{$order->order_no} has been deleted.",
                    "created_at" => date('Y-m-d H:i:s'),
                    "status" => 1,
                    "to" => 1,
                    "unix_timestamp" => "$time"
                );

                $msgID = sendPushNotification_($fbIDs, 1, $notificationAlert, $dataPayload);
                $selfApp = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->where('id', Auth::user()->EmployeeId())->pluck('firebase_token');
                sendPushNotification_($selfApp, 1, null, $dataPayload);
            } else {
                DB::rollback();
            }
        }
        Session::flash('success', 'Order has been deleted.');
        if ($request->has('return_url')) {
            if ($request->return_url == domain_route('company.admin.order.edit', [$id])) {
                return redirect()->to(domain_route('company.admin.order'));
            }
            return redirect()->to($request->return_url);
        }
        return back();
    }

    public function changeDeliveryStatus(Request $request)
    {
        $company_id = config('settings.company_id');
        date_default_timezone_set($this->getTimeZone($company_id));
        $orderId = $request->order_id;
        $orderPrefix = getClientSetting()->order_prefix;
        if (is_array($orderId)) {
            $orderIds = explode(',', $orderId[0]);
            if (!empty($orderIds)) {
                $updatedOrders = Order::where('company_id', $company_id)->whereIn('id', $orderIds)->update(['delivery_status_id' => $request->delivery_status, 'updated_at' => date('Y-m-d H:i:s'), 'include_delivery_details' => isset($request->include_delivery_details) ? 1 : 0]);
                if ($updatedOrders > 0) {
                    $allOrders = Order::select('orders.*', 'module_attributes.id as maID', 'module_attributes.title as delivery_status', 'module_attributes.color as delivery_status_color', 'clients.id as clientID', 'clients.company_name as client_company_name', 'clients.name as client_name', 'clients.opening_balance')
                        ->leftJoin('clients', 'orders.client_id', 'clients.id')
                        ->leftJoin('module_attributes', 'orders.delivery_status_id', 'module_attributes.id')
                        ->where('orders.company_id', $company_id)->whereIn('orders.id', $orderIds)->get();
                    // $orders = $allOrders->where('employee_id', '!=', 0);
                    $ordersEmployeeId = $allOrders->unique('employee_id')->where('employee_id', '!=', 0)->pluck('employee_id')->toArray();
                    $superiors = array();
                    foreach ($ordersEmployeeId as $employeeId) {
                        $superior_ids = Employee::EmployeeParents($employeeId, array());
                        if (!empty($superior_ids)) {
                            foreach ($superior_ids as $superior_id) {
                                if (!in_array($superior_id, $superiors)) {
                                    array_push($superiors, $superior_id);
                                }
                            }
                        }
                        unset($superior_ids);
                    }
                    $fbIDs = Employee::where('company_id', $company_id)->where('status', 'Active')->whereIn('id', $superiors)->whereNotNull('firebase_token')->where('id', '<>', Auth::user()->EmployeeId())->select('firebase_token', 'id')->get();
                    if (!empty($allOrders)) {
                        foreach ($allOrders as $order) {
                            $notificationData = array();
                            $sendingOrder = array();
                            $time = time();
                            $notificationData[] = array(
                                "company_id" => $company_id,
                                "employee_id" => $order->employee_id,
                                "data_type" => "order",
                                "data" => "",
                                "action_id" => $order->id,
                                "title" => "Order " . $order->title,
                                "description" => "Order {$orderPrefix}{$order->order_no} status has been changed to {$order->delivery_status}.",
                                "created_at" => date('Y-m-d H:i:s'),
                                "status" => 1,
                                "to" => 1,
                                "unix_timestamp" => "$time"
                            );

                            $client = getClient($order->client_id);
                            array_push($sendingOrder, $order);
                            if ($order->employee_id == 0) {
                                $employee_ids = DB::table('handles')->where('client_id', $order->client_id)->pluck('employee_id')->toArray();
                                $fbID = Employee::where('company_id', $company_id)->where('status', 'Active')->whereIn('id', $employee_ids)->whereNotNull('firebase_token')->pluck('firebase_token');
                            } else {
                                $fbID = (clone $fbIDs)->where('id', $order->employee_id)->pluck('firebase_token')->toArray();
                            }
                            $dataPayload = array("data_type" => "order", "order" => $order->id, "scheme_response" => array(), "action" => "update_statuses");
                            $orderData['id'] = $order->id;
                            saveAdminNotification($company_id, Auth::user()->EmployeeId(), date("Y-m-d H:i:s"), "Updated Status", "order", $orderData);

                            // $superiors = Employee::employeeParents($order->employee_id, array());
                            // $this->orderNotification($company_id, $superiors, $order->id, $order->orderScheme->toArray(), "Updated Order");


                            $sent = sendPushNotification_($fbID, 1, $notificationData, $dataPayload);
                            $selfApp = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->where('id', Auth::user()->EmployeeId())->pluck('firebase_token');
                            sendPushNotification_($selfApp, 1, null, $dataPayload);
                        }
                    }
                }
            }
        } else {
            $order = Order::select('orders.*', 'module_attributes.id as maID', 'module_attributes.title', 'module_attributes.color as delivery_status_color', 'clients.id as clientID', 'clients.company_name as client_company_name', 'clients.name as client_name', 'clients.opening_balance')
                ->leftJoin('clients', 'orders.client_id', 'clients.id')
                ->leftJoin('module_attributes', 'orders.delivery_status_id', 'module_attributes.id')
                ->where('orders.company_id', $company_id)->where('orders.id', $orderId)->first();
            if ($request->delivery_status)
                $order->delivery_status_id = $request->delivery_status;

            if ($request->delivery_date != "NaN-NaN-NaN") {
                if (config('settings.ncal') == 0)
                    $order->delivery_date = ($request->delivery_date) ? $request->delivery_date : null;
                else
                    $order->delivery_date = ($request->delivery_date) ? $request->delivery_date : null;
            }
            $order->delivery_place = $request->delivery_place;
            $order->transport_name = $request->transport_name;
            $order->transport_number = $request->transport_number;
            $order->billty_number = $request->billty_number;
            $order->delivery_note = $request->delivery_note;
            $order->include_delivery_details = isset($request->include_delivery_details) ? 1 : 0;
            $order->updated_at = date('Y-m-d H:i:s');
            $saved = $order->update();

            $order = Order::select('orders.*', 'module_attributes.id as maID', 'module_attributes.title', 'module_attributes.color as delivery_status_color', 'clients.id as clientID', 'clients.company_name as client_company_name', 'clients.name as client_name')
                ->leftJoin('clients', 'orders.client_id', 'clients.id')
                ->leftJoin('module_attributes', 'orders.delivery_status_id', 'module_attributes.id')
                ->where('orders.company_id', $company_id)->where('orders.id', $orderId)->first();
            if ($order) $order->delivery_status = $order->title;
            if ($saved && $order) {
                if ($order->employee_id == 0) {
                    $superiors = DB::table('handles')->where('client_id', $order->client_id)->pluck('employee_id')->toArray();
                } else {
                    $superiors = Employee::EmployeeParents($order->employee_id, array());
                }
                $partyHandles = DB::table('handles')->where('client_id', $order->client_id)->whereNotIn('employee_id', $superiors)->pluck('employee_id')->toArray();
                $superiors = array_merge($partyHandles, $superiors);
                $fbID = Employee::where('company_id', $company_id)->where('status', 'Active')->whereIn('id', $superiors)->whereNotNull('firebase_token')->where('id', '<>', Auth::user()->EmployeeId())->pluck('firebase_token');
                $time = time();
                $notificationData = array(
                    "company_id" => $company_id,
                    "employee_id" => $order->employee_id,
                    "data_type" => "order",
                    "data" => "",
                    "action_id" => $order->id,
                    "title" => "Order " . $order->title,
                    "description" => "Order {$orderPrefix}{$order->order_no} status has been changed to {$order->title}.",
                    "created_at" => date('Y-m-d H:i:s'),
                    "status" => 1,
                    "to" => 1,
                    "unix_timestamp" => "$time"
                );
                $schemeResponse = $order->orderScheme->toArray();
                $dataPayload = array("data_type" => "order", "order" => $order->id, "scheme_response" => array(), "action" => "update_status");
                $orderData['id'] = $order->id;
                saveAdminNotification($company_id, Auth::user()->EmployeeId(), date("Y-m-d H:i:s"), "Updated Status", "order", $orderData);
                // $this->orderNotification($company_id, $superiors, $order->id, $order->orderScheme->toArray(), "Updated Order");
                $sent = sendPushNotification_($fbID, 1, $notificationData, $dataPayload);
                $selfApp = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->where('id', Auth::user()->EmployeeId())->pluck('firebase_token');
                sendPushNotification_($selfApp, 1, null, $dataPayload);

            }
        }
        $request->session()->flash('success', "Order Status updated successfully.");
        return back();
    }

    public function deleteOrderProducts(Request $request)
    {
        $orderInstance = Order::findOrFail($request->id);
        if ($orderInstance->module_status->order_delete_flag == 1) {
            $orderProductsId = $request->orderProductsId;
            $orderProductsInstance = OrderDetails::where('order_id', $orderInstance->id)->where('id', $orderProductsId)->first();
            if ($orderProductsInstance) {
                $orderProductsInstance->taxes()->detach();
                $orderProductsInstance->delete();
            }

            $orderScheme = OrderScheme::where('order_id', $request->id)->get();

            $schemeResponse = array();

            foreach ($orderScheme as $scheme) {
                $scheme->delete();
                $schemeResponse[] = $scheme;
            }
            $schemeResponse = [];

            $superiors = Employee::employeeParents($orderInstance->employee_id, array());
            $this->orderNotification($orderInstance->company_id, $superiors, $orderInstance->id, $schemeResponse, "update");
            return response()->json(['statuscode' => 200, 'message' => 'Deleted Successfully.']);
        } else {
            return response()->json(['statuscode' => 400, 'message' => 'Failed Deleting!!!']);
        }

        return response()->json(['statuscode' => 400, 'message' => 'Some Error Occured!!!']);
    }

    public function updateOrderAmount(Request $request)
    {
        $orderInstance = Order::findOrFail($request->id);
        $subtotal = $request->subtotal;
        $grandtotal = $request->grandtotal;
        $orderInstance->tot_amount = $subtotal;
        $orderInstance->grand_total = $grandtotal;
        $updated = $orderInstance->save();
        if ($updated) {
            return response()->json(['statuscode' => 200, 'message' => 'Amount updated successfully.', 'order' => $orderInstance]);
        } else {
            return response()->json(['statuscode' => 400, 'message' => 'Failed updating amount!!!']);
        }
    }

    public function massActions($domain, Request $request)
    {
        if ($request->mass_order_type == "massmaildownload" || $request->mass_order_type == "massmail") {
            if (!($request->email)) {
                return response(['status' => false, "type" => $request->mass_order_type, "message" => "Email is required if you want to mail."]);
            }
            if (!(filter_var($request->email, FILTER_VALIDATE_EMAIL))) {
                return response(['status' => false, "type" => $request->mass_order_type, "message" => "Email given is not valid mail."]);
            }
        }
        $company_id = config('settings.company_id');
        $company_name = config('settings.title');
        $orderIDS = explode(",", $request->order_id);
        if ($request->mass_order_type == "massdelete") {
            $orders = Order::select('orders.*', 'module_attributes.id as module_attributesID', 'module_attributes.title', 'module_attributes.color', 'module_attributes.order_amt_flag', 'module_attributes.order_edit_flag', 'module_attributes.order_delete_flag')->leftJoin('module_attributes', 'orders.delivery_status_id', 'module_attributes.id')->where('orders.company_id', $company_id)->whereIn('orders.id', $orderIDS)->get();
            $employeeIds = $orders->pluck('employee_id', 'id')->toArray();
            $deleteAbleIds = [];
            $deletedIDs = [];
            $safeOrders = [];
            foreach ($orders as $order) {
                if ($order->order_delete_flag == 1) {
                    array_push($deleteAbleIds, $order->id);
                    array_push($deletedIDs, $order->order_no);
                } else {
                    array_push($safeOrders, $order->order_no);
                }
            }
            Order::whereIn('id', $deleteAbleIds)->delete();
            foreach ($employeeIds as $orderId => $employeeid) {
                $finalArray = $orders->where('id', $orderId)->first();
                // $dataPayload = array("data_type" => "order", "order" => $orderId, "action" => "delete");

                // if ($employeeid == 0) {
                //     $superiors = DB::table('handles')->where('client_id', $finalArray->client_id)->pluck('employee_id')->toArray();
                // } else {
                //     $superiors = Employee::employeeParents($employeeid, array());
                // }
                // $fbIDs = DB::table('employees')->where(array(array('company_id', $finalArray->company_id), array('status', 'Active')))->whereIn('id', $superiors)->whereNotNull('firebase_token')->pluck('firebase_token');
                // $msgID = sendPushNotification_($fbIDs, 1, null, $dataPayload);

                $dataPayload = array("data_type" => "order", "order" => $orderId, "action" => "delete");

                if ($employeeid == 0) {
                    $superiors = DB::table('handles')->where('client_id', $finalArray->client_id)->pluck('employee_id')->toArray();
                } else {
                    $superiors = Employee::employeeParents($employeeid, array());
                }
                $partyHandles = DB::table('handles')->where('client_id', $finalArray->client_id)->whereNotIn('employee_id', $superiors)->pluck('employee_id')->toArray();
                $superiors = array_merge($partyHandles, $superiors);
                $fbIDs = DB::table('employees')->where(array(array('company_id', $finalArray->company_id), array('status', 'Active')))->whereIn('id', $superiors)->where('id', '<>', Auth::user()->EmployeeId())->whereNotNull('firebase_token')->pluck('firebase_token');
                $orderData['id'] = $orderId;
                saveAdminNotification($finalArray->company_id, Auth::user()->EmployeeId(), date("Y-m-d H:i:s"), "Deleted Order", "order", $orderData);

                $notificationAlert = array();
                $orderPrefix = getClientSetting()->order_prefix;
                $time = time();
                $notificationAlert[] = array(
                    "company_id" => $finalArray->company_id,
                    "employee_id" => $employeeid,
                    "data_type" => "order",
                    "data" => "",
                    "action_id" => $orderId,
                    "title" => "Order Deleted",
                    "description" => "Order {$orderPrefix}{$finalArray->order_no} has been deleted.",
                    "created_at" => date('Y-m-d H:i:s'),
                    "status" => 1,
                    "to" => 1,
                    "unix_timestamp" => "$time"
                );

                $msgID = sendPushNotification_($fbIDs, 1, $notificationAlert, $dataPayload);
                $selfApp = DB::table('employees')->where(array(array('company_id', $finalArray->company_id), array('status', 'Active')))->whereNotNull('firebase_token')->where('id', Auth::user()->EmployeeId())->pluck('firebase_token');
                sendPushNotification_($selfApp, 1, null, $dataPayload);
                




            }
            return response(['status' => true, 'type' => $request->mass_order_type, 'message' => 'Mass Delete Successful', 'deletedIDs' => $deletedIDs, 'safeIDs' => $safeOrders]);
        } else {
            $orders = Order::select('orders.*', 'module_attributes.id as ma_id', 'module_attributes.title')->leftJoin('module_attributes', 'orders.delivery_status_id', 'module_attributes.id')->where('orders.company_id', $company_id)->whereIn('orders.id', $orderIDS)->get();
            $ordersCount = $orders->count();
        }
        $pdfName = "";
        $html = '<!DOCTYPE html>
                <head>
                  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                  <title>
                  </title>
                  <style>
                    * { font-family: Noto Sans, sans-serif; }
                    th {
                      text-align: left;
                    }
                  
                    table.dataTable tbody th,
                    table.dataTable tbody td {
                      padding-left: 18px;
                    }
                  
                    .modal-dialog {
                      width: 850px;
                      margin: 30px auto;
                    }
                    .page-break {
                        page-break-after: always;
                    }
                  </style>
                </head>
                <body>';
        $count = 0;
        foreach ($orders as $order) {

            $partyTypeLevel = getCompanyPartyTypeLevel($company_id);
            $scheme_off_value = 0;
            $free_schemes = [];

            if (!$order->orderScheme->isEmpty()) {
                foreach ($order->orderScheme as $scheme) {
                    if ($scheme->is_amount) {
                        $scheme_off_value += $scheme->discount_amount;
                    } else {
                        $getScheme = App\Scheme::find($scheme->scheme_id);
                        $product = Product::withTrashed()->find($getScheme->offered_product);
                        $variant = ProductVariant::withTrashed()->where('product_id', $getScheme->offered_product)->find($getScheme->offered_product_variant);
                        $unit = isset($variant) ? UnitTypes::withTrashed()->find($variant->unit) : UnitTypes::withTrashed()->find($product->unit);
                        $free_schemes[] = array(
                            'id' => $scheme->scheme_id,
                            'freeItem' => $scheme->free_item,
                            'product_name' => isset($getScheme->offered_product) ? Product::find($getScheme->offered_product)->product_name : ' ',
                            'product_variant' => isset($getScheme->offered_product_variant) ? ProductVariant::where('product_id', $getScheme->offered_product)->find($getScheme->offered_product_variant)->variant : ' ',
                            'unit_name' => isset($unit) ? $unit->symbol : ''
                        );
                    }
                }
            }

            $module_attribute = ModuleAttribute::where('id', $order->delivery_status_id)->first();
            if ($module_attribute)
                $order->delivery_status = $module_attribute->title;


            $count++;
            $getClientSetting = getClientSetting();
            if (getClientSetting()->order_with_amt == 0) {
                $colspan = 5;
                if ($order->product_level_tax_flag == 1) {
                    $colspan += 1;
                }
                if ($order->product_level_discount_flag == 1) {
                    $colspan += 1;
                }
            } else {
                $colspan = 3;
            }
            $orderdetails = $order->orderdetails;
            if ($order->discount_type == '%') {
                $tot_amount = 0;
                if ($order->product_level_tax_flag == 1) {
                    $taxesTotal = [];
                    foreach ($orderdetails as $orderdetail) {
                        foreach ($orderdetail->taxes as $tax) {
                            $rate = $orderdetail->rate;
                            $taxAmount = ($rate * $tax->percent) / 100;
                            if (array_key_exists($tax->id, $taxesTotal)) {
                                $taxesTotal[$tax->id]["amount"] = $taxesTotal[$tax->id]["amount"] + $taxAmount;
                            } else {
                                $taxesTotal[$tax->id]["amount"] = $taxAmount;
                            }
                            $taxesTotal[$tax->id]["name"] = $tax->name . " (" . $tax->percent . "%)";
                        }
                        $qtyRate = $orderdetail->quantity * $orderdetail->rate;
                        $tot_amount += $qtyRate;
                        $orderdetail->qtyRate = $qtyRate;
                    }
                    $order->applicable_taxes = $taxesTotal;
                } else {
                    foreach ($orderdetails as $orderdetail) {
                        $qtyRate = $orderdetail->quantity * $orderdetail->rate;
                        $tot_amount += $qtyRate;
                        $orderdetail->qtyRate = $qtyRate;
                    }
                }
                $order->discount_amount = isset($order->discount) ? ($tot_amount * $order->discount) / 100 : 0;
            } else {
                $tot_amount = 0;

                if ($order->product_level_tax_flag == 1) {
                    $taxesTotal = [];
                    foreach ($orderdetails as $orderdetail) {
                        foreach ($orderdetail->taxes as $tax) {
                            $rate = $orderdetail->rate;
                            $taxAmount = ($rate * $tax->percent) / 100;
                            if (array_key_exists($tax->id, $taxesTotal)) {
                                $taxesTotal[$tax->id]["amount"] = $taxesTotal[$tax->id]["amount"] + $taxAmount;
                            } else {
                                $taxesTotal[$tax->id]["amount"] = $taxAmount;
                            }
                            $taxesTotal[$tax->id]["name"] = $tax->name . " (" . $tax->percent . "%)";
                        }
                        $qtyRate = $orderdetail->quantity * $orderdetail->rate;
                        $tot_amount += $qtyRate;
                        $orderdetail->qtyRate = $qtyRate;
                    }
                    $order->applicable_taxes = $taxesTotal;
                }
                $order->discount_amount = isset($order->discount) ? $order->discount : 0;
            }
            //Total
            $order->total = $order->tot_amount - $order->discount_amount;
            // Sub Total
            $order->tot_amount = $order->tot_amount;
            $view = view('company.orders.massdownload', compact('order', 'orderdetails', 'colspan', 'getClientSetting','partyTypeLevel', 'free_schemes', 'scheme_off_value'))->render();
            $html = $html . '<br>' . $view;
            if ($count != $ordersCount) {
                $html .= "<div class='page-break'></div>";
            }
        }
        $pdfName = date('Y-m-d') . '_' . rand(0, 1000) . '.pdf';
        $html .= '</body></html>';
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($html);
        $domain_directory = "cms/storage/app/public/uploads/" . $domain . "/invoices";
        $urls = "cms/storage/app/public/uploads/" . $domain . "/invoices/" . $pdfName;
        if (!is_dir($domain_directory)) {
            if (!is_dir("cms/storage/app/public/uploads/" . $domain)) mkdir("cms/storage/app/public/uploads/" . $domain);
            mkdir($domain_directory);
        }
        $pdf->save($urls);
        if ($request->mass_order_type == "massmaildownload" || $request->mass_order_type == "massmail") {
            $company_email = Auth::user()->isCompanyManager()->contact_email;
            $report_mail = array(
                'email' => $request->input('email'),
                'semail' => $company_email,
            );
            Mail::send([], ['url' => '', 'company_name' => ''], function ($message) use ($report_mail, $pdf, $pdfName, $company_name) {
                $message->attachData($pdf->output(), $pdfName);
                $message->from($report_mail['email'], $company_name);
                $message->to($report_mail['email'])->subject('Order Status');
            });
        }
        return response(["status" => true, "urls" => $urls, 'type' => $request->mass_order_type]);
    }

    public function productVariant(Request $request)
    {
        $product_variants = ProductVariant::where('product_id', $request->id)->with(array('colors' => function ($query) {
            $query->select('colors.value');
        }))->with('units')->get(['id', 'product_id', 'variant', 'mrp', 'unit', 'short_desc']);

        $enable_party_wise_rates = $request->enablePartyRate;
        $enable_party_category_wise_rates = $request->enablePartyCategoryRate;
        $rate_id = $request->rateId;

        foreach ($product_variants as $product_variant) {
            $colors = $product_variant->colors;
            if ($colors) {
                $product_variant->variant_colors = json_encode($colors->pluck('value')->toArray());
            } else {
                $product_variant->variant_colors = null;
            }

            $product_variant->unit_types = $product_variant->units->symbol;

            if ($enable_party_wise_rates == "true" && $rate_id) {
                $rate_instance = RateDetail::whereRateId($rate_id)->whereVariantId($product_variant->id)->first();
                if ($rate_instance) $product_variant->mrp = $rate_instance->mrp;
            }elseif($enable_party_category_wise_rates == "true" && $request->categoryRateIdToApply!=0){
                
                $rate_instance = CategoryRateTypeRate::whereCategoryRateTypeId($request->categoryRateIdToApply)->whereProductVariantId($product_variant->id)->first();
                if ($rate_instance) $product_variant->mrp = $rate_instance->mrp;
            }
        }
        return response()->json($product_variants);
    }

    public function productPartiesMrp(Request $request)
    {
        $enable_party_wise_rates = $request->enablePartyRate;
        $enable_party_category_wise_rates = $request->enablePartyCategoryRate;
        
        $default_mrp = $request->default_mrp;
        if($enable_party_wise_rates == "true"){
          $rate_id = $request->rateId;
          $rate_instance = RateDetail::whereRateId($rate_id)->whereProductId($request->id)->whereVariantId(NULL)->first();
          if ($rate_instance) return $rate_instance->mrp;
        }elseif($enable_party_category_wise_rates == "true" && $request->categoryRateIdToApply!=0 ){
          $rate_id = $request->categoryRateIdToApply;
          $rate_instance = CategoryRateTypeRate::whereCategoryRateTypeId($rate_id)->whereProductId($request->id)->whereProductVariantId(NULL)->first();
          if ($rate_instance) return $rate_instance->mrp;
        }

        return $default_mrp;
    }

    public function variantPartiesMrp(Request $request)
    {
        $enable_party_wise_rates = $request->enablePartyRate;
        $enable_party_category_wise_rates = $request->enablePartyCategoryRate;
        if($enable_party_wise_rates == "true"){
          $rate_id = $request->rateId;
          $rate_instance = RateDetail::whereRateId($rate_id)->whereVariantId($request->id)->first();
          if ($rate_instance) {
              return $rate_instance->mrp;
          } 
        }elseif($enable_party_category_wise_rates == "true" && $request->categoryRateIdToApply!=0){
          $rate_id = $request->categoryRateIdToApply;
          $rate_instance = CategoryRateTypeRate::whereCategoryRateTypeId($rate_id)->whereProductVariantId($request->id)->first();
          if ($rate_instance) return $rate_instance->mrp;
        }
        $default_mrp = ProductVariant::find($request->id);
        return $default_mrp->mrp;
    }

    private function getColors($id)
    {
        $colorIds = DB::table('color_product_variant')->where('product_variant_id', $id)->pluck('color_id')->toArray();
        if (empty($colorIds)) {
            return null;
        } else {
            $colors = Color::whereIn('id', $colorIds)->pluck('value')->toArray();
            return json_encode($colors);
        }
    }
}

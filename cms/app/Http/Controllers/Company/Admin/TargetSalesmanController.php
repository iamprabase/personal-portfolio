<?php
 
namespace App\Http\Controllers\Company\Admin; 

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Carbon\Carbon;
use Auth;
use App\Http\Controllers\Company\Admin\EmployeeController;
use App\User;
use App\TargetSalesman;
use App\Client;
use App\TargetSalesmanassign;
use App\TargetSalesmanassignHistory;
use App\Employee;
use App\Holiday;
use App\Order;
use App\NoOrder;
use App\Collection;
use App\ClientVisit;
use Log;
use Barryvdh\DomPDF\Facade as PDF;
use Session;

class TargetSalesmanController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
      $this->middleware('auth');
      $this->middleware('permission:targets-create', ['only' => ['create','store']]);
      $this->middleware('permission:targets-view');
      $this->middleware('permission:targets-update', ['only' => ['edit','update']]);
      $this->middleware('permission:targets_rep-view', ['only' => ['genReport']]);
    }

    public function index(Request $request){
      $company_id = config('settings.company_id');
      $data['alltargets'] = TargetSalesman::where('company_id', $company_id)->orderBy('created_at', 'asc')->pluck('target_name', 'id')->toArray();
      $data['allsalesman'] = Auth::user()->handleQuery('employee')->where('company_id', $company_id)->where('status', '=', 'Active')->orderby('id','asc')->where('name','!=','')->get()->pluck('name','id')->toArray();
      $data['alltargetoptions'] = DB::table('salesmantarget_optionstype')->get()->toArray();
      $data['targetvalue'] = ['1'=>'Daily','2'=>'Weekly','3'=>'Monthly'];
      
      return view('company.targetsalesman.index2',compact('data'));
    }

    public function targetSalesmanListsEdit(Request $request){
      $company_id = config('settings.company_id');
      $data['targetvalue'] = ['1'=>'Daily','2'=>'Weekly','3'=>'Monthly'];
      $company_id = config('settings.company_id');
      $salesman_id = (int)$request->id;
      $salesman_exist = Employee::find($salesman_id);
      $alltargetoptions = DB::table('salesmantarget_optionstype')->get()->toArray();
      if($salesman_exist){
        $salesman_name = $salesman_exist->name;
        $client_check = TargetSalesmanassign::where('company_id',$company_id)->where('salesman_id',$salesman_id)->get()->toArray();
        $employee_targets = Employee::has('targetsalesmanassign')->where('company_id', $company_id)->where('id',$salesman_id)->where('status', '=', 'Active')->get();

        $alltargets = TargetSalesman::where('company_id',$company_id)->groupby('target_groupid')->get()->toArray();
        $nestedData = array();
        // foreach($client_check as $res){
        //   $assigned_targets = $res->targetsalesmanassign->toArray();
        // }
        // print_r($assigned_targets);die();
        if(count($client_check)>0){
          $data['salestargetrecord'] = array();
          $i=0;
          $roles = '';$assigned_roles = array(); 
          foreach($client_check as $v=>$m){
            $assigned_roles[$m['target_groupid']][] = $m['target_rule'];
          }
          foreach($employee_targets as $v=>$m){
            $nestedData['id'] = ++$i;
            $nestedData['salesman_name'] = $salesman_name;
            $nestedData['salesman_id'] = $salesman_id;
            foreach($m->targetsalesmanassign->toArray() as $bb=>$gg){
              foreach($assigned_roles as $ll=>$xx){
                if($gg['target_groupid']==$ll){
                  $nestedData['assigned_roles'][$ll][] = $gg;
                }
              }
            }
            $salestargetrec = $nestedData;
          }
          
          // $data['assigned_roles'] = $assigned_roles; 
          $data['allrec'] = $salestargetrec;

          // print_r($client_check);die();
          // foreach($client_check as $m=>$n){
          //   $roles_a = (json_decode($n['target_rules']))->$salesman_id;
          //   foreach($roles_a as $n=>$p){
          //     foreach($p as $h){
          //         $target_name = TargetSalesman::where('target_groupid','=',(int)$h)->firstOrFail();
          //         if($target_name){
          //           $target_names = $target_name->id;
          //           foreach(json_decode($target_name->target_rules) as $tr){
          //             $abb = DB::table('salesmantarget_optionstype')->where('id',(int)$tr)->get()->toArray();
          //             $assigned_roles[$salesman_id][$target_names][] = $abb[0]->options_value;
          //           }
          //         }
          //     }
          //   }
          // }
          return view('company.targetsalesman.salesmantargetedit',compact('salesman_id','salesman_name','alltargets','client_check','alltargetoptions','data'));
        }else{  
          return redirect()->route('company.admin.salesmantarget', ['domain' => domain()])->with('warning', 'Selected salesman does not have any targets to update.');
        }
      }else{
        return redirect()->route('company.admin.salesmantarget', ['domain' => domain()])->with('error', 'Salesman does not exist.');
      }
    }

    public function targetSalesmanListsUpdate(Request $request){
      $company_id = config('settings.company_id');
      $salesmanid = (int)$request->get('salemsids');   
      $salesmanid_exist = Employee::find($salesmanid);
      if($salesmanid_exist){
        $saleman_target = TargetSalesmanassign::where('company_id',$company_id)->where('salesman_id',$salesmanid)->get()->toArray();
        if($saleman_target){
          $errMessages = [
            'tname.*.required' => 'Target Name is required.',
            'tname.*.unique' => 'Target Name already exist.',
            'topt.*.required' => 'Target Options is required .',
            'tinterval.*.required' => 'Target value is required .',
            'topt.*.numeric' => 'Wrong Target options.',
          ];
          $this->validate($request, [
            'tname.*' => 'required|unique:salesmantarget_assign,target_name,NULL,id,company_id,' . $company_id,
            'topt.*.*' => 'required|numeric',
            'tinterval.*.*' => 'required|numeric|between:1,3',
          ], $errMessages); 
          $targetname = $request->get('tname');
          $target_interval = $request->get('tinterval');
          $target_ids = $request->get('tgid');
          $target_value = $request->get('tval');
          $target_options = $request->get('topt');
          $totrows = $request->get('numofRows');


          foreach($totrows as $index=>$dt){
            $tg_name = $targetname[$index];
            foreach($dt as $kk=>$ll){
              $target_id = (int)$target_ids[$index][$ll];
              $check_exist = TargetSalesmanassign::where('id',$target_id)->where('salesman_id',$salesmanid)->where('company_id',$company_id)->first();
              if($check_exist){
                $salesmanassign = TargetSalesmanassign::find($target_id);
                $salesmanassign->target_name = $tg_name;
                $salesmanassign->target_interval = $target_interval[$index][$ll];
                $salesmanassign->target_values = $target_value[$index][$ll];
                $salesmanassign->target_rule = $target_options[$index][$ll];
                $salesmanassign->update();
              }
            }
          }


          //creating new target so it is availble for other salesman as well
          //target group id
          $targets = TargetSalesman::all()->count();
          if($targets==0){
            $targetgroup_id = 1;
          }else{
            $tgid = TargetSalesman::orderby('target_groupid','desc')->first();
            $targetgroup_id = ((int)$tgid->target_groupid)+1;
          }
          foreach($totrows as $index=>$dt){
            $tg_name = $targetname[$index];
            foreach($dt as $kk=>$ll){
              $salesmantarget = new TargetSalesman;
              $salesmantarget->target_name = $tg_name;
              $salesmantarget->company_id = $company_id;
              $salesmantarget->target_groupid = $targetgroup_id;
              $salesmantarget->target_interval = $target_interval[$index][$ll];
              $salesmantarget->target_value = $target_value[$index][$ll];
              $salesmantarget->target_rules = $target_options[$index][$ll];
              $salesmantarget->save();
            }
          }
          // $sl = TargetSalesmanassign::where('company_id',$company_id)->where('salesman_id',$salesmanid);
          // $sl->delete();
          // $total_options = count($target_options);
          // $salesmantargetassign = new TargetSalesmanassign;
          // $salesmantargetassign->company_id = $company_id;
          // $salesmantargetassign->salesman_id = $salesmanid;
          // $salesmantargetassign->target_tot_workingdays = $this->total_no_workingdays(Date('m'),Date('Y'));
          // $salesmantargetassign->target_progress = 0;
          // $salesmantargetassign->target_rules = json_encode(array($salesmanid=>array($target_options[0])));
          // $salesmantargetassign->save();
          // $salesmantargetassign = new TargetSalesmanassign;
          // $salesmantargetassign->company_id = $company_id;
          // $salesmantargetassign->salesman_id = $salesmanid;
          // $salesmantargetassign->target_groupid = $n['target_groupid'];
          // $salesmantargetassign->target_subid = $n['id'];
          // $salesmantargetassign->target_name = $n['target_name'];
          // $salesmantargetassign->target_interval = $n['target_interval'];
          // $salesmantargetassign->target_rule = $n['target_rules'];
          // $salesmantargetassign->target_values = $n['target_value'];
          // $salesmantargetassign->target_tot_workingdays = $this->total_no_workingdays(Date('m'),Date('Y'));
          // $salesmantargetassign->target_progress = 0;
          // $salesmantargetassign->target_rules = json_encode(array($val=>$salesmantarget));

          // if($request->has('numofRows')){
          //   $numOfRows = $request->get('numofRows');
          //   foreach($numOfRows as $index){
          //     $salesmantarget = TargetSalesman::find((int)$targed_ids[$index]);
          //     if($salesmantarget){
          //       $salesmantarget->target_name = $targetname;
          //       $salesmantarget->target_interval = $target_interval[$index];
          //       $salesmantarget->target_value = $target_value[$index];
          //       if(is_array($target_options)){
          //         if(array_key_exists($index, $target_options)){
          //           $salesmantarget->target_rules = json_encode($target_options[$index]);
          //         }
          //       }
          //       $salesmantarget->update();
          //     }
          //   }
          // }
  
          // if($request->has('newrow_numofRows')){
          //   $numOfRows = $request->get('newrow_numofRows');
          //   foreach($numOfRows as $index){
          //     $salesmantarget = new TargetSalesman;
          //     $salesmantarget->company_id = $company_id;
          //     $salesmantarget->target_groupid = $targetgroupid;
          //     $salesmantarget->target_name = $targetname;
          //     $salesmantarget->target_interval = $target_interval[$index];
          //     $salesmantarget->target_value = $target_value[$index];
          //     if(is_array($target_options)){
          //       if(array_key_exists($index, $target_options)){
          //         $salesmantarget->target_rules = json_encode($target_options[$index]);
          //       }
          //     }
          //     $salesmantarget->save();
          //   }
          // }

          return redirect()->route('company.admin.salesmantarget', ['domain' => domain()])->with('success', 'Salesman Target updated.');
        }else{
          return redirect()->route('company.admin.salesmantarget', ['domain' => domain()])->with('error', 'Targets Not available for update');
        }
      }else{
        return redirect()->route('company.admin.salesmantarget', ['domain' => domain()])->with('error', 'Wrong Salesman ID');
      }

    }


    public function targetsForDatatable(Request $request){
      $columns = array(0 => 'id',1 => 'salesman_name',2 => 'email',3 => 'phone',4 => 'assigned_roles',5 => 'target_progress',
        6 => 'action');
      $company_id = config('settings.company_id');
      $salesman = $request->input('brand');
      $order = $columns[0];
      $start = 0;
      $totalData = 1;
      $totalFiltered = 0;

      if($order == "id"){
        $order = 'name'; 
      }
      $dir = $request->input('order.0.dir');

      $prepQuery = Client::where('company_id', $company_id)->where('status', '=', 'Active')->where('name','!=','')->orderby('id','asc');

      if(isset($salesman))  $prepQuery = $prepQuery->where('name', $salesman);

      $m = 0;$totdata=0;
      $data = array();
      $selIds = $request->selIds; 
      $selectThisPageCheckBox = true;
      $results = $prepQuery->get();
      if(!empty($results)){   
        $i = $start;
        foreach($prepQuery->get() as $c=>$res){
          $clientwith_targetsassigned = ($res->targetsalesmanassign->toArray());
            if(count($clientwith_targetsassigned)>0){
              $totalFiltered = $totdata+1; 
              $totdata++;
              if(empty($request->input('search.value'))){
                  $totalFiltered = $totalFiltered;
                  $results = $prepQuery
                          ->orderBy('id', 'desc')
                          ->orderBy($order,$dir)
                          ->get();
                  if(!empty($results)){   
                    $i = $start;
            
                    foreach ($results as $c=>$res){
                      $id = $res->id;
                      $show = domain_route('company.admin.salesmantarget',[$id]);
                      $edit = domain_route('company.admin.salesmantarget',[$id]);
                      $delete = domain_route('company.admin.salesmantarget', [$id]);
            
                      $clientwith_targetsassigned = ($res->targetsalesmanassign->toArray());
                      if(count($clientwith_targetsassigned)>0){
                        $nestedData['id'] = ++$i;
                        $nestedData['salesman_name'] = $res->name;
                        $roles = '';
                        foreach($res->targetsalesmanassign->toArray() as $m=>$n){
                          $roles_a = json_decode($res->targetsalesmanassign->toArray()[$m]['target_rules']);
                          foreach($roles_a as $n=>$p){
                            foreach($p as $h){
                              foreach($h as $t){
                                $target_name = TargetSalesman::find((int)$t);
                                if($target_name){
                                  $roles .= $target_name->target_name.' ';
                                  foreach(json_decode($target_name->target_rules) as $tr){
                                    $abb = DB::table('salesmantarget_optionstype')->where('id',(int)$tr)->get()->toArray();
                                    $roles .= $abb[0]->options_value.' ';
                                  }
                                  $roles .= '<br>';
                                }
                              }
                            }
                          }
                        }
                        $nestedData['assigned_roles'] = $roles;
                        $nestedData['email'] = $res->email;
                        $nestedData['phone'] = $res->phone;
                        $nestedData['target_progress'] = $res->targetsalesmanassign->toArray()[$m]['target_progress'];
                        $deleteBtn = "<a class='btn btn-danger btn-sm delete' data-mid='{$id}' data-url='{$delete}' data-toggle='modal' data-target='#delete' style='padding: 3px 6px;'><i class='fa fa-trash-o'></i></a>"; 
                        $nestedData['action'] = "<a href='{$show}' class='btn btn-success btn-sm' style='    padding: 3px 6px;'><i class='fa fa-eye'></i></a>";
                        $nestedData['action'] = $nestedData['action'] ."<a href='{$edit}' class='btn btn-warning btn-sm' style='    padding: 3px 6px;'><i class='fa fa-edit'></i></a>";
                        $data[$nestedData['id']-1] = $nestedData;

                      }
                    }
                  }else{
                    $selectThisPageCheckBox = false;
                  }

              }elseif(!(empty($request->input('search.value')))) {
                $search = $request->input('search.value'); 
        
                $productsSearchQuery = $prepQuery
                                      ->where(function($query) use ($search){
                                        $query->orWhere('id','LIKE',"%{$search}%");
                                        $query->orWhere('name','LIKE',"%{$search}%");
                                        $query->orWhere('email','LIKE',"%{$search}%");
                                        $query->orWhere('phone','LIKE',"%{$search}%");
                                      });
                $results =  $productsSearchQuery
                                ->orderBy('id', 'desc')
                                ->orderBy($order,$dir)
                                ->get();
                if(!empty($results)){   
                  $i = $start;
                  $r = 0;
                  foreach ($results as $c=>$res){
                    $id = $res->id;
                    $show = domain_route('company.admin.salesmantarget',[$id]);
                    $edit = domain_route('company.admin.salesmantarget',[$id]);
                    $delete = domain_route('company.admin.salesmantarget', [$id]);
          
                    $clientwith_targetsassigned = ($res->targetsalesmanassign->toArray());
                    if(count($clientwith_targetsassigned)>0){
                      $r++;
                      $nestedData['id'] = ++$i;
                      $nestedData['salesman_name'] = $res->name;
                      $roles = '';
                      foreach($res->targetsalesmanassign->toArray() as $m=>$n){
                        $roles_a = json_decode($res->targetsalesmanassign->toArray()[$m]['target_rules']);
                        foreach($roles_a as $n=>$p){
                          foreach($p as $h){
                            foreach($h as $t){
                              $target_name = TargetSalesman::find((int)$t);
                              if($target_name){
                                $roles .= $target_name->target_name.' ';
                                foreach(json_decode($target_name->target_rules) as $tr){
                                  $abb = DB::table('salesmantarget_optionstype')->where('id',(int)$tr)->get()->toArray();
                                  $roles .= $abb[0]->options_value.' ';
                                }
                                $roles .= '<br>';
                              }
                            }
                          }
                        }
                      }
                      $nestedData['assigned_roles'] = $roles;
                      $nestedData['email'] = $res->email;
                      $nestedData['phone'] = $res->phone;
                      $nestedData['target_progress'] = $res->targetsalesmanassign->toArray()[$m]['target_progress'];
                      $deleteBtn = "<a class='btn btn-danger btn-sm delete' data-mid='{$id}' data-url='{$delete}' data-toggle='modal' data-target='#delete' style='padding: 3px 6px;'><i class='fa fa-trash-o'></i></a>"; 
                      $nestedData['action'] = "<a href='{$show}' class='btn btn-success btn-sm' style='    padding: 3px 6px;'><i class='fa fa-eye'></i></a>";
                      $nestedData['action'] = $nestedData['action'] ."<a href='{$edit}' class='btn btn-warning btn-sm' style='    padding: 3px 6px;'><i class='fa fa-edit'></i></a>";
                      $data[$nestedData['id']-1] = $nestedData;
                    }
                  }
                }else{
                  $selectThisPageCheckBox = false;
                }
                $totalFiltered = $r;
              }
            }
            $totalData = $totdata;
        }
      }

      $json_data = array(
          "draw"            => intval($request->input('draw')),  
          "recordsTotal"    => intval($totalData),  
          "recordsFiltered" => intval($totalFiltered), 
          "data"            => $data,
          "prevSelVal"      => $selIds,
          "selectThisPageCheckBox" => $selectThisPageCheckBox
      );

      return json_encode($json_data); 
    }

    public function create(){
      $data['targetvalue'] = ['1'=>'Daily','2'=>'Weekly','3'=>'Monthly'];
      $targetoptions = DB::table('salesmantarget_optionstype')->get();
      $data['targetoptions'] = array();
      foreach($targetoptions as $k=>$topt){
        switch($topt->options_module){
          case 'ord':
            if(config('settings.orders')==1){
              $data['targetoptions'][] = $topt;
            }
            break;

          case 'coll':
            if(config('settings.collections')==1){
              $data['targetoptions'][] = $topt;
            }
            break;

          case 'vis':
            if(config('settings.visit_module')==1){
              $data['targetoptions'][] = $topt;
            }
            break;

          case 'party':
            if(config('settings.party')==1){
              $data['targetoptions'][] = $topt;
            }
            break;

          case 'ord_zero':
            if(config('settings.orders')==1 && config('settings.zero_orders')==1){
              $data['targetoptions'][] = $topt;
            }
            break;
        }
      }
      return view('company.targetsalesman.newtarget',compact('data'));
    }

    public function store(Request $request){
      $company_id = config('settings.company_id');
      $getClientSetting = getClientSetting();
      $errMessages = [
        'tname.required' => 'Target Name is required.',
        'tname.unique' => 'Target Name already exist.',
        'topt.*.required' => 'Target Options is required .',
        'tinterval.*.required' => 'Target value is required .',
        'topt.*.numeric' => 'Wrong Target options.',
      ];
      $this->validate($request, [
        'tname' => 'required|unique:salesmantarget,target_name,NULL,id,company_id,' . $company_id,
        'topt.*.*' => 'required|numeric',
        'tinterval.*' => 'required|numeric|between:1,3',
      ], $errMessages);    
      $targetname = $request->get('tname')[0];
      $target_interval = $request->get('tinterval');
      $target_options = $request->get('topt');
      $target_value = $request->get('tval');
      $totalrows = $request->get('numofRows');
      foreach($totalrows as $v){
        $availablerows[] = explode('_',$v)[1];
      }

      //target group id
      $targets = TargetSalesman::all()->count();
      if($targets==0){
        $targetgroup_id = 1;
      }else{
        $tgid = TargetSalesman::orderby('target_groupid','desc')->first();
        $targetgroup_id = ((int)$tgid->target_groupid)+1;
      }

      foreach($availablerows as $index){
        $salesmantarget = new TargetSalesman;
        $salesmantarget->target_name = $targetname;
        $salesmantarget->employee_id = Auth::user()->EmployeeId();
        $salesmantarget->company_id = $company_id;
        $salesmantarget->target_groupid = $targetgroup_id;
        $salesmantarget->target_interval = $target_interval[$index];
        $salesmantarget->target_value = $target_value[$index];
        if(is_array($target_options)){
          if(array_key_exists($index, $target_options)){
            $salesmantarget->target_rules = ($target_options[$index]);
          }
        }
        $salesmantarget->save();
      }
      return redirect()->route('company.admin.salesmantargetlist.show', ['domain' => domain()])->with('success', 'Salesman Target created');
    }

    public function targetAssign(){
      $company_id = config('settings.company_id');
      // $data['alltargets'] = TargetSalesman::where('company_id', $company_id)->groupby('target_groupid')->orderBy('created_at', 'asc')->pluck('target_name', 'target_groupid')->toArray();
      $userid = Auth::user()->EmployeeId();
      $data['allsalesman'] = Auth::user()->handleQuery('employee')->whereNotIn('id',[$userid])->where('company_id', $company_id)->where('status', '=', 'Active')->where('name','!=','')->orderBy('name', 'asc')->pluck('name', 'id')->toArray();
      $data['targetvalue'] = ['1'=>'Daily','2'=>'Weekly','3'=>'Monthly'];
      // $data['targetoptions'] = DB::table('salesmantarget_optionstype')->get();
      $data['alltargets'] = Auth::user()->handleQuery('TargetSalesman')->where('company_id', $company_id)->groupby('target_groupid')->pluck('target_name', 'target_groupid')->toArray();
      $targetoptions = DB::table('salesmantarget_optionstype')->get();
      $data['targetoptions'] = array();
      foreach($targetoptions as $k=>$topt){
        switch($topt->options_module){
          case 'ord':
            if(config('settings.orders')==1){
              $data['targetoptions'][] = $topt;
            }
            break;

          case 'coll':
            if(config('settings.collections')==1){
              $data['targetoptions'][] = $topt;
            }
            break;

          case 'vis':
            if(config('settings.visit_module')==1){
              $data['targetoptions'][] = $topt;
            }
            break;

          case 'party':
            if(config('settings.party')==1){
              $data['targetoptions'][] = $topt;
            }
            break;

          case 'ord_zero':
            if(config('settings.orders')==1 && config('settings.zero_orders')==1){
              $data['targetoptions'][] = $topt;
            }
            break;
        }
      }
      return view('company.targetsalesman.assigntarget2',compact('data'));
    }

    public function total_no_workingdays($month,$year){
      $saturdays=0;
      $total_days=cal_days_in_month(CAL_GREGORIAN, $month, $year);
      for($i=1;$i<=$total_days;$i++)
      if(date('N',strtotime($year.'-'.$month.'-'.$i))==6)
      $saturdays++;
      $total_workingdays = $total_days-$saturdays;
      return $total_workingdays;
    }

    public function targetAssignSave(Request $request){
      // echo 'test';die();
      $company_id = config('settings.company_id');
      $errMessages = [
        'salesmnaname.*.required' => 'Salesman Name is required.',
        'salesmantarget.*.required' => 'Salesman Target is required .',
        'salesmnaname.*.numeric' => 'Invalid Salesman .',
        'salesmantarget.*.numeric' => 'Wrong Target assignment to Salesman.',
      ];
      $this->validate($request, [
        'salesmnaname.*.*' => 'required|numeric',
        'salesmantarget.*.*' => 'required|numeric',
      ], $errMessages);    
      $salesmnaname = $request->get('salesmnaname');
      $salesmantarget = $request->get('salesmantarget');

      //copy old targets to target assign history table
      //delete current targets from the target assign table and save current targets to this table

      $targetparameters = TargetSalesman::where('target_groupid',$salesmantarget)->get()->toArray();

        if(is_array($salesmnaname[0])){
          if(count($targetparameters)>0){

            $allsalesman_id = $salesmnaname[0];
            foreach($allsalesman_id as $salesmanid){
              //copy old targets to target assign history table
              $targets_count = DB::table('salesmantarget_assign_history')->count();
              if($targets_count==0){
                $targethist_newgroupid = 1;
              }else{
                $tgid = DB::table('salesmantarget_assign_history')->orderby('targethist_newgroupid','desc')->first();
                $targethist_newgroupid = ((int)$tgid->targethist_newgroupid)+1;
              }

              $targetsassignedit_count = DB::table('salesmantarget_assignedit_history')->count();
              if($targetsassignedit_count==0){
                $targetassedithist_newgroupid = 1;
              }else{
                $tgid = DB::table('salesmantarget_assignedit_history')->orderby('targethist_newgroupid','desc')->first();
                $targetassedithist_newgroupid = ((int)$tgid->targethist_newgroupid)+1;
              }


              $salesmanid_oldtgt = TargetSalesmanassign::where('company_id',$company_id)->where('salesman_id',$salesmanid)->get()->toArray();
              foreach($salesmanid_oldtgt as $ndx=>$xnd){
                DB::table('salesmantarget_assign_history')->insert([
                  'targetid_original' => $salesmanid_oldtgt[$ndx]['id'],
                  'targethist_newgroupid' => $targethist_newgroupid,
                  'company_id' => $salesmanid_oldtgt[$ndx]['company_id'],
                  'salesman_id' => $salesmanid_oldtgt[$ndx]['salesman_id'],
                  'target_groupid' => $salesmanid_oldtgt[$ndx]['target_groupid'],
                  'target_name' => $salesmanid_oldtgt[$ndx]['target_name'],
                  'target_subid' => $salesmanid_oldtgt[$ndx]['target_subid'],
                  'target_rule' => $salesmanid_oldtgt[$ndx]['target_rule'],
                  'target_interval' => $salesmanid_oldtgt[$ndx]['target_interval'],
                  'target_values' => $salesmanid_oldtgt[$ndx]['target_values'],
                  'target_tot_workingdays' => $salesmanid_oldtgt[$ndx]['target_tot_workingdays'],
                  'target_progress' => $salesmanid_oldtgt[$ndx]['target_progress'],
                  'target_assigneddate' => $salesmanid_oldtgt[$ndx]['created_at'],
                  'target_changeddate' => Carbon::now()->toDateTimeString(),
                  'target_startmonth' => $salesmanid_oldtgt[$ndx]['target_startmonth'],
                ]);


                DB::table('salesmantarget_assignedit_history')->insert([
                  'targetid_original' => $salesmanid_oldtgt[$ndx]['id'],
                  'targethist_newgroupid' => $targetassedithist_newgroupid,
                  'company_id' => $salesmanid_oldtgt[$ndx]['company_id'],
                  'salesman_id' => $salesmanid_oldtgt[$ndx]['salesman_id'],
                  'target_groupid' => $salesmanid_oldtgt[$ndx]['target_groupid'],
                  'target_name' => $salesmanid_oldtgt[$ndx]['target_name'],
                  'target_subid' => $salesmanid_oldtgt[$ndx]['target_subid'],
                  'target_rule' => $salesmanid_oldtgt[$ndx]['target_rule'],
                  'target_interval' => $salesmanid_oldtgt[$ndx]['target_interval'],
                  'target_values' => $salesmanid_oldtgt[$ndx]['target_values'],
                  'target_tot_workingdays' => $salesmanid_oldtgt[$ndx]['target_tot_workingdays'],
                  'target_progress' => $salesmanid_oldtgt[$ndx]['target_progress'],
                  'target_assigneddate' => $salesmanid_oldtgt[$ndx]['target_startmonth'],
                  'target_changeddate' => Carbon::now()->toDateTimeString(),
                  'target_startmonth' => $salesmanid_oldtgt[$ndx]['target_startmonth'],
                ]);

              }

              $salesman_oldtgtdel = TargetSalesmanassign::where('company_id',$company_id)->where('salesman_id',$salesmanid);
              $salesman_oldtgtdel->delete();

              foreach($targetparameters as $b=>$n){
                $salesmantargetassign = new TargetSalesmanassign;
                $salesmantargetassign->company_id = $company_id;
                $salesmantargetassign->salesman_id = $salesmanid;
                $salesmantargetassign->target_groupid = $n['target_groupid'];
                $salesmantargetassign->target_subid = $n['id'];
                $salesmantargetassign->target_name = $n['target_name'];
                $salesmantargetassign->target_interval = $n['target_interval'];
                $salesmantargetassign->target_rule = $n['target_rules'];
                $salesmantargetassign->target_values = $n['target_value'];
                $salesmantargetassign->target_tot_workingdays = $this->total_no_workingdays(Date('m'),Date('Y'));
                $salesmantargetassign->target_progress = 0;
                $salesmantargetassign->target_startmonth = Carbon::now()->format('Y-m-d');
                $salesmantargetassign->save();
              }

            }
            return redirect()->route('company.admin.salesmantarget', ['domain' => domain()])->with('success', 'Target assigned to selected Salesman.');
          }else{
            return redirect()->route('company.admin.salesmantarget', ['domain' => domain()])->with('error', 'No Such targets Found.');

          }
        }
    }

    public function targetlistShow(){
      $company_id = config('settings.company_id');
      // $target_options = DB::table('salesmantarget_optionstype')->get()->toArray();
      $curruser = Auth::user()->EmployeeId();
      $emptargets = TargetSalesmanassign::where('company_id',$company_id)->where('salesman_id',$curruser)->pluck('target_groupid')->toArray();
      $emptargets = array_values(array_unique($emptargets));

      $targetoptions = DB::table('salesmantarget_optionstype')->get()->toArray();
      $tgtpts = array();
      foreach($targetoptions as $k=>$topt){
        switch($topt->options_module){
          case 'ord':
            if(config('settings.orders')==1){
              $tgtpts[] = $topt;
            }
            break;

          case 'coll':
            if(config('settings.collections')==1){
              $tgtpts[] = $topt;
            }
            break;

          case 'vis':
            if(config('settings.visit_module')==1){
              $tgtpts[] = $topt;
            }
            break;

          case 'party':
            if(config('settings.party')==1){
              $tgtpts[] = $topt;
            }
            break;

          case 'ord_zero':
            if(config('settings.orders')==1 && config('settings.zero_orders')==1){
              $tgtpts[] = $topt;
            }
            break;
        }
      }
      $target_options = $tgtpts;
   
      // $is_admin = Auth::user()->employee->is_admin; 
      // $handled_employees = $is_admin? Employee::whereCompanyId($company_id)->pluck('id')->toArray(): Employee::EmployeeChilds(Auth::user()->EmployeeId(), array());
      // array_push($handled_employees, Auth::user()->EmployeeId());
      // $results = TargetSalesmanassign::where('company_id',$company_id)
      //                 ->whereIn('salesman_id',$handled_employees)
      //                 ->groupby('target_subid')
      //                 ->orderby('created_at','desc')->get();
      $results = Auth::user()->handleQuery('TargetSalesman')->where('company_id',$company_id)
                  ->orderby('updated_at','desc')->get();

      $target_ifdelpossible = TargetSalesman::whereNotIN('target_groupid',DB::table('salesmantarget_assign')->pluck('target_groupid')->toArray())->orderby('created_at','desc')->get()->toArray();
      $tgid = array(); $data = array();
      $canbedeleted = array();
      foreach($target_ifdelpossible as $koo=>$oko){
        $canbedeleted[] = $oko['target_groupid'];
      }
      $canbedeleted = array_values(array_unique($canbedeleted));

      foreach($target_options as $ky=>$vl){
        $tgtopt[$vl->id] = $vl->options_value;
      }
      if(!empty($results)){   
        $i = 1;
        foreach($results as $ky=>$res){
          $targetgroupid = $res['target_groupid'];
          $tgid[] = $targetgroupid;
        }
        $tgid = array_values(array_unique($tgid));
        foreach($results as $res){
          for($j=0;$j<count($tgid);$j++){
            $id = $res['id'];
            $targetgrpid = $res['target_groupid'];
            $nestedData['id'] = ++$i;
            $nestedData['target_name'] = $res['target_name'];
            $nestedData['target_groupid'] = $res['target_groupid'];
            $nestedData['target_rules'] = $res['target_rules'];
            $nestedData['target_interval'] = $res['target_interval'];
            $nestedData['target_value'] = $res['target_value'];
            $nestedData['emp_id'] = $res['employee_id'];
            if($tgid[$j]==$targetgrpid){
              $data[$tgid[$j]][] = $nestedData;
            }
          }
        }
        if(count($data)>0){
          foreach($data as $k=>$v){
            array_multisort( array_column( $v, 'target_rules' ), SORT_ASC, SORT_NUMERIC, $v );
            $data[$k] = $v;
          }
        }       
      }
      return view('company.targetsalesman.targetlist',compact('data','tgtopt','canbedeleted','emptargets','curruser'));
    }

    public function targetsListsEdit(Request $request){ 
      $company_id = config('settings.company_id');
      $data['targetvalue'] = ['1'=>'Daily','2'=>'Weekly','3'=>'Monthly'];
      // $data['targetoptions'] = DB::table('salesmantarget_optionstype')->get();
      $targetoptions = DB::table('salesmantarget_optionstype')->get();
      $data['targetoptions'] = array();
      foreach($targetoptions as $k=>$topt){
        switch($topt->options_module){
          case 'ord':
            if(config('settings.orders')==1){
              $data['targetoptions'][] = $topt;
            }
            break;

          case 'coll':
            if(config('settings.collections')==1){
              $data['targetoptions'][] = $topt;
            }
            break;

          case 'vis':
            if(config('settings.visit_module')==1){
              $data['targetoptions'][] = $topt;
            }
            break;

          case 'party':
            if(config('settings.party')==1){
              $data['targetoptions'][] = $topt;
            }
            break;

          case 'ord_zero':
            if(config('settings.orders')==1 && config('settings.zero_orders')==1){
              $data['targetoptions'][] = $topt;
            }
            break;
        }
      }
      $targetid = (int)$request->id;
      $target = TargetSalesman::where('company_id',$company_id)->where('target_groupid','=',$targetid)->get()->toArray();
      if($target){
        return view('company.targetsalesman.edittarget',compact('data','target','targetid'));
      }else{
        return redirect()->route('company.admin.salesmantargetlist.show', ['domain' => domain()])->with('error', 'Selected salesman target does not exist.');
      }
    }

    public function update(Request $request){ 
      $company_id = config('settings.company_id');
      $targetgroupid = (int)$request->id;
      $target = TargetSalesman::where('company_id',$company_id)->where('target_groupid','=',$targetgroupid)->get()->toArray();

      if($target){
        $name_chk = 'same';
        $previous_name = $target[0]['target_name'];
        $targetname = $request->get('tname');
        if(strcmp($previous_name,$targetname)!=0){
          $name_chk = 'nsame';
        }
        $target_interval = $request->get('tinterval');
        $target_options = $request->get('topt');
        $target_value = $request->get('tval');
        $targed_ids = $request->get('tgid');
        $errMessages = [
          'tname.required' => 'Target Name is required.',
          'tname.unique' => 'Target Name already exist.',
          'topt.*.required' => 'Target Options is required .',
          'tinterval.*.required' => 'Target value is required .',
          'topt.*.numeric' => 'Wrong Target options.',
        ];
        $this->validate($request, [
          'tname' => 'required|unique:salesmantarget,target_name,' . $request->id . ',target_groupid,company_id,' . $company_id,
          'topt.*.*' => 'required|numeric',
          'tinterval.*' => 'required|numeric|between:1,3',
        ], $errMessages);   

        $removeIds = explode('-',$request->get('removeTgtIds'));
        unset($removeIds[(count($removeIds))-1]);

        //create new entry in salesmantarget history table after updating this target
        $targets_count = DB::table('salesmantarget_edit_history')->count();
        if($targets_count==0){
          $targethist_newgroupid = 1;
        }else{
          $tgid = DB::table('salesmantarget_edit_history')->orderby('targethist_newgroupid','desc')->first();
          $targethist_newgroupid = ((int)$tgid->targethist_newgroupid)+1;
        }
        $targetsassignedit_count = DB::table('salesmantarget_assignedit_history')->count();
        if($targetsassignedit_count==0){
            $targetassedithist_newgroupid = 1;
        }else{
            $tgid = DB::table('salesmantarget_assignedit_history')->orderby('targethist_newgroupid','desc')->first();
            $targetassedithist_newgroupid = ((int)$tgid->targethist_newgroupid)+1;
        }
        $saleman_target = TargetSalesman::where('company_id',$company_id)->where('target_groupid',$targetgroupid)->get()->toArray();
        $currently_assignedsalesman = TargetSalesmanassign::where('company_id',$company_id)->where('target_groupid',$targetgroupid)->pluck('salesman_id')->toArray();
        $currently_assignedsalesman_dt = array_values(array_unique($currently_assignedsalesman));
        
        $fromforms = $fromdb = array();
        if($request->has('numofRows')){
          $nrows = $request->get('numofRows');
          foreach($nrows as $inx){
            $fromforms['target_rules'][] = $target_options[$inx];
            $fromforms['target_interval'][] = $target_interval[$inx];
            $fromforms['target_value'][] = $target_value[$inx];
          }
        }
        if($request->has('newrow_numofRows')){
          $nrows2 = $request->get('newrow_numofRows');
          foreach($nrows2 as $inx2){
            $fromforms['target_rules'][] = $target_options[$inx2];
            $fromforms['target_interval'][] = $target_interval[$inx2];
            $fromforms['target_value'][] = $target_value[$inx2];
          }
        }
        foreach($target as $kl=>$lm){
          $fromdb['target_rules'][] = $lm['target_rules'];
          $fromdb['target_interval'][] = $lm['target_interval'];
          $fromdb['target_value'][] = $lm['target_value'];
        }
        $chkcounter = 'same';
        if(count($fromdb['target_rules'])==count($fromforms['target_rules'])){
          foreach($fromdb as $opt=>$optval){
            foreach($optval as $ind=>$inv){
              if(in_array($inv,$fromforms[$opt])){
                $tgint1 = (int)$fromdb['target_interval'][$ind];
                $tgval1 = (int)$fromdb['target_value'][$ind];
                $tgint2 = (int)$fromforms['target_interval'][$ind];
                $tgval2 = (int)$fromforms['target_value'][$ind];
                if($tgint1!=$tgint2 || $tgval1!=$tgval2){
                  $chkcounter = 'nsame';
                  break;
                }
              }else{
                $chkcounter = 'nsame';
                break;
              }
            }
          }
        }else{
          $chkcounter = 'nsame';
        }

        foreach($saleman_target as $ndx=>$xnd){
          DB::table('salesmantarget_edit_history')->insert([
            'targetid_original' => $saleman_target[$ndx]['id'],
            'targethist_newgroupid' => $targethist_newgroupid,
            'company_id' => $saleman_target[$ndx]['company_id'],
            'target_groupid' => $saleman_target[$ndx]['target_groupid'],
            'target_name' => $saleman_target[$ndx]['target_name'],
            'target_rule' => $saleman_target[$ndx]['target_rules'],
            'target_interval' => $saleman_target[$ndx]['target_interval'],
            'target_values' => $saleman_target[$ndx]['target_value'],
            'target_editdate' => Carbon::now()->toDateTimeString(),
            'target_createddate' => $saleman_target[$ndx]['created_at'],
          ]);
          
          if($chkcounter=='nsame'){
            foreach($currently_assignedsalesman_dt as $kh){
              DB::table('salesmantarget_assignedit_history')->insert([
                'targetid_original' => $saleman_target[$ndx]['id'],
                'targethist_newgroupid' => $targetassedithist_newgroupid,
                'company_id' => $saleman_target[$ndx]['company_id'],
                'salesman_id' => $kh,
                'target_groupid' => $saleman_target[$ndx]['target_groupid'],
                'target_name' => $saleman_target[$ndx]['target_name'],
                'target_subid' => 1,
                'target_rule' => $saleman_target[$ndx]['target_rules'],
                'target_interval' => $saleman_target[$ndx]['target_interval'],
                'target_values' => $saleman_target[$ndx]['target_value'],
                'target_tot_workingdays' => 1,
                'target_progress' => '0',
                'target_assigneddate' => $saleman_target[$ndx]['created_at'],
                'target_changeddate' => Carbon::now()->toDateTimeString(),
                'target_startmonth' => Carbon::now()->toDateTimeString(),
              ]);
            }
          }

        }
        
        if($request->has('numofRows')){
          $numOfRows = $request->get('numofRows');
          foreach($numOfRows as $index){
            $salesmantarget = TargetSalesman::find((int)$targed_ids[$index]);
            if($salesmantarget){
              $salesmantarget->target_name = $targetname;
              $salesmantarget->target_interval = $target_interval[$index];
              $salesmantarget->target_value = $target_value[$index];
              $salesmantarget->employee_id = Auth::user()->EmployeeId();
              if(is_array($target_options)){
                if(array_key_exists($index, $target_options)){
                  $salesmantarget->target_rules = $target_options[$index];
                }
              }
              $salesmantarget->update();
            }
            //change to other assigned targets as well
            $salesmanassignedtgts = TargetSalesmanassign::where('target_subid',(int)$targed_ids[$index])->get()->toArray();
            if(count($salesmanassignedtgts)>0){
              foreach($salesmanassignedtgts as $ky=>$vle){
                $assignedtgt_upd = TargetSalesmanassign::find((int)$vle['id']);
                $assignedtgt_upd->target_name = $targetname;
                $assignedtgt_upd->target_interval = $target_interval[$index];
                $assignedtgt_upd->target_values = $target_value[$index];
                $assignedtgt_upd->target_rule = $target_options[$index];
                $assignedtgt_upd->target_startmonth = Carbon::now()->format('Y-m-d');
                $assignedtgt_upd->update();              
              }
            }
          }
        }

        if($request->has('newrow_numofRows')){
          $numOfRows = $request->get('newrow_numofRows');
          foreach($numOfRows as $index){
            $salesmantarget = new TargetSalesman;
            $salesmantarget->company_id = $company_id;
            $salesmantarget->target_groupid = $targetgroupid;
            $salesmantarget->target_name = $targetname;
            $salesmantarget->target_interval = $target_interval[$index];
            $salesmantarget->target_value = $target_value[$index];
            $salesmantarget->employee_id = Auth::user()->EmployeeId();
            if(is_array($target_options)){
              if(array_key_exists($index, $target_options)){
                $salesmantarget->target_rules = $target_options[$index];
              }
            }
            $salesmantarget->save();            
            //insert to assigned salesman as well
            $new_targetsubid = $salesmantarget->id;
            $salesmanassignedtgts = TargetSalesmanassign::where('target_groupid',$targetgroupid)->groupby('salesman_id')->get()->toArray();
            if(count($salesmanassignedtgts)>0){
              foreach($salesmanassignedtgts as $ky=>$vle){
                $assignedtgt_upd = new TargetSalesmanassign;
                $assignedtgt_upd->company_id = $vle['company_id'];;
                $assignedtgt_upd->target_subid = $new_targetsubid;
                $assignedtgt_upd->salesman_id = $vle['salesman_id'];
                $assignedtgt_upd->target_groupid = $targetgroupid;
                $assignedtgt_upd->target_tot_workingdays = $vle['target_tot_workingdays'];
                $assignedtgt_upd->target_progress = $vle['target_progress'];
                $assignedtgt_upd->target_name = $targetname;
                $assignedtgt_upd->target_interval = $target_interval[$index];
                $assignedtgt_upd->target_values = $target_value[$index];
                $assignedtgt_upd->target_rule = $target_options[$index];
                $assignedtgt_upd->target_startmonth = Carbon::now()->format('Y-m-d');
                $assignedtgt_upd->save();              
              }
            }
          }
        }

        //remove from salestarget, salestargetassign, target edit history and salestarget assign history
        foreach($removeIds as $subtgtid){
          $salesmantarget = TargetSalesman::find((int)$subtgtid);
          $salesmantarget->delete();
          $salesmantarget_assign = TargetSalesmanassign::where('company_id',$company_id)->where('target_subid',$subtgtid);
          $salesmantarget_assign->delete();
          // $salesmantarget_assign_history = DB::table('salesmantarget_assign_history')->where('target_subid',$subtgtid)->delete();
          // $salesmantarget_edit_history = DB::table('salesmantarget_edit_history')->where('targetid_original',$subtgtid)->delete();
        }

        return redirect()->route('company.admin.salesmantargetlist.show', ['domain' => domain()])->with('success', 'Selected Target updated.');
      }
    }

    public function targetDelete(Request $request){
      $tgroupid = $request->input('groupid');
      $tgroupexist = TargetSalesman::where('target_groupid',$tgroupid);
      if($tgroupexist){
        $groupid_assigned = TargetSalesmanassign::where('target_groupid',$tgroupid)->first();
        if($groupid_assigned){
          return response()->json(['error'=>'Cant delete, Target is assigned to the Salesman'],402);
        }else{
          $groupid_assigned = TargetSalesman::where('target_groupid',$tgroupid);
          $groupid_assigned->delete();
          return response()->json(['msg'=>'Target Deleted'],200);
        }
      }else{
        return response()->json(['error'=>'Target not exist'],404);
      }
    }

    public function targetsListDatatable(Request $request){
      $columns = array(0 => 'id',1 => 'target_name',2 => 'target_rules',3 => 'target_interval',4 => 'target_value',5 => 'created_at', 6 => 'action');
      $company_id = config('settings.company_id');
      $start = $request->input('start');
      $limit = $request->input('length');
      $order = $columns[$request->input('order.0.column')];
      if($order == "id"){
        $order = 'target_name'; 
      }
      $dir = $request->input('order.0.dir');
      $prepQuery = TargetSalesman::where('company_id',$company_id)->groupby('target_groupid')->groupby('target_name');
      
      $totalData =  $prepQuery->count();
      $totalFiltered = $totalData; 
      
      $data = array();
      $selIds = $request->selIds; 
      $selectThisPageCheckBox = true;
      
      if(empty($request->input('search.value'))){
          
        $totalFiltered = $prepQuery->count();
        $results = $prepQuery
                ->offset($start)
                ->limit($limit)
                ->orderBy('id', 'asc')
                ->orderBy($order,$dir)
                ->get();
      }elseif(!(empty($request->input('search.value')))) {

        $search = $request->input('search.value'); 

        $productsSearchQuery = $prepQuery
                              ->where(function($query) use ($search){
                                $query->orWhere('target_name','LIKE',"%{$search}%");
                                $query->orWhere('target_rules','LIKE',"%{$search}%");
                                $query->orWhere('target_value','LIKE',"%{$search}%");
                                $query->orWhere('target_interval','LIKE',"%{$search}%");
                                $query->orWhere('created_at','LIKE',"%{$search}%");
                              });
        $totalFiltered = $productsSearchQuery->count();
        $results =  $productsSearchQuery
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy('id', 'asc')
                        ->orderBy($order,$dir)
                        ->get();
      }

      if(!empty($results)){   
        $i = $start;
        foreach($results as $res){
          $targetgroupid = $res->target_groupid;
          $tgid[] = $targetgroupid;
        }
        $tgid = array_values(array_unique($tgid));
        foreach($results as $res){
          for($j=0;$j<count($tgid);$j++){
            $id = $res->id;
            $show = domain_route('company.admin.salesmantarget',[$id]);
            $edit = domain_route('company.admin.salesmantarget',[$id]);
            $delete = domain_route('company.admin.salesmantarget', [$id]);

            $targetgrpid = $res->target_groupid;
            $nestedData['id'] = ++$i;
            $nestedData['target_name'] = $res->target_name;
            $nestedData['target_rules'] = $res->target_rules;
            $nestedData['target_interval'] = $res->target_interval;
            $nestedData['target_value'] = $res->target_value;
            $nestedData['created_at'] = $res->created_at;
          
            // $nestedData['id'] = ++$i;
            // $nestedData['target_name'] = $res->target_name;
            // $nestedData['target_rules'] = $res->target_rules;
            // $nestedData['target_interval'] = $res->target_interval;
            // $nestedData['target_value'] = $res->target_value;
            // $nestedData['created_at'] = $res->created_at;
            $deleteBtn = "<a class='btn btn-danger btn-sm delete' data-mid='{$id}' data-url='{$delete}' data-toggle='modal' data-target='#delete' style='padding: 3px 6px;'><i class='fa fa-trash-o'></i></a>"; 
            $nestedData['action'] = "<a href='{$show}' class='btn btn-success btn-sm' style='    padding: 3px 6px;'><i class='fa fa-eye'></i></a>";
            $nestedData['action'] = $nestedData['action'] ."<a href='{$edit}' class='btn btn-warning btn-sm' style='    padding: 3px 6px;'><i class='fa fa-edit'></i></a>".' '.$deleteBtn;

             if($tgid[$j]==$targetgrpid){
              $data[$tgid[$j]][] = $nestedData;
             }
          }
        }
      }else{
        $selectThisPageCheckBox = false;
      }

      // print_r(($data));
      // die();

      $json_data = array(
          "draw"            => intval($request->input('draw')),  
          "recordsTotal"    => intval($totalData),  
          "recordsFiltered" => intval($totalFiltered), 
          "data"            => $data,
          "prevSelVal"      => $selIds,
          "selectThisPageCheckBox" => $selectThisPageCheckBox
      );

      return json_encode($json_data); 
    }


    public function genReport(Request $request){
      if(!Auth::user()->can('targets_rep-view')){
        return redirect()->route('company.admin.home',['domain' => domain()])->with(['error'=>'You don\'t have sufficient permission to view this content. ']);
      }
      $company_id = config('settings.company_id');
      $data['allsalesman'] = Auth::user()->handleQuery('employee')->where('company_id', $company_id)->where('status', '=', 'Active')->orderby('id','asc')->where('name','!=','')->get()->pluck('name','id')->toArray();
      $data['targetvalue'] = ['1'=>'Daily','2'=>'Weekly','3'=>'Monthly'];
      $data['months'] = ['01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June','07'=>'July','08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December'];
      $salesman_id = array();
      $data['alltargetoptions'] = DB::table('salesmantarget_optionstype')->get()->pluck('options_value','id')->toArray();
      $getMinTargetReportDate = (Carbon::now()->format('Y'))-1;
      $getNepDate = getMthYrDeltaDate(date('Y-m-d'));
      return view('company.targetsalesman.genreport',compact('data','getNepDate','getMinTargetReportDate'));
    }

    public function weeksInMonth($numOfDaysInMont){  
      $daysInWeek = 7;
      $result = $numOfDaysInMont/$daysInWeek;
      $numberOfFullWeeks = floor($result);
      $numberOfRemaningDays = ($result - $numberOfFullWeeks)*7;
      return $numberOfFullWeeks; 
    }


    public function convert_targetvalues($company_id,$intervalid,$targetvalues,$act_date,$total_monthdays){
      $holidays = Holiday::where('company_id',$company_id)->where('start_date','like',$act_date.'%')->get()->toArray();
      $holiday_count = 0;
      foreach($holidays as $h=>$days){
        $std = Carbon::parse($days['start_date']);
        $edd = Carbon::parse($days['end_date']);
        if($std==$edd){
          $holiday_count += 1;
        }else{
          $holiday_count += ($std->diffInDays($edd))+1;
        }
      } 
      $total_workingdays = $total_monthdays-$holiday_count;
      $intervalid = (int)$intervalid;

      Log::info($intervalid);
      switch($intervalid){
        case 1:
          $targetfor_wholemonth = $targetvalues*$total_workingdays;
          return $targetfor_wholemonth;
          break;
        case 2:
          $targetfor_wholemonth = ($targetvalues/7)*$total_monthdays;
          return $targetfor_wholemonth;
          break;
        case 3:
          $targetfor_wholemonth = ($targetvalues/$total_monthdays)*$total_monthdays;
          return $targetfor_wholemonth;
          break;
      }
    }


    public function salesmanTargetHistory(Request $request){
      $company_id = config('settings.company_id');
      $salesmanid = $request->id; $salesman_name = '';
      $salesman_exist = Auth::user()->handleQuery('employee')->where('status', '=', 'Active')->find($salesmanid);
      if($salesman_exist){
        $alltargetoptions = DB::table('salesmantarget_optionstype')->get()->toArray();
        $targetvalue = ['1'=>'Daily','2'=>'Weekly','3'=>'Monthly'];
        $salesmantargets = DB::table('salesmantarget_assignedit_history')->where('company_id',$company_id)->where('salesman_id',$salesmanid)->orderby('targethist_newgroupid','desc')->get()->toArray();
        $salesmantargets_cur = TargetSalesmanassign::where('company_id',$company_id)->where('salesman_id',$salesmanid)->get()->toArray();
        $htmldata = ''; $zop = 1; $tru='---';$tinter='---';$tvale='---';
        if(count($salesmantargets_cur)>0){
          $htmldata .= "<tr>";
          $htmldata .= "<td rowspan='".(count($salesmantargets_cur)+1)."'><b>".$zop++."</b></td>";
          $htmldata .= "<td rowspan='".(count($salesmantargets_cur)+1)."'><b>".$salesmantargets_cur[0]['target_name']."</b><br><span style='color:blue;font-style:italic;'>*Current Target</span></td>";
          $htmldata .= "</tr>";
          foreach($salesmantargets_cur as $ne=>$pal){
            $htmldata .= "<tr>";
            foreach($alltargetoptions as $rrt=>$trr){
              if($pal['target_rule']==$alltargetoptions[$rrt]->id){
                $tru = $alltargetoptions[$rrt]->options_value;
              }
            }
            foreach($targetvalue as $jjk=>$kjj){
              if($pal['target_interval']==$jjk){
                $tinter = $kjj;
              }
            }
            if($pal['target_values']==0){$tvale='---';}else{$tvale=$pal['target_values'];}
            $htmldata .= "<td><b>".$tru."</b></td>";
            $htmldata .= "<td><b>".$tinter."</b></td>";
            $htmldata .= "<td><b>".$tvale."</b></td>";
            if(config('settings.ncal')==0){ $dt = Carbon::parse($pal['created_at'])->format('Y-m-d'); }else{ $dt = getDeltaDate(Carbon::parse($pal['created_at'])->format('Y-m-d'));}
            $htmldata .= "<td><b>[".$dt.']&nbsp;&nbsp;-&nbsp;&nbsp;[Now'."]</b></td>";
            $htmldata .= "</tr>";
          }
        }
        if(count($salesmantargets)>0){
          foreach($salesmantargets as $gr=>$rg){
            $tgid[$salesmantargets[$gr]->targethist_newgroupid][] = $salesmantargets[$gr];
            $tggrpid[] = $rg->target_groupid;
          }
          foreach($tgid as $ytt=>$tty){
            $htmldata .= "<tr>";
            $htmldata .= "<td rowspan='".(count($tty)+1)."'>".$zop++."</td>";
            $htmldata .= "<td rowspan='".(count($tty)+1)."'>".$tgid[$ytt][0]->target_name."</td>";
            $htmldata .= "</tr>";
            foreach($tty as $ggh=>$hhg){
              $trule = '---'; $tinterval = '---';$tval = '---';
              if($ytt==$hhg->targethist_newgroupid){
                foreach($alltargetoptions as $rrt=>$trr){
                  if($hhg->target_rule==$alltargetoptions[$rrt]->id){
                    $trule = $alltargetoptions[$rrt]->options_value;
                  }
                }
                foreach($targetvalue as $jjk=>$kjj){
                  if($hhg->target_interval==$jjk){
                    $tinterval = $kjj;
                  }
                }
                // $this->fetchTGTUpdHistory($hhg->target_groupid);
                if($hhg->target_values==0){$tval='---';}else{$tval=$hhg->target_values;}
                $htmldata .= "<tr>";
                $htmldata .= "<td>".$trule."</td>";
                $htmldata .= "<td>".$tinterval."</td>";
                $htmldata .= "<td>".$tval."</td>";
                if(config('settings.ncal')==0){ $sdt = Carbon::parse($hhg->target_assigneddate)->format('Y-m-d'); }else{ $sdt = getDeltaDate(Carbon::parse($hhg->target_assigneddate)->format('Y-m-d')); }
                if(config('settings.ncal')==0){ $edt = Carbon::parse($hhg->target_changeddate)->format('Y-m-d'); }else{ $edt = getDeltaDate(Carbon::parse($hhg->target_changeddate)->format('Y-m-d')); }
                $htmldata .= "<td>[".$sdt.']&nbsp;&nbsp;-&nbsp;&nbsp;['.$edt."]</td>";
                $htmldata .= "</tr>";
              }
            }
          }
        }else{
          $htmldata .= "<tr>";
          $htmldata .= "<td colspan='5'>No Target History Available for this Salesman.</td>";
          $htmldata .= "</tr>";
        }
        $salesman_name = $salesman_exist->toArray()['name'];
        return response()->json(['msg'=>$htmldata,'sname'=>$salesman_name],200);
      }else{
        return response()->json(['msg'=>'Wrong Salesman'],400);
      }
    }

    private function fetchTGTUpdHistory($tgtgrpid){
      $company_id = config('settings.company_id');
      $tgthistory = DB::table('salesmantarget_edit_history')->where('company_id',$company_id)->whereIn('target_groupid',$tgtgrpid)->orderby('targethist_newgroupid','desc')->get()->toArray();
      print_r($tgthistory);die();

      $tgtedithistory = array();
      if(count($tgthistory)>0){
        $tgthist = $tgthistory[0]->targethist_newgroupid;
        foreach($tgthistory as $tgt=>$hist){
          if($tgthist==$hist->targethist_newgroupid){
            $tgtedithistory[$tgthist][] = $hist;
          }else{
            $tgthist = $hist->targethist_newgroupid;
            $tgtedithistory[$tgthist][] = $hist;
          }
        }
      }
      return $tgtedithistory;
    }

    public function targetUpdHistory(Request $request){
      $company_id = config('settings.company_id');
      $targetgroupid = $request->id; $targetname = '';
      $target_exist = TargetSalesman::where('target_groupid',$targetgroupid)->get()->toArray();
      if(count($target_exist)>0){
        $alltargetoptions = DB::table('salesmantarget_optionstype')->get()->toArray();
        $targetvalue = ['1'=>'Daily','2'=>'Weekly','3'=>'Monthly'];
        $salesmantargets = DB::table('salesmantarget_edit_history')->where('company_id',$company_id)->where('target_groupid',$targetgroupid)->orderby('targethist_newgroupid','desc')->get()->toArray();
        $htmldata = ''; 
        if(count($salesmantargets)>0){
          foreach($salesmantargets as $gr=>$rg){
            $tgid[$salesmantargets[$gr]->targethist_newgroupid][] = $salesmantargets[$gr];
          }
          $trule = ''; $tinterval = '';$zop = 1;
          foreach($tgid as $ytt=>$tty){
            $htmldata .= "<tr>";
            $htmldata .= "<td rowspan='".(count($tty)+1)."'>".$zop++."</td>";
            $htmldata .= "<td rowspan='".(count($tty)+1)."'> ".$tgid[$ytt][0]->target_name."</td>";
            $htmldata .= "</tr>";
            foreach($tty as $ggh=>$hhg){
              if($ytt==$hhg->targethist_newgroupid){
                foreach($alltargetoptions as $rrt=>$trr){
                  if($hhg->target_rule==$alltargetoptions[$rrt]->id){
                    $trule = $alltargetoptions[$rrt]->options_value;
                  }
                }
                foreach($targetvalue as $jjk=>$kjj){
                  if($hhg->target_interval==$jjk){
                    $tinterval = $kjj;
                  }
                }
                $htmldata .= "<tr>";
                $htmldata .= "<td>".$trule."</td>";
                $htmldata .= "<td>".$tinterval."</td>";
                $htmldata .= "<td>".$hhg->target_values."</td>";
                if(config('settings.ncal')==0){ $sdt = Carbon::parse($hhg->target_editdate)->format('Y-m-d'); }else{ $sdt = getDeltaDate(Carbon::parse($hhg->target_editdate)->format('Y-m-d'));}
                $htmldata .= "<td>".$sdt."</td>";
                $htmldata .= "</tr>";
              }
            }
          }
        }else{
          $htmldata .= "<tr>";
          $htmldata .= "<td colspan='6'>No Update History Available for this Target.</td>";
          $htmldata .= "</tr>";
        }
        return response()->json(['msg'=>$htmldata],200);
      }else{
        return response()->json(['msg'=>'Wrong Salesman'],400);
      }
    }


    public function targetCreateAssign(Request $request){
      $company_id = config('settings.company_id');
      $getClientSetting = getClientSetting();
      $errMessages = [
        'tname.required' => 'Target Name is required.',
        'tname.unique' => 'Target Name already exist.',
        'topt.*.required' => 'Target Options is required .',
        'tinterval.*.required' => 'Target value is required .',
        'topt.*.numeric' => 'Wrong Target options.',
      ];
      $this->validate($request, [
        'tname' => 'required|unique:salesmantarget,target_name,NULL,id,company_id,' . $company_id,
        'topt.*.*' => 'required|numeric',
        'tinterval.*' => 'required|numeric|between:1,3',
      ], $errMessages);    
      $targetname = $request->get('tname')[0];
      $target_interval = $request->get('tinterval');
      $target_options = $request->get('topt');
      $target_value = $request->get('tval');
      $totalrows = $request->get('numofRows');
      foreach($totalrows as $v){
        $availablerows[] = explode('_',$v)[1];
      }
      //target group id
      $targets = TargetSalesman::all()->count();
      if($targets==0){
        $targetgroup_id = 1;
      }else{
        $tgid = TargetSalesman::orderby('target_groupid','desc')->first();
        $targetgroup_id = ((int)$tgid->target_groupid)+1;
      }
      foreach($availablerows as $index){
        $salesmantarget = new TargetSalesman;
        $salesmantarget->target_name = $targetname;
        $salesmantarget->employee_id = Auth::user()->EmployeeId();
        $salesmantarget->company_id = $company_id;
        $salesmantarget->target_groupid = $targetgroup_id;
        $salesmantarget->target_interval = $target_interval[$index];
        $salesmantarget->target_value = $target_value[$index];
        if(is_array($target_options)){
          if(array_key_exists($index, $target_options)){
            $salesmantarget->target_rules = ($target_options[$index]);
          }
        }
        $salesmantarget->save();
      }     
      $newly_createdtgtid = $salesmantarget->id;
      $newly_createdtgt = TargetSalesman::where('id',$newly_createdtgtid)->get()->toArray();
      $newly_createdtgtall = TargetSalesman::where('target_groupid',($newly_createdtgt[0])['target_groupid'])->get()->toArray();
      if(isset($request->sel_salesman) && $request->sel_salesman!=''){
        $assign_tosalesman = explode('-',$request->sel_salesman);
        unset($assign_tosalesman[(count($assign_tosalesman))-1]);
        foreach($assign_tosalesman as $nep=>$al){
          //maintain target history first
          $targets_count = DB::table('salesmantarget_assign_history')->count();
          if($targets_count==0){
            $targethist_newgroupid = 1;
          }else{
            $tgid = DB::table('salesmantarget_assign_history')->orderby('targethist_newgroupid','desc')->first();
            $targethist_newgroupid = ((int)$tgid->targethist_newgroupid)+1;
          }
          $targetsassignedit_count = DB::table('salesmantarget_assignedit_history')->count();
          if($targetsassignedit_count==0){
            $targetassedithist_newgroupid = 1;
          }else{
            $tgid = DB::table('salesmantarget_assignedit_history')->orderby('targethist_newgroupid','desc')->first();
            $targetassedithist_newgroupid = ((int)$tgid->targethist_newgroupid)+1;
          }
          $salesmanid_oldtgt = TargetSalesmanassign::where('company_id',$company_id)->where('salesman_id',$al)->get()->toArray();
          foreach($salesmanid_oldtgt as $ndx=>$xnd){
            DB::table('salesmantarget_assign_history')->insert([
              'targetid_original' => $salesmanid_oldtgt[$ndx]['id'],
              'targethist_newgroupid' => $targethist_newgroupid,
              'company_id' => $salesmanid_oldtgt[$ndx]['company_id'],
              'salesman_id' => $salesmanid_oldtgt[$ndx]['salesman_id'],
              'target_groupid' => $salesmanid_oldtgt[$ndx]['target_groupid'],
              'target_name' => $salesmanid_oldtgt[$ndx]['target_name'],
              'target_subid' => $salesmanid_oldtgt[$ndx]['target_subid'],
              'target_rule' => $salesmanid_oldtgt[$ndx]['target_rule'],
              'target_interval' => $salesmanid_oldtgt[$ndx]['target_interval'],
              'target_values' => $salesmanid_oldtgt[$ndx]['target_values'],
              'target_tot_workingdays' => $salesmanid_oldtgt[$ndx]['target_tot_workingdays'],
              'target_progress' => $salesmanid_oldtgt[$ndx]['target_progress'],
              'target_assigneddate' => $salesmanid_oldtgt[$ndx]['created_at'],
              'target_changeddate' => Carbon::now()->toDateTimeString(),
              'target_startmonth' => $salesmanid_oldtgt[$ndx]['target_startmonth'],
            ]);


            DB::table('salesmantarget_assignedit_history')->insert([
              'targetid_original' => $salesmanid_oldtgt[$ndx]['id'],
              'targethist_newgroupid' => $targetassedithist_newgroupid,
              'company_id' => $salesmanid_oldtgt[$ndx]['company_id'],
              'salesman_id' => $salesmanid_oldtgt[$ndx]['salesman_id'],
              'target_groupid' => $salesmanid_oldtgt[$ndx]['target_groupid'],
              'target_name' => $salesmanid_oldtgt[$ndx]['target_name'],
              'target_subid' => $salesmanid_oldtgt[$ndx]['target_subid'],
              'target_rule' => $salesmanid_oldtgt[$ndx]['target_rule'],
              'target_interval' => $salesmanid_oldtgt[$ndx]['target_interval'],
              'target_values' => $salesmanid_oldtgt[$ndx]['target_values'],
              'target_tot_workingdays' => $salesmanid_oldtgt[$ndx]['target_tot_workingdays'],
              'target_progress' => $salesmanid_oldtgt[$ndx]['target_progress'],
              'target_assigneddate' => $salesmanid_oldtgt[$ndx]['target_startmonth'],
              'target_changeddate' => Carbon::now()->toDateTimeString(),
              'target_startmonth' => $salesmanid_oldtgt[$ndx]['target_startmonth'],
          ]);

          }
          $salesman_oldtgtdel = TargetSalesmanassign::where('company_id',$company_id)->where('salesman_id',$al);
          $salesman_oldtgtdel->delete();
          foreach($newly_createdtgtall as $del=>$ta){
            $salesmantargetassign = new TargetSalesmanassign;
            $salesmantargetassign->company_id = $company_id;
            $salesmantargetassign->salesman_id = $al;
            $salesmantargetassign->target_groupid = $ta['target_groupid'];
            $salesmantargetassign->target_subid = $ta['id'];
            $salesmantargetassign->target_name = $ta['target_name'];
            $salesmantargetassign->target_interval = $ta['target_interval'];
            $salesmantargetassign->target_rule = $ta['target_rules'];
            $salesmantargetassign->target_values = $ta['target_value'];
            $salesmantargetassign->target_tot_workingdays = $this->total_no_workingdays(Date('m'),Date('Y'));
            $salesmantargetassign->target_progress = 0;
            $salesmantargetassign->target_startmonth = Carbon::now()->format('Y-m-d');
            $salesmantargetassign->save();
          }
        }
        return redirect()->route('company.admin.salesmantarget', ['domain' => domain()])->with('success', 'Target Created and  assigned to selected Salesman.');
      }else{
        return redirect()->route('company.admin.salesmantarget.set', ['domain' => domain()])->with('error', 'Wrong salesman selected.');
      }
    }

    public function targetSalesmanListsEdit2(Request $request){
      $company_id = config('settings.company_id');
      $data['targetvalue'] = ['1'=>'Daily','2'=>'Weekly','3'=>'Monthly'];
      // $data['targetoptions'] = DB::table('salesmantarget_optionstype')->get();
      $targetoptions = DB::table('salesmantarget_optionstype')->get();
      $data['targetoptions'] = array();
      foreach($targetoptions as $k=>$topt){
        switch($topt->options_module){
          case 'ord':
            if(config('settings.orders')==1){
              $data['targetoptions'][] = $topt;
            }
            break;

          case 'coll':
            if(config('settings.collections')==1){
              $data['targetoptions'][] = $topt;
            }
            break;

          case 'vis':
            if(config('settings.visit_module')==1){
              $data['targetoptions'][] = $topt;
            }
            break;

          case 'party':
            if(config('settings.party')==1){
              $data['targetoptions'][] = $topt;
            }
            break;

          case 'ord_zero':
            if(config('settings.orders')==1 && config('settings.zero_orders')==1){
              $data['targetoptions'][] = $topt;
            }
            break;
        }
      }
      $salesman_id = (int)$request->id;
      $salesman_exist = Auth::user()->handleQuery('employee')->where('status', '=', 'Active')->find($salesman_id);
      $alltargetoptions = DB::table('salesmantarget_optionstype')->get()->toArray();
      if($salesman_exist){
        $salesman_name = $salesman_exist->name;
        $client_check = TargetSalesmanassign::where('company_id',$company_id)->where('salesman_id',$salesman_id)->get()->toArray();
        // $data['alltargets'] = TargetSalesman::where('company_id', $company_id)->groupby('target_groupid')->orderBy('created_at', 'asc')->pluck('target_name', 'target_groupid')->toArray();
        $data['alltargets'] = Auth::user()->handleQuery('TargetSalesman')->where('company_id', $company_id)->groupby('target_groupid')->pluck('target_name', 'target_groupid')->toArray();
        $nestedData = array();
        if(count($client_check)>0){
          $data['alltargets'][$client_check[0]['target_groupid']] = $client_check[0]['target_name'];
          $salesman_targetid = $client_check[0]['target_groupid']; 
          return view('company.targetsalesman.salesmantargetedit2',compact('salesman_id','salesman_name','client_check','salesman_targetid','data'));
        }else{  
          return redirect()->route('company.admin.salesmantarget', ['domain' => domain()])->with('warning', 'Selected salesman does not have any targets to update, Assign Target First.');
        }
      }else{
        return redirect()->route('company.admin.salesmantarget', ['domain' => domain()])->with('error', 'Salesman does not exist.');
      }
    }

    public function targetSalesmanDats(Request $request){
      $tgtid = (int)$request->sel_tgtgrpid;
      $targetexist = TargetSalesman::where('target_groupid',$tgtid)->get();
      if(count($targetexist)>0){
        // $alltargetoptions = DB::table('salesmantarget_optionstype')->get()->toArray();
        $targetoptions = DB::table('salesmantarget_optionstype')->get();
        $alltargetoptions = array();
        foreach($targetoptions as $k=>$topt){
          switch($topt->options_module){
            case 'ord':
              if(config('settings.orders')==1){
                $alltargetoptions[] = $topt;
              }
              break;
  
            case 'coll':
              if(config('settings.collections')==1){
                $alltargetoptions[] = $topt;
              }
              break;
  
            case 'vis':
              if(config('settings.visit_module')==1){
                $alltargetoptions[] = $topt;
              }
              break;
  
            case 'party':
              if(config('settings.party')==1){
                $alltargetoptions[] = $topt;
              }
              break;
  
            case 'ord_zero':
              if(config('settings.orders')==1 && config('settings.zero_orders')==1){
                $alltargetoptions[] = $topt;
              }
              break;
          }
        }
        $targetinterval = ['1'=>'Daily','2'=>'Weekly','3'=>'Monthly'];
        $htmldata = '';$count = 1;
        foreach($targetexist->toArray() as $ytt=>$tty){
          $htmldata .= "<tr>";
          $trule = '';
          foreach($alltargetoptions as $rrt=>$trr){
            if($tty['target_rules']==$alltargetoptions[$rrt]->id){
              $trule = $alltargetoptions[$rrt]->options_value;
            }
          }
          foreach($targetinterval as $jjk=>$kjj){
            if($tty['target_interval']==$jjk){
              $tinterval = $kjj;
            }
          }
          if($trule==''){
          }else{
            $htmldata .= "<td>".$count++."</td>";
            $htmldata .= "<td><input class='form-control variantClass' type='text' disabled value='".$trule."'></td>";
            $htmldata .= "<td><input class='form-control variantClass'  type='text' disabled value='".$tinterval."'></td>";
            $htmldata .= "<td><input class='form-control variantClass'  type='text' disabled value='".$tty['target_value']."'></td>";
            $htmldata .= "</tr>";
          }
        }
        return response()->json(['msg'=>$htmldata],200);
      }else{
        return response()->json(['msg'=>'Target does not exist'],404);
      }
    }

    public function targetSalesmanChange(Request $request){
      $salesmantarget = $request->get('salesmantarget');
      $salesmnaname = $request->get('salemsids');
      $company_id = config('settings.company_id');
      if($salesmantarget=='notaraget'){
        $targets_count = DB::table('salesmantarget_assign_history')->count();
        if($targets_count==0){
          $targethist_newgroupid = 1;
        }else{
          $tgid = DB::table('salesmantarget_assign_history')->orderby('targethist_newgroupid','desc')->first();
          $targethist_newgroupid = ((int)$tgid->targethist_newgroupid)+1;
        }
        $targetsassignedit_count = DB::table('salesmantarget_assignedit_history')->count();
        if($targetsassignedit_count==0){
            $targetassedithist_newgroupid = 1;
        }else{
            $tgid = DB::table('salesmantarget_assignedit_history')->orderby('targethist_newgroupid','desc')->first();
            $targetassedithist_newgroupid = ((int)$tgid->targethist_newgroupid)+1;
        }
        $salesmanid = $salesmnaname;
        $salesmanid_oldtgt = TargetSalesmanassign::where('company_id',$company_id)->where('salesman_id',$salesmanid)->get()->toArray();
        foreach($salesmanid_oldtgt as $ndx=>$xnd){
          DB::table('salesmantarget_assign_history')->insert([
            'targetid_original' => $salesmanid_oldtgt[$ndx]['id'],
            'targethist_newgroupid' => $targethist_newgroupid,
            'company_id' => $salesmanid_oldtgt[$ndx]['company_id'],
            'salesman_id' => $salesmanid_oldtgt[$ndx]['salesman_id'],
            'target_groupid' => $salesmanid_oldtgt[$ndx]['target_groupid'],
            'target_name' => $salesmanid_oldtgt[$ndx]['target_name'],
            'target_subid' => $salesmanid_oldtgt[$ndx]['target_subid'],
            'target_rule' => $salesmanid_oldtgt[$ndx]['target_rule'],
            'target_interval' => $salesmanid_oldtgt[$ndx]['target_interval'],
            'target_values' => $salesmanid_oldtgt[$ndx]['target_values'],
            'target_tot_workingdays' => $salesmanid_oldtgt[$ndx]['target_tot_workingdays'],
            'target_progress' => $salesmanid_oldtgt[$ndx]['target_progress'],
            'target_assigneddate' => $salesmanid_oldtgt[$ndx]['created_at'],
            'target_changeddate' => Carbon::now()->toDateTimeString(),
            'target_startmonth' => $salesmanid_oldtgt[$ndx]['target_startmonth'],
          ]);

          DB::table('salesmantarget_assignedit_history')->insert([
            'targetid_original' => $salesmanid_oldtgt[$ndx]['id'],
            'targethist_newgroupid' => $targetassedithist_newgroupid,
            'company_id' => $salesmanid_oldtgt[$ndx]['company_id'],
            'salesman_id' => $salesmanid_oldtgt[$ndx]['salesman_id'],
            'target_groupid' => $salesmanid_oldtgt[$ndx]['target_groupid'],
            'target_name' => $salesmanid_oldtgt[$ndx]['target_name'],
            'target_subid' => $salesmanid_oldtgt[$ndx]['target_subid'],
            'target_rule' => $salesmanid_oldtgt[$ndx]['target_rule'],
            'target_interval' => $salesmanid_oldtgt[$ndx]['target_interval'],
            'target_values' => $salesmanid_oldtgt[$ndx]['target_values'],
            'target_tot_workingdays' => $salesmanid_oldtgt[$ndx]['target_tot_workingdays'],
            'target_progress' => $salesmanid_oldtgt[$ndx]['target_progress'],
            'target_assigneddate' => $salesmanid_oldtgt[$ndx]['target_startmonth'],
            'target_changeddate' => Carbon::now()->toDateTimeString(),
            'target_startmonth' => $salesmanid_oldtgt[$ndx]['target_startmonth'],
          ]);


        }
        $salesman_oldtgtdel = TargetSalesmanassign::where('company_id',$company_id)->where('salesman_id',$salesmanid);
        $salesman_oldtgtdel->delete();

        $salesmantargetassign = new TargetSalesmanassign;
        $salesmantargetassign->company_id = $company_id;
        $salesmantargetassign->salesman_id = $salesmnaname;
        $salesmantargetassign->target_groupid = 0;
        $salesmantargetassign->target_subid = 0;
        $salesmantargetassign->target_name = 'No Target';
        $salesmantargetassign->target_interval = 0;
        $salesmantargetassign->target_rule = 0;
        $salesmantargetassign->target_values = 0;
        $salesmantargetassign->target_tot_workingdays = 0;
        $salesmantargetassign->target_progress = 0;
        $salesmantargetassign->save();
        return redirect()->route('company.admin.salesmantarget', ['domain' => domain()])->with('success', 'Target removed from selected Salesman.');
      }else{
        $errMessages = [
          'salemsids.required' => 'Salesman Name is required.',
          'salesmantarget.required' => 'Salesman Target is required .',
          'salemsids.numeric' => 'Invalid Salesman .',
          'salesmantarget.numeric' => 'Wrong Target assignment to Salesman.',
        ];
        $this->validate($request, [
          'salemsids.*' => 'required|numeric',
          'salesmantarget.*' => 'required|numeric',
        ], $errMessages);    
        // $salesmnaname = $request->get('salemsids');
        // $salesmantarget = $request->get('salesmantarget');

        //copy old targets to target assign history table
        //delete current targets from the target assign table and save current targets to this table
        $targetparameters = TargetSalesman::where('target_groupid',$salesmantarget)->get()->toArray();
        if(count($targetparameters)>0){
          //copy old targets to target assign history table
          $targets_count = DB::table('salesmantarget_assign_history')->count();
          if($targets_count==0){
            $targethist_newgroupid = 1;
          }else{
            $tgid = DB::table('salesmantarget_assign_history')->orderby('targethist_newgroupid','desc')->first();
            $targethist_newgroupid = ((int)$tgid->targethist_newgroupid)+1;
          }
          $targetsassignedit_count = DB::table('salesmantarget_assignedit_history')->count();
          if($targetsassignedit_count==0){
              $targetassedithist_newgroupid = 1;
          }else{
              $tgid = DB::table('salesmantarget_assignedit_history')->orderby('targethist_newgroupid','desc')->first();
              $targetassedithist_newgroupid = ((int)$tgid->targethist_newgroupid)+1;
          }
          $salesmanid = $salesmnaname;
          $salesmanid_oldtgt = TargetSalesmanassign::where('company_id',$company_id)->where('salesman_id',$salesmanid)->get()->toArray();
          foreach($salesmanid_oldtgt as $ndx=>$xnd){
            DB::table('salesmantarget_assign_history')->insert([
              'targetid_original' => $salesmanid_oldtgt[$ndx]['id'],
              'targethist_newgroupid' => $targethist_newgroupid,
              'company_id' => $salesmanid_oldtgt[$ndx]['company_id'],
              'salesman_id' => $salesmanid_oldtgt[$ndx]['salesman_id'],
              'target_groupid' => $salesmanid_oldtgt[$ndx]['target_groupid'],
              'target_name' => $salesmanid_oldtgt[$ndx]['target_name'],
              'target_subid' => $salesmanid_oldtgt[$ndx]['target_subid'],
              'target_rule' => $salesmanid_oldtgt[$ndx]['target_rule'],
              'target_interval' => $salesmanid_oldtgt[$ndx]['target_interval'],
              'target_values' => $salesmanid_oldtgt[$ndx]['target_values'],
              'target_tot_workingdays' => $salesmanid_oldtgt[$ndx]['target_tot_workingdays'],
              'target_progress' => $salesmanid_oldtgt[$ndx]['target_progress'],
              'target_assigneddate' => $salesmanid_oldtgt[$ndx]['created_at'],
              'target_changeddate' => Carbon::now()->toDateTimeString(),
              'target_startmonth' => $salesmanid_oldtgt[$ndx]['target_startmonth'],
            ]);


            DB::table('salesmantarget_assignedit_history')->insert([
              'targetid_original' => $salesmanid_oldtgt[$ndx]['id'],
              'targethist_newgroupid' => $targetassedithist_newgroupid,
              'company_id' => $salesmanid_oldtgt[$ndx]['company_id'],
              'salesman_id' => $salesmanid_oldtgt[$ndx]['salesman_id'],
              'target_groupid' => $salesmanid_oldtgt[$ndx]['target_groupid'],
              'target_name' => $salesmanid_oldtgt[$ndx]['target_name'],
              'target_subid' => $salesmanid_oldtgt[$ndx]['target_subid'],
              'target_rule' => $salesmanid_oldtgt[$ndx]['target_rule'],
              'target_interval' => $salesmanid_oldtgt[$ndx]['target_interval'],
              'target_values' => $salesmanid_oldtgt[$ndx]['target_values'],
              'target_tot_workingdays' => $salesmanid_oldtgt[$ndx]['target_tot_workingdays'],
              'target_progress' => $salesmanid_oldtgt[$ndx]['target_progress'],
              'target_assigneddate' => $salesmanid_oldtgt[$ndx]['target_startmonth'],
              'target_changeddate' => Carbon::now()->toDateTimeString(),
              'target_startmonth' => $salesmanid_oldtgt[$ndx]['target_startmonth'],
            ]);

          }
          $salesman_oldtgtdel = TargetSalesmanassign::where('company_id',$company_id)->where('salesman_id',$salesmanid);
          $salesman_oldtgtdel->delete();
          foreach($targetparameters as $b=>$n){
            $salesmantargetassign = new TargetSalesmanassign;
            $salesmantargetassign->company_id = $company_id;
            $salesmantargetassign->salesman_id = $salesmnaname;
            $salesmantargetassign->target_groupid = $n['target_groupid'];
            $salesmantargetassign->target_subid = $n['id'];
            $salesmantargetassign->target_name = $n['target_name'];
            $salesmantargetassign->target_interval = $n['target_interval'];
            $salesmantargetassign->target_rule = $n['target_rules'];
            $salesmantargetassign->target_values = $n['target_value'];
            $salesmantargetassign->target_tot_workingdays = $this->total_no_workingdays(Date('m'),Date('Y'));
            $salesmantargetassign->target_progress = 0;
            $salesmantargetassign->target_startmonth = Carbon::now()->format('Y-m-d');
            $salesmantargetassign->save();
          }
          return redirect()->route('company.admin.salesmantarget', ['domain' => domain()])->with('success', 'Target assigned to selected Salesman.');
        }else{
          return redirect()->route('company.admin.salesmantarget', ['domain' => domain()])->with('error', 'No Such targets Found.');
        }
      }

    }

    public function targetCreateAssignSingleEmp(Request $request){
      $company_id = config('settings.company_id');
      $getClientSetting = getClientSetting();
      $errMessages = [
        'tname.required' => 'Target Name is required.',
        'tname.unique' => 'Target Name already exist.',
        'topt.*.required' => 'Target Options is required .',
        'tinterval.*.required' => 'Target value is required .',
        'topt.*.numeric' => 'Wrong Target options.',
      ];
      $this->validate($request, [
        'tname' => 'required|unique:salesmantarget,target_name,NULL,id,company_id,' . $company_id,
        'topt.*.*' => 'required|numeric',
        'tinterval.*' => 'required|numeric|between:1,3',
      ], $errMessages);    
      $targetname = $request->get('tname')[0];
      $target_interval = $request->get('tinterval');
      $target_options = $request->get('topt');
      $target_value = $request->get('tval');
      $totalrows = $request->get('numofRows');
      foreach($totalrows as $v){
        $availablerows[] = explode('_',$v)[1];
      }
      $salesmanid = ($request->id)?($request->id):false;
      $salesman_exist = Auth::user()->handleQuery('employee')->where('status', '=', 'Active')->find($salesmanid);
      if($salesman_exist){
        //target group id
        $targets = TargetSalesman::all()->count();
        if($targets==0){
          $targetgroup_id = 1;
        }else{
          $tgid = TargetSalesman::orderby('target_groupid','desc')->first();
          $targetgroup_id = ((int)$tgid->target_groupid)+1;
        }
        foreach($availablerows as $index){
          $salesmantarget = new TargetSalesman;
          $salesmantarget->target_name = $targetname;
          $salesmantarget->employee_id = Auth::user()->EmployeeId();
          $salesmantarget->company_id = $company_id;
          $salesmantarget->target_groupid = $targetgroup_id;
          $salesmantarget->target_interval = $target_interval[$index];
          $salesmantarget->target_value = $target_value[$index];
          if(is_array($target_options)){
            if(array_key_exists($index, $target_options)){
              $salesmantarget->target_rules = ($target_options[$index]);
            }
          }
          $salesmantarget->save();
        }     
        $newly_createdtgtid = $salesmantarget->id;

        //maintain target history
        $targets_count = DB::table('salesmantarget_assign_history')->count();
        if($targets_count==0){
          $targethist_newgroupid = 1;
        }else{
          $tgid = DB::table('salesmantarget_assign_history')->orderby('targethist_newgroupid','desc')->first();
          $targethist_newgroupid = ((int)$tgid->targethist_newgroupid)+1;
        }
        $targetsassignedit_count = DB::table('salesmantarget_assignedit_history')->count();
        if($targetsassignedit_count==0){
            $targetassedithist_newgroupid = 1;
        }else{
            $tgid = DB::table('salesmantarget_assignedit_history')->orderby('targethist_newgroupid','desc')->first();
            $targetassedithist_newgroupid = ((int)$tgid->targethist_newgroupid)+1;
        }
        $salesmanid_oldtgt = TargetSalesmanassign::where('company_id',$company_id)->where('salesman_id',$salesmanid)->get()->toArray();
        foreach($salesmanid_oldtgt as $ndx=>$xnd){
          DB::table('salesmantarget_assign_history')->insert([
            'targetid_original' => $salesmanid_oldtgt[$ndx]['id'],
            'targethist_newgroupid' => $targethist_newgroupid,
            'company_id' => $salesmanid_oldtgt[$ndx]['company_id'],
            'salesman_id' => $salesmanid_oldtgt[$ndx]['salesman_id'],
            'target_groupid' => $salesmanid_oldtgt[$ndx]['target_groupid'],
            'target_name' => $salesmanid_oldtgt[$ndx]['target_name'],
            'target_subid' => $salesmanid_oldtgt[$ndx]['target_subid'],
            'target_rule' => $salesmanid_oldtgt[$ndx]['target_rule'],
            'target_interval' => $salesmanid_oldtgt[$ndx]['target_interval'],
            'target_values' => $salesmanid_oldtgt[$ndx]['target_values'],
            'target_tot_workingdays' => $salesmanid_oldtgt[$ndx]['target_tot_workingdays'],
            'target_progress' => $salesmanid_oldtgt[$ndx]['target_progress'],
            'target_assigneddate' => $salesmanid_oldtgt[$ndx]['created_at'],
            'target_changeddate' => Carbon::now()->toDateTimeString(),
            'target_startmonth' => $salesmanid_oldtgt[$ndx]['target_startmonth'],
          ]);

          DB::table('salesmantarget_assignedit_history')->insert([
            'targetid_original' => $salesmanid_oldtgt[$ndx]['id'],
            'targethist_newgroupid' => $targetassedithist_newgroupid,
            'company_id' => $salesmanid_oldtgt[$ndx]['company_id'],
            'salesman_id' => $salesmanid_oldtgt[$ndx]['salesman_id'],
            'target_groupid' => $salesmanid_oldtgt[$ndx]['target_groupid'],
            'target_name' => $salesmanid_oldtgt[$ndx]['target_name'],
            'target_subid' => $salesmanid_oldtgt[$ndx]['target_subid'],
            'target_rule' => $salesmanid_oldtgt[$ndx]['target_rule'],
            'target_interval' => $salesmanid_oldtgt[$ndx]['target_interval'],
            'target_values' => $salesmanid_oldtgt[$ndx]['target_values'],
            'target_tot_workingdays' => $salesmanid_oldtgt[$ndx]['target_tot_workingdays'],
            'target_progress' => $salesmanid_oldtgt[$ndx]['target_progress'],
            'target_assigneddate' => $salesmanid_oldtgt[$ndx]['target_startmonth'],
            'target_changeddate' => Carbon::now()->toDateTimeString(),
            'target_startmonth' => $salesmanid_oldtgt[$ndx]['target_startmonth'],
          ]);

        }
        $salesman_oldtgtdel = TargetSalesmanassign::where('company_id',$company_id)->where('salesman_id',$salesmanid);
        $salesman_oldtgtdel->delete();

        $newly_createdtgt = TargetSalesman::where('id',$newly_createdtgtid)->get()->toArray();
        $newly_createdtgtall = TargetSalesman::where('target_groupid',($newly_createdtgt[0])['target_groupid'])->get()->toArray();
        foreach($newly_createdtgtall as $del=>$ta){
          $salesmantargetassign = new TargetSalesmanassign;
          $salesmantargetassign->company_id = $company_id;
          $salesmantargetassign->salesman_id = $salesmanid;
          $salesmantargetassign->target_groupid = $ta['target_groupid'];
          $salesmantargetassign->target_subid = $ta['id'];
          $salesmantargetassign->target_name = $ta['target_name'];
          $salesmantargetassign->target_interval = $ta['target_interval'];
          $salesmantargetassign->target_rule = $ta['target_rules'];
          $salesmantargetassign->target_values = $ta['target_value'];
          $salesmantargetassign->target_tot_workingdays = $this->total_no_workingdays(Date('m'),Date('Y'));
          $salesmantargetassign->target_progress = 0;
          $salesmantargetassign->target_startmonth = Carbon::now()->format('Y-m-d');
          $salesmantargetassign->save();
        }
        return redirect()->route('company.admin.salesmantarget', ['domain' => domain()])->with('success', 'New Target Created and  assigned to the Salesman.');
      }else{
        return redirect()->route('company.admin.salesmantarget.set', ['domain' => domain()])->with('error', 'Wrong salesman.');
      }
    }

    public function genReportDT_orig(Request $request){
      $company_id = config('settings.company_id');
      $data['allsalesman'] = Auth::user()->handleQuery('employee')->where('company_id', $company_id)->where('status', '=', 'Active')->orderby('id','asc')->where('name','!=','')->get()->pluck('name','id')->toArray();
      $data['targetvalue'] = ['1'=>'Daily','2'=>'Weekly','3'=>'Monthly'];
      $data['months'] = ['01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June','07'=>'July','08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December'];
      // $data['alltargetoptions'] = DB::table('salesmantarget_optionstype')->get()->pluck('options_value','id')->toArray();

      $targetoptions = DB::table('salesmantarget_optionstype')->get();
      $alltargetoptions = array();
      foreach($targetoptions as $k=>$topt){
        switch($topt->options_module){
          case 'ord':
            if(config('settings.orders')==1){
              $alltargetoptions[$topt->id] = $topt->options_value;
            }
            break;

          case 'coll':
            if(config('settings.collections')==1){
              $alltargetoptions[$topt->id] = $topt->options_value;
            }
            break;

          case 'vis':
            if(config('settings.visit_module')==1){
              $alltargetoptions[$topt->id] = $topt->options_value;
            }
            break;

          case 'party':
            if(config('settings.party')==1){
              $alltargetoptions[$topt->id] = $topt->options_value;
            }
            break;

          case 'ord_zero':
            if(config('settings.orders')==1 && config('settings.zero_orders')==1){
              $alltargetoptions[$topt->id] = $topt->options_value;
            }
            break;
        }
      }
      $data['alltargetoptions'] = $alltargetoptions;


      if(isset($request->yearDate) && isset($request->monthDate)){
        $year = $request->yearDate; 
        $month = $request->monthDate;
        if(config('settings.ncal')==0)
          $monthNum = ($month<10)?('0'.$month):$month;
        else
          $monthNum = $month;
        $monthName = date("F", mktime(0, 0, 0, $monthNum, 10));
        $resultmonth = array($year=>array($monthNum=>$monthName));
        $completedate = $year.'-'.$monthNum;
      }else{
        $year = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');
        $monthName = date("F", mktime(0, 0, 0, $month, 10));
        $resultmonth = array($year=>array($month=>$monthName));
        $completedate = $year.'-'.$month;
      }

      $columns = array(0 => 'id',1 => 'name',2 => 'noof_order',3 => 'value_orders',4 => 'noof_collections',5 => 'value_collections', 6 => 'noof_visits', 7 => 'golden_calls', 8 => 'total_calls');
      $start = $request->input('start');
      $limit = $request->input('length');
      $order = $columns[$request->input('order.0.column')];
      $dir = $request->input('order.0.dir');
      $prepQuery = Auth::user()->handleQuery('employee')->where('company_id', $company_id)->where('status', '=', 'Active'); 

      if(isset($request->salesmanID)){
        $salesmanIDs = explode(',',$request->salesmanID);
        $prepQuery = $prepQuery->whereIn('id',$salesmanIDs);
      }

      if(!empty($request->input('search.value'))){
        $prepQuery = $prepQuery->where('name','like',$request->input('search.value').'%');
      }

      $prepQuery = $prepQuery->with(['targetsalesmanassign' => function($q) use($completedate){
          $q->where('target_startmonth','like',$completedate.'%');
      }]);


      $prepQuery = $prepQuery->with(['targetsalesmanassignhistory' => function($q) use($completedate){
          $q->where('target_startmonth','like',$completedate.'%')->orderby('target_assigneddate','desc');
      }]);

      if($order=='id' || $order=='name'){
        $prepQuery = $prepQuery->orderby($order,$dir);
      }
    
      $arrange_targets = $salesmantgt_hist= $salesmantgt = array();
      $prepQuery = $prepQuery->get();
      $i = 0;
      $year2 = Carbon::now()->format('Y');
      $month2 = Carbon::now()->format('m');
      $completedate2 = $year2.'-'.$month2;
      foreach($prepQuery as $indx=>$emptgt){
        $salesmantgt = $emptgt->targetsalesmanassign->toArray();
        if($completedate!=$completedate2){
          $salesmantgt_hist = $emptgt->targetsalesmanassignhistory->toArray();
        }
        $salesmantgt_dates = array();
        if(count($salesmantgt)>0){
          foreach($salesmantgt as $aa=>$bb){
            $salesmantgt_dates['tg_rule'][] = $salesmantgt[$aa]['target_rule'];
            $salesmantgt_dates['tg_interval'][] = $salesmantgt[$aa]['target_interval'];
            $salesmantgt_dates['tg_values'][] = $salesmantgt[$aa]['target_values'];
            $salesmantgt_dates['name'] = $prepQuery[$indx]['name'];
            $salesmantgt_dates['salesman_id'] = $salesmantgt[$aa]['salesman_id'];
          }
          $chk_tgruleval = $salesmantgt_dates['tg_rule'][0];
          $chk_tgintervalval = $salesmantgt_dates['tg_interval'][0];
          $chk_tgvaluesval = $salesmantgt_dates['tg_values'][0];
          $chk_tgrule = count($salesmantgt_dates['tg_rule']);
          $chk_tginterval = count($salesmantgt_dates['tg_interval']);
          $chk_tgvalues = count($salesmantgt_dates['tg_values']);
          if($chk_tgruleval==0 && $chk_tgintervalval==0 && $chk_tgvaluesval==0 && $chk_tgrule==1 && $chk_tginterval==1 && $chk_tgvalues==1){
            //pass
          }else{
            $arrange_targets[$bb['salesman_id']] = $salesmantgt_dates;
          }
        }

        if(count($salesmantgt)==0 && count($salesmantgt_hist)>0){
          $targetgroupid = $salesmantgt_hist[0]['target_groupid'];
          foreach($salesmantgt_hist as $aa=>$bb){
            if($targetgroupid==$salesmantgt_hist[$aa]['target_groupid']){
              $salesmantgt_dates['tg_rule'][] = $salesmantgt_hist[$aa]['target_rule'];
              $salesmantgt_dates['tg_interval'][] = $salesmantgt_hist[$aa]['target_interval'];
              $salesmantgt_dates['tg_values'][] = $salesmantgt_hist[$aa]['target_values'];
              $salesmantgt_dates['name'] = $prepQuery[$indx]['name'];
              $salesmantgt_dates['salesman_id'] = $salesmantgt_hist[$aa]['salesman_id'];
            }
          }
          $chk_tgruleval = $salesmantgt_dates['tg_rule'][0];
          $chk_tgintervalval = $salesmantgt_dates['tg_interval'][0];
          $chk_tgvaluesval = $salesmantgt_dates['tg_values'][0];
          $chk_tgrule = count($salesmantgt_dates['tg_rule']);
          $chk_tginterval = count($salesmantgt_dates['tg_interval']);
          $chk_tgvalues = count($salesmantgt_dates['tg_values']);
          if($chk_tgruleval==0 && $chk_tgintervalval==0 && $chk_tgvaluesval==0 && $chk_tgrule==1 && $chk_tginterval==1 && $chk_tgvalues==1){
            //pass
          }else{
            $arrange_targets[$bb['salesman_id']] = $salesmantgt_dates;
          }
        }

        if(count($salesmantgt)>0 || count($salesmantgt_hist)>0){
          $i++;
        }
      }
      $totalData =  $i;
      if(!isset($request->salesmanID)){
        $totalFiltered = $totalData; 
      }

      if(empty($request->input('search.value'))){
        $totalFiltered = $totalData;
      }

    
      foreach($resultmonth as $tr=>$rt){
        foreach($rt as $dni=>$month){
          foreach($arrange_targets as $u=>$v){
            foreach($arrange_targets[$u]['tg_rule'] as $yt=>$ty){
              $order_rule = (int)$ty;
              $empid = (int)$u;
              $act_date = $tr.'-'.$dni;
              $total_monthdays = cal_days_in_month(CAL_GREGORIAN,$dni,$tr);
              $total_noofweek = $this->weeksInMonth($total_monthdays);
              switch($order_rule){
                case 1:
                  $tot_order = Order::where('company_id',$company_id)->where('employee_id',$empid)->where('order_date','like',$act_date.'%')->get()->count();
                  $all_orders = $tot_order;//+$tot_noorder;
                  $intervalid = $arrange_targets[$u]['tg_interval'][$yt];
                  $arrange_targets[$u]['results'][$tr][$month][$order_rule] = $all_orders;
                  $targetvalues = $arrange_targets[$u]['tg_values'][$yt];
                  $arrange_targets[$u]['tg_values_calculated'][$tr][$month][$order_rule] = round($this->convert_targetvalues($company_id,$intervalid,$targetvalues,$act_date,$total_monthdays));
                  $order_targetdata = $all_orders;
                  break;
                case 2: 
                  $amt_order = Order::where('company_id',$company_id)->where('employee_id',$empid)->where('order_date','like',$act_date.'%')->get()->sum('grand_total');
                  $amt_noorder = 0;//NoOrder::where('company_id',$company_id)->where('employee_id',$empid)->where('date','like',$act_date.'%')->get()->sum();
                  $allamt_orders = $amt_order+$amt_noorder;
                  $intervalid = $arrange_targets[$u]['tg_interval'][$yt];
                  $arrange_targets[$u]['results'][$tr][$month][$order_rule] = $allamt_orders;
                  $targetvalues = $arrange_targets[$u]['tg_values'][$yt];
                  $arrange_targets[$u]['tg_values_calculated'][$tr][$month][$order_rule] = round($this->convert_targetvalues($company_id,$intervalid,$targetvalues,$act_date,$total_monthdays));
                  $order_targetdata = $allamt_orders;                  
                  break;
                case 3:
                  $tot_collection = Collection::where('company_id',$company_id)->where('employee_id',$empid)->where('payment_date','like',$act_date.'%')->get()->count();
                  $intervalid = $arrange_targets[$u]['tg_interval'][$yt];
                  $arrange_targets[$u]['results'][$tr][$month][$order_rule] = $tot_collection;
                  $targetvalues = $arrange_targets[$u]['tg_values'][$yt];
                  $arrange_targets[$u]['tg_values_calculated'][$tr][$month][$order_rule] = round($this->convert_targetvalues($company_id,$intervalid,$targetvalues,$act_date,$total_monthdays));
                  $order_targetdata = $tot_collection;                                    
                  break;
                case 4:
                  $amt_collection = Collection::where('company_id',$company_id)->where('employee_id',$empid)->where('payment_date','like',$act_date.'%')->get()->sum('payment_received');
                  $intervalid = $arrange_targets[$u]['tg_interval'][$yt];
                  $arrange_targets[$u]['results'][$tr][$month][$order_rule] = $amt_collection;
                  $targetvalues = $arrange_targets[$u]['tg_values'][$yt];
                  $arrange_targets[$u]['tg_values_calculated'][$tr][$month][$order_rule] = round($this->convert_targetvalues($company_id,$intervalid,$targetvalues,$act_date,$total_monthdays));
                  $order_targetdata = $amt_collection;                                                      
                  break;
                case 5:
                  $tot_visits = ClientVisit::where('company_id',$company_id)->where('employee_id',$empid)->where('date','like',$act_date.'%')->get()->count();
                  $intervalid = $arrange_targets[$u]['tg_interval'][$yt];
                  $arrange_targets[$u]['results'][$tr][$month][$order_rule] = $tot_visits;
                  $targetvalues = $arrange_targets[$u]['tg_values'][$yt];
                  $arrange_targets[$u]['tg_values_calculated'][$tr][$month][$order_rule] = round($this->convert_targetvalues($company_id,$intervalid,$targetvalues,$act_date,$total_monthdays));
                  $order_targetdata = $tot_visits;                                                            break;
                case 6:
                  $tot_visits = Client::where('company_id',$company_id)->where('created_by',$empid)->where('created_at','like',$act_date.'%')->get()->count();
                  $intervalid = $arrange_targets[$u]['tg_interval'][$yt];
                  $arrange_targets[$u]['results'][$tr][$month][$order_rule] = $tot_visits;
                  $targetvalues = $arrange_targets[$u]['tg_values'][$yt];
                  $arrange_targets[$u]['tg_values_calculated'][$tr][$month][$order_rule] = round($this->convert_targetvalues($company_id,$intervalid,$targetvalues,$act_date,$total_monthdays));
                  $order_targetdata = $tot_visits;
                  break;
                case 7:
                  $tot_order = Order::where('company_id',$company_id)->where('employee_id',$empid)->where('order_date','like',$act_date.'%')->get()->count();
                  $tot_noorder = NoOrder::where('company_id',$company_id)->where('employee_id',$empid)->where('date','like',$act_date.'%')->get()->count();
                  $all_orders = $tot_order+$tot_noorder;
                  $intervalid = $arrange_targets[$u]['tg_interval'][$yt];
                  $arrange_targets[$u]['results'][$tr][$month][$order_rule] = $all_orders;
                  $targetvalues = $arrange_targets[$u]['tg_values'][$yt];
                  $arrange_targets[$u]['tg_values_calculated'][$tr][$month][$order_rule] = round($this->convert_targetvalues($company_id,$intervalid,$targetvalues,$act_date,$total_monthdays));
                  $order_targetdata = $all_orders;                  
                  break;
              }
              $order_targets[$u][$order_rule] = $order_targetdata;
            }
          }
        } 
      }


      $finaldata = array(); 
      if(count($arrange_targets)>0){
        foreach($order_targets as $yy=>$tt){
          foreach($data['alltargetoptions'] as $ran=>$dom){
            if(array_key_exists($ran,$tt)){
            }else{
              $order_targets[$yy][$ran] = 0;
            }
          }
        }
        if($order=='noof_order'){
          $keys = array_column($order_targets, 1);
          if($dir=='desc'){
            array_multisort($keys, SORT_ASC, $arrange_targets); 
          }else{
            array_multisort($keys, SORT_DESC, $arrange_targets); 
          }
        }else if($order=='value_orders'){
          $keys = array_column($order_targets, 2);
          if($dir=='desc'){
            array_multisort($keys, SORT_ASC, $arrange_targets); 
          }else{
            array_multisort($keys, SORT_DESC, $arrange_targets); 
          }
        }else if($order=='noof_collections'){
          $keys = array_column($order_targets, 3);
          if($dir=='desc'){
            array_multisort($keys, SORT_ASC, $arrange_targets); 
          }else{
            array_multisort($keys, SORT_DESC, $arrange_targets); 
          }
        }else if($order=='value_collections'){
          $keys = array_column($order_targets, 4);
          if($dir=='desc'){
            array_multisort($keys, SORT_ASC, $arrange_targets); 
          }else{
            array_multisort($keys, SORT_DESC, $arrange_targets); 
          }
        }else if($order=='noof_visits'){
          $keys = array_column($order_targets, 5);
          if($dir=='desc'){
            array_multisort($keys, SORT_ASC, $arrange_targets); 
          }else{
            array_multisort($keys, SORT_DESC, $arrange_targets); 
          }
        }else if($order=='golden_calls'){
          $keys = array_column($order_targets, 6);
          if($dir=='desc'){
            array_multisort($keys, SORT_ASC, $arrange_targets); 
          }else{
            array_multisort($keys, SORT_DESC, $arrange_targets); 
          }
        }else if($order=='total_calls'){
          $keys = array_column($order_targets, 7);
          if($dir=='desc'){
            array_multisort($keys, SORT_ASC, $arrange_targets); 
          }else{
            array_multisort($keys, SORT_DESC, $arrange_targets); 
          }
        }
      }

      $finalresults = array();
      if(count($arrange_targets)>0){
        $i = 1;
        foreach($arrange_targets as $y=>$t){
          $nestedData['id'] = $i++;
          $nestedData['salesman_name'] = "<a href='".domain_route('company.admin.employee.show',[$t['salesman_id']])."'>".$t['name']."</a>";
          $nestedData['noof_order'] = $nestedData['value_orders'] = $nestedData['noof_collections'] = $nestedData['value_collections'] = $nestedData['noof_visits'] = $nestedData['golden_calls'] = $nestedData['total_calls'] = '';
          foreach($t['tg_rule'] as $del=>$ta){
            if($ta==1){
              if($t['results'][$year][$month][$ta]>=$t['tg_values_calculated'][$year][$month][$ta]){
                $nestedData['noof_order'] = $t['results'][$year][$month][$ta].'/'.$t['tg_values_calculated'][$year][$month][$ta];
              }else{
                $nestedData['noof_order'] = $t['results'][$year][$month][$ta].'/'.$t['tg_values_calculated'][$year][$month][$ta].'/mr'; 
              }
            }else if($ta==2){
              if($t['results'][$year][$month][$ta]>=$t['tg_values_calculated'][$year][$month][$ta]){
                $nestedData['value_orders'] = $t['results'][$year][$month][$ta].'/'.$t['tg_values_calculated'][$year][$month][$ta];
              }else{
                $nestedData['value_orders'] = $t['results'][$year][$month][$ta].'/'.$t['tg_values_calculated'][$year][$month][$ta].'/mr';
              }
            }else if($ta==3){
              if($t['results'][$year][$month][$ta]>=$t['tg_values_calculated'][$year][$month][$ta]){
                $nestedData['noof_collections'] = $t['results'][$year][$month][$ta].'/'.$t['tg_values_calculated'][$year][$month][$ta];
              }else{
                $nestedData['noof_collections'] = $t['results'][$year][$month][$ta].'/'.$t['tg_values_calculated'][$year][$month][$ta].'/mr';
              }
            }else if($ta==4){
              if($t['results'][$year][$month][$ta]>=$t['tg_values_calculated'][$year][$month][$ta]){
                $nestedData['value_collections'] = $t['results'][$year][$month][$ta].'/'.$t['tg_values_calculated'][$year][$month][$ta];
              }else{
                $nestedData['value_collections'] = $t['results'][$year][$month][$ta].'/'.$t['tg_values_calculated'][$year][$month][$ta].'/mr';
              }
            }else if($ta==5){
              if($t['results'][$year][$month][$ta]>=$t['tg_values_calculated'][$year][$month][$ta]){
                $nestedData['noof_visits'] = $t['results'][$year][$month][$ta].'/'.$t['tg_values_calculated'][$year][$month][$ta];
              }else{
                $nestedData['noof_visits'] = $t['results'][$year][$month][$ta].'/'.$t['tg_values_calculated'][$year][$month][$ta].'/mr';
              }
            }else if($ta==6){
              if($t['results'][$year][$month][$ta]>=$t['tg_values_calculated'][$year][$month][$ta]){
                $nestedData['golden_calls'] = $t['results'][$year][$month][$ta].'/'.$t['tg_values_calculated'][$year][$month][$ta];
              }else{
                $nestedData['golden_calls'] = $t['results'][$year][$month][$ta].'/'.$t['tg_values_calculated'][$year][$month][$ta].'/mr';
              }
            }else if($ta==7){
              if($t['results'][$year][$month][$ta]>=$t['tg_values_calculated'][$year][$month][$ta]){
                $nestedData['total_calls'] = $t['results'][$year][$month][$ta].'/'.$t['tg_values_calculated'][$year][$month][$ta];
              }else{
                $nestedData['total_calls'] = $t['results'][$year][$month][$ta].'/'.$t['tg_values_calculated'][$year][$month][$ta].'/mr';
              }
            }
          }
          $finalresults[] = $nestedData;
        }
      }

      $finalresults = array_slice($finalresults,$start,$limit);
      $json_data = array(
        "draw"            => intval($request->input('draw')),  
        "recordsTotal"    => intval($totalData),  
        "recordsFiltered" => intval($totalFiltered), 
        "data"            => $finalresults,
      );
      return json_encode($json_data); 
    }

    public function custompdfdexport(Request $request){
      $getExportData = json_decode($request->exportedData)->data;
      $pageTitle = $request->pageTitle;
      $columns = json_decode($request->columns);
      $properties = json_decode($request->properties);
      set_time_limit ( 300 );
      $pdf = PDF::loadView('company.targetsalesman.exportpdf', compact('getExportData', 'pageTitle', 'properties', 'columns'))->setPaper('a4', 'portrait');
      $download = $pdf->download($pageTitle.'.pdf');
      return $download;
    }

    public function salesmanTargetList(Request $request){
      $company_id = config('settings.company_id');
      $data['alltargets'] = TargetSalesman::where('company_id', $company_id)->orderBy('created_at', 'asc')->pluck('target_name', 'id')->toArray();
      $data['allsalesman'] = Auth::user()->handleQuery('employee')->where('company_id', $company_id)->where('status', '=', 'Active')->orderby('id','asc')->where('name','!=','')->get()->pluck('name','id')->toArray();
      $data['alltargetoptions'] = DB::table('salesmantarget_optionstype')->get()->toArray();
      $data['targetvalue'] = ['1'=>'Daily','2'=>'Weekly','3'=>'Monthly'];

      $salesman_id = '';
      $salestargetrec = array();
      if(isset($request->id)){
        $salesman_id = $request->id;
        $clientid = Employee::find((int)$salesman_id);
        if($clientid){
          $prepQuery = Auth::user()->handleQuery('employee')->has('targetsalesmanassign')->where('company_id', $company_id)->where('status', '=', 'Active')->where('name','!=','')->where(function($query) use ($salesman_id){
                            $query->orWhere('id','LIKE',"%{$salesman_id}%");
                          });
        }else{
          return redirect()->route('company.admin.salesmantarget', ['domain' => domain()])->with('error', 'Salesman does not exist.');
        }
      }else{        
        $prepQuery = Auth::user()->handleQuery('employee')->has('targetsalesmanassign')->where('company_id', $company_id)->where('status', '=', 'Active');
      }
 
      $results = $prepQuery->get();
      $i=0;
      $roles = '';$assigned_roles = array(); 
      foreach($results as $vv=>$mm){
        $assigned_roles = array(); 
        $nestedData['id'] = ++$i;
        $nestedData['salesman_name'] = $mm['name'];
        $nestedData['salesman_id'] = $mm['id'];
        $client_check = TargetSalesmanassign::where('company_id',$company_id)->where('salesman_id',$mm['id'])->get()->toArray();
        foreach($client_check as $ab=>$cd){
          $assigned_roles[$mm['id']][$cd['target_groupid']][] = $cd['target_rule'];
        }
        $nestedData['assigned_roles'] = array();
        foreach($mm->targetsalesmanassign->toArray() as $bb=>$gg){
          foreach($assigned_roles as $ll=>$xx){
            foreach($xx as $xy=>$yx){
              if((int)$gg['target_groupid']==$xy){
                $nestedData['assigned_roles'][$xy][] = $gg;
              }
            }
          }
        }
        $salestargetrec[] = $nestedData;
      }
      $data['salestargetrecord'] = $salestargetrec;
            
      return view('company.targetsalesman.index2',compact('data','salesman_id'));
    }

    public function salesmanTargetListDT_orig(Request $request){
      $targetvalue = ['1'=>'Daily','2'=>'Weekly','3'=>'Monthly'];
      $alltargetoptions = DB::table('salesmantarget_optionstype')->get()->pluck('options_value','id')->toArray();
      $company_id = config('settings.company_id');
      $columns = array(0 => 'id',1 => 'name',2 => 'target_name');
      $start = $request->input('start');
      $limit = $request->input('length');
      $order = $columns[$request->input('order.0.column')];
      $dir = $request->input('order.0.dir');

      $search_value = $request->input('search.value');
      $thismonth_targets = Carbon::now()->format('Y-m');

      // $prepQuery = Auth::user()->handleQuery('employee')->has('targetsalesmanassign')->where('company_id', $company_id)->where('status', '=', 'Active');
      $prepQuery = Auth::user()->handleQuery('employee')->where('company_id', $company_id)->where('status', '=', 'Active');

      $salestargetrec = array();
      if(isset($request->salesmanID)){
        $salesman_id = (int)$request->salesmanID;
        $prepQuery = $prepQuery->where('id',$salesman_id);
      }
      if(!empty($search_value)){
        $prepQuery = $prepQuery->where('name','like',$search_value.'%');
      }

      $prepQuery = $prepQuery->with('targetsalesmanassign');

      if($order!='target_name'){
        $prepQuery = $prepQuery->orderby($order,$dir);
      }

      $results = $prepQuery->get();
      $i=0; 
      $roles = '';$assigned_roles= $salestargetrec = array(); 
      if(count($results)>0){
        foreach($results->toArray() as $vv=>$mm){
          if(count($mm['targetsalesmanassign'])>0){
            $assigned_roles = array(); 
            $nestedData['id'] = ++$i;
            $nestedData['salesman_name'] = "<a href='".domain_route('company.admin.employee.show',[$mm['id']])."'>".$mm['name']."</a>";
            $client_check = TargetSalesmanassign::where('company_id',$company_id)->where('salesman_id',$mm['id'])->where('target_startmonth','like',$thismonth_targets.'%')->get()->toArray();
            foreach($client_check as $ab=>$cd){
              $assigned_roles[$mm['id']][$cd['target_groupid']][] = $cd['target_rule'];
            }
            $salesman_roles = $tgname = '';
            foreach($mm['targetsalesmanassign'] as $bb=>$gg){
              if($gg['target_groupid']!=0){
                $tgname = $gg['target_name'];
                $salesman_roles .= $alltargetoptions[$gg['target_rule']];
                $salesman_roles .= '['.$gg['target_values'].' '.$targetvalue[$gg['target_interval']].']<br>';
                $nestedData['sort_date'] = $gg['created_at'];
              }else{
                $tgname = $gg['target_name'];
                $salesman_roles = '';
                $nestedData['sort_date'] = $gg['created_at'];
              }
            }
            $nestedData['assgned_roles_name'] = $tgname;
            $nestedData['assigned_roles'] = '<b><u>'.$tgname.'</u></b>'.'<br>'.$salesman_roles;
            $html = '';
            $html .= "<a class='btn btn-success btn-sm' id='".$mm['id']."' onClick='showSalesmanHistory(this)' style='padding: 3px 6px;'><i class='fa fa-eye'></i></a>";
            if(Auth::user()->can('targets-update')){
              $acturl = domain_route('company.admin.salesmanindivtargetlist.modify',[$mm['id']]);
              $html .= "<a href='".$acturl."' class='btn btn-warning btn-sm' style='padding: 3px 6px;'><i class='fa fa-edit'></i></a>"; 
            }
            $nestedData['action'] = $html;

            $salestargetrec[] = $nestedData;
          }
        }
      }
      
      $finaldata = array(); 
      if(count($salestargetrec)>0){
        if($order=='id'){
          foreach ($salestargetrec as $key => $part) {
            $sort[$key] = strtotime($part['sort_date']);
          }
          array_multisort($sort, SORT_DESC, $salestargetrec);

          $yy = 1;
          foreach($salestargetrec as $subKey => $subArray){
            $arry_tgt = array();
            foreach($subArray as $l=>$m){
              if($l == 'sort_date'){
              }elseif($l == 'id'){
                $arry_tgt[$l] = $yy++;
              }else{
                $arry_tgt[$l] = $m;
              }
            }
            $finaldata[] = $arry_tgt;
          }
        }else if($order=='target_name'){
          $keys = array_column($salestargetrec, 'assgned_roles_name');
          if($dir=='asc'){
            array_multisort($keys, SORT_ASC, $salestargetrec); 
          }else{
            array_multisort($keys, SORT_DESC, $salestargetrec); 
          }
          $yy = 1;
          foreach($salestargetrec as $subKey => $subArray){
            $arry_tgt = array();
            foreach($subArray as $l=>$m){
              if($l == 'assgned_roles_name'){
              }elseif($l == 'id'){
                $arry_tgt[$l] = $yy++;
              }else{
                $arry_tgt[$l] = $m;
              }
            }
            $finaldata[] = $arry_tgt;
          }
        }else{
          $finaldata = $salestargetrec;     
        }
      }

      $prepQuery2 = Auth::user()->handleQuery('employee')->where('company_id', $company_id)->where('status', '=', 'Active');
      $prepQuery2 = $prepQuery2->with('targetsalesmanassign')->get()->toArray();
      $c = 0;
      foreach($prepQuery2 as $h=>$g){
        if(count($g['targetsalesmanassign'])>0){
          $c++;
        }
      }
      if(!isset($request->salesmanID) && empty($request->input('search.value'))){
        $totalData =  $c;
        $totalFiltered = $totalData; 
      }else if(isset($request->salesmanID) && empty($request->input('search.value'))){
        $totalData = $c;
        $totalFiltered = $i; 
      }else if(!isset($request->salesmanID) && !empty($request->input('search.value'))){
        $totalData = $c;
        $totalFiltered = $i; 
      }

      $finaldata = array_slice($finaldata,$start,$limit);
      $json_data = array(
        "draw"            => intval($request->input('draw')),  
        "recordsTotal"    => intval($totalData),  
        "recordsFiltered" => intval($totalFiltered), 
        "data"            => $finaldata,
      );
      return json_encode($json_data); 
    }


    public function salesmanTargetHistory2(Request $request){
      $company_id = config('settings.company_id');
      $salesmanid = $request->id; $salesman_name = '';
      $salesman_exist = Auth::user()->handleQuery('employee')->where('status', '=', 'Active')->find($salesmanid);
      if($salesman_exist){
        $alltargetoptions = DB::table('salesmantarget_optionstype')->get()->toArray();
        $targetvalue = ['1'=>'Daily','2'=>'Weekly','3'=>'Monthly'];
        $salesmantargets = DB::table('salesmantarget_assignedit_history')->where('company_id',$company_id)->where('salesman_id',$salesmanid)->orderby('targethist_newgroupid','desc')->get()->toArray();

        // print_r($salesmantargets);die();

        $salesmantargets_cur = TargetSalesmanassign::where('company_id',$company_id)->where('salesman_id',$salesmanid)->get()->toArray();
        $htmldata = ''; $zop = 1; $tru='---';$tinter='---';$tvale='---';
        if(count($salesmantargets_cur)>0){
          $htmldata .= "<tr>";
          $htmldata .= "<td rowspan='".(count($salesmantargets_cur)+1)."'><b>".$zop++."</b></td>";
          $htmldata .= "<td rowspan='".(count($salesmantargets_cur)+1)."'><b>".$salesmantargets_cur[0]['target_name']."</b><br><span style='color:blue;font-style:italic;'>*Current Target</span></td>";
          $htmldata .= "</tr>";
          foreach($salesmantargets_cur as $ne=>$pal){
            $htmldata .= "<tr>";
            foreach($alltargetoptions as $rrt=>$trr){
              if($pal['target_rule']==$alltargetoptions[$rrt]->id){
                $tru = $alltargetoptions[$rrt]->options_value;
              }
            }
            foreach($targetvalue as $jjk=>$kjj){
              if($pal['target_interval']==$jjk){
                $tinter = $kjj;
              }
            }
            if($pal['target_values']==0){$tvale='---';}else{$tvale=$pal['target_values'];}
            $htmldata .= "<td><b>".$tru."</b></td>";
            $htmldata .= "<td><b>".$tinter."</b></td>";
            $htmldata .= "<td><b>".$tvale."</b></td>";
            if(config('settings.ncal')==0){ $dt = Carbon::parse($pal['created_at'])->format('Y-m-d'); }else{ $dt = getDeltaDate(Carbon::parse($pal['created_at'])->format('Y-m-d'));}
            $htmldata .= "<td><b>[".$dt.']&nbsp;&nbsp;-&nbsp;&nbsp;[Now'."]</b></td>";
            $htmldata .= "</tr>";
          }
        }
        if(count($salesmantargets)>0){
          foreach($salesmantargets as $gr=>$rg){
            $tgid[$salesmantargets[$gr]->targethist_newgroupid][] = $salesmantargets[$gr];
            $tggrpid[] = $rg->target_groupid;
          }
          foreach($tgid as $ytt=>$tty){
            $htmldata .= "<tr>";
            $htmldata .= "<td rowspan='".(count($tty)+1)."'>".$zop++."</td>";
            $htmldata .= "<td rowspan='".(count($tty)+1)."'>".$tgid[$ytt][0]->target_name."</td>";
            $htmldata .= "</tr>";
            foreach($tty as $ggh=>$hhg){
              $trule = '---'; $tinterval = '---';$tval = '---';
              if($ytt==$hhg->targethist_newgroupid){
                foreach($alltargetoptions as $rrt=>$trr){
                  if($hhg->target_rule==$alltargetoptions[$rrt]->id){
                    $trule = $alltargetoptions[$rrt]->options_value;
                  }
                }
                foreach($targetvalue as $jjk=>$kjj){
                  if($hhg->target_interval==$jjk){
                    $tinterval = $kjj;
                  }
                }
                // $this->fetchTGTUpdHistory($hhg->target_groupid);
                if($hhg->target_values==0){$tval='---';}else{$tval=$hhg->target_values;}
                $htmldata .= "<tr>";
                $htmldata .= "<td>".$trule."</td>";
                $htmldata .= "<td>".$tinterval."</td>";
                $htmldata .= "<td>".$tval."</td>";
                if(config('settings.ncal')==0){ $sdt = Carbon::parse($hhg->target_assigneddate)->format('Y-m-d'); }else{ $sdt = getDeltaDate(Carbon::parse($hhg->target_assigneddate)->format('Y-m-d')); }
                if(config('settings.ncal')==0){ $edt = Carbon::parse($hhg->created_at)->format('Y-m-d'); }else{ $edt = getDeltaDate(Carbon::parse($hhg->created_at)->format('Y-m-d')); }
                $htmldata .= "<td>[".$sdt.']&nbsp;&nbsp;-&nbsp;&nbsp;['.$edt."]</td>";
                $htmldata .= "</tr>";
              }
            }
          }
        }else{
          $htmldata .= "<tr>";
          $htmldata .= "<td colspan='5'>No Target History Available for this Salesman.</td>";
          $htmldata .= "</tr>";
        }
        $salesman_name = $salesman_exist->toArray()['name'];
        return response()->json(['msg'=>$htmldata,'sname'=>$salesman_name],200);
      }else{
        return response()->json(['msg'=>'Wrong Salesman'],400);
      }
    }


    public function salesmanTargetListDT_orig2(Request $request){
      $targetvalue = ['1'=>'Daily','2'=>'Weekly','3'=>'Monthly'];
      $company_id = config('settings.company_id');
      // $alltargetoptions = DB::table('salesmantarget_optionstype')->get()->pluck('options_value','id')->toArray();
      $targetoptions = DB::table('salesmantarget_optionstype')->get();
      $alltargetoptions = array();
      foreach($targetoptions as $k=>$topt){
        switch($topt->options_module){
          case 'ord':
            if(config('settings.orders')==1){
              $alltargetoptions[$topt->id] = $topt->options_value;
            }
            break;

          case 'coll':
            if(config('settings.collections')==1){
              $alltargetoptions[$topt->id] = $topt->options_value;
            }
            break;

          case 'vis':
            if(config('settings.visit_module')==1){
              $alltargetoptions[$topt->id] = $topt->options_value;
            }
            break;

          case 'party':
            if(config('settings.party')==1){
              $alltargetoptions[$topt->id] = $topt->options_value;
            }
            break;

          case 'ord_zero':
            if(config('settings.orders')==1 && config('settings.zero_orders')==1){
              $alltargetoptions[$topt->id] = $topt->options_value;
            }
            break;
        }
      }

      $curruser = Auth::user()->EmployeeId();
      $emptargets = TargetSalesmanassign::where('company_id',$company_id)->pluck('salesman_id')->toArray();
      $emptargets = array_values(array_unique($emptargets));
      $emptgt = array();
      if(count($emptargets)>0){
        foreach($emptargets as $yt){
          if($yt==$curruser){
          }else{
            $emptgt[] = $yt;
          }
        }
      }

      $columns = array(0 => 'id',1 => 'name',2 => 'target_name');
      $start = $request->input('start');
      $limit = $request->input('length');
      $order = $columns[$request->input('order.0.column')];
      $dir = $request->input('order.0.dir');

      $search_value = $request->input('search.value');
      $thismonth_targets = Carbon::now()->format('Y-m');

      // $prepQuery = Auth::user()->handleQuery('employee')->has('targetsalesmanassign')->where('company_id', $company_id)->where('status', '=', 'Active');
      $prepQuery = Auth::user()->handleQuery('employee')->where('company_id', $company_id)->where('status', '=', 'Active');

      $salestargetrec = array();
      if(isset($request->salesmanID)){
        $salesman_id = (int)$request->salesmanID;
        $prepQuery = $prepQuery->where('id',$salesman_id);
      }
      if(!empty($search_value)){
        $prepQuery = $prepQuery->where('name','like',$search_value.'%');
      }

      $prepQuery = $prepQuery->with('targetsalesmanassign');

      if($order!='target_name'){
        $prepQuery = $prepQuery->orderby($order,$dir);
      }

      $results = $prepQuery->get();
      $i=0; 
      $roles = '';$assigned_roles= $salestargetrec = array(); 
      if(count($results)>0){
        foreach($results->toArray() as $vv=>$mm){
          if(count($mm['targetsalesmanassign'])>0){
            $assigned_roles = array(); 
            $nestedData['id'] = ++$i;
            $empid = $mm['id'];
            $nestedData['salesman_name'] = "<a href='".domain_route('company.admin.employee.show',[$mm['id']])."'>".$mm['name']."</a>";
            $client_check = TargetSalesmanassign::where('company_id',$company_id)->where('salesman_id',$mm['id'])->where('target_startmonth','like',$thismonth_targets.'%')->get()->toArray();
            foreach($client_check as $ab=>$cd){
              $assigned_roles[$mm['id']][$cd['target_groupid']][] = $cd['target_rule'];
            }
            $salesman_roles = $tgname = '';
            foreach($mm['targetsalesmanassign'] as $bb=>$gg){
              if($gg['target_groupid']!=0){
                $tgname = $gg['target_name'];
                $salesman_roles .= $alltargetoptions[$gg['target_rule']];
                $salesman_roles .= '['.$gg['target_values'].' '.$targetvalue[$gg['target_interval']].']<br>';
                $nestedData['sort_date'] = $gg['created_at'];
              }else{
                $tgname = $gg['target_name'];
                $salesman_roles = '';
                $nestedData['sort_date'] = $gg['created_at'];
              }
            }
            $nestedData['assgned_roles_name'] = $tgname;
            $nestedData['assigned_roles'] = '<b><u>'.$tgname.'</u></b>'.'<br>'.$salesman_roles;
            $html = '';
            $html .= "<a class='btn btn-success btn-sm' id='".$mm['id']."' onClick='showSalesmanHistory(this)' style='padding: 3px 6px;'><i class='fa fa-eye'></i></a>";
            if(in_array($empid,$emptgt)){
              if(Auth::user()->can('targets-update')){
                $acturl = domain_route('company.admin.salesmanindivtargetlist.modify',[$mm['id']]);
                $html .= "<a href='".$acturl."' class='btn btn-warning btn-sm' style='padding: 3px 6px;'><i class='fa fa-edit'></i></a>"; 
              }
            }
            $nestedData['action'] = $html;

            $salestargetrec[] = $nestedData;
          }
        }
      }
     
      $finaldata = array(); 
      if(count($salestargetrec)>0){
        if($order=='id'){
          foreach ($salestargetrec as $key => $part) {
            $sort[$key] = strtotime($part['sort_date']);
          }
          array_multisort($sort, SORT_DESC, $salestargetrec);

          $yy = 1;
          foreach($salestargetrec as $subKey => $subArray){
            $arry_tgt = array();
            foreach($subArray as $l=>$m){
              if($l == 'sort_date'){
              }elseif($l == 'id'){
                $arry_tgt[$l] = $yy++;
              }else{
                $arry_tgt[$l] = $m;
              }
            }
            $finaldata[] = $arry_tgt;
          }
        }else if($order=='target_name'){
          $keys = array_column($salestargetrec, 'assgned_roles_name');
          if($dir=='asc'){
            array_multisort($keys, SORT_ASC, $salestargetrec); 
          }else{
            array_multisort($keys, SORT_DESC, $salestargetrec); 
          }
          $yy = 1;
          foreach($salestargetrec as $subKey => $subArray){
            $arry_tgt = array();
            foreach($subArray as $l=>$m){
              if($l == 'assgned_roles_name'){
              }elseif($l == 'id'){
                $arry_tgt[$l] = $yy++;
              }else{
                $arry_tgt[$l] = $m;
              }
            }
            $finaldata[] = $arry_tgt;
          }
        }else{
          $finaldata = $salestargetrec;     
        }
      }

      $prepQuery2 = Auth::user()->handleQuery('employee')->where('company_id', $company_id)->where('status', '=', 'Active');
      $prepQuery2 = $prepQuery2->with('targetsalesmanassign')->get()->toArray();
      $c = 0;
      foreach($prepQuery2 as $h=>$g){
        if(count($g['targetsalesmanassign'])>0){
          $c++;
        }
      }
      if(!isset($request->salesmanID) && empty($request->input('search.value'))){
        $totalData =  $c;
        $totalFiltered = $totalData; 
      }else if(isset($request->salesmanID) && empty($request->input('search.value'))){
        $totalData = $c;
        $totalFiltered = $i; 
      }else if(!isset($request->salesmanID) && !empty($request->input('search.value'))){
        $totalData = $c;
        $totalFiltered = $i; 
      }

      $finaldata = array_slice($finaldata,$start,$limit);
      $json_data = array(
        "draw"            => intval($request->input('draw')),  
        "recordsTotal"    => intval($totalData),  
        "recordsFiltered" => intval($totalFiltered), 
        "data"            => $finaldata,
      );
      return json_encode($json_data); 
    }

    public function custompdfdexport_target(Request $request){
      $getExportData = json_decode($request->exportedData)->data;
      $pageTitle = $request->pageTitle;
      $columns = json_decode($request->columns);
      $properties = json_decode($request->properties);
      set_time_limit ( 300 );
      $pdf = PDF::loadView('company.targetsalesman.exportpdf_target', compact('getExportData', 'pageTitle', 'properties', 'columns'))->setPaper('a4', 'portrait');
      $download = $pdf->download($pageTitle.'.pdf');
      return $download;
    }


    public function genReportDT(Request $request){
      $company_id = config('settings.company_id');
      $data['allsalesman'] = Auth::user()->handleQuery('employee')->where('company_id', $company_id)->where('status', '=', 'Active')->orderby('id','asc')->where('name','!=','')->get()->pluck('name','id')->toArray();
      $data['targetvalue'] = ['1'=>'Daily','2'=>'Weekly','3'=>'Monthly'];
      $data['months'] = ['01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June','07'=>'July','08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December'];
      // $data['alltargetoptions'] = DB::table('salesmantarget_optionstype')->get()->pluck('options_value','id')->toArray();

      $targetoptions = DB::table('salesmantarget_optionstype')->get();
      $alltargetoptions = array();
      foreach($targetoptions as $k=>$topt){
        switch($topt->options_module){
          case 'ord':
            if(config('settings.orders')==1){
              $alltargetoptions[$topt->id] = $topt->options_value;
            }
            break;

          case 'coll':
            if(config('settings.collections')==1){
              $alltargetoptions[$topt->id] = $topt->options_value;
            }
            break;

          case 'vis':
            if(config('settings.visit_module')==1){
              $alltargetoptions[$topt->id] = $topt->options_value;
            }
            break;

          case 'party':
            if(config('settings.party')==1){
              $alltargetoptions[$topt->id] = $topt->options_value;
            }
            break;

          case 'ord_zero':
            if(config('settings.orders')==1 && config('settings.zero_orders')==1){
              $alltargetoptions[$topt->id] = $topt->options_value;
            }
            break;
        }
      }
      $data['alltargetoptions'] = $alltargetoptions;


      if(isset($request->yearDate) && isset($request->monthDate)){
        $year = $request->yearDate; 
        $month = $request->monthDate;
        if(config('settings.ncal')==0){
          $monthNum = ($month<10)?('0'.$month):$month;
          $curmonthdays = cal_days_in_month(CAL_GREGORIAN,$month,$year);
          $eng_stdt = $year.'-'.$monthNum.'-01';
          $eng_eddt = $year.'-'.$monthNum.'-'.$curmonthdays;
        }else{
          $monthNum = $month;
          $eng_stdt = $request->engstdt;
          $eng_eddt = $request->engeddt;
        }
        $monthName = date("F", mktime(0, 0, 0, $monthNum, 10));
        $resultmonth = array($year=>array($monthNum=>$monthName));
        $completedate = $year.'-'.$monthNum;
      }else{
        $year = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');
        $monthName = date("F", mktime(0, 0, 0, $month, 10));
        $resultmonth = array($year=>array($month=>$monthName));
        $completedate = $year.'-'.$month;
        $curmonthdays = cal_days_in_month(CAL_GREGORIAN,$month,$year);
        $eng_stdt = $year.'-'.$monthNum.'-01';
        $eng_eddt = $year.'-'.$monthNum.'-'.$curmonthdays;
      }
      $columns = array(0 => 'id',1 => 'name',2 => 'noof_order',3 => 'value_orders',4 => 'noof_collections',5 => 'value_collections', 6 => 'noof_visits', 7 => 'golden_calls', 8 => 'total_calls');
      $start = $request->input('start');
      $limit = $request->input('length');
      $order = $columns[$request->input('order.0.column')];
      $dir = $request->input('order.0.dir');
      $prepQuery = Auth::user()->handleQuery('employee')->where('company_id', $company_id)->where('status', '=', 'Active'); 

      if(isset($request->salesmanID)){
        $salesmanIDs = explode(',',$request->salesmanID);
        $prepQuery = $prepQuery->whereIn('id',$salesmanIDs);
      }

      if(!empty($request->input('search.value'))){
        $prepQuery = $prepQuery->where('name','like',$request->input('search.value').'%');
      }

      if(config('settings.ncal')==0){
        $prepQuery = $prepQuery->with(['targetsalesmanassign' => function($q) use($completedate){
          $q->where('target_startmonth','like',$completedate.'%');
        }]);
        $prepQuery = $prepQuery->with(['targetsalesmanassignhistory' => function($q) use($completedate){
            $q->where('target_startmonth','like',$completedate.'%')->orderby('target_assigneddate','desc');
        }]);
      }else{
        $prepQuery = $prepQuery->with(['targetsalesmanassign' => function($q) use($eng_stdt,$eng_eddt){
          $q->whereBetween('target_startmonth',[$eng_stdt,$eng_eddt]);
        }]);
        $prepQuery = $prepQuery->with(['targetsalesmanassignhistory' => function($q) use($eng_stdt,$eng_eddt){
            $q->whereBetween('target_startmonth',[$eng_stdt,$eng_eddt])->orderby('target_assigneddate','desc');
        }]);
      }

      if($order=='id' || $order=='name'){
        $prepQuery = $prepQuery->orderby($order,$dir);
      }
    
      $arrange_targets = $salesmantgt_hist= $salesmantgt = array();
      $prepQuery = $prepQuery->get();
      $i = 0;
      $year2 = Carbon::now()->format('Y');
      $month2 = Carbon::now()->format('m');
      $completedate2 = $year2.'-'.$month2;
      foreach($prepQuery as $indx=>$emptgt){
        $salesmantgt = $emptgt->targetsalesmanassign->toArray();
        if($completedate!=$completedate2){
          $salesmantgt_hist = $emptgt->targetsalesmanassignhistory->toArray();
        }
        $salesmantgt_dates = array();
        if(count($salesmantgt)>0){
          foreach($salesmantgt as $aa=>$bb){
            $salesmantgt_dates['tg_rule'][] = $salesmantgt[$aa]['target_rule'];
            $salesmantgt_dates['tg_interval'][] = $salesmantgt[$aa]['target_interval'];
            $salesmantgt_dates['tg_values'][] = $salesmantgt[$aa]['target_values'];
            $salesmantgt_dates['name'] = $prepQuery[$indx]['name'];
            $salesmantgt_dates['salesman_id'] = $salesmantgt[$aa]['salesman_id'];
          }
          $chk_tgruleval = $salesmantgt_dates['tg_rule'][0];
          $chk_tgintervalval = $salesmantgt_dates['tg_interval'][0];
          $chk_tgvaluesval = $salesmantgt_dates['tg_values'][0];
          $chk_tgrule = count($salesmantgt_dates['tg_rule']);
          $chk_tginterval = count($salesmantgt_dates['tg_interval']);
          $chk_tgvalues = count($salesmantgt_dates['tg_values']);
          if($chk_tgruleval==0 && $chk_tgintervalval==0 && $chk_tgvaluesval==0 && $chk_tgrule==1 && $chk_tginterval==1 && $chk_tgvalues==1){
            //pass
          }else{
            $arrange_targets[$bb['salesman_id']] = $salesmantgt_dates;
          }
        }

        if(count($salesmantgt)==0 && count($salesmantgt_hist)>0){
          $targetgroupid = $salesmantgt_hist[0]['target_groupid'];
          foreach($salesmantgt_hist as $aa=>$bb){
            if($targetgroupid==$salesmantgt_hist[$aa]['target_groupid']){
              $salesmantgt_dates['tg_rule'][] = $salesmantgt_hist[$aa]['target_rule'];
              $salesmantgt_dates['tg_interval'][] = $salesmantgt_hist[$aa]['target_interval'];
              $salesmantgt_dates['tg_values'][] = $salesmantgt_hist[$aa]['target_values'];
              $salesmantgt_dates['name'] = $prepQuery[$indx]['name'];
              $salesmantgt_dates['salesman_id'] = $salesmantgt_hist[$aa]['salesman_id'];
            }
          }
          $chk_tgruleval = $salesmantgt_dates['tg_rule'][0];
          $chk_tgintervalval = $salesmantgt_dates['tg_interval'][0];
          $chk_tgvaluesval = $salesmantgt_dates['tg_values'][0];
          $chk_tgrule = count($salesmantgt_dates['tg_rule']);
          $chk_tginterval = count($salesmantgt_dates['tg_interval']);
          $chk_tgvalues = count($salesmantgt_dates['tg_values']);
          if($chk_tgruleval==0 && $chk_tgintervalval==0 && $chk_tgvaluesval==0 && $chk_tgrule==1 && $chk_tginterval==1 && $chk_tgvalues==1){
            //pass
          }else{
            $arrange_targets[$bb['salesman_id']] = $salesmantgt_dates;
          }
        }

        if(count($salesmantgt)>0 || count($salesmantgt_hist)>0){
          $i++;
        }
      }
      $totalData =  $i;
      if(!isset($request->salesmanID)){
        $totalFiltered = $totalData; 
      }

      if(empty($request->input('search.value'))){
        $totalFiltered = $totalData;
      }

    
      foreach($resultmonth as $tr=>$rt){
        foreach($rt as $dni=>$month){
          foreach($arrange_targets as $u=>$v){
            foreach($arrange_targets[$u]['tg_rule'] as $yt=>$ty){
              $order_rule = (int)$ty;
              $empid = (int)$u;
              $act_date = $tr.'-'.$dni;
              $total_monthdays = cal_days_in_month(CAL_GREGORIAN,$dni,$tr);
              $total_noofweek = $this->weeksInMonth($total_monthdays);
              switch($order_rule){
                case 1:
                  $tot_order = Order::where('company_id',$company_id)->where('employee_id',$empid)->whereBetween('order_date',[$eng_stdt,$eng_eddt])->get()->count();
                  $all_orders = $tot_order;//+$tot_noorder;
                  $intervalid = $arrange_targets[$u]['tg_interval'][$yt];
                  $arrange_targets[$u]['results'][$tr][$month][$order_rule] = $all_orders;
                  $targetvalues = $arrange_targets[$u]['tg_values'][$yt];
                  $arrange_targets[$u]['tg_values_calculated'][$tr][$month][$order_rule] = round($this->convert_targetvalues($company_id,$intervalid,$targetvalues,$act_date,$total_monthdays));
                  $order_targetdata = $all_orders;
                  break;
                case 2: 
                  $amt_order = Order::where('company_id',$company_id)->where('employee_id',$empid)->whereBetween('order_date',[$eng_stdt,$eng_eddt])->get()->sum('grand_total');
                  $amt_noorder = 0;//NoOrder::where('company_id',$company_id)->where('employee_id',$empid)->where('date','like',$act_date.'%')->get()->sum();
                  $allamt_orders = $amt_order+$amt_noorder;
                  $intervalid = $arrange_targets[$u]['tg_interval'][$yt];
                  $arrange_targets[$u]['results'][$tr][$month][$order_rule] = $allamt_orders;
                  $targetvalues = $arrange_targets[$u]['tg_values'][$yt];
                  $arrange_targets[$u]['tg_values_calculated'][$tr][$month][$order_rule] = round($this->convert_targetvalues($company_id,$intervalid,$targetvalues,$act_date,$total_monthdays));
                  $order_targetdata = $allamt_orders;                  
                  break;
                case 3:
                  $tot_collection = Collection::where('company_id',$company_id)->where('employee_id',$empid)->whereBetween('payment_date',[$eng_stdt,$eng_eddt])->get()->count();
                  $intervalid = $arrange_targets[$u]['tg_interval'][$yt];
                  $arrange_targets[$u]['results'][$tr][$month][$order_rule] = $tot_collection;
                  $targetvalues = $arrange_targets[$u]['tg_values'][$yt];
                  $arrange_targets[$u]['tg_values_calculated'][$tr][$month][$order_rule] = round($this->convert_targetvalues($company_id,$intervalid,$targetvalues,$act_date,$total_monthdays));
                  $order_targetdata = $tot_collection;                                    
                  break;
                case 4:
                  $amt_collection = Collection::where('company_id',$company_id)->where('employee_id',$empid)->whereBetween('payment_date',[$eng_stdt,$eng_eddt])->get()->sum('payment_received');
                  $intervalid = $arrange_targets[$u]['tg_interval'][$yt];
                  $arrange_targets[$u]['results'][$tr][$month][$order_rule] = $amt_collection;
                  $targetvalues = $arrange_targets[$u]['tg_values'][$yt];
                  $arrange_targets[$u]['tg_values_calculated'][$tr][$month][$order_rule] = round($this->convert_targetvalues($company_id,$intervalid,$targetvalues,$act_date,$total_monthdays));
                  $order_targetdata = $amt_collection;                                                      
                  break;
                case 5:
                  $tot_visits = ClientVisit::where('company_id',$company_id)->where('employee_id',$empid)->whereBetween('date',[$eng_stdt,$eng_eddt])->get()->count();
                  $intervalid = $arrange_targets[$u]['tg_interval'][$yt];
                  $arrange_targets[$u]['results'][$tr][$month][$order_rule] = $tot_visits;
                  $targetvalues = $arrange_targets[$u]['tg_values'][$yt];
                  $arrange_targets[$u]['tg_values_calculated'][$tr][$month][$order_rule] = round($this->convert_targetvalues($company_id,$intervalid,$targetvalues,$act_date,$total_monthdays));
                  $order_targetdata = $tot_visits;                                                            break;
                case 6:
                  $tot_visits = Client::where('company_id',$company_id)->where('created_by',$empid)->whereBetween('created_at',[$eng_stdt,$eng_eddt])->get()->count();
                  $intervalid = $arrange_targets[$u]['tg_interval'][$yt];
                  $arrange_targets[$u]['results'][$tr][$month][$order_rule] = $tot_visits;
                  $targetvalues = $arrange_targets[$u]['tg_values'][$yt];
                  $arrange_targets[$u]['tg_values_calculated'][$tr][$month][$order_rule] = round($this->convert_targetvalues($company_id,$intervalid,$targetvalues,$act_date,$total_monthdays));
                  $order_targetdata = $tot_visits;
                  break;
                case 7:
                  $tot_order = Order::where('company_id',$company_id)->where('employee_id',$empid)->whereBetween('order_date',[$eng_stdt,$eng_eddt])->get()->count();
                  $tot_noorder = NoOrder::where('company_id',$company_id)->where('employee_id',$empid)->whereBetween('date',[$eng_stdt,$eng_eddt])->get()->count();
                  $all_orders = $tot_order+$tot_noorder;
                  $intervalid = $arrange_targets[$u]['tg_interval'][$yt];
                  $arrange_targets[$u]['results'][$tr][$month][$order_rule] = $all_orders;
                  $targetvalues = $arrange_targets[$u]['tg_values'][$yt];
                  $arrange_targets[$u]['tg_values_calculated'][$tr][$month][$order_rule] = round($this->convert_targetvalues($company_id,$intervalid,$targetvalues,$act_date,$total_monthdays));
                  $order_targetdata = $all_orders;                  
                  break;
              }
              $order_targets[$u][$order_rule] = $order_targetdata;
            }
          }
        } 
      }


      $finaldata = array(); 
      if(count($arrange_targets)>0){
        foreach($order_targets as $yy=>$tt){
          foreach($data['alltargetoptions'] as $ran=>$dom){
            if(array_key_exists($ran,$tt)){
            }else{
              $order_targets[$yy][$ran] = 0;
            }
          }
        }
        if($order=='noof_order'){
          $keys = array_column($order_targets, 1);
          if($dir=='desc'){
            array_multisort($keys, SORT_ASC, $arrange_targets); 
          }else{
            array_multisort($keys, SORT_DESC, $arrange_targets); 
          }
        }else if($order=='value_orders'){
          $keys = array_column($order_targets, 2);
          if($dir=='desc'){
            array_multisort($keys, SORT_ASC, $arrange_targets); 
          }else{
            array_multisort($keys, SORT_DESC, $arrange_targets); 
          }
        }else if($order=='noof_collections'){
          $keys = array_column($order_targets, 3);
          if($dir=='desc'){
            array_multisort($keys, SORT_ASC, $arrange_targets); 
          }else{
            array_multisort($keys, SORT_DESC, $arrange_targets); 
          }
        }else if($order=='value_collections'){
          $keys = array_column($order_targets, 4);
          if($dir=='desc'){
            array_multisort($keys, SORT_ASC, $arrange_targets); 
          }else{
            array_multisort($keys, SORT_DESC, $arrange_targets); 
          }
        }else if($order=='noof_visits'){
          $keys = array_column($order_targets, 5);
          if($dir=='desc'){
            array_multisort($keys, SORT_ASC, $arrange_targets); 
          }else{
            array_multisort($keys, SORT_DESC, $arrange_targets); 
          }
        }else if($order=='golden_calls'){
          $keys = array_column($order_targets, 6);
          if($dir=='desc'){
            array_multisort($keys, SORT_ASC, $arrange_targets); 
          }else{
            array_multisort($keys, SORT_DESC, $arrange_targets); 
          }
        }else if($order=='total_calls'){
          $keys = array_column($order_targets, 7);
          if($dir=='desc'){
            array_multisort($keys, SORT_ASC, $arrange_targets); 
          }else{
            array_multisort($keys, SORT_DESC, $arrange_targets); 
          }
        }
      }

      $finalresults = array();
      if(count($arrange_targets)>0){
        $i = 1;
        foreach($arrange_targets as $y=>$t){
          $nestedData['id'] = $i++;
          $nestedData['salesman_name'] = "<a href='".domain_route('company.admin.employee.show',[$t['salesman_id']])."'>".$t['name']."</a>";
          $nestedData['noof_order'] = $nestedData['value_orders'] = $nestedData['noof_collections'] = $nestedData['value_collections'] = $nestedData['noof_visits'] = $nestedData['golden_calls'] = $nestedData['total_calls'] = '';
          foreach($t['tg_rule'] as $del=>$ta){
            if($ta==1){
              if($this->checkRulesAcctoModuleAcc(1)=='ok'){
                if($t['results'][$year][$month][$ta]>=$t['tg_values_calculated'][$year][$month][$ta]){
                  $nestedData['noof_order'] = $t['results'][$year][$month][$ta].'/'.$t['tg_values_calculated'][$year][$month][$ta];
                }else{
                  $nestedData['noof_order'] = $t['results'][$year][$month][$ta].'/'.$t['tg_values_calculated'][$year][$month][$ta].'/mr'; 
                }
              }
            }else if($ta==2){
              if($this->checkRulesAcctoModuleAcc(2)=='ok'){
                if($t['results'][$year][$month][$ta]>=$t['tg_values_calculated'][$year][$month][$ta]){
                  $nestedData['value_orders'] = $t['results'][$year][$month][$ta].'/'.$t['tg_values_calculated'][$year][$month][$ta];
                }else{
                  $nestedData['value_orders'] = $t['results'][$year][$month][$ta].'/'.$t['tg_values_calculated'][$year][$month][$ta].'/mr';
                }
              }
            }else if($ta==3){
              if($this->checkRulesAcctoModuleAcc(3)=='ok'){
                if($t['results'][$year][$month][$ta]>=$t['tg_values_calculated'][$year][$month][$ta]){
                  $nestedData['noof_collections'] = $t['results'][$year][$month][$ta].'/'.$t['tg_values_calculated'][$year][$month][$ta];
                }else{
                  $nestedData['noof_collections'] = $t['results'][$year][$month][$ta].'/'.$t['tg_values_calculated'][$year][$month][$ta].'/mr';
                }
              }
            }else if($ta==4){
              if($this->checkRulesAcctoModuleAcc(4)=='ok'){
                if($t['results'][$year][$month][$ta]>=$t['tg_values_calculated'][$year][$month][$ta]){
                  $nestedData['value_collections'] = $t['results'][$year][$month][$ta].'/'.$t['tg_values_calculated'][$year][$month][$ta];
                }else{
                  $nestedData['value_collections'] = $t['results'][$year][$month][$ta].'/'.$t['tg_values_calculated'][$year][$month][$ta].'/mr';
                }
              }
            }else if($ta==5){
              if($this->checkRulesAcctoModuleAcc(5)=='ok'){
                if($t['results'][$year][$month][$ta]>=$t['tg_values_calculated'][$year][$month][$ta]){
                  $nestedData['noof_visits'] = $t['results'][$year][$month][$ta].'/'.$t['tg_values_calculated'][$year][$month][$ta];
                }else{
                  $nestedData['noof_visits'] = $t['results'][$year][$month][$ta].'/'.$t['tg_values_calculated'][$year][$month][$ta].'/mr';
                }
              }
            }else if($ta==6){
              if($this->checkRulesAcctoModuleAcc(6)=='ok'){
                if($t['results'][$year][$month][$ta]>=$t['tg_values_calculated'][$year][$month][$ta]){
                  $nestedData['golden_calls'] = $t['results'][$year][$month][$ta].'/'.$t['tg_values_calculated'][$year][$month][$ta];
                }else{
                  $nestedData['golden_calls'] = $t['results'][$year][$month][$ta].'/'.$t['tg_values_calculated'][$year][$month][$ta].'/mr';
                }
              }
            }else if($ta==7){
              if($this->checkRulesAcctoModuleAcc(7)=='ok'){
                if($t['results'][$year][$month][$ta]>=$t['tg_values_calculated'][$year][$month][$ta]){
                  $nestedData['total_calls'] = $t['results'][$year][$month][$ta].'/'.$t['tg_values_calculated'][$year][$month][$ta];
                }else{
                  $nestedData['total_calls'] = $t['results'][$year][$month][$ta].'/'.$t['tg_values_calculated'][$year][$month][$ta].'/mr';
                }
              }
            }
          }
          $finalresults[] = $nestedData;
        }
      }

      $finalresults = array_slice($finalresults,$start,$limit);
      $json_data = array(
        "draw"            => intval($request->input('draw')),  
        "recordsTotal"    => intval($totalData),  
        "recordsFiltered" => intval($totalFiltered), 
        "data"            => $finalresults,
      );
      return json_encode($json_data); 
    }

    public function salesmanTargetListDT(Request $request){
      $targetvalue = ['1'=>'Daily','2'=>'Weekly','3'=>'Monthly'];
      $company_id = config('settings.company_id');
      // $alltargetoptions = DB::table('salesmantarget_optionstype')->get()->pluck('options_value','id')->toArray();
      $targetoptions = DB::table('salesmantarget_optionstype')->get();
      $alltargetoptions = array();
      foreach($targetoptions as $k=>$topt){
        switch($topt->options_module){
          case 'ord':
            if(config('settings.orders')==1){
              $alltargetoptions[$topt->id] = $topt->options_value;
            }
            break;

          case 'coll':
            if(config('settings.collections')==1){
              $alltargetoptions[$topt->id] = $topt->options_value;
            }
            break;

          case 'vis':
            if(config('settings.visit_module')==1){
              $alltargetoptions[$topt->id] = $topt->options_value;
            }
            break;

          case 'party':
            if(config('settings.party')==1){
              $alltargetoptions[$topt->id] = $topt->options_value;
            }
            break;

          case 'ord_zero':
            if(config('settings.orders')==1 && config('settings.zero_orders')==1){
              $alltargetoptions[$topt->id] = $topt->options_value;
            }
            break;
        }
      }

      $curruser = Auth::user()->EmployeeId();
      $emptargets = TargetSalesmanassign::where('company_id',$company_id)->pluck('salesman_id')->toArray();
      $emptargets = array_values(array_unique($emptargets));
      $emptgt = array();
      if(count($emptargets)>0){
        foreach($emptargets as $yt){
          if($yt==$curruser){
          }else{
            $emptgt[] = $yt;
          }
        }
      }

      $columns = array(0 => 'id',1 => 'name',2 => 'target_name');
      $start = $request->input('start');
      $limit = $request->input('length');
      $order = $columns[$request->input('order.0.column')];
      $dir = $request->input('order.0.dir');

      $search_value = $request->input('search.value');
      $thismonth_targets = Carbon::now()->format('Y-m');

      // $prepQuery = Auth::user()->handleQuery('employee')->has('targetsalesmanassign')->where('company_id', $company_id)->where('status', '=', 'Active');
      $prepQuery = Auth::user()->handleQuery('employee')->where('company_id', $company_id)->where('status', '=', 'Active');

      $salestargetrec = array();
      if(isset($request->salesmanID)){
        $salesman_id = (int)$request->salesmanID;
        $prepQuery = $prepQuery->where('id',$salesman_id);
      }
      if(!empty($search_value)){
        $prepQuery = $prepQuery->where('name','like',$search_value.'%');
      }

      $prepQuery = $prepQuery->with('targetsalesmanassign');

      if($order!='target_name'){
        $prepQuery = $prepQuery->orderby($order,$dir);
      }

      $results = $prepQuery->get();
      $i=0; 
      $roles = '';$assigned_roles= $salestargetrec = array(); 
      if(count($results)>0){
        foreach($results->toArray() as $vv=>$mm){
          if(count($mm['targetsalesmanassign'])>0){
            $assigned_roles = array(); 
            $nestedData['id'] = ++$i;
            $empid = $mm['id'];
            $nestedData['salesman_name'] = "<a href='".domain_route('company.admin.employee.show',[$mm['id']])."'>".$mm['name']."</a>";
            $client_check = TargetSalesmanassign::where('company_id',$company_id)->where('salesman_id',$mm['id'])->where('target_startmonth','like',$thismonth_targets.'%')->get()->toArray();
            foreach($client_check as $ab=>$cd){
              $assigned_roles[$mm['id']][$cd['target_groupid']][] = $cd['target_rule'];
            }
            $salesman_roles = $tgname = '';
            $ruleaccchk = 'npres';
            foreach($mm['targetsalesmanassign'] as $bb=>$gg){
              if($gg['target_groupid']!=0){
                $tgname = $gg['target_name'];
                if($this->checkRulesAcctoModuleAcc($gg['target_rule'])=='ok'){
                  $salesman_roles .= $alltargetoptions[$gg['target_rule']];
                  $salesman_roles .= '['.$gg['target_values'].' '.$targetvalue[$gg['target_interval']].']<br>';
                  $ruleaccchk = 'pres';
                }
                $nestedData['sort_date'] = $gg['created_at'];
              }else{
                $tgname = $gg['target_name'];
                $salesman_roles = '';
                $nestedData['sort_date'] = $gg['created_at'];
              }
            }
            if($ruleaccchk=='npres'){
              $salesman_roles .= '--';
            }
            $nestedData['assgned_roles_name'] = $tgname;
            $nestedData['assigned_roles'] = '<b><u>'.$tgname.'</u></b>'.'<br>'.$salesman_roles;
            $html = '';
            $html .= "<a class='btn btn-success btn-sm' id='".$mm['id']."' onClick='showSalesmanHistory(this)' style='padding: 3px 6px;'><i class='fa fa-eye'></i></a>";
            if(in_array($empid,$emptgt)){
              if(Auth::user()->can('targets-update')){
                $acturl = domain_route('company.admin.salesmanindivtargetlist.modify',[$mm['id']]);
                $html .= "<a href='".$acturl."' class='btn btn-warning btn-sm' style='padding: 3px 6px;'><i class='fa fa-edit'></i></a>"; 
              }
            }
            $nestedData['action'] = $html;

            $salestargetrec[] = $nestedData;
          }
        }
      }
     
      $finaldata = array(); 
      if(count($salestargetrec)>0){
        if($order=='id'){
          foreach ($salestargetrec as $key => $part) {
            $sort[$key] = strtotime($part['sort_date']);
          }
          array_multisort($sort, SORT_DESC, $salestargetrec);

          $yy = 1;
          foreach($salestargetrec as $subKey => $subArray){
            $arry_tgt = array();
            foreach($subArray as $l=>$m){
              if($l == 'sort_date'){
              }elseif($l == 'id'){
                $arry_tgt[$l] = $yy++;
              }else{
                $arry_tgt[$l] = $m;
              }
            }
            $finaldata[] = $arry_tgt;
          }
        }else if($order=='target_name'){
          $keys = array_column($salestargetrec, 'assgned_roles_name');
          if($dir=='asc'){
            array_multisort($keys, SORT_ASC, $salestargetrec); 
          }else{
            array_multisort($keys, SORT_DESC, $salestargetrec); 
          }
          $yy = 1;
          foreach($salestargetrec as $subKey => $subArray){
            $arry_tgt = array();
            foreach($subArray as $l=>$m){
              if($l == 'assgned_roles_name'){
              }elseif($l == 'id'){
                $arry_tgt[$l] = $yy++;
              }else{
                $arry_tgt[$l] = $m;
              }
            }
            $finaldata[] = $arry_tgt;
          }
        }else{
          $finaldata = $salestargetrec;     
        }
      }

      $prepQuery2 = Auth::user()->handleQuery('employee')->where('company_id', $company_id)->where('status', '=', 'Active');
      $prepQuery2 = $prepQuery2->with('targetsalesmanassign')->get()->toArray();
      $c = 0;
      foreach($prepQuery2 as $h=>$g){
        if(count($g['targetsalesmanassign'])>0){
          $c++;
        }
      }
      if(!isset($request->salesmanID) && empty($request->input('search.value'))){
        $totalData =  $c;
        $totalFiltered = $totalData; 
      }else if(isset($request->salesmanID) && empty($request->input('search.value'))){
        $totalData = $c;
        $totalFiltered = $i; 
      }else if(!isset($request->salesmanID) && !empty($request->input('search.value'))){
        $totalData = $c;
        $totalFiltered = $i; 
      }

      $finaldata = array_slice($finaldata,$start,$limit);
      $json_data = array(
        "draw"            => intval($request->input('draw')),  
        "recordsTotal"    => intval($totalData),  
        "recordsFiltered" => intval($totalFiltered), 
        "data"            => $finaldata,
      );
      return json_encode($json_data); 
    }


    public function checkRulesAcctoModuleAcc($rule){
      $rule = (int)$rule;
      if($rule==1){
        if(config('settings.orders')==1){
          return 'ok';
        }
      }else if($rule==2){
        if(config('settings.orders')==1){
          return 'ok';
        }
      }else if($rule==3){
        if(config('settings.collections')==1){
          return 'ok';
        }
      }else if($rule==4){
        if(config('settings.collections')==1){
          return 'ok';
        }
      }else if($rule==5){
        if(config('settings.visit_module')==1){
          return 'ok';
        }
      }else if($rule==6){
        if(config('settings.party')==1){
          return 'ok';
        }
      }else if($rule==7){
        if(config('settings.orders')==1 && config('settings.zero_orders')==1){
          return 'ok';
        }
      }
    }

}



 
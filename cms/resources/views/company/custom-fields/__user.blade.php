<div class="form-group">
    <label>{{$field->title}}
        @if($field->required == 1) <span>*</span> @endif
    </label>
    <select type="text" class="form-control select2" name="{{$field->slug}}"
            @if($field->required == 1) required @endif>
        <option value="">Please Select</option>
        @php

            $path = explode('/',request()->path());
            $juniors = \App\Employee::EmployeeChilds(Auth::user()->EmployeeId(), array());
        if(end($path) == 'create'){
            if (Auth::user()->employee->is_admin){
              $users= \App\Employee::where('status','Active')->where('company_id',config('settings.company_id'))->orderBy('name', 'ASC')->get(['id', 'name']);
            }else{
                $users= \App\Employee::where('status','Active')->where('company_id',config('settings.company_id'))->whereIn('id', $juniors)->orderBy('name', 'ASC')->get(['id', 'name']);
            }
        }else {
            $id = $form_data->{$field->slug};
            if (Auth::user()->employee->is_admin){
               $users= \App\Employee::withTrashed()->where(function ($query) use($id){
                   $query->where(function ($query){
                                $query->where('status','Active');
                                $query->where('deleted_at');
                            });
            $query->orWhere('id',$id);
        })->where('company_id',config('settings.company_id'))->orderBy('name', 'ASC')->get(['id', 'name']);
            }else{
                 $users= \App\Employee::withTrashed()->where(function ($query) use($id){
                    $query->where(function ($query){
                                        $query->where('status','Active');
                                        $query->where('deleted_at');
                                    });
            $query->orWhere('id',$id);
        })->where('company_id',config('settings.company_id'))->where(function ($query) use($juniors, $id){
           $query->whereIn('id', $juniors);
           $query->orWhere('id', $id);
        })->orderBy('name', 'ASC')->get(['id', 'name']);
            }
        }
        @endphp
        @foreach ($users as $user)
            <option value="{{$user->id}}"
                    @if(isset( $form_data->{$field->slug}) &&  $form_data->{$field->slug} == $user->id) selected @endif
            >{{$user->name}}</option>
        @endforeach
    </select>
</div>
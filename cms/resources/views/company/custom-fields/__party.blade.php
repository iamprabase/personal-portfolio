<div class="form-group">
    <label>{{$field->title}}
        @if($field->required == 1) <span>*</span> @endif
    </label>
    <select type="text" class="form-control select2" name="{{$field->slug}}"
            @if($field->required == 1) required @endif>
        <option value="">Please Select</option>
        @php
            $path = explode('/',request()->path());
            if(end($path) == 'create'){
            $clients = \Auth::user()->handleQuery('client')->where('status','Active')->get(['id','company_name']);
            }else {
                $id = $form_data->{$field->slug};

                $handles = \DB::table('handles')
            ->where('employee_id', auth()->user()->employeeId())
            ->where('handles.company_id', config('settings.company_id'))
            ->pluck('client_id')->toArray();
                if (isset($id)){
                    array_push($handles, $id);
                }

               $clients =  \App\Client::withTrashed()->where('company_id', config('settings.company_id'))->whereIn('id', array_unique($handles))
               ->where(function ($query) use($id){
                        $query->where('status','Active')->where('deleted_at');
                        $query->orWhere('id', $id);
                })->get(['id','company_name']);

        }
        @endphp
        @foreach ($clients as $client)
            <option value="{{$client->id}}"
                    @if(isset( $form_data->{$field->slug}) &&  $form_data->{$field->slug} == $client->id) selected @endif>
                {{$client->company_name}}
            </option>
        @endforeach
    </select>
</div>
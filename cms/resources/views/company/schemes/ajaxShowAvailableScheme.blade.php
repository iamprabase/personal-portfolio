@if(!empty($validated_scheme))
    @foreach($validated_scheme as $schemes)
        <div class="modal-body">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8">
                        <p>{{$schemes['scheme_type']->name}}</p>
                    </div>
                    <div class="col-md-4">
                        <label for="scheme-{{$schemes['scheme_type']->id}}">
                            <input type="checkbox" value="{{$schemes['scheme_type']->id}}"
                                   id="scheme-{{$schemes['scheme_type']->id}}"
                                   class="checkbox-inline"
                                   data-freeitem="{{isset($schemes['free_items']) ? $schemes['free_items'] : ''}}"
                                   data-discount="{{isset($schemes['discount']) ? $schemes['discount'] : ''}}"
                                   name="schemes"
                                   style="margin: -10px;"
                                   @if(isset($applied_schemes))
                                   @if(in_array($schemes['scheme_type']->id, $applied_schemes)) checked @endif
                                    @endif
                            >
                        </label>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@else

    <div class="modal-body">
        <div class="container-fluid">
            <p>No scheme Available</p>
        </div>
    </div>

@endif



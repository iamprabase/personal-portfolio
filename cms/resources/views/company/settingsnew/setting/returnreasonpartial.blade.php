<table id="returnreason" class="table table-bordered table-striped">
                
                    <thead>
                
                        <tr>
                
                            <th>S.No.</th>
                
                            <th>Name</th>
                
                            <th>Action</th>
                
                        </tr>
                
                    </thead>
                
                    <tbody>
                
                        @php($i = 0)
                
                        @forelse($returnreasons as $returnReason)
                
                        @php($i++)
                
                        <tr>
                
                            <td>{{ $i }}</td>
                
                            <td>{{ $returnReason->name}}</td>
                
                            <td>
                
                                <a class="btn btn-warning btn-sm rowEditReturnReason" returnreason-id="{{$returnReason->id}}"
                                    returnreason-name="{{$returnReason->name}}" style=" padding: 3px 6px;"><i class="fa fa-edit"></i></a>
                                @if(in_array($returnReason->id,$existingReturnReasons))
                                
                                @else
                                    <a class="btn btn-danger btn-sm delete rowDeleteReturnReason" returnreason-id="{{$returnReason->id}}"
                                    returnreason-name="{{$returnReason->name}}" style="padding: 3px 6px;"><i class="fa fa-trash-o"></i></a>
                                @endif
                            </td>
                
                        </tr>
                        @empty
                
                        @endforelse
                
                    </tbody>
                
                </table>
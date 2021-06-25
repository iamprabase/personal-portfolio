@extends('layouts.company')
@section('title', 'Customs Modules Create')
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-xs-12">

                @if (\session()->has('error'))
                    <div class="alert alert-error">
                        <p>{{ \Session::get('error') }}</p>
                    </div>
                    <br/>
                @endif
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Custom Module Form</h3>
                    </div>
                    <div class="box-body">
                        <div class="col-xs-12">
                            {!! Form::open(array('url' => url(domain_route("company.admin.custom.modules.store")), 'method' => 'post',
                            'files'=>true)) !!}

                            <div class="form-group col-xs-12">
                                <label for="name">Module Name</label>
                                <input type="text" name="name" class="form-control " id="name"
                                       placeholder="Name of Module" required autofocus value="{{old('name')}}">
                            </div>
                            {!! Form::submit('Submit', ['class' => 'btn btn-primary pull-right', 'id'=>'submitBtn']) !!}
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@extends('layouts.company')

@section('title', 'Settings')

@section('stylesheets')

    <link rel="stylesheet" href="{{asset('assets/dist/css/settings.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/bower_components/select2/dist/css/select2.min.css') }}">

    <style>
      .ordTabContent {
        border: 1px solid #f5f1f1!important;
        padding: 0px!important;
        border-radius: 4px;
        display: inline-block;
        width: 100%;
        background: #fff;
      }
        .headerTab {
            background-color: #0b7676 !important;
        }

           .layoutLogo {
            width: 275px;
        }
        .layoutfavicon {
            width: 50px;
        }

        .mb-20 {
            margin-bottom: 20px;
        }

        .checkbox label, .radio label {
            font-weight: normal !important;
        }


        .clickable{
            cursor: pointer;   
        }

        .panel-heading span {
          margin-top: -20px;
          font-size: 15px;
        }

        .panel-primary>.panel-heading {
            color: #fff;
            background-color: #499e9c;
            border-color: #499e9c;
        }

        .mt-40{
        margin-top:40px;
        }
        .margin-r-20 {
          margin-right: 20px;
        }  

        input[type=checkbox], input[type=radio]{
          cursor: pointer;
        }      
    </style>
@endsection

@section('content')
    <section class="content">
        <div class="row">
            @if (\Session::has('active'))
                @php $active = \Session::get('active') @endphp
            @else
                @php $active = 'profile' @endphp
            @endif

            @include('company.settingsnew.settingheader')
        </div>
        <div class="row">
            <div class="bs-example bs-example-tabs" data-example-id="togglable-tabs" style="margin-top:20px;">
                <div class="col-xs-3 right-pd">
                    <ul class="nav nav-tabs" id="myTabs" role="tablist">
                        <li role="presentation" class="{{($active == 'profile')? 'active':''}}">
                            <a href="#company" id="compamy" role="tab" data-toggle="tab" aria-controls="company"
                               aria-expanded="true">Profile</a>
                        </li>
                        <!-- <li role="presentation" class="{{($active == 'layout')? 'active':''}}">
                            <a href="#admin" role="tab" id="admin-tab" data-toggle="tab" aria-controls="admin"
                               aria-expanded="false">Admin Layout</a>
                        </li> -->
                        <li role="presentation" class="{{($active == 'other')? 'active':''}}">
                            <a href="#setup" role="tab" id="setup-tab" data-toggle="tab" aria-controls="setup"
                               aria-expanded="false">Setup</a>
                        </li>
                        @if(config('settings.orders') == 1 && Auth::user()->can('order-view'))
                            <li role="presentation" class="{{($active == 'ordersetup')? 'active':''}}">
                                <a href="#ordersetup" role="tab" id="ordersetup-tab" data-toggle="tab"
                                   aria-controls="ordersetup"
                                   aria-expanded="false">Order Setup</a>
                            </li>
                        @endif
                        @if(config('settings.visit_module') == 1 && Auth::user()->can('PartyVisit-view'))
                            <li role="presentation" class="{{($active == 'partyvisit')? 'active':''}}">
                                <a href="#partyvisit" role="tab" id="partyvisit-tab" data-toggle="tab"
                                   aria-controls="partyvisit"
                                   aria-expanded="false">Party Visit Setup</a>
                            </li>
                        @endif
                        @if(config('settings.odometer_report') == 1)
                        <li role="presentation" class="{{($active == 'odometer_reportsetup')? 'active':''}}">
                            <a href="#odometer_reportsetup" role="tab" id="odometer_reportsetup-tab" data-toggle="tab"
                               aria-controls="odometer_reportsetup" aria-expanded="false">Odometer Setup</a>
                        </li>
                        @endif
                        <li role="presentation" class="{{($active == 'plan')? 'active':''}}">
                            <a href="#plan-detail" role="tab" id="plan-detail-tab" data-toggle="tab"
                               aria-controls="plan-detail"
                               aria-expanded="false">Plan Detail</a>
                        </li>
                    </ul>

                </div>
                @php $clientSettings = getClientSetting(); @endphp
                @include('company.settingsnew._setuptabs')
            </div>
        </div>

        <div class="modal modal-default fade" id="updateTax" tabindex="-1" role="dialog" aria-labelledby="updateTax"
             data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title text-center" id="updateTax">Update Tax</h4>
                    </div>
                    <form method="post" class="update-modal"
                          action="{{domain_route('app.company.updateTax', [$clientSettings->id])}}">
                        {{csrf_field()}}
                        <div class="modal-body">
                            <input type="hidden" name="updateId" id="updateId" value="">
                            <div class="form-group">
                                <label for="">Tax Name</label>
                                <input class="form-control ed_tax_name" placeholder="Tax Name" id="ed_tax_name"
                                       name="tax_name" type="text"
                                       required>
                            </div>
                            <div class="form-group">
                                <label for="">Tax Percent</label>
                                <input class="form-control ed_tax_percent" placeholder="Tax Percent" id="ed_tax_percent"
                                       name="tax_percent"
                                       type="text" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-warning submit-update-btn" id="submit-update-btn">
                                Confirm
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal modal-default fade" id="deleteTax" tabindex="-1" role="dialog" aria-labelledby="deleteTax"
             data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title text-center" id="myModalLabel">Delete Confirmation</h4>
                    </div>
                    <div class="modal-body">
                        <p class="text-center">
                            Are you sure you want to delete this?
                        </p>
                        <input type="hidden" name="tax_id" id="deltax_id" value="">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning delete-button" id="delTax">Yes, Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('assets/bower_components/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/bower_components/bootstrap-timepicker/js/bootstrap-timepicker.js') }}"></script>
    <script src="{{ asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script>
        $('.select2').select2();
        const validatePositiveNumber = (el, minVal=100) => {
            if (/^-\d+$/.test(el.value)) el.value = 0;
            if(el.value < minVal) {
              el.value = minVal;
              alert(`Radius should not be less than ${minVal} m.`);
            }
        }


        $(document).on('change', '#default_currency', function () {
            var symbol = $('option:selected', this).attr('symbol');
            $('#currency_symbol').val(symbol);
        });

        /**
         * Setup Tab Updates
         */
        $('#btnSetupUpdate').on('click', function (e) {
            var time_zone = $('#time_zone').val();
            var dateFormat = $('#date_format_settings').val();
            var default_currency = $('#default_currency').val();
            var currency_symbol = $('#currency_symbol').val();
            @if(config('settings.livetracking')==1)
            var batterySetup = $("input[name='loc_accuracy']:checked").val();
            @else
            var batterySetup = "{{config('settings.loc_fetch_interval')}}";
            @endif
            @if(config('settings.party')==1)
            var allowDuplication = $("input[name='allow_party_duplication']:checked").val();
            @else
            var allowDuplication = "{{config('settings.allow_party_duplication')}}";
            @endif
            $(this).prop('disabled', true);
            updateClientSettingField({
                'time_zone': time_zone,
                'date_format': dateFormat,
                'default_currency': default_currency,
                'currency_symbol': currency_symbol,
                'loc_fetch_interval': batterySetup,
                'allow_party_duplication': allowDuplication
            })
                .then(updateResponse => {
                    if (updateResponse.status) {
                        alert('Updated Successfully');
                        $('#btnSetupUpdate').prop('disabled', false);
                        $('#btnSetupUpdate').val('Update');

                    } else {
                        alert('Sorry something went wrong');
                        $('#btnSetupUpdate').val('Update');
                        $('#btnSetupUpdate').prop('disabled', false);
                    }
                }).catch(error => {
                alert(error);
                $('#btnSetupUpdate').val('Update');
                $('#btnSetupUpdate').prop('disabled', false);
            });
        });


        $('.timepicker').timepicker({
            showInputs: false,
            step: 1,
        })

        const updateClientSettingField = (fields) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{domain_route('company.admin.settingnew.updateClientSettingTable')}}",
                    type: "POST",
                    data: {
                        fields: fields
                    },
                    success: function (data) {
                        resolve(data);
                    },
                    error: function (data) {
                        reject(data);
                    }
                });
            });
        };

        $(document).on('change', '#default_currency', function () {
            var symbol = $('option:selected', this).attr('symbol');
            $('#currency_symbol').val(symbol);
        });


        /**Order Setup Updates */
        function removeTaxAlert(tax_id) {
            $('#deleteTax').modal('show');
            $('#deltax_id').val(tax_id);
        }

        function removeTax(tax_id) {
            var csrf_token = "{{ csrf_token() }}";
            var tax_url = "{{domain_route('company.admin.settingnew.deleteTax')}}";
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: tax_url,
                data: {"tax_id": tax_id, "_token": csrf_token},
                beforeSend: function () {
                    $(document).find('#delTax').prop("disabled", true)
                },
                success: function (data) {
                    if (data.code === 200) {
                        $("#taxRow" + tax_id).remove();
                    }
                    alert(data.message);
                    $('#deleteTax').modal('hide');
                    $(document).find('#delTax').prop("disabled", false)
                }
            });
        }

        $('#btnTaxSetupUpdate').on('click', function (e) {
            
            let taxArray = new Array();
            if ($('#dynamic_field tbody>tr').length) {
                $('#dynamic_field tbody>tr').map((index, el) => {
                    let rowId = el.dataset.id;
                    let taxName = $(`#tax_name${rowId}`).val();
                    let taxPercent = $(`#tax_percent${rowId}`).val();
                    let defaultTax = $(`#defaultTax${rowId}`)[0].checked ? 1 : 0;
                    if (!taxName || !taxPercent){ el.remove()
                    }else {
                      let taxDetails = {
                          "company_id": "{{config('settings.company_id')}}",
                          "name": taxName,
                          "percent": taxPercent,
                          "default_flag": defaultTax
                      };
                      taxArray.push(taxDetails);

                    }

                })
                if(taxArray.length < 1){
                  return;
                }
            }else{
              return;
            }

            $(this).prop('disabled', true);
            updateClientSettingField({
                'taxArray': taxArray
            })
                .then(updateResponse => {
                    if (updateResponse.status) {
                        alert('Updated Successfully');
                        if ($('#dynamic_field tbody>tr').length) {
                            $('#dynamic_field tbody').html('');
                            updateResponse.taxes.map((el, index) => {
                                if (!($(`#taxRow${el.id}`).length)) {
                                    let taxName = el.tax_name;
                                    let taxPercent = el.tax_percent;
                                    let defaultTax = el.default_flag;
                                    let id = el.id;

                                    let checkboxHiddenField = `<input type="hidden" name="edit_tax_id[${id}]" value="${id}">`;
                                    let checkboxField = `<input type="checkbox" name="edit_defaultTax[${id}]" class="edit_defaultTax" id="edit_defaultTax${id}" data-id="${id}" ${defaultTax ? "checked" : ""} >`;

                                    $(`#taxRow${id}`).find('td').eq(2).html(`${checkboxHiddenField}${checkboxField}`)
                                    let edBtn = `<a id="updateTax_${id}" class="btn btn-warning btn-xs update-tax-btn" data-id="${id}" data-name="${taxName}" data-percent="${taxPercent}"><i class="fa fa-edit"></i></a>`;
                                    let delBtn = `<a id="removeTax_${id}" onclick="removeTaxAlert(${id})" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></a>`;

                                    $(`#showTaxes tbody`).append(`<tr id="taxRow${id}"><td>${taxName}</td><td>${taxPercent}</td><td>${checkboxHiddenField}${checkboxField}</td> <td>${edBtn}${delBtn}</td> </tr>`)
                                }

                            })

                        }
                        $('#btnTaxSetupUpdate').prop('disabled', false);
                        $('#btnTaxSetupUpdate').val('Update');
                        $('#addTaxesModal').modal('hide');

                    } else {
                        alert('Sorry something went wrong');
                        $('#btnTaxSetupUpdate').val('Update');
                        $('#btnTaxSetupUpdate').prop('disabled', false);
                    }
                }).catch(error => {
                alert(error);
                $('#btnTaxSetupUpdate').val('Update');
                $('#btnTaxSetupUpdate').prop('disabled', false);
            });
        });
        $('#btnOrderSetupUpdate').on('click', function (e) {
            @if($partyTypeLevel)
            var orderTo = $("input[name='order_to']:checked").val();
            @else
            var orderTo = "{{$clientSettings->order_to}}";
            @endif
            var orderAboveCreditLimit = $("input[name='order_above_credit_limit']:checked").val();
            var oderSign = $("input[name='order_with_authsign']:checked").val();
            var includeDispatchDetails = $("input[name='order_approval']:checked").val();
            var nonZeroDiscount = $("input[name='non_zero_discount']:checked").val();
            var prodLevelDiscount = $("input[name='product_level_discount']:checked").val();
            var prodLevelTax = $("input[name='product_level_tax']:checked").val();
            var autoOutStandingCalculation = $("input[name='outstanding_amt_calculation']:checked").val();
            @if(config('settings.ageing')==1 && Auth::user()->can('ageing-view'))
            var credit_days = $('#creditDays').val();
            @else
            var credit_days = "{{config('settings.credit_days')}}";
            @endif
            var order_prefix = $('#order_prefix').val();
            var enable_salesman_to_party_radius = $("input[name='enable_salesman_to_party_radius']")[0].checked ? 1 :0;
            var salesman_to_party_radius = $('#salesman_to_party_radius').val();
            

            $(this).prop('disabled', true);
            updateClientSettingField({
                'order_to': orderTo,
                'order_above_credit_limit': orderAboveCreditLimit,
                'order_with_authsign': oderSign,
                'order_approval': includeDispatchDetails,
                'non_zero_discount': nonZeroDiscount,
                'product_level_discount': prodLevelDiscount,
                'product_level_tax': prodLevelTax,
                'outstanding_amt_calculation': autoOutStandingCalculation,
                'credit_days': credit_days,
                'order_prefix': order_prefix,
                'enable_salesman_to_party_radius': enable_salesman_to_party_radius,
                'salesman_to_party_radius': salesman_to_party_radius,
            })
                .then(updateResponse => {
                    if (updateResponse.status) {
                        alert('Updated Successfully');
                       
                        $('#btnOrderSetupUpdate').prop('disabled', false);
                        $('#btnOrderSetupUpdate').val('Update');

                    } else {
                        alert('Sorry something went wrong');
                        $('#btnOrderSetupUpdate').val('Update');
                        $('#btnOrderSetupUpdate').prop('disabled', false);
                    }
                }).catch(error => {
                alert(error);
                $('#btnOrderSetupUpdate').val('Update');
                $('#btnOrderSetupUpdate').prop('disabled', false);
            });
        });
        var i = 0;
        $('#add').click(function () {
            i++;
            $('#dynamic_field tbody').append('<tr id="row' + i + '" data-id="' + i + '"><td><input type="text" required name="tax_name[' + i + ']" id="tax_name' + i + '" class="form-control"></td><td><input type="text" name="tax_percent[' + i + ']" id="tax_percent' + i + '" class="form-control" required></td><td><input type="checkbox" name="defaultTax[' + i + ']" class="defaultTax" id="defaultTax' + i + '" data-id=' + i + '/></td><td><button type="button" name="remove" id="' + i + '" class="btn btn-danger btn_remove">X</button></td></tr>');
            $("#tax_percent" + i).keyup(function (e) {
                if ($("#tax_percent" + i).val() > 100) $("#tax_percent" + i).val(0)
            })
            $("#tax_percent" + i).keydown(function (e) {
                // Allow: backspace, delete, tab, escape, enter and .
                if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
                    // Allow: Ctrl+A, Command+A
                    (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                    // Allow: home, end, left, right, down, up
                    (e.keyCode >= 35 && e.keyCode <= 40)) {
                    // let it happen, don't do anything
                    return;
                }
                // Ensure that it is a number and stop the keypress
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57 && e.keyCode != 190 && e.keyCode != 110)) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                }

            });
        });
        $(document).on('click', '.btn_remove', function () {
            var button_id = $(this).attr("id");
            $('#row' + button_id + '').remove();
        });
        $('#delTax').click(function () {
            removeTax($('#deltax_id').val());
        });
        $('#showTaxes').on("click", '.update-tax-btn', function () {
            let currentEl = $(this);
            let taxId = currentEl.data("id");
            let tax_name = currentEl.data("name");
            let tax_percent = currentEl.data("percent");
            let modal = $('#updateTax');
            modal.modal('show');
            modal.find('#ed_tax_name').val(tax_name);
            modal.find('#ed_tax_percent').val(tax_percent);
            modal.find('#updateId').val(taxId);
        });
        $(document).find('#submit-update-btn').click(function (e) {
            e.preventDefault();
            let modal = $('#updateTax');
            let taxId = modal.find('#updateId').val();
            let tax_name = modal.find('#ed_tax_name').val();
            let tax_percent = modal.find('#ed_tax_percent').val();
            let url = modal.find('.update-modal')[0].action;
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{domain_route('company.admin.settingnew.updateTax', [$clientSettings->id])}}",
                type: "POST",
                "data": {"taxId": taxId, "tax_name": tax_name, "tax_percent": tax_percent},
                beforeSend: function () {
                    $('.btn').prop('disabled', true);
                },
                success: function (data) {
                    alert(data.message);
                    $('.btn').prop('disabled', false);
                    modal.modal('hide');
                    let currentInstance = data.instance;
                    $(`#taxRow${currentInstance.id}`).find('td').eq(0).text(currentInstance.name)
                    $(`#taxRow${currentInstance.id}`).find('td').eq(1).text(currentInstance.percent)
                    let checkboxHiddenField = `<input type="hidden" name="edit_tax_id[${currentInstance.id}]" value="${currentInstance.id}">`;
                    let checkboxField = `<input type="checkbox" name="edit_defaultTax[${currentInstance.id}]" class="edit_defaultTax" id="edit_defaultTax${currentInstance.id}" data-id="${currentInstance.id}" ${currentInstance.default_flag ? "checked" : ""} >`;

                    $(`#taxRow${currentInstance.id}`).find('td').eq(2).html(`${checkboxHiddenField}${checkboxField}`)
                    let edBtn = `<a id="updateTax_${currentInstance.id}" class="btn btn-warning btn-xs update-tax-btn" data-id="${currentInstance.id}" data-name="${currentInstance.name}" data-percent="${currentInstance.percent}"><i class="fa fa-edit"></i></a>`;
                    let delBtn = `<a id="removeTax_${currentInstance.id}" onclick="removeTaxAlert(${currentInstance.id})" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></a>`;

                    $(`#taxRow${currentInstance.id}`).find('td').eq(3).html(`${edBtn}${delBtn}`)
                },
                error: function (xhr, responseJSON) {
                    $('.btn').prop('disabled', false);
                }
            });
        });
        $('#showTaxes').on("change", '.edit_defaultTax', function () {
            let currentEl = $(this);
            let taxId = currentEl.data("id");
            let allEl = $('.edit_defaultTax');
            let flagVal = 0;
            let checkedCounter = 0;
            if (this.checked) {
                flagVal = 1;
            } else {
                $('.edit_defaultTax').each(function () {
                    if (this.checked)
                        checkedCounter += 1;
                });
                if (checkedCounter < 1) {
                    alert("A tax type must at least be selected.");
                    currentEl.prop("checked", true);
                    return false;
                }
            }
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{domain_route('company.admin.settingnew.updateDefaultFlag', [$clientSettings->id])}}",
                type: "POST",
                "data": {flagVal: flagVal, taxId: taxId},
                beforeSend: function () {
                    $('.btn').prop('disabled', true);
                },
                success: function (data) {
                    alert(data.message);
                    $('.btn').prop('disabled', false);
                },
                error: function (xhr, responseJSON) {
                    $('.btn').prop('disabled', false);
                }
            });
        });
        /**Party Visit Setup */
        $('input[name="enable_auto_finish_visit_radius"]').change(function(){
          if(this.checked){
            if($('input[name="enable_max_visit_radius_with_client"]')[0].checked){
              return;
            }
            alert("Please allow marking visit option first");
            $('input[name="enable_auto_finish_visit_radius"]').prop("checked",false)
          }
        })
        $('input[name="enable_max_visit_radius_with_client"]').change(function(){
          if(!this.checked){
            if(!$('input[name="enable_auto_finish_visit_radius"]')[0].checked){
              return;
            }
            alert("Please unmark auto complete visit option first");
            $('input[name="enable_max_visit_radius_with_client"]').prop("checked","checked")
          }
        });
        $('#btnpartyVisitSetupUpdate').on('click', function () {
            var allowPhonePicture = $("input[name='allow_phonepicture_in_visit']:checked").val();
            var max_visit_radius_with_client = $('#max_visit_radius_with_client').val();
            var auto_finish_visit_radius = $('#auto_finish_visit_radius').val();
            var enable_max_visit_radius_with_client = $("input[name='enable_max_visit_radius_with_client']")[0].checked ? 1 :0;
            var enable_auto_finish_visit_radius = $("input[name='enable_auto_finish_visit_radius']")[0].checked ? 1 :0;
            if(enable_auto_finish_visit_radius && enable_max_visit_radius_with_client){
              if(auto_finish_visit_radius - max_visit_radius_with_client < 1){
                alert("Auto-complete radius must be at least 1 meter greater than visit radius.");
                $('input[name="enable_max_visit_radius_with_client"]').focus();
                return;
              }
            }
            $(this).prop('disabled', true);
            updateClientSettingField({
                "allow_phonepicture_in_visit": allowPhonePicture,
                "max_visit_radius_with_client": max_visit_radius_with_client,
                "auto_finish_visit_radius": auto_finish_visit_radius,
                "enable_max_visit_radius_with_client":enable_max_visit_radius_with_client,
                "enable_auto_finish_visit_radius":enable_auto_finish_visit_radius,
            })
                .then(updateResponse => {
                    if (updateResponse.status) {
                        alert('Updated Successfully');
                        $('#btnpartyVisitSetupUpdate').prop('disabled', false);
                        $('#btnpartyVisitSetupUpdate').val('Update');

                    } else {
                        alert('Sorry something went wrong');
                        $('#btnpartyVisitSetupUpdate').val('Update');
                        $('#btnpartyVisitSetupUpdate').prop('disabled', false);
                    }
                }).catch(error => {
                alert(error);
                $('#btnpartyVisitSetupUpdate').val('Update');
                $('#btnpartyVisitSetupUpdate').prop('disabled', false);
            });
        });


        var oldOdometerValue = $('#odometer_rate').val();
        var oldOdometerUnit = $("input[name='odometer_distance_unit']:checked").val();


        /**Odometer setup */
        $('#btnOdometerSetupUpdate').on('click', function () {

            var odometerRate = $('#odometer_rate').val();
            if (odometerRate === '') {
                alert('Rate is required');
                return;
            }
            if (parseFloat(odometerRate) < 0) {
                alert('Rate cannot be negative value');
                return;
            }
            var OdometerDistance = $("input[name='odometer_distance_unit']:checked").val();

            var clickedYes = confirm(" Only new entries done henceforth would be calculated based on the new rate. Old reimbursal rate would remain the same.");

            if (!clickedYes) {
                $('#odometer_rate').val(oldOdometerValue);
                $("input[name='odometer_distance_unit']").val([oldOdometerUnit]);
                return;
            }
            $(this).prop('disabled', true);
            updateClientSettingField({
                "odometer_rate": odometerRate,
                "odometer_distance_unit": OdometerDistance,
            })
                .then(updateResponse => {
                    if (updateResponse.status) {
                        alert('Updated Successfully');
                        $('#btnOdometerSetupUpdate').prop('disabled', false);
                        $('#btnOdometerSetupUpdate').val('Update');
                        oldOdometerValue = $('#odometer_rate').val();
                        oldOdometerUnit = $("input[name='odometer_distance_unit']:checked").val();

                    } else {
                        alert('Sorry something went wrong');
                        $('#odometer_rate').val(oldOdometerValue);
                        $("input[name='odometer_distance_unit']").val([oldOdometerUnit]);
                        $('#btnOdometerSetupUpdate').val('Update');
                        $('#btnOdometerSetupUpdate').prop('disabled', false);
                    }
                }).catch(error => {
                alert(error);
                $('#odometer_rate').val(oldOdometerValue);
                $("input[name='odometer_distance_unit']").val([oldOdometerUnit]);
                $('#btnOdometerSetupUpdate').val('Update');
                $('#btnOdometerSetupUpdate').prop('disabled', false);
            });
        });


        $(function () {
            $('#odometer_rate').keyup(function () {
                if ($(this).val().indexOf('.') !== -1) {
                    if ($(this).val().split(".")[1].length > 2) {
                        if (isNaN(parseFloat(this.value))) return;
                        this.value = parseFloat(this.value).toFixed(2);
                    }
                }
                return this; //for chaining
            });
        });

        $('#odometer_rate').on('change', function () {
            $(this).val(parseFloat($(this).val()).toString());
        });

        function readURL(input, el, maxSize, currentTarget) {
            if (input.files && input.files[0]) {
                if (input.files[0].size / 1024 > maxSize) {
                    alert(`Maximum allowed size is ${maxSize} KB`)

                    var field = document.createElement("INPUT");
                    field.classList.add(input.classList.value);
                    field.setAttribute("type", "file");
                    field.setAttribute("name", input.name);
                    field.setAttribute("accept", "image/*");
                    input.parentElement.append(field);
                    input.remove();
                    return;
                }
                var reader = new FileReader();
                reader.onload = function (e) {
                    el.attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);

            }
        }

        $(".tab-content").on("change", ".imgInp", function (e) {
            readURL(this, $('#logo-upload'), 400, $(this));
        });
        $(".tab-content").on("change", ".imgInpSmall", function (e) {
            readURL(this, $('#small-logo'), 250, $(this));
        });
        $(".tab-content").on("change", ".imgInpFavicon", function (e) {
            readURL(this, $('#favicon'), 50, $(this));
        });

        $('select[name="country"]').on('change', function () {
            var countryId = $(this).val();
            if (countryId) {
                $.ajax({
                    url: '/get-state-list?country_id=' + countryId,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $("#state").empty();
                        $('#city').empty();
                        $("#city").append('<option value>Select a City</option>');
                        $("#state").append('<option value>Select a State</option>');
                        $.each(data, function (key, value) {
                            $("#state").append('<option value="' + key + '">' + value + '</option>');
                        });
                    }
                });
            } else {
                $('#state').empty();
                $('#city').empty();
            }
        });

        $('select[name="state"]').on('change', function () {
            var stateId = $(this).val();
            if (stateId) {
                $.ajax({
                    url: '/get-city-list?state_id=' + stateId,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $("#city").empty();
                        $("#city").append('<option value>Select a City</option>');
                        $.each(data, function (key, value) {
                            $("#city").append('<option value="' + key + '">' + value + '</option>');
                        });
                    }
                });
            } else {
                $('#city').empty();
            }
        });

        $(document).on('click', '.panel-heading span.clickable', function(e){
            var $this = $(this);
          if(!$this.hasClass('panel-collapsed')) {
            $this.parents('.panel').find('.panel-body').slideUp();
            $this.addClass('panel-collapsed');
            $this.find('i').removeClass('fa-arrow-up').addClass('fa-arrow-down');
          } else {
            $this.parents('.panel').find('.panel-body').slideDown();
            $this.removeClass('panel-collapsed');
            $this.find('i').removeClass('fa-arrow-down').addClass('fa-arrow-up');
          }
        })
    </script>
@endsection
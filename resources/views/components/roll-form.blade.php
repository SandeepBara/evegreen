<!-- Modal Form -->
 
<div class="modal fade modal-lg" id="rollModal" tabindex="-1" aria-labelledby="rollModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rollModalLabel">Add New Vendor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="rollForm">
                    @csrf
                    <!-- Hidden field for roll ID -->
                    <input type="hidden" id="id" name="id" value="">

                    <div class="row">
                        <!-- Roll Name -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="rollNo">Roll No.<span class="text-danger">*</span></label>
                                <input type="text" maxlength="100" id="rollNo" name="rollNo" class="form-control" placeholder="Enter Roll No." required>
                                <span class="error-text" id="rollNo-error"></span>
                            </div>
                        </div>
                        
                        <!-- Vendor Name -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="vendorId">Vender Name <span class="text-danger">*</span></label>
                                <select name="vendorId" id="vendorId" class="form-control" required>
                                    <option value="">Select</option>
                                    @foreach ($vendorList as $val)
                                        <option value="{{ $val->id }}">{{ $val->vendor_name }}</option>
                                    @endforeach
                                </select>
                                <span class="error-text" id="vendorId-error"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <!-- Vendor Email -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="purchaseDate">Purchase Date<span class="text-danger">*</span></label>
                                <input type="date" max="{{date('Y-m-d')}}" value="{{date('Y-m-d')}}" id="purchaseDate" name="purchaseDate" class="form-control" required />
                                <span class="error-text" id="purchaseDate-error"></span>
                            </div>
                        </div>
                        
                        <!-- Vendor Mobile Number -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="rollSize">Roll Size<span class="text-danger">*</span></label>
                                <input type="text" id="rollSize" name="rollSize" class="form-control" placeholder="Roll Size" required onkeypress="return isNumDot(event);">
                                <span class="error-text" id="rollSize-error"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <!-- Vendor Address -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="rollGsm">Roll GSM<span class="text-danger">*</span></label>
                                <input id="rollGsm" name="rollGsm" class="form-control" placeholder="Roll GSM" required onkeypress="return isNumDot(event);"/>
                                <span class="error-text" id="rollGsm-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="rollColor">Roll Color<span class="text-danger">*</span></label>
                                <input id="rollColor" name="rollColor" class="form-control" placeholder="Roll Color" required />
                                <span class="error-text" id="rollColor-error"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <!-- Vendor Address -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="rollLength">Roll Length <span style="font-size:small;color:aquamarine">(In Meter)</span><span class="text-danger">*</span></label>
                                <input id="rollLength" name="rollLength" class="form-control" placeholder="Roll Length" required onkeypress="return isNumDot(event);"/>
                                <span class="error-text" id="rollLength-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="netWeight">Net Weight <span style="font-size:small;color:aquamarine">(In Kg)</span><span class="text-danger">*</span></label>
                                <input id="netWeight" name="netWeight" class="form-control" placeholder="Roll Net Weight" required onkeypress="return isNumDot(event);"/>
                                <span class="error-text" id="netWeight-error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <!-- Vendor Address -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="grossWeight">Gross Weight <span style="font-size:small;color:aquamarine">(In Meter)</span><span class="text-danger">*</span></label>
                                <input id="grossWeight" name="grossWeight" class="form-control" placeholder="Roll Gross Weight" required onkeypress="return isNumDot(event);"/>
                                <span class="error-text" id="grossWeight-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="forClientId">
                                    Book For Client 
                                    <span onclick="openClineModel()"  style="font-weight: bolder; font-size:small; text-decoration: underline;"> 
                                        <i class="bi bi-person-add"></i> Add Client
                                    </span>
                                </label>
                                <select name="forClientId" id="forClientId" class="form-control" onchange="openCloseClientMode()">
                                    <option value="" >Select</option>
                                    @foreach ($clientList as $val)
                                        <option value="{{ $val->id }}">{{ $val->client_name }}</option>
                                    @endforeach
                                </select>
                                <span class="error-text" id="forClientId-error"></span>
                            </div>
                        </div>                        
                    </div>
                    <div  client="client">
                        <div class="row mt-3">
                            <!-- Vendor Address -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="estimatedDespatchDate">Dispatch Date</label>
                                    <input type="date" min="{{date('Y-m-d')}}" name="estimatedDespatchDate" id="estimatedDespatchDate" class="form-control" required/>                                  
                                    <span class="error-text" id="estimatedDespatchDate-error"></span>
                                </div>
                            </div> 
                             
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="bagUnits">Bag Unit</label>
                                    <select name="bagUnits" id="bagUnits" class="form-control">
                                        <option value="">Select</option>
                                        <option value="Kg">Kg</option>
                                        <option value="Pice">Pice</option>
                                    </select>                                    
                                    <span class="error-text" id="bagUnits-error"></span>
                                </div>
                            </div> 
                            
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="bagTypeId">Bag Type </label>
                                    <select name="bagTypeId" id="bagTypeId" class="form-control">
                                        <option value="">Select</option>
                                        @foreach ($bagType as $val)
                                            <option value="{{ $val->id }}">{{ $val->bag_type }}</option>
                                        @endforeach
                                    </select>                                                                       
                                    <span class="error-text" id="bagTypeId-error"></span>
                                </div>
                            </div>                        
                        </div>
                        <div class="row mt-3">
                            <!-- Vendor Address -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="printingColor">Printing Color</label>
                                    <div class="col-md-12 form-control">
                                    <select name="printingColor[]" id="printingColor" class="form-control select2" multiple="multiple"> 
                                        <option>Select</option>                                     
                                        
                                        <!-- Red HTML Color Names -->
                                        <option data-color="#CD5C5C" value="IndianRed" style="background-color: #CD5C5C;">IndianRed</option>
                                        <option data-color="#F08080" value="LightCoral" style="background-color: #F08080;">LightCoral</option>
                                        <option data-color="#FA8072" value="Salmon" style="background-color: #FA8072;">Salmon </option>
                                        <option data-color="#E9967A" value="DarkSalmon" style="background-color: #E9967A;">DarkSalmon </option>
                                        <option data-color="#FFA07A" value="LightSalmon" style="background-color: #FFA07A;">LightSalmon </option>
                                        <option data-color="#DC143C" value="LightSalmon" style="background-color: #DC143C;">Crimson </option>
                                        <option data-color="#FF0000" value="Red" style="background-color: #FF0000;">Red </option>
                                        <option data-color="#B22222" value="FireBrick" style="background-color: #B22222;">FireBrick </option>
                                        <option data-color="#8B0000" value="DarkRed" style="background-color: #8B0000;">DarkRed </option>
                                    </select>
                                    </div>                                                                                                         
                                    <span class="error-text" id="printingColor-error"></span>

                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="printingDescription">Printing Description</label>
                                    <textarea id="printingDescription" name="printingDescription" class="form-control" placeholder="Printing Description" ></textarea>                                                                                                                                            
                                    <span class="error-text" id="printingDescription-error"></span>
                                </div>
                            </div>                      
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="row mt-4">
                        <div class="col-sm-12 text-end">
                            <button type="submit" class="btn btn-success">Add</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<x-client-form/>
<script>
    $(document).ready(function(){
        $('.select2').select2();
        $('#printingColor').select2({
            placeholder: "Select tags",
            allowClear: true,
            dropdownParent: $('#rollModal'),
            templateResult: formatOption,
            templateSelection: formatOption 
        });
        openCloseClientMode();
        $('#clientModal').on('hidden.bs.modal', function() {
            $('#rollModal').css("z-index","");
        });

        $("#clientForm").validate({
            rules: {
                clientName: {
                    required: true,
                    minlength: 3
                },

                clientMobileNo: {
                    required: true,
                    number: true,
                    minlength:10,
                    minlength:10
                },
                clientAddress: {
                    required: true,
                },
            },
            submitHandler: function(form) {
                // If form is valid, prevent default form submission and submit via AJAX
                addClint();
            }
        });

    });

    function openClineModel(){
        $('#rollModal').css("z-index",0);
        $('#clientModal').css("z-index",1060);
        $('#clientModal').modal('show');
    }

    function addClint(){
        $.ajax({
                type: "POST",
                'url':"{{route('client.add')}}",            
                                
                "deferRender": true,
                "dataType": "json",
                'data': $("#clientForm").serialize(),
                beforeSend: function () {
                    $("#loadingDiv").show();
                },
                success: function (data) {
                    $("#loadingDiv").hide();
                    console.log(data);
                    if(data.status){
                        $("#clientForm").get(0).reset();
                        $("#clientModal").modal('hide');
                        var newOptionValue = data?.data?.client?.id;
                        var clientName = data?.data?.client?.client_name;
                        if (newOptionValue !== "") {
                            // Check if the option already exists
                            if ($('#forClientId option[value="' + newOptionValue + '"]').length === 0) {
                                // Add the new option to the select list
                                $('#forClientId').append('<option value="' + newOptionValue + '">' + clientName + '</option>');                                
                            } 
                            if ($('#bookingForClientId option[value="' + newOptionValue + '"]').length === 0) {
                                // Add the new option to the select list
                                $('#bookingForClientId').append('<option value="' + newOptionValue + '">' + clientName + '</option>');                                
                            } 
                        } 
                        modelInfo(data.messages);
                    }else{
                        modelInfo("Something Went Wrong!!");
                    }
                },
            }

        ) 
    } 

    function openCloseClientMode(){
        forClientId = $("#forClientId").val();
        if(forClientId!=""){
            $("div[client='client']").show();
        }
        else{
            $("div[client='client']").hide();
        }
    }

    function formatOption(option) {
        if (!option.id) {
            return option.text; // return default option text if no ID
        }
        var color = $(option.element).data('color');
        return $('<span style="background-color: ' + color + '; padding: 3px 10px; color: white; border-radius: 3px;">' + option.text + '</span>');
    }
</script>
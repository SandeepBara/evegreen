<!-- Modal Form -->
 
<div class="modal fade modal-lg" id="rollBookingModal" tabindex="-1" aria-labelledby="rollBookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rollBookingModalLabel">Booking Roll</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="rollBookingForm">
                    @csrf
                    <!-- Hidden field for roll ID -->
                    <input type="hidden" id="rollId" name="rollId" value="">

                    <div class="row">
                        
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="bookingForClientId">Book For Client 
                                    <span onclick="openRollBookingClineModel()"  style="font-weight: bolder; font-size:small; text-decoration: underline;"> 
                                        <i class="bi bi-person-add"></i> Add Client
                                    </span> 
                                </label>
                                <select name="bookingForClientId" id="bookingForClientId" class="form-control" onchange="openCloseClientMode()">
                                    <option value="" >Select</option>
                                    @foreach ($clientList as $val)
                                        <option value="{{ $val->id }}">{{ $val->client_name }}</option>
                                    @endforeach
                                </select>
                                <span class="error-text" id="bookingForClientId-error"></span>
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="bookingEstimatedDespatchDate">Dispatch Date</label>
                                <input type="date" min="{{date('Y-m-d')}}" name="bookingEstimatedDespatchDate" id="bookingEstimatedDespatchDate" class="form-control" required/>                                  
                                <span class="error-text" id="bookingEstimatedDespatchDate-error"></span>
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="bookingBagUnits">Bag Unit</label>
                                <select name="bookingBagUnits" id="bookingBagUnits" class="form-control">
                                    <option value="">Select</option>
                                    <option value="Kg">Kg</option>
                                    <option value="Pice">Pice</option>
                                </select>                                    
                                <span class="error-text" id="bookingBagUnits-error"></span>
                            </div>
                        </div> 
                        <div class="col-sm-6">
                                
                            <div class="form-group">
                                <label class="control-label" for="bookingBagTypeId">Bag Type </label>
                                <select name="bookingBagTypeId" id="bookingBagTypeId" class="form-control">
                                    <option value="">Select</option>
                                    @foreach ($bagType as $val)
                                        <option value="{{ $val->id }}">{{ $val->bag_type }}</option>
                                    @endforeach
                                </select>                                                                       
                                <span class="error-text" id="bookingBagTypeId-error"></span>
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="bookingPrintingColor">Printing Color</label>
                                <div class="col-md-12 form-control">
                                <select name="bookingPrintingColor[]" id="bookingPrintingColor" class="form-control select22" multiple="multiple" required> 
                                    <option value="">Select</option>                                     
                                    
                                    <!-- Red HTML Color Names -->
                                    <option data-color="#CD5C5C" value="IndianRed" style="background-color: #CD5C5C;">IndianRed</option>
                                    <option data-color="#F08080" value="LightCoral" style="background-color: #F08080;">LightCoral</option>
                                    <option data-color="#FA8072" value="Salmon" style="background-color: #FA8072;">Salmon </option>

                                    <option data-color="#CD5C5C" value="IndianRed" style="background-color: #CD5C5C;">IndianRed</option>
                                    <option data-color="#F08080" value="LightCoral" style="background-color: #F08080;">LightCoral</option>
                                    <option data-color="#FA8072" value="Salmon" style="background-color: #FA8072;">Salmon </option>

                                    <option data-color="#CD5C5C" value="IndianRed" style="background-color: #CD5C5C;">IndianRed</option>
                                    <option data-color="#F08080" value="LightCoral" style="background-color: #F08080;">LightCoral</option>
                                    <option data-color="#FA8072" value="Salmon" style="background-color: #FA8072;">Salmon </option>
                                    <option data-color="#CD5C5C" value="IndianRed" style="background-color: #CD5C5C;">IndianRed</option>
                                    <option data-color="#F08080" value="LightCoral" style="background-color: #F08080;">LightCoral</option>
                                    <option data-color="#FA8072" value="Salmon" style="background-color: #FA8072;">Salmon </option>

                                    <option data-color="#CD5C5C" value="IndianRed" style="background-color: #CD5C5C;">IndianRed</option>
                                    <option data-color="#F08080" value="LightCoral" style="background-color: #F08080;">LightCoral</option>
                                    <option data-color="#FA8072" value="Salmon" style="background-color: #FA8072;">Salmon </option>

                                    <option data-color="#CD5C5C" value="IndianRed" style="background-color: #CD5C5C;">IndianRed</option>
                                    <option data-color="#F08080" value="LightCoral" style="background-color: #F08080;">LightCoral</option>
                                    <option data-color="#FA8072" value="Salmon" style="background-color: #FA8072;">Salmon </option>
                                    
                                </select>
                                </div>                                                                                                         
                                <span class="error-text" id="bookingPrintingColor-error"></span>

                            </div>
                        </div>
                        <div class="col-sm-6">
                                
                            <div class="form-group">
                                <label class="control-label" for="bookingPrintingDescription">Printing Description</label>
                                <textarea id="bookingPrintingDescription" name="bookingPrintingDescription" class="form-control" placeholder="Printing Description" ></textarea>                                                                                                                                            
                                <span class="error-text" id="bookingPrintingDescription-error"></span>
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
        $('.select22').select2();
        $('#bookingPrintingColor').select2({
            placeholder: "Select tags",
            allowClear: true,
            dropdownParent: $('#rollBookingModal'),
            templateResult: formatOption,
            templateSelection: formatOption 
        });

        $('#clientModal').on('hidden.bs.modal', function() {
            $('#rollBookingModal').css("z-index","");
        });

    });
    function openRollBookingClineModel(){
        $('#rollBookingModal').css("z-index",0);
        $('#clientModal').css("z-index",1060);
        $('#clientModal').modal('show');
    }
    

    $('#bookingForClientId').on('change', function() {
        var selectedValue = $(this).val();
        if (selectedValue === '') {
            $('#rollBookingModal').css("z-index", 0);
            $('#clientModal').css("z-index", 1060);
            $('#clientModal').modal('show'); // Open modal when "Add" option is selected
        }
    });

    function formatOption(option) {
        if (!option.id) {
            return option.text; // return default option text if no ID
        }
        var color = $(option.element).data('color');
        return $('<span style="background-color: ' + color + '; padding: 3px 10px; color: white; border-radius: 3px; z-index:40000">' + option.text + '</span>');
    }
</script>
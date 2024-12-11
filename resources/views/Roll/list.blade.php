@include("layout.header")
<!-- Main Component -->

<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Roll</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">List</li>
                </ol>
            </nav>

        </div>
    </div>
    <div class="container">
        <div class="panel-heading">
            <h5 class="panel-title">Roll List</h5>
            <div class="panel-control">
                <button id="addRoll" type="button" class="btn btn-primary fa fa-arrow-right" data-bs-toggle="modal" data-bs-target="#rollModal">
                    Add <ion-icon name="add-circle-outline"></ion-icon>
                </button>
                <button id="addRollImport" type="button" class="btn btn-primary fa fa-arrow-right" data-bs-toggle="modal" data-bs-target="#fileImportModal">
                    Add Roll Import Excel <ion-icon name="add-circle-outline"></ion-icon>
                </button>
            </div>
        </div>
        @if($flag && $flag=='history')
        <div class="panel-body">
            <form id="searchForm">
                <div class="row g-3">
                    <!-- From Date -->
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label" for="fromDate">From Date</label>
                            <input type="date" name="fromDate" id="fromDate" class="form-control" max="{{date('Y-m-d')}}" />
                        </div>
                    </div>

                    <!-- Upto Date -->
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label" for="uptoDate">Upto Date</label>
                            <input type="date" name="uptoDate" id="uptoDate" class="form-control" max="{{date('Y-m-d')}}" />
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-sm-3">
                        <!-- Search Button -->
                        <input type="button" id="btn_search" class="btn btn-primary w-100" onclick="searchData()" value="Search"/>
                    </div>
                </div>
            </form>

        </div>
        @endif
        <div class="panel-body">
            <table id="postsTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Roll No</th>
                        <th>Purchase Date</th>
                        <th>Vendor Name</th>
                        <th>Roll Size</th>
                        <th>GSM</th>
                        <th>Roll Color</th>
                        <th>Roll Length</th>
                        <th>Net Weight</th>
                        <th>Gross Weight</th>
                        <th>Book For</th>
                        <th>Delivery Date</th>
                        <th>Printing Color</th>
                        <th>Bag Type</th>
                        <th>Bag Unit</th>
                        <th>Printing Date</th>
                        <th>Printing Schedule Date</th>
                        <th>Weight After Printing</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <x-roll-form />
    <x-import-file />
    <x-roll-booking />
    <x-printing-schedule-form />
    <x-printing-update-form />
</main>
<script>
    const rules = {
        rollNo: {
            required: true,
        },
        vendorId: {
            required: true,
        },
        purchaseDate: {
            required: true,
        },
        rollSize: {
            required: true,
        },
        rollGsm: {
            required: true,
        },
        rollColor: {
            required: true,
        },
        netWeight: {
            required: true,
        },
        grossWeight: {
            required: true,
        },
        estimatedDespatchDate:{
            required: (element) => {
                return $("#forClientId").val() != "";
            },
        },
        bagUnits: {
            required: (element) => {
                return $("#forClientId").val() != "";
            },
        },
        bagTypeId: {
            required: (element) => {
                return $("#forClientId").val() != "";
            },
        },
        "printingColor[]": {
            required: (element) => {
                return $("#forClientId").val() != "";
            },
        },
    };

    var flag = window.location.pathname.split('/').pop();
    $(document).ready(function() {
        if (flag != "stoke") {
            $("#addRoll").hide();
            $("#addRollImport").hide();
        }
        const table = $('#postsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{route('roll.list',':flag')}}".replace(':flag', flag), // The route where you're getting data from
                data: function(d) {

                    // Add custom form data to the AJAX request
                    var formData = $("#searchForm").serializeArray();
                    $.each(formData, function(i, field) {
                        d[field.name] = field.value; // Corrected: use d[field.name] instead of d.field.name
                    });

                },
                beforeSend: function() {
                    $("#btn_search").val("LOADING ...");
                    $("#loadingDiv").show();
                },
                complete: function() {
                    $("#btn_search").val("SEARCH");
                    $("#loadingDiv").hide();
                },
            },

            columns: [{
                    data: "DT_RowIndex",
                    name: "DT_RowIndex",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "roll_no",
                    name: "roll_no"
                },
                {
                    data: "purchase_date",
                    name: "purchase_date"
                },
                {
                    data: "vendor_name",
                    name: "vendor_name",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "roll_size",
                    data: "roll_size",
                },
                {
                    data: "roll_gsm",
                    name: "roll_gsm",
                },
                {
                    data: "roll_color",
                    name: "roll_color",
                },
                {
                    data: "roll_length",
                    name: "roll_length",
                },
                {
                    data: "net_weight",
                    name: "net_weight",
                },
                {
                    data: "gross_weight",
                    name: "gross_weight",
                },
                {
                    data: "client_name",
                    name: "client_name",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "estimated_despatch_date",
                    name: "estimated_despatch_date",
                },                
                {
                    data: "print_color",
                    name: "print_color",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "bag_type",
                    name: "bag_type",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "bag_units",
                    name: "bag_units",
                },
                {
                    data: "printing_date",
                    name: "printing_date",
                },
                {
                    data: "schedule_date_for_print",
                    name: "schedule_date_for_print",
                },
                {
                    data: "weight_after_printing",
                    name: "weight_after_printing",
                },
                {
                    data: "action",
                    name: "action",
                    orderable: false,
                    searchable: false
                },
            ],
            dom: 'lBfrtip', // This enables the buttons
            language: {
                lengthMenu: "Show _MENU_" // Removes the "entries" text
            },
            lengthMenu: [
                [10, 25, 50, 100, -1], // The internal values
                ["10 Row", "25 Row", "50 Row", "100 Row", "All"] // The display values, replace -1 with "All"
            ],
            buttons: [{
                eextend: 'csv',
                text: 'Export to Excel',
                className: 'btn btn-success',
                action: function(e, dt, button, config) {
                    $.ajax({
                        url: "{{ route('roll.list', ':flag') }}".replace(':flag', flag) + "?export=true",
                        xhrFields: {
                            responseType: 'blob' // Important for handling binary data
                        },
                        success: function(data, status, xhr) {
                            // Extract the filename from the response header
                            const filename = xhr.getResponseHeader('Content-Disposition')
                                ?.match(/filename="(.+)"/)?.[1] || 'export.xlsx';

                            // Create a new Blob object for the data
                            const blob = new Blob([data], {
                                type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                            });

                            // Create a download link
                            const link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob);
                            link.download = filename;
                            document.body.appendChild(link);

                            // Trigger the download
                            link.click();

                            // Cleanup
                            document.body.removeChild(link);
                            window.URL.revokeObjectURL(link.href);
                        },
                        error: function(xhr, status, error) {
                            console.error('Error downloading file:', error);
                            alert('Error downloading file. Please try again.');
                        }
                    });

                }


            }],
            createdRow: function(row, data, dataIndex) {
                // Apply the custom class to the row
                if (data.row_color) {
                    $(row).addClass(data.row_color);
                    if(data.row_color=="tr-client"){
                        $(row).attr("title", "book for client");
                    }else if(data.row_color=="tr-client-printed"){
                        $(row).attr("title", "roll have book and printed");
                    }else if(data.row_color=="tr-printed"){
                        $(row).attr("title", "roll is printed");
                    }else if(data.row_color=="tr-primary-print"){
                        $(row).attr("title", "this roll will delivered soon");
                    }else if(data.row_color=="tr-expiry-print blink"){
                        $(row).attr("title", "this roll  delivery will expired");
                    }else if(data.row_color=="tr-argent-print"){
                        $(row).attr("title", "this roll  delivery is argent");
                    }
                }
            }
        });
        if(flag=="history"){
            table.column(18).visible(false);
        }
        else if(["schedule","print"].includes(flag)){
            table.column(15).visible(false);
        }else{            
            table.column(16).visible(false);
        }

        $("#addMenu").on("click", function() {
            $("#myForm").submit();
        });

        $('button[data-bs-target="#addMenuModel"]').on("click", () => {
            $("#myForm").get(0).reset();
        });
        $("#rollForm").validate({
            rules: rules,
            messages: {
                menu_name: {
                    required: "Please enter a menu name",
                    minlength: "Menu name must be at least 3 characters long"
                },
                order_no: {
                    required: "Please enter an order number",
                    number: "Please enter a valid number for the order"
                },
                parent_menu_mstr_id: {
                    required: "Please select a parent menu"
                },
                parent_sub_menu_mstr_id: {
                    required: "Please select a parent sub-menu"
                },
                url_path: {
                    required: "Please enter the menu path"
                },
                menu_icon: {
                    required: "Please select a menu icon"
                },
                "user_type_mstr_id[]": {
                    required: "Please select at least one user type"
                }
            },
            submitHandler: function(form) {
                // If form is valid, prevent default form submission and submit via AJAX
                addRoll();
            }
        });
        $("#importForm").validate({
            rules: {
                csvFile: {
                    required: true,
                }
            },
            submitHandler: function(form) {
                importFile();
                return false;
            }
        });

        $("#printingScheduleModalForm").validate({
            rules: {
                printingScheduleDate: {
                    required: true,
                }
            },
            submitHandler: function(form) {
                printingScheduleDate();
            }
        });

        $("#printingUpdateModalForm").validate({
            rules: {
                printingUpdateDate: {
                    required: true,
                }
            },
            submitHandler: function(form) {
                printingUpdateModal();
            }
        });

        $("#rollBookingForm").validate({
            rules: {
                bookingForClientId: {
                    required: true,
                },
                bookingEstimatedDespatchDate:{
                    required:true
                },
                bookingBagUnits: {
                    required: true,
                },
                bookingBagTypeId: {
                    required: true,
                },
                bookingPrintingColor: {
                    required: true,
                },
            },
            submitHandler: function(form) {
                bookForClient();
            }
        });
        addEventListenersToForm();

    });

    function addEventListenersToForm() {
        const form = document.getElementById("rollForm");
        // Loop through all elements in the form
        Array.from(form.elements).forEach((element) => {
            // Add event listeners based on input type
            if (element.tagName === "INPUT" || element.tagName === "SELECT" || element.tagName === "TEXTAREA") {
                element.addEventListener("input", hideErrorMessage);
                element.addEventListener("change", hideErrorMessage);
            }
        });
    }

    function hideErrorMessage(event) {
        const element = event.target;
        // Find and hide the error message associated with the element
        const errorMessage = document.getElementById(`${element.id}-error`);
        if (errorMessage) {
            errorMessage.innerHTML = "";
        }
    }

    function addRoll() {
        $.ajax({
                type: "POST",
                'url': "{{route('roll.add')}}",

                "deferRender": true,
                "dataType": "json",
                'data': $("#rollForm").serialize(),
                beforeSend: function() {
                    $("#loadingDiv").show();
                },
                success: function(data) {
                    $("#loadingDiv").hide();
                    if (data.status) {
                        $("#rollForm").get(0).reset();
                        $("#rollModal").modal('hide');
                        $('#postsTable').DataTable().draw();
                        modelInfo(data.messages);
                    } else if (data?.errors) {
                        let errors = data?.errors;
                        console.log(data?.errors?.rollNo[0]);
                        modelInfo(data.messages);
                        for (field in errors) {
                            console.log(field);
                            $(`#${field}-error`).text(errors[field][0]);
                        }
                    } else {
                        modelInfo("Something Went Wrong!!");
                    }
                },
            }

        )
    }

    var loadSubMenuMstr = () => {
        subMenuLoadCount++;
        if ($('#parent_menu_mstr_id').val() != 0 && $('#parent_menu_mstr_id').val() != -1) {
            $.ajax({
                type: "get",
                url: "{{route('submenu-list')}}",
                dataType: "json",
                data: {
                    "id": $('#parent_menu_mstr_id').val(),
                },
                beforeSend: function() {
                    $("#loadingDiv").show();
                },
                success: function(data) {
                    if (data.status == true) {
                        $("#parent_sub_menu_mstr_id").html(data.data);
                        if (parent_sub_menu_mstr_id != '' && subMenuLoadCount == 1) {
                            $("#parent_sub_menu_mstr_id").val(parent_sub_menu_mstr_id);
                        }
                    } else {
                        $("#parent_sub_menu_mstr_id").html('<option value="0">#</option>');
                    }
                    $("#loadingDiv").hide();
                }
            });
        } else {
            $("#parent_sub_menu_mstr_id").val("0");
        }
    };

    function importFile() {
        var formData = new FormData($("#importForm")[0]);
        $.ajax({
                type: "POST",
                'url': "{{route('roll.import')}}",
                "deferRender": true,
                processData: false, // Do not process data (let FormData handle it)
                contentType: false, // Do not set content type (let the browser handle it)
                dataType: "json",

                'data': formData,
                beforeSend: function() {
                    $("#loadingDiv").show();
                },
                success: function(data) {
                    $("#loadingDiv").hide();
                    if (data.status) {
                        document.getElementById("importForm").reset();
                        $("#fileImportModal").modal('hide');
                        $('#postsTable').DataTable().draw();
                        modelInfo(data.messages);
                    } else if (data?.errors) {
                        let errors = data?.errors;
                        console.log(data?.errors?.rollNo[0]);
                        modelInfo(data.messages);
                        for (field in errors) {
                            console.log(field);
                            $(`#${field}-error`).text(errors[field][0]);
                        }
                    } else {
                        modelInfo("Something Went Wrong!!");
                    }
                },
                error: function(error) {
                    $("#loadingDiv").hide();
                    console.log(error);
                }
            }

        )
    }

    function openModelBookingModel(id) {
        if (id) {
            $("#rollId").val(id);
            $("#rollBookingModal").modal("show");

        }
        return;
    }

    function openPrintingScheduleModel(id){
        $.ajax({
            type:"GET",
            url: "{{ route('roll.dtl', ':id') }}".replace(':id', id),
            dataType: "json",
            beforeSend: function() {
                $("#loadingDiv").show();
            },
            success:function(data){
                if(data.status==true) {
                    rolDtl = data.data;
                    console.log(rolDtl); 
                    $("#printingScheduleRollId").val(rolDtl?.id);
                    $("#printingScheduleDate").val(rolDtl?.schedule_date_for_print);
                    $("#roll_no_display").html(rolDtl?.roll_no);
                    $("#printingScheduleModal").modal("show");
                
                } 
                $("#loadingDiv").hide();
            },
            error:function(error){
                $("#loadingDiv").hide();
            }
        });
    }

    function printingScheduleDate(){
        $.ajax({
                type: "POST",
                'url': "{{route('roll.printing.schedule')}}",

                "deferRender": true,
                "dataType": "json",
                'data': $("#printingScheduleModalForm").serialize(),
                beforeSend: function() {
                    $("#loadingDiv").show();
                },
                success: function(data) {
                    $("#loadingDiv").hide();
                    if (data.status) {
                        $("#printingScheduleModalForm").get(0).reset();
                        $("#roll_no_display").html("");
                        $("#printingScheduleModal").modal('hide');
                        $('#postsTable').DataTable().draw();
                        modelInfo(data.messages);
                    } else if (data?.errors) {
                        let errors = data?.errors;
                        console.log(data?.errors?.rollNo[0]);
                        modelInfo(data.messages);
                        for (field in errors) {
                            console.log(field);
                            $(`#${field}-error`).text(errors[field][0]);
                        }
                    } else {
                        modelInfo("Something Went Wrong!!");
                    }
                },
            }

        ); 
    }

    function openPrintingModel(id){
        $.ajax({
            type:"GET",
            url: "{{ route('roll.dtl', ':id') }}".replace(':id', id),
            dataType: "json",
            beforeSend: function() {
                $("#loadingDiv").show();
            },
            success:function(data){
                if(data.status==true) {
                    rolDtl = data.data;
                    console.log(rolDtl); 
                    $("#printingUpdateRollId").val(rolDtl?.id);
                    $('[display_roll_no="roll_no_display"]').each(function(index, element) {
                        // Use jQuery to wrap the raw DOM element
                        $(element).html(rolDtl?.roll_no || '');
                    });
                    $("#printingUpdateModal").modal("show");
                
                } 
                $("#loadingDiv").hide();
            },
            error:function(error){
                $("#loadingDiv").hide();
            }
        });
    }

    function printingUpdateModal(){
        $.ajax({
                type: "POST",
                'url': "{{route('roll.printing.update')}}",

                "deferRender": true,
                "dataType": "json",
                'data': $("#printingUpdateModalForm").serialize(),
                beforeSend: function() {
                    $("#loadingDiv").show();
                },
                success: function(data) {
                    $("#loadingDiv").hide();
                    if (data.status) {
                        $("#printingUpdateModalForm").get(0).reset();
                        $('[display_roll_no="roll_no_display"]').each(function(index, element) {
                            // Use jQuery to wrap the raw DOM element
                            $(element).html('');
                        });
                        $("#printingUpdateModal").modal('hide');
                        $('#postsTable').DataTable().draw();
                        modelInfo(data.messages);
                    } else if (data?.errors) {
                        let errors = data?.errors;
                        console.log(data?.errors?.rollNo[0]);
                        modelInfo(data.messages);
                        for (field in errors) {
                            console.log(field);
                            $(`#${field}-error`).text(errors[field][0]);
                        }
                    } else {
                        modelInfo("Something Went Wrong!!");
                    }
                },
            }

        );
    }

    function bookForClient() {
        $.ajax({
                type: "POST",
                'url': "{{route('roll.book')}}",

                "deferRender": true,
                "dataType": "json",
                'data': $("#rollBookingForm").serialize(),
                beforeSend: function() {
                    $("#loadingDiv").show();
                },
                success: function(data) {
                    $("#loadingDiv").hide();
                    if (data.status) {
                        $("#rollBookingForm").get(0).reset();
                        $("#rollBookingModal").modal('hide');
                        $('#postsTable').DataTable().draw();
                        modelInfo(data.messages);
                    } else if (data?.errors) {
                        let errors = data?.errors;
                        console.log(data?.errors?.rollNo[0]);
                        modelInfo(data.messages);
                        for (field in errors) {
                            console.log(field);
                            $(`#${field}-error`).text(errors[field][0]);
                        }
                    } else {
                        modelInfo("Something Went Wrong!!");
                    }
                },
            }

        )
    }
    function searchData(){
        $('#postsTable').DataTable().draw();
    }
</script>
@include("layout.footer")
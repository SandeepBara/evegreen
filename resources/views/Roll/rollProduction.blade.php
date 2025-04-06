@include("layout.header")
<!-- Main Component -->



<!-- Modal Form -->
<style>
    .modal-body {
        max-height: calc(100vh - 150px); /* Adjust height as needed */
        overflow-y: auto; /* Enable vertical scrolling */
    }
</style>

<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Roll</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">{{$machine->name??""}}</li>
                </ol>
            </nav>

        </div>
    </div>
    <div class="container">       
        <div class="panel-body">
            <form id="printingRoll">
                @csrf
                <!-- Hidden field for Client ID -->
                <input type="hidden" id="id" name="id" value="">

                <div class="row mt-3">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="form-label" for="printingUpdate">Date <span class="text-danger">*</span></label>
                            <input type="date" name="printingUpdate" id="printingUpdate" class="form-control" max="{{date('Y-m-d')}}" required />
                            <span class="error-text" id="printingUpdate-error"></span>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="form-label" for="rollNo">Roll No</label>
                            <input type="text" id="rollNo" name="rollNo" class="form-control">
                            <span class="error-text" id="rollNo-error"></span>
                        </div>
                    </div>
                    <div class="col-sm-4 d-flex align-items-end">
                        <button type="button" id="search" class="btn btn-primary w-100">Search</button>
                    </div>
                </div>
                <hr>
                <table class="table table-striped table-bordered table-responsive">
                    <thead>
                        <th>Roll No</th>
                        <th>Punches Date</th>
                        <th>Roll Size</th>
                        <th>Roll Color</th>
                        <th>Client Name</th>
                        <th>Printing Color</th>
                        <th>Weight After Printing</th>
                        <th>Color Ratio</th>
                        <th>Remove</th>
                    </thead>
                    <tbody id="rollTableBody">
                    </tbody>
                </table>

                <!-- Submit Button -->
                <div class="row mt-4">
                    <div class="col-sm-12 text-end">
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal -->
</main>

<script>
    let sl = 0;
    var machineId = window.location.pathname.split('/').pop();
    $(document).ready(function () {
        $("#id").val(machineId);
        // Handle Search Button Click
        $("#search").on("click", function () {
            let rollNo = $("#rollNo").val();
            let machineId = $("#id").val();
            if (rollNo != "") {
                $.ajax({
                    url: "{{ route('roll.search.printing') }}",
                    type: "POST",
                    dataType: "json",
                    data: { "rollNo": rollNo,"machineId":machineId },
                    beforeSend: function () {
                        $("#loadingDiv").show();
                    },
                    success: function (data) {
                        $("#loadingDiv").hide();
                        if (data.status && data.data) {
                            $("#rollNo").val("");
                            let roll = data.data;
                            const rowId = `row${sl}`;
                            const colors = roll.printing_color.split(",");
                            const colorInputs = colors.map((color, index) => {
                                return `<input type='text' name='roll[${sl}][color][${index}]' id='color${index}_${rowId}' class='form-control dynamic-field' required onkeypress="return isNumDot(event);" placeholder='${color}' /> <input type='hidden' name='roll[${sl}][colorName][${index}]' value='${color}' />`;
                            }).join("");

                            let row = `
                                <tr id='${rowId}'>
                                    <td>
                                        <input type='hidden' name='roll[${sl}][id]' value='${roll.id}' />${roll.roll_no}
                                    </td>
                                    <td>${roll.purchase_date}</td>
                                    <td>${roll.size}</td>
                                    <td>${roll.roll_color}</td>
                                    <td>${roll.client_name}</td>
                                    <td>${roll.printing_color}</td>
                                    <td>
                                        <input type='text' name='roll[${sl}][printingWeight]' id='printingWeight${rowId}' class='form-control dynamic-field' min='${roll.net_weight}' onkeypress="return isNumDot(event);" required />
                                    </td>
                                    <td>${colorInputs}</td>
                                    <td><span onclick='removeTr(this)' class='btn btn-sm btn-warning'>X</span></td>
                                </tr>`;
                            $("#rollTableBody").append(row);
                            sl = sl+1;
                            applyValidationRules(rowId);
                        } else if(data.status){
                            modelInfo(data.message || "Invalid Roll No", "warning");
                        }
                        else {
                            modelInfo(data.messages || "Invalid Roll No", "warning");
                        }
                    }
                });
            }
        });

        // Handle Form Validation
        $("#printingRoll").validate({
            ignore: [],
            rules: {
                id: { required: true },
                printingUpdate: { required: true },
            },
            submitHandler: function (form) {
                // If form is valid, submit it
                updateRollPrinting();
            }
        });
    });

    // Apply validation rules to dynamically added fields
    function applyValidationRules(rowId) {
        $(`#${rowId} input.dynamic-field`).each(function () {
            $(this).rules("add", {
                required: true,
                messages: {
                    required: "This field is required.",
                },
            });
        });
    }

    // Remove Row from Table
    function removeTr(element) {
        $(element).closest("tr").remove();
    }

    // Update Roll Printing (Submit form via AJAX)

    function updateRollPrinting() {
        $.ajax({
            type: "POST",
            url: "{{ route('roll.printing.update') }}",
            dataType: "json",
            data: $("#printingRoll").serialize(),
            beforeSend: function () {
                $("#loadingDiv").show();
            },
            success: function (data) {
                $("#loadingDiv").hide();
                if (data.status) {
                    sl =0;
                    // Reset form and clear the table
                    $("#printingRoll")[0].reset();
                    $("#rollTableBody").empty();
                    $("#UpdatePrintingModel").modal('hide');
                    modelInfo(data.messages || "Update Successful!");
                } else if (data.errors) {
                    let errors = data.errors;
                    for (field in errors) {
                        $(`#${field}-error`).text(errors[field][0]);
                    }
                    modelInfo(data.messages || "Validation Error", "error");
                } else {
                    modelInfo("Something Went Wrong!!", "error");
                }
            },            
        });
    }

</script>
@include("layout.footer")
<!-- Modal Form -->
<div class="modal fade modal-lg" id="clientModal" tabindex="-1" aria-labelledby="clientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clientModalLabel">Add New Client</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="clientForm">
                    @csrf
                    <!-- Hidden field for Client ID -->
                    <input type="hidden" id="id" name="id" value="">

                    <div class="row">
                        <!-- Client Name -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="clientName">Client Name<span class="text-danger">*</span></label>
                                <input type="text" maxlength="100" id="clientName" name="client_name" class="form-control" placeholder="Enter Client Name" required>
                            </div>
                        </div>
                        
                        <!-- Client Email -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="clientEmail">Email</label>
                                <input type="email" maxlength="100" id="clientEmail" name="client_email" class="form-control" placeholder="client@example.com">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <!-- Client Mobile Number -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="clientMobileNo">Mobile Number<span class="text-danger">*</span></label>
                                <input type="text" maxlength="15" id="clientMobileNo" name="client_mobile_no" class="form-control" placeholder="Enter Mobile Number" required onkeypress="return isNum(event);">
                            </div>
                        </div>
                        
                        <!-- Client Address -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="clientAddress">Address<span class="text-danger">*</span></label>
                                <textarea id="clientAddress" name="client_address" class="form-control" placeholder="Enter Client Address" rows="3" required></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="row mt-4">
                        <div class="col-sm-12 text-end">
                            <button type="submit" class="btn btn-success">Add Client</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

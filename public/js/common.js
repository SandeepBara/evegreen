
function isNum(e){
    if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        return false;
    }
}
function isAlpha(e){
    var keyCode = (e.which) ? e.which : e.keyCode
    if ((keyCode < 65 || keyCode > 90) && (keyCode < 97 || keyCode > 123) && keyCode != 32)
        return false;

    return true;
}
function isNumDot(e) {
    var charCode = (e.which) ? e.which : e.keyCode;
    if (charCode == 46) {
        var txt = e.target.value;
        if ((txt.indexOf(".") > -1) || txt.length == 0) {
            return false;
        }
    } else {
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            return false;
        }
    }
}
jQuery.validator.addMethod("dateFormatYYYMMDD", function(value, element) {
    return this.optional(element) || /^([12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))+$/i.test(value);
}, "Invalid format (YYYY-MM-DD)"); 

jQuery.validator.addMethod("alphaSpace", function(value, element) {
    return this.optional(element) || /^[a-zA-Z ]+$/i.test(value);
}, "Letters only please (a-z, A-Z )");

$(".toggler-btn").on("click", function() {
    document.querySelector("#sidebar").classList.toggle("collapsed");
});
toastr.options = {
    "closeButton": true, // Show close (dismiss) button
    "debug": false, 
    "newestOnTop": true,
    "progressBar": true, // Display a progress bar
    "positionClass": "toast-top-right", // Top right corner
    "preventDuplicates": true, // Avoid duplicate toasts
    "onclick": null,
    "showDuration": "300", // How long the show animation lasts
    "hideDuration": "1000", // How long the hide animation lasts
    "timeOut": "3000", // Auto-hide delay (5 seconds)
    "extendedTimeOut": "1000", // Extra time for mouse hover
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};
function modelInfo(msg,className="success") {
    

    if(className=="error") {
        toastr.error(msg);
    }
    else if(className=="info") {
        toastr.info(msg);
    }
    else if(className=="warning") {
        toastr.warning(msg);
    }else{
        toastr.success(msg);
    }

}
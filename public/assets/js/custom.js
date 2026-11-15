// Global Custom Alert Function for OMSystem Enterprise
function showAlert(type, message) {
    $('.custom-alert').remove();
    const alertBox = `
        <div class="alert alert-${type} custom-alert alert-dismissible fade show text-start" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 999999; min-width: 320px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); border-radius: 10px;">
            <div class="d-flex align-items-center gap-2">
                <i class="bi ${type === 'success' ? 'bi-check-circle-fill text-success' : 'bi-exclamation-triangle-fill text-danger'} fs-5"></i>
                <span class="fw-medium">${message}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`;
    $('body').append(alertBox);
    $('.custom-alert').fadeIn();
    setTimeout(function () {
        $('.custom-alert').fadeOut(function () {
            $(this).remove();
        });
    }, 4000);
}

$('.toggle-sidebar-btn').on('click', function (e) {
    e.preventDefault();
    $('body').toggleClass('toggle-sidebar');
});
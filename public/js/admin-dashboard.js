/**
 * Mental Health Screening Admin Dashboard JavaScript
 * Enhancements for the mental health screening admin panel
 */

$(document).ready(function() {
    initializeDashboard();
});

function initializeDashboard() {
    // Initialize all dashboard components
    setupCSRFToken();
    initializeCharts();
    setupDataTables();
    setupAlerts();
    setupQuickActions();
    setupRealTimeUpdates();
    initializeTooltips();
}

/**
 * CSRF Token Setup
 */
function setupCSRFToken() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
}

/**
 * Chart Initialization
 */
function initializeCharts() {
    // Only initialize if Chart.js is loaded
    if (typeof Chart !== 'undefined') {
        setupDashboardCharts();
    }
}

function setupDashboardCharts() {
    // PHQ-9 Distribution Chart
    const phq9Canvas = document.getElementById('phq9Chart');
    if (phq9Canvas) {
        new Chart(phq9Canvas, {
            type: 'doughnut',
            data: {
                labels: ['Sangat Rendah', 'Rendah', 'Sedang', 'Tinggi', 'Sangat Tinggi'],
                datasets: [{
                    data: [], // Will be populated by server
                    backgroundColor: [
                        '#4CAF50',
                        '#2196F3', 
                        '#FF9800',
                        '#F44336',
                        '#9C27B0'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Monthly Trends Chart
    const trendsCanvas = document.getElementById('monthlyTrendsChart');
    if (trendsCanvas) {
        new Chart(trendsCanvas, {
            type: 'line',
            data: {
                labels: [], // Will be populated by server
                datasets: [{
                    label: 'Partisipan per Bulan',
                    data: [], // Will be populated by server
                    borderColor: '#4facfe',
                    backgroundColor: 'rgba(79, 172, 254, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
}

/**
 * DataTables Setup
 */
function setupDataTables() {
    // Initialize DataTables for response lists
    if ($.fn.DataTable) {
        $('.data-table').DataTable({
            responsive: true,
            pageLength: 25,
            order: [[0, 'desc']], // Latest first
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            },
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn btn-success btn-sm'
                },
                {
                    extend: 'csv',
                    text: '<i class="fas fa-file-csv"></i> CSV',
                    className: 'btn btn-info btn-sm'
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print"></i> Print',
                    className: 'btn btn-secondary btn-sm'
                }
            ]
        });
    }
}

/**
 * Alert System
 */
function setupAlerts() {
    // Auto-hide success alerts
    setTimeout(function() {
        $('.alert-success').fadeOut('slow');
    }, 5000);

    // Check for high-risk responses
    checkHighRiskAlerts();
}

function checkHighRiskAlerts() {
    $.get('/admin/api/high-risk-check')
        .done(function(data) {
            if (data.highRiskCount > 0) {
                showHighRiskAlert(data.highRiskCount);
                updateAlertCounter(data.highRiskCount);
            }
        })
        .fail(function() {
            console.log('Could not check for high-risk responses');
        });
}

function showHighRiskAlert(count) {
    const alertHtml = `
        <div class="alert alert-admin-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Perhatian!</strong> Terdapat ${count} responden dengan tingkat risiko tinggi yang memerlukan tindak lanjut segera.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    $('#alertContainer').prepend(alertHtml);
}

function updateAlertCounter(count) {
    $('#alertCounter').text(count > 9 ? '9+' : count);
}

/**
 * Quick Actions Setup
 */
function setupQuickActions() {
    // Export data functionality
    $('.btn-export').on('click', function(e) {
        e.preventDefault();
        const exportType = $(this).data('type');
        showExportModal(exportType);
    });
    
    // Bulk actions
    $('.btn-bulk-action').on('click', function(e) {
        e.preventDefault();
        const action = $(this).data('action');
        handleBulkAction(action);
    });
    
    // Quick filters
    $('.quick-filter').on('click', function(e) {
        e.preventDefault();
        const filter = $(this).data('filter');
        applyQuickFilter(filter);
    });
}

function showExportModal(type) {
    const modal = `
        <div class="modal fade" id="exportModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Export Data ${type.toUpperCase()}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="exportForm">
                            <div class="mb-3">
                                <label class="form-label">Rentang Tanggal</label>
                                <div class="row">
                                    <div class="col">
                                        <input type="date" class="form-control" name="start_date">
                                    </div>
                                    <div class="col">
                                        <input type="date" class="form-control" name="end_date">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Filter Fakultas</label>
                                <select class="form-select" name="faculty_id">
                                    <option value="">Semua Fakultas</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tingkat Risiko</label>
                                <select class="form-select" name="risk_level">
                                    <option value="">Semua Tingkat</option>
                                    <option value="Low">Low</option>
                                    <option value="Moderate">Moderate</option>
                                    <option value="High">High</option>
                                    <option value="Critical">Critical</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" onclick="executeExport('${type}')">
                            <i class="fas fa-download me-2"></i>Export
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('body').append(modal);
    $('#exportModal').modal('show');
    
    // Clean up when modal is closed
    $('#exportModal').on('hidden.bs.modal', function() {
        $(this).remove();
    });
}

function executeExport(type) {
    const formData = $('#exportForm').serialize();
    const url = `/admin/export/${type}?${formData}`;
    
    // Show loading
    showLoading('Menyiapkan file export...');
    
    window.location.href = url;
    
    setTimeout(() => hideLoading(), 3000);
    $('#exportModal').modal('hide');
}

/**
 * Real-time Updates
 */
function setupRealTimeUpdates() {
    // Update dashboard stats every 5 minutes
    setInterval(updateDashboardStats, 300000);
    
    // Check for new responses every 2 minutes
    setInterval(checkNewResponses, 120000);
}

function updateDashboardStats() {
    $.get('/admin/api/dashboard-stats')
        .done(function(data) {
            updateStatsCards(data);
        })
        .fail(function() {
            console.log('Could not update dashboard stats');
        });
}

function updateStatsCards(data) {
    if (data.totalResponses) {
        $('#totalResponses').text(data.totalResponses.toLocaleString());
    }
    if (data.highRiskCount) {
        $('#highRiskCount').text(data.highRiskCount.toLocaleString());
    }
    if (data.completionRate) {
        $('#completionRate').text(data.completionRate + '%');
    }
}

function checkNewResponses() {
    $.get('/admin/api/new-responses-check')
        .done(function(data) {
            if (data.newCount > 0) {
                showNewResponseNotification(data.newCount);
            }
        });
}

function showNewResponseNotification(count) {
    const notification = `
        <div class="alert alert-admin-primary alert-dismissible fade show" role="alert">
            <i class="fas fa-bell me-2"></i>
            ${count} respons baru telah masuk. 
            <a href="${window.location.pathname}" class="alert-link">Refresh halaman</a> untuk melihat data terbaru.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    $('#alertContainer').prepend(notification);
}

/**
 * Utility Functions
 */
function initializeTooltips() {
    // Initialize Bootstrap tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Initialize popovers
    $('[data-bs-toggle="popover"]').popover();
}

function showLoading(message = 'Memproses...') {
    const loadingHtml = `
        <div id="loadingOverlay" class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" 
             style="background: rgba(0,0,0,0.5); z-index: 9999;">
            <div class="bg-white rounded p-4 text-center">
                <div class="bima-spinner mx-auto mb-3"></div>
                <div>${message}</div>
            </div>
        </div>
    `;
    
    $('body').append(loadingHtml);
}

function hideLoading() {
    $('#loadingOverlay').remove();
}

function showToast(message, type = 'success') {
    const toastClass = `toast-${type}`;
    const iconClass = type === 'success' ? 'fa-check-circle' : 
                     type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle';
    
    const toastHtml = `
        <div class="toast align-items-center text-white bg-${type} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas ${iconClass} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    // Create toast container if it doesn't exist
    if (!$('#toastContainer').length) {
        $('body').append('<div id="toastContainer" class="toast-container position-fixed top-0 end-0 p-3"></div>');
    }
    
    const $toast = $(toastHtml);
    $('#toastContainer').append($toast);
    
    const bsToast = new bootstrap.Toast($toast[0]);
    bsToast.show();
    
    // Remove toast element after it's hidden
    $toast.on('hidden.bs.toast', function() {
        $(this).remove();
    });
}

/**
 * Data Handling Functions
 */
function applyQuickFilter(filter) {
    const table = $('.data-table').DataTable();
    
    switch(filter) {
        case 'high-risk':
            table.column(4).search('High|Critical', true, false).draw();
            break;
        case 'today':
            const today = new Date().toISOString().split('T')[0];
            table.column(3).search(today).draw();
            break;
        case 'this-week':
            // Implementation for this week filter
            break;
        default:
            table.search('').columns().search('').draw();
    }
}

function handleBulkAction(action) {
    const selectedRows = $('.data-table tbody input[type="checkbox"]:checked');
    
    if (selectedRows.length === 0) {
        showToast('Pilih minimal satu item', 'warning');
        return;
    }
    
    const ids = selectedRows.map(function() {
        return $(this).val();
    }).get();
    
    switch(action) {
        case 'export':
            exportSelected(ids);
            break;
        case 'flag':
            flagForFollowup(ids);
            break;
        case 'delete':
            confirmDelete(ids);
            break;
    }
}

function exportSelected(ids) {
    showLoading('Menyiapkan export data...');
    
    $.post('/admin/export/selected', { ids: ids })
        .done(function(data) {
            window.location.href = data.downloadUrl;
            showToast('Export berhasil!', 'success');
        })
        .fail(function() {
            showToast('Export gagal. Silakan coba lagi.', 'error');
        })
        .always(function() {
            hideLoading();
        });
}

function flagForFollowup(ids) {
    if (confirm(`Flag ${ids.length} responden untuk tindak lanjut?`)) {
        $.post('/admin/flag-followup', { ids: ids })
            .done(function() {
                showToast('Berhasil di-flag untuk tindak lanjut', 'success');
                location.reload();
            })
            .fail(function() {
                showToast('Gagal memproses. Silakan coba lagi.', 'error');
            });
    }
}

function confirmDelete(ids) {
    if (confirm(`Hapus ${ids.length} responden? Tindakan ini tidak dapat dibatalkan.`)) {
        $.ajax({
            url: '/admin/responses/bulk-delete',
            method: 'DELETE',
            data: { ids: ids }
        })
        .done(function() {
            showToast('Data berhasil dihapus', 'success');
            location.reload();
        })
        .fail(function() {
            showToast('Gagal menghapus data. Silakan coba lagi.', 'error');
        });
    }
}

/**
 * Form Enhancement Functions
 */
function enhanceForms() {
    // Auto-save form data
    $('form').on('input change', function() {
        const formId = $(this).attr('id');
        if (formId) {
            autoSaveForm(formId);
        }
    });
    
    // Form validation enhancement
    $('form').on('submit', function(e) {
        if (!validateForm($(this))) {
            e.preventDefault();
            return false;
        }
    });
}

function autoSaveForm(formId) {
    const formData = $(`#${formId}`).serialize();
    localStorage.setItem(`admin_form_${formId}`, formData);
}

function restoreForm(formId) {
    const savedData = localStorage.getItem(`admin_form_${formId}`);
    if (savedData) {
        // Parse and restore form data
        const params = new URLSearchParams(savedData);
        params.forEach((value, key) => {
            $(`#${formId} [name="${key}"]`).val(value);
        });
    }
}

function validateForm($form) {
    let isValid = true;
    
    $form.find('[required]').each(function() {
        if (!$(this).val().trim()) {
            $(this).addClass('is-invalid');
            isValid = false;
        } else {
            $(this).removeClass('is-invalid');
        }
    });
    
    return isValid;
}
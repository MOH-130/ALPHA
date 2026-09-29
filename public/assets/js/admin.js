/**
 * NOVASTORE - ADMIN PANEL JAVASCRIPT & CHARTS
 */

document.addEventListener('DOMContentLoaded', function() {
    initSalesChart();
    initThemeLivePreview();
    initImagePreviews();
});

// ================= 1. ADMIN SALES & ORDERS CHART =================
function initSalesChart() {
    const canvas = document.getElementById('salesChart');
    if (!canvas || typeof Chart === 'undefined') return;

    const labels = JSON.parse(canvas.getAttribute('data-labels') || '[]');
    const salesData = JSON.parse(canvas.getAttribute('data-sales') || '[]');
    const ordersData = JSON.parse(canvas.getAttribute('data-orders') || '[]');

    new Chart(canvas.getContext('2d'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'إجمالي المبيعات (د.ج - DA)',
                    data: salesData,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    yAxisID: 'y'
                },
                {
                    label: 'عدد الطلبات',
                    data: ordersData,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    grid: { color: '#f1f5f9' }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: { drawOnChartArea: false }
                }
            }
        }
    });
}

// ================= 2. THEME LIVE CUSTOMIZER PREVIEW =================
function initThemeLivePreview() {
    const primaryInput = document.getElementById('theme_primary_color');
    const secondaryInput = document.getElementById('theme_secondary_color');
    const previewBox = document.getElementById('theme_preview_box');

    if (primaryInput && previewBox) {
        primaryInput.addEventListener('input', function(e) {
            previewBox.style.setProperty('--preview-primary', e.target.value);
            const previewBtns = previewBox.querySelectorAll('.preview-btn-primary');
            previewBtns.forEach(btn => btn.style.backgroundColor = e.target.value);
        });
    }

    if (secondaryInput && previewBox) {
        secondaryInput.addEventListener('input', function(e) {
            previewBox.style.setProperty('--preview-secondary', e.target.value);
            const previewHeaders = previewBox.querySelectorAll('.preview-header');
            previewHeaders.forEach(hdr => hdr.style.backgroundColor = e.target.value);
        });
    }
}

// ================= 3. IMAGE PREVIEWS =================
function initImagePreviews() {
    document.querySelectorAll('.image-file-input').forEach(input => {
        input.addEventListener('change', function(e) {
            const previewTarget = document.querySelector(this.getAttribute('data-preview'));
            if (previewTarget && this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    previewTarget.src = ev.target.result;
                    previewTarget.style.display = 'block';
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
}

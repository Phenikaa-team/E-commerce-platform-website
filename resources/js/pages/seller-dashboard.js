/**
 * Seller Dashboard Page Script
 * Manages Seller Store Performance Chart & Modals
 */

export function openSellerProfileModal() {
    const modal = document.getElementById('edit-profile-modal');
    if (modal) modal.classList.remove('hidden');
}

export function closeSellerProfileModal() {
    const modal = document.getElementById('edit-profile-modal');
    if (modal) modal.classList.add('hidden');
}

export function initSellerPerformanceChart() {
    const dataEl = document.getElementById('seller-dashboard-data');
    if (!dataEl) return;

    let config = {};
    try {
        config = JSON.parse(dataEl.textContent);
    } catch (e) {
        console.error('Failed to parse seller dashboard data:', e);
        return;
    }

    if (typeof window.Chart === 'undefined') return;

    const perfCtx = document.getElementById('storePerformanceChart')?.getContext('2d');
    if (perfCtx) {
        new window.Chart(perfCtx, {
            type: 'line',
            data: {
                labels: config.chartLabels || ['10/05', '14/05', '18/05', '22/05', '26/05', '30/05', '02/06'],
                datasets: [
                    {
                        label: 'Lượt truy cập',
                        data: config.chartVisits || [450, 920, 1100, 1250, 1420, 1600, 1850],
                        borderColor: '#06b6d4',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        tension: 0.35,
                        pointRadius: 2,
                        pointHoverRadius: 4,
                        pointBackgroundColor: '#06b6d4',
                    },
                    {
                        label: 'Doanh thu',
                        data: config.chartRevenue || [320, 680, 850, 1020, 1190, 1340, 1550],
                        borderColor: '#f59e0b',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        tension: 0.35,
                        pointRadius: 2,
                        pointHoverRadius: 4,
                        pointBackgroundColor: '#f59e0b',
                    },
                    {
                        label: 'Đơn hàng',
                        data: config.chartOrders || [280, 520, 640, 760, 890, 1020, 1180],
                        borderColor: '#3b82f6',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        tension: 0.35,
                        pointRadius: 2,
                        pointHoverRadius: 4,
                        pointBackgroundColor: '#3b82f6',
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
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        padding: 8,
                        titleFont: { size: 11, weight: 'bold' },
                        bodyFont: { size: 10 }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 }, color: '#94a3b8' }
                    },
                    y: {
                        min: 0,
                        max: 2000,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            stepSize: 500,
                            font: { size: 10 },
                            color: '#94a3b8',
                            callback: function(val) {
                                if (val === 0) return '0';
                                return val >= 1000 ? (val / 1000).toFixed(1) + 'K' : val;
                            }
                        }
                    }
                }
            }
        });
    }
}

export function initSellerDashboard() {
    window.openSellerProfileModal = openSellerProfileModal;
    window.closeSellerProfileModal = closeSellerProfileModal;

    initSellerPerformanceChart();
}

document.addEventListener('DOMContentLoaded', () => {
    initSellerDashboard();
});

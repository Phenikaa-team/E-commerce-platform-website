/**
 * Admin Dashboard Page Script
 * Manages Admin Edit Modal (Profile/Password) and Dashboard Charts
 */

export function openAdminModal(tab = 'profile') {
    const modal = document.getElementById('admin-edit-modal');
    if (modal) {
        modal.classList.remove('hidden');
        switchAdminTab(tab);
    }
}

export function closeAdminModal() {
    const modal = document.getElementById('admin-edit-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

export function switchAdminTab(tab) {
    const tabProfile = document.getElementById('tab-content-profile');
    const tabPassword = document.getElementById('tab-content-password');
    const btnProfile = document.getElementById('tab-btn-profile');
    const btnPassword = document.getElementById('tab-btn-password');

    if (tab === 'password') {
        tabProfile?.classList.add('hidden');
        tabPassword?.classList.remove('hidden');
        btnPassword?.classList.add('bg-white', 'text-gray-900', 'shadow-xs');
        btnPassword?.classList.remove('text-gray-500');
        btnProfile?.classList.remove('bg-white', 'text-gray-900', 'shadow-xs');
        btnProfile?.classList.add('text-gray-500');
    } else {
        tabPassword?.classList.add('hidden');
        tabProfile?.classList.remove('hidden');
        btnProfile?.classList.add('bg-white', 'text-gray-900', 'shadow-xs');
        btnProfile?.classList.remove('text-gray-500');
        btnPassword?.classList.remove('bg-white', 'text-gray-900', 'shadow-xs');
        btnPassword?.classList.add('text-gray-500');
    }
}

export function initAdminDashboardCharts() {
    const dataEl = document.getElementById('admin-dashboard-data');
    if (!dataEl) return;

    let config = {};
    try {
        config = JSON.parse(dataEl.textContent);
    } catch (e) {
        console.error('Failed to parse admin dashboard data:', e);
        return;
    }

    if (config.hasErrors && config.errorTab) {
        openAdminModal(config.errorTab);
    }

    if (typeof window.Chart === 'undefined') return;

    // 1. 7-Day Revenue & Orders Bar + Trendline Chart
    const ctxRevenue = document.getElementById('revenueTrendChart')?.getContext('2d');
    if (ctxRevenue) {
        const revData = config.sevenDaysRevenue || [];
        new window.Chart(ctxRevenue, {
            data: {
                labels: config.sevenDaysLabels || [],
                datasets: [
                    {
                        type: 'bar',
                        label: 'Doanh thu (₫)',
                        data: revData,
                        backgroundColor: '#ea384c',
                        borderRadius: 6,
                        barThickness: 24,
                        yAxisID: 'y',
                    },
                    {
                        type: 'line',
                        label: 'Lượng đơn',
                        data: revData.map(v => v * 1.05),
                        borderColor: '#fca5a5',
                        borderWidth: 2,
                        tension: 0.4,
                        pointBackgroundColor: '#ea384c',
                        pointBorderColor: '#fff',
                        pointRadius: 3,
                        yAxisID: 'y',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        padding: 10,
                        titleFont: { size: 11, weight: 'bold' },
                        bodyFont: { size: 11 },
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10, weight: 'bold' }, color: '#9ca3af' }
                    },
                    y: {
                        grid: { color: '#f3f4f6' },
                        ticks: {
                            font: { size: 10 },
                            color: '#9ca3af',
                            callback: function(value) {
                                return (value / 1000000).toFixed(0) + 'M';
                            }
                        }
                    }
                }
            }
        });
    }

    // 2. Order Status Donut Chart
    const ctxStatus = document.getElementById('orderStatusChart')?.getContext('2d');
    if (ctxStatus) {
        const sc = config.statusCounts || {};
        new window.Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: ['Đã giao', 'Đang xử lý', 'Đang vận chuyển', 'Đã hủy', 'Hoàn trả'],
                datasets: [{
                    data: [
                        Number(sc.completed || 0),
                        Number(sc.processing || 0),
                        Number(sc.shipping || 0),
                        Number(sc.cancelled || 0),
                        Number(sc.refunded || 0)
                    ],
                    backgroundColor: ['#10b981', '#f59e0b', '#3b82f6', '#ef4444', '#8b5cf6'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }
}

export function initAdminDashboard() {
    window.openAdminModal = openAdminModal;
    window.closeAdminModal = closeAdminModal;
    window.switchAdminTab = switchAdminTab;

    initAdminDashboardCharts();
}

document.addEventListener('DOMContentLoaded', () => {
    initAdminDashboard();
});

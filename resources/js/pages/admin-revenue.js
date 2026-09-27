/**
 * Admin Revenue Page Script
 * Initializes daily revenue chart and payment method distribution donut chart
 */

export function initAdminRevenueCharts() {
    const dataEl = document.getElementById('admin-revenue-data');
    if (!dataEl) return;

    let config = {};
    try {
        config = JSON.parse(dataEl.textContent);
    } catch (e) {
        console.error('Failed to parse admin revenue data:', e);
        return;
    }

    if (typeof window.Chart === 'undefined') return;

    // 1. Detailed Daily Revenue Chart
    const ctxRevenue = document.getElementById('detailedRevenueChart')?.getContext('2d');
    if (ctxRevenue) {
        new window.Chart(ctxRevenue, {
            data: {
                labels: config.chartLabels || [],
                datasets: [
                    {
                        type: 'bar',
                        label: 'Doanh thu GMV (₫)',
                        data: config.chartRevenue || [],
                        backgroundColor: '#ea384c',
                        borderRadius: 6,
                        barThickness: 16,
                        yAxisID: 'y',
                    },
                    {
                        type: 'line',
                        label: 'Số lượng đơn hàng',
                        data: config.chartOrders || [],
                        borderColor: '#3B82F6',
                        borderWidth: 2,
                        pointBackgroundColor: '#3B82F6',
                        pointRadius: 3,
                        tension: 0.3,
                        yAxisID: 'y1',
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
                        position: 'left',
                        grid: { color: '#f3f4f6' },
                        ticks: {
                            font: { size: 10 },
                            color: '#9ca3af',
                            callback: function(value) {
                                return (value / 1000000).toFixed(0) + 'M';
                            }
                        }
                    },
                    y1: {
                        position: 'right',
                        grid: { display: false },
                        ticks: { font: { size: 10 }, color: '#3B82F6' }
                    }
                }
            }
        });
    }

    // 2. Payment Method Distribution Doughnut Chart
    const ctxPayment = document.getElementById('paymentMethodChart')?.getContext('2d');
    if (ctxPayment) {
        const pm = config.paymentMethodsDistribution || {};
        new window.Chart(ctxPayment, {
            type: 'doughnut',
            data: {
                labels: ['COD (Tiền mặt)', 'VNPay', 'MoMo', 'Khác'],
                datasets: [{
                    data: [
                        Number(pm.cod || 0),
                        Number(pm.vnpay || 0),
                        Number(pm.momo || 0),
                        Number(pm.other || 0)
                    ],
                    backgroundColor: ['#10B981', '#3B82F6', '#EC4899', '#9CA3AF'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    initAdminRevenueCharts();
});

/**
 * Seller Revenue Page Script
 * Initializes live clock, revenue/orders line chart, and order status donut chart
 */

export function initLiveClock() {
    const clockEl = document.getElementById('live-clock');
    if (!clockEl) return;

    const update = () => {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const mins = String(now.getMinutes()).padStart(2, '0');
        clockEl.textContent = `${hours}:${mins}`;
    };

    update();
    setInterval(update, 1000);
}

export function initSellerRevenueCharts() {
    const dataEl = document.getElementById('seller-revenue-data');
    if (!dataEl) return;

    let config = {};
    try {
        config = JSON.parse(dataEl.textContent);
    } catch (e) {
        console.error('Failed to parse seller revenue data:', e);
        return;
    }

    if (typeof window.Chart === 'undefined') return;

    // 1. Revenue & Orders Multi-line Chart
    const lineCanvas = document.getElementById('sellerRevenueChart');
    if (lineCanvas) {
        const lineCtx = lineCanvas.getContext('2d');
        if (lineCtx) {
            new window.Chart(lineCtx, {
                type: 'line',
                data: {
                    labels: config.chartLabels || [],
                    datasets: [
                        {
                            label: 'Doanh thu (₫)',
                            data: config.chartRevenues || [],
                            borderColor: '#ea384c',
                            backgroundColor: 'rgba(234, 56, 76, 0.08)',
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.35,
                            yAxisID: 'yRevenue',
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#ea384c',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                        },
                        {
                            label: 'Đơn hàng',
                            data: config.chartOrders || [],
                            borderColor: '#3b82f6',
                            backgroundColor: 'transparent',
                            borderWidth: 2,
                            borderDash: [4, 4],
                            tension: 0.35,
                            yAxisID: 'yOrders',
                            pointRadius: 3.5,
                            pointHoverRadius: 5,
                            pointBackgroundColor: '#3b82f6',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
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
                            padding: 10,
                            titleFont: { size: 12, weight: 'bold' },
                            bodyFont: { size: 11 },
                            callbacks: {
                                label: function(context) {
                                    if (context.datasetIndex === 0) {
                                        return 'Doanh thu: ' + new Intl.NumberFormat('vi-VN').format(context.raw) + '₫';
                                    }
                                    return 'Đơn hàng: ' + context.raw + ' đơn';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11, weight: '600' }, color: '#94a3b8' }
                        },
                        yRevenue: {
                            type: 'linear',
                            position: 'left',
                            grid: { color: '#f1f5f9' },
                            ticks: {
                                font: { size: 10 },
                                color: '#94a3b8',
                                callback: function(val) {
                                    return (val / 1000000).toFixed(1) + 'M';
                                }
                            }
                        },
                        yOrders: {
                            type: 'linear',
                            position: 'right',
                            grid: { display: false },
                            ticks: {
                                font: { size: 10 },
                                color: '#3b82f6',
                                stepSize: 2
                            }
                        }
                    }
                }
            });
        }
    }

    // 2. Donut Chart: Orders by Status
    const donutCanvas = document.getElementById('sellerOrderDoughnut');
    if (donutCanvas) {
        const donutCtx = donutCanvas.getContext('2d');
        if (donutCtx) {
            const completed = Number(config.completedOrders || 0);
            const processing = Number(config.processingOrders || 0) + Number(config.shippingOrders || 0);
            const pending = Number(config.pendingOrders || 0);
            const cancelled = Number(config.cancelledOrders || 0);
            const refunded = Number(config.refundedOrders || 0);

            const total = completed + processing + pending + cancelled + refunded;
            const dataCounts = total > 0 
                ? [completed, processing, pending, cancelled, refunded]
                : [18, 5, 3, 1, 1];

            new window.Chart(donutCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Đã giao', 'Đang vận chuyển', 'Chờ xác nhận', 'Đã hủy', 'Hoàn trả'],
                    datasets: [{
                        data: dataCounts,
                        backgroundColor: [
                            '#10b981',
                            '#3b82f6',
                            '#f59e0b',
                            '#f43f5e',
                            '#a855f7'
                        ],
                        borderWidth: 3,
                        borderColor: '#ffffff',
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.9)',
                            padding: 8,
                            callbacks: {
                                label: function(context) {
                                    return ` ${context.label}: ${context.raw} đơn`;
                                }
                            }
                        }
                    }
                }
            });
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    initLiveClock();
    initSellerRevenueCharts();
});

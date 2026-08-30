document.addEventListener("DOMContentLoaded", function () {

    const element = document.getElementById("kpi-dashboard-data");

    if (!element) {
        return;
    }

    // Veri etiketlerini oku (chart için kullanılacak)
    const labelCompleted = element.dataset.labelCompleted || "Completed";
    const labelOpen = element.dataset.labelOpen || "Open";
    const labelOverdue = element.dataset.labelOverdue || "Overdue";
    const labelDone = element.dataset.labelDone || "Done";
    const labelOngoing = element.dataset.labelOngoing || "Ongoing";
    const labelPending = element.dataset.labelPending || "Pending";
    const labelPlanned = element.dataset.labelPlanned || "Planned";
    const labelScheduled = element.dataset.labelScheduled || "Scheduled";
    const labelOverallTask = element.dataset.labelOverallTask || "Overall Task";
    const labelNoData = element.dataset.labelNoData || "No Data";

    const completed = parseInt(element.dataset.completed) || 0;
    const open = parseInt(element.dataset.open) || 0;
    const overdue = parseInt(element.dataset.overdue) || 0;
    const progress = parseInt(element.dataset.progress) || 0;

    const done = parseInt(element.dataset.done) || 0;
    const ongoing = parseInt(element.dataset.ongoing) || 0;
    const pending = parseInt(element.dataset.pending) || 0;
    const scheduled = parseInt(element.dataset.scheduled) || 0;
    const planned = parseInt(element.dataset.planned) || 0;

    const taskTrendLabel = JSON.parse(element.dataset.tasktrendlabel || '[]');
    const taskTrendData = JSON.parse(element.dataset.tasktrenddata || '[]');

    /* ==========================
       KPI STATUS CHART
    ========================== */

    const kpiCanvas = document.getElementById("kpiChart");

    if (kpiCanvas) {

        const total = done + ongoing + pending;

        const chartData = total === 0
            ? {
                labels: [labelNoData],
                datasets: [{
                    data: [1],
                    backgroundColor: ["#d6d6d6"],
                    borderWidth: 0
                }]
            }
            : {
                labels: [labelDone, labelOngoing, labelPending, labelPlanned, labelScheduled],
                datasets: [{
                    data: [done, ongoing, pending, planned, scheduled],
                    backgroundColor: [
                        "#43A047",
                        "#F2C94C",
                        "#E53935",
                        "#9E9E9E",
                        "#2196F3"
                    ],
                    borderColor: "#ffffff",
                    borderWidth: 2
                }]
            };

        new Chart(kpiCanvas, {
            type: "doughnut",
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: "65%",
                animation: { animateRotate: true, animateScale: true },
                plugins: {
                    legend: { position: "bottom" },
                    tooltip: { enabled: total > 0 }
                }
            }
        });
    }

    /* ==========================
       TASK STATUS CHART
    ========================== */

    const taskCanvas = document.getElementById("taskChart");

    if (taskCanvas) {

        const total = completed + open + overdue;

        const chartData = total === 0
            ? {
                labels: [labelNoData],
                datasets: [{
                    data: [1],
                    backgroundColor: ["#d6d6d6"],
                    borderWidth: 0
                }]
            }
            : {
                labels: [labelCompleted, labelOpen, labelOverdue],
                datasets: [{
                    data: [completed, open, overdue],
                    backgroundColor: ["#43A047", "#f2ff00", "#E53935"],
                    borderColor: "#ffffff",
                    borderWidth: 2
                }]
            };

        new Chart(taskCanvas, {
            type: "doughnut",
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: "65%",
                animation: { animateRotate: true, animateScale: true },
                plugins: {
                    legend: { position: "bottom" },
                    tooltip: { enabled: total > 0 }
                }
            }
        });
    }

    /* ==========================
       TASK TREND
    ========================== */

    const trendCanvas = document.getElementById("trendChart");

    if (trendCanvas) {
        new Chart(trendCanvas, {
            type: "line",
            data: {
                labels: taskTrendLabel,
                datasets: [{
                    label: labelOverallTask,
                    data: taskTrendData,
                    borderColor: "#1976D2",
                    backgroundColor: "rgba(25,118,210,.10)",
                    fill: true,
                    tension: 0.35,
                    pointRadius: 4,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: "index" },
                plugins: { legend: { position: "top" } },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: { callback: function(value) { return value + "%"; } }
                    }
                }
            }
        });
    }
});
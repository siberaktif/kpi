document.addEventListener("DOMContentLoaded", function () {

    const element = document.getElementById("kpi-dashboard-data");

    if (!element) {
        return;
    }

    const completed = parseInt(element.dataset.completed) || 0;
    const open = parseInt(element.dataset.open) || 0;
    const overdue = parseInt(element.dataset.overdue) || 0;
    const progress = parseInt(element.dataset.progress) || 0;

    const done = parseInt(element.dataset.done) || 0;
    const ongoing = parseInt(element.dataset.ongoing) || 0;
    const pending = parseInt(element.dataset.pending) || 0;
    const scheduled = parseInt(element.dataset.scheduled) || 0;
    const planned = parseInt(element.dataset.planned) || 0;

    const taskTrendLabel = JSON.parse(element.dataset.tasktrendlabel);
    const taskTrendData = JSON.parse(element.dataset.tasktrenddata);

    // console.log(taskTrendLabel);
    // console.log(taskTrendData);


    /* ==========================
       KPI STATUS CHART
    ========================== */

    const kpiCanvas = document.getElementById("kpiChart");

    if (kpiCanvas) {

        const total = done + ongoing + pending;

        const chartData = total === 0
            ? {
                labels: ["No Data"],
                datasets: [{
                    data: [1],
                    backgroundColor: ["#d6d6d6"],
                    borderWidth: 0
                }]
            }
            : {
                labels: ["Done", "Ongoing", "Pending", "Planned", "Scheduled"],
                datasets: [{
                    data: [done, ongoing, pending, planned, scheduled],
                    backgroundColor: [
                        "#43A047", // DONE - green
                        "#F2C94C", // ONGOING - yellow
                        "#E53935", // PENDING - red
                        "#9E9E9E", // PLANNED - gray
                        "#2196F3"  // SCHEDULED - blue
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

                animation: {
                    animateRotate: true,
                    animateScale: true
                },

                plugins: {

                    legend: {
                        position: "bottom"
                    },

                    tooltip: {
                        enabled: total > 0
                    }

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
                labels: ["No Data"],
                datasets: [{
                    data: [1],
                    backgroundColor: ["#d6d6d6"],
                    borderWidth: 0
                }]
            }
            : {
                labels: ["Completed", "Open", "Overdue"],
                datasets: [{
                    data: [completed, open, overdue],
                    backgroundColor: [
                        "#43A047",
                        "#f2ff00",
                        "#E53935"
                    ],
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

                animation: {
                    animateRotate: true,
                    animateScale: true
                },

                plugins: {

                    legend: {
                        position: "bottom"
                    },

                    tooltip: {
                        enabled: total > 0
                    }

                }

            }

        });

    }

    /* ==========================
       TASK TREND
    ========================== */

    const trendCanvas = document.getElementById("trendChart");

    if (trendCanvas) {

        const lastValue = progress === 0 ? 89 : progress;

        new Chart(trendCanvas, {

            type: "line",

            data: {
                labels: taskTrendLabel,

                datasets: [{
                    label: "Overall Task",

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

                interaction: {
                    intersect: false,
                    mode: "index"
                },

                plugins: {

                    legend: {
                        position: "top"
                    }

                },

                scales: {

                    y: {

                        beginAtZero: true,

                        max: 100,

                        ticks: {
                            callback: function(value) {
                                return value + "%";
                            }
                        }

                    }

                }

            }

        });

    }

});

const projectSwitcher = document.getElementById("projectSwitcher");

if(projectSwitcher){

    projectSwitcher.addEventListener("change",function(){

        window.location =
            "?controller=KPIController"
            +"&action=project"
            +"&plugin=KPI"
            +"&project_id="
            +this.value;

    });

}
// attendance.js

// Function to initialize the chart with the provided data
function initAttendanceChart(daysData, weeksData, monthsData, yearsData, 
                           daysCounts, weeksCounts, monthsCounts, yearsCounts) {
    // Initialize the chart with day data by default
    const ctx = document.getElementById('visitorsChart').getContext('2d');
    window.visitorsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: daysData,
            datasets: [{
                label: 'Number of Visitors',
                data: daysCounts,
                backgroundColor: 'rgba(52, 152, 219, 0.7)',
                borderColor: 'rgba(52, 152, 219, 1)',
                borderWidth: 1,
                borderRadius: 5,
                hoverBackgroundColor: 'rgba(52, 152, 219, 0.9)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Visitors by Day (Last 7 Days)',
                    font: {
                        size: 16
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.7)',
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 14
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        font: {
                            size: 12
                        }
                    },
                    title: {
                        display: true,
                        text: 'Number of Visitors',
                        font: {
                            size: 14,
                            weight: 'bold'
                        }
                    }
                },
                x: {
                    ticks: {
                        font: {
                            size: 12
                        }
                    },
                    title: {
                        display: true,
                        text: 'Days',
                        font: {
                            size: 14,
                            weight: 'bold'
                        }
                    }
                }
            }
        }
    });

    // Store the data for later use
    window.chartData = {
        daysData: daysData,
        weeksData: weeksData,
        monthsData: monthsData,
        yearsData: yearsData,
        daysCounts: daysCounts,
        weeksCounts: weeksCounts,
        monthsCounts: monthsCounts,
        yearsCounts: yearsCounts
    };
}

// Function to update the chart based on the selected grouping
function updateChart() {
    const groupBy = document.getElementById('groupBy').value;
    let labels = [];
    let data = [];
    let title = '';
    let xAxisTitle = '';

    switch (groupBy) {
        case 'day':
            labels = window.chartData.daysData;
            data = window.chartData.daysCounts;
            title = 'Visitors by Day (Last 7 Days)';
            xAxisTitle = 'Days';
            break;
        case 'week':
            labels = window.chartData.weeksData;
            data = window.chartData.weeksCounts;
            title = 'Visitors by Week';
            xAxisTitle = 'Weeks';
            break;
        case 'month':
            labels = window.chartData.monthsData;
            data = window.chartData.monthsCounts;
            title = 'Visitors by Month';
            xAxisTitle = 'Months';
            break;
        case 'year':
            labels = window.chartData.yearsData;
            data = window.chartData.yearsCounts;
            title = 'Visitors by Year';
            xAxisTitle = 'Years';
            break;
    }

    // Check if data is empty
    if (data.length === 0) {
        alert('No data available for the selected grouping.');
        return;
    }

    // Update chart
    window.visitorsChart.data.labels = labels;
    window.visitorsChart.data.datasets[0].data = data;
    window.visitorsChart.options.plugins.title.text = title;
    window.visitorsChart.options.scales.x.title.text = xAxisTitle;
    window.visitorsChart.update();
}

// Function to confirm actions (for other pages)
function confirmAction(message) {
    return confirm(message);
}

// Function to validate forms
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;
    
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            field.style.borderColor = '#e74c3c';
        } else {
            field.style.borderColor = '';
        }
    });
    
    return isValid;
}

// Initialize when document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners to forms for validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(form.id)) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    });
    
    // Add datepicker functionality if needed
    const dateInputs = document.querySelectorAll('input[type="date"]');
    if (dateInputs.length > 0) {
        // Set today's date as default for date inputs
        const today = new Date().toISOString().split('T')[0];
        dateInputs.forEach(input => {
            if (!input.value) {
                input.value = today;
            }
        });
    }
});
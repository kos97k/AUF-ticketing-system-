document.addEventListener('DOMContentLoaded', function () {
    const ticketsData = window.ticketsData; // Ensure the tickets data is passed to the global scope

    if (!ticketsData || ticketsData.length === 0) {
        console.error('No ticket data available');
        return;
    }

    // Debugging: Check parsed data
    console.log('Tickets Data:', ticketsData);

    // Process data
    const statusCounts = ticketsData.reduce((counts, ticket) => {
        counts[ticket.status] = (counts[ticket.status] || 0) + 1;
        return counts;
    }, {});

    const priorityCounts = ticketsData.reduce((counts, ticket) => {
        counts[ticket.priority] = (counts[ticket.priority] || 0) + 1;
        return counts;
    }, {});

    // Chart data preparation
    const statusLabels = Object.keys(statusCounts).map(status => `Status: ${status}`);
    const statusData = Object.values(statusCounts);
    const statusColors = {
        'open': '#007bff',    // Blue for Open
        'closed': '#dc3545',  // Red for Closed
        'pending': '#ffc107', // Yellow for Pending
        'solved': '#28a745'   // Green for Solved
    };
    const statusBackgroundColors = Object.keys(statusCounts).map(status => statusColors[status.toLowerCase()] || '#6c757d');

    const priorityLabels = Object.keys(priorityCounts).map(priority => `Priority: ${priority}`);
    const priorityData = Object.values(priorityCounts);
    const priorityColors = {
        'low': '#28a745',     // Green for Low
        'medium': '#ffc107',  // Yellow for Medium
        'high': '#dc3545'     // Red for High
    };
    const priorityBackgroundColors = Object.keys(priorityCounts).map(priority => priorityColors[priority.toLowerCase()] || '#6c757d');

    // Render Status Chart
    const ctxStatus = document.getElementById('statusChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'pie',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusData,
                backgroundColor: statusBackgroundColors,
                borderColor: '#fff',
                borderWidth: 1,
            }],
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true, position: 'bottom' },
            },
        },
    });

    // Render Priority Chart
    const ctxPriority = document.getElementById('priorityChart').getContext('2d');
    new Chart(ctxPriority, {
        type: 'pie',
        data: {
            labels: priorityLabels,
            datasets: [{
                data: priorityData,
                backgroundColor: priorityBackgroundColors,
                borderColor: '#fff',
                borderWidth: 1,
            }],
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true, position: 'bottom' },
            },
        },
    });
});

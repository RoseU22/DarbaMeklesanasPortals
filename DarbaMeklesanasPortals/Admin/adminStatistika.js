document.addEventListener("DOMContentLoaded", () => {
    const labels = [];
    for (let i = 6; i >= 0; i--) {
        const d = new Date();
        d.setDate(d.getDate() - i);
        labels.push(`${d.getDate()}.${d.getMonth() + 1}`);
    }

    const klientsValues = new Array(6).fill(0);
    klientsValues.push(window.totalKlients || 0);

    const uznemumsValues = new Array(6).fill(0);
    uznemumsValues.push(window.totalUznemums || 0);

    const ctx = document.getElementById('userChart').getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Klienti',
                    data: klientsValues,
                    borderColor: '#007BFF',             
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 5,
                    pointBackgroundColor: '#007BFF'    
                },
                {
                    label: 'Uzņēmumi',
                    data: uznemumsValues,
                    borderColor: '#DC3545',             
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 5,
                    pointBackgroundColor: '#DC3545'   
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: 'var(--text-color)',
                        precision: 0
                    },
                    title: {
                        display: true,
                        text: 'Lietotāju skaits',
                        color: 'var(--text-color)',
                        font: { weight: 'bold', size: 16 }
                    }
                },
                x: {
                    ticks: {
                        color: 'var(--text-color)',
                        maxRotation: 45,
                        minRotation: 30,
                        maxTicksLimit: 7
                    },
                    title: {
                        display: true,
                        text: 'Datums',
                        color: 'var(--text-color)',
                        font: { weight: 'bold', size: 16 }
                    }
                }
            },
            plugins: {
                legend: {
                    labels: { color: 'var(--text-color)' }
                },
                tooltip: { enabled: true, mode: 'nearest' }
            }
        }
    });
});

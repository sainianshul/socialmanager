{{-- ApexCharts for Social Activity Trends --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const categories = {!! json_encode($chartDates ?? []) !!};
    const postSeries = {!! json_encode($postCounts ?? []) !!};
    const commentSeries = {!! json_encode($commentCounts ?? []) !!};

    const isDarkMode = function() {
        return (document.documentElement.getAttribute('data-bs-theme') === 'dark' || document.body.getAttribute('data-bs-theme') === 'dark');
    };

    let dark = isDarkMode();

    const options = {
        chart: {
            type: "area",
            fontFamily: 'inherit',
            height: 290,
            parentHeightOffset: 0,
            toolbar: { show: false },
            animations: { enabled: true },
        },
        dataLabels: { enabled: false },
        stroke: {
            width: [2.5, 2.5],
            curve: "smooth",
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.25,
                opacityTo: 0.05,
                stops: [0, 95, 100]
            }
        },
        series: [
            {
                name: "Posts Published",
                data: postSeries
            },
            {
                name: "Comments Synced",
                data: commentSeries
            }
        ],
        colors: ["#1db8ab", "#0284c7"],
        xaxis: {
            categories: categories,
            labels: {
                style: { colors: dark ? '#8896ab' : '#64748b', fontSize: '12px' }
            },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            tickAmount: 5,
            labels: {
                style: { colors: dark ? '#8896ab' : '#64748b', fontSize: '12px' }
            }
        },
        grid: {
            strokeDashArray: 3,
            borderColor: dark ? 'rgba(255, 255, 255, 0.08)' : '#f1f5f9',
            padding: { top: -20, right: 0, left: -4, bottom: -4 }
        },
        legend: { show: false },
        tooltip: { theme: dark ? 'dark' : 'light' }
    };

    const chartEl = document.querySelector("#chart-social-activity");
    if (chartEl) {
        const chart = new ApexCharts(chartEl, options);
        chart.render();

        window.addEventListener('themeChanged', function (e) {
            const nextDark = (e.detail?.theme === 'dark');
            chart.updateOptions({
                xaxis: { labels: { style: { colors: nextDark ? '#8896ab' : '#64748b' } } },
                yaxis: { labels: { style: { colors: nextDark ? '#8896ab' : '#64748b' } } },
                grid: { borderColor: nextDark ? 'rgba(255, 255, 255, 0.08)' : '#f1f5f9' },
                tooltip: { theme: nextDark ? 'dark' : 'light' }
            });
        });
    }
});
</script>

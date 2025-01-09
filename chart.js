// Asynchronous function to fetch data and render a cryptocurrency chart
async function renderChart() {
    // Fetch Bitcoin market chart data for the last 7 days from the CoinGecko API
    const response = await fetch('https://api.coingecko.com/api/v3/coins/bitcoin/market_chart?vs_currency=usd&days=7');
    
    // Parse the response into JSON format
    const data = await response.json();

    // Extract Bitcoin prices from the data (2nd element in each array)
    const prices = data.prices.map(item => item[1]);

    // Extract and format the dates from the data (1st element in each array)
    const dates = data.prices.map(item => new Date(item[0]).toLocaleDateString());

    // Get the canvas element's 2D context where the chart will be rendered
    const ctx = document.getElementById('cryptoChart').getContext('2d');

    // Create a new line chart using Chart.js
    new Chart(ctx, {
        type: 'line', // Specify the chart type as a line chart
        data: {
            labels: dates, // Set the x-axis labels as the extracted dates
            datasets: [{
                label: 'Bitcoin Price (USD)', // Label for the dataset
                data: prices, // Bitcoin prices as the y-axis data
                borderColor: 'rgba(75, 192, 192, 1)', // Line color
                backgroundColor: 'rgba(75, 192, 192, 0.2)', // Fill color below the line
                borderWidth: 2 // Thickness of the line
            }]
        },
        options: {
            responsive: true, // Ensure the chart adjusts to the container size
            plugins: {
                legend: { display: true }, // Show the legend
                tooltip: { enabled: true } // Enable tooltips on hover
            }
        }
    });
}

// Call the function to fetch the data and render the chart
renderChart();


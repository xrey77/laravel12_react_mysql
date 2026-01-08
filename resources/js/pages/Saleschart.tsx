import React, { useEffect, useState } from 'react';
import { 
  Chart as ChartJS, 
  CategoryScale, 
  LinearScale, 
  BarElement, 
  Title, 
  Tooltip, 
  Legend 
} from 'chart.js';
import { Bar } from 'react-chartjs-2';
import { ChartData } from 'chart.js';
import axios from 'axios';

const api = axios.create({
  baseURL: "http://localhost:8000",
  headers: {'Accept': 'application/json',
            'Content-Type': 'application/json'}
})

ChartJS.register(
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend
);

const options = {
  responsive: true,
  plugins: {
    legend: { position: 'top' as const },
    title: { display: true, text: 'Diebold-Nixdorf' },
  },
};

const labels = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV','DEC'];

const data = {
  labels,
  datasets: [
    {
      label: 'Dataset 1',
      data: [10, 20, 30, 40, 50],
      backgroundColor: 'rgba(255, 99, 132, 0.5)',
    },
  ],
};

interface SalesData {
  date: string;
  amount: number;
}

export default function Saleschart() {

  const [chartData, setChartData] = useState<ChartData<'bar'>>({
    labels: [],
    datasets: [],
  });
    
  const fetchSales = async () => {
    try {
      const res = await api.get<SalesData[]>("/api/chartdata");
      const apiData = res.data;
  
      setChartData({
        labels: labels,  //apiData.map(item => item.date)
        datasets: [
          {
            label: 'Sales Amount',
            data: apiData.map(item => item.amount),
            backgroundColor: 'rgba(255, 99, 132, 0.5)',
          },
        ],
      });
    } catch (error: any) {
      console.error("Error fetching data:", error.response?.data?.message || error.message);
    }
  };


  useEffect(() => {  
    fetchSales();
  },[])

  return (
    <div>
      {chartData.datasets.length > 0 ? (
        <Bar options={options} data={chartData} />
      ) : (
        <p>Loading chart data...</p>
      )}
  </div>    
  )
}
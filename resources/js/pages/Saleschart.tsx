import React, { useEffect, useRef, useState } from 'react';
import { useReactToPrint } from 'react-to-print';
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
import { viewport } from '@popperjs/core';

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

const logo = new Image();
logo.src = '/images/logo.png';

const logoPlugin = {
  id: 'logoPlugin',
  beforeDraw: (chart: any) => {
    if (logo.complete) {
      const { ctx, width } = chart;
      const logoWidth = 100;
      const logoHeight = 30;
      const x = (width - logoWidth) / 2; 
      const y = 10; 
      
      ctx.drawImage(logo, x, y, logoWidth, logoHeight);
    } else {
      logo.onload = () => chart.draw();
    }
  }
};

const options = {
  responsive: true,
  layout: {
    padding: {
      top: 40 
    }
  },
  plugins: {
    legend: { position: 'top' },
    title: { 
      display: true,
      text: 'Diebold-Nixdorf',
      padding: {
        top: 10, 
        bottom: 5
      },
      font: {
        size: 24,
        family: 'Arial',
        weight: 'bold',
      }
    },
  },
};

interface SalesData {
  date: string;
  amount: number;
}

export default function Saleschart() {
  // 1. Ensure the ref is typed for an HTMLDivElement
  const chartRef = useRef<HTMLDivElement>(null);

  const handlePrint = useReactToPrint({
    // 2. Point to the ref attached to the wrapper
    contentRef: chartRef,
    documentTitle: "Sales Chart Report",
  });

  const [chartData, setChartData] = useState<ChartData<'bar'>>({
    labels: [],
    datasets: [],
  });
    
  const fetchSales = async () => {
    try {
      const res = await api.get<SalesData[]>("/api/chartdata");
      const apiData = res.data;
  
      setChartData({
        labels: apiData.map(item => 
          new Date(item.date).toLocaleString('en-US', { month: 'short' })
        ),        
        datasets: [{
            label: 'Sales Amount',
            data: apiData.map(item => item.amount),
            backgroundColor: 'rgba(60, 179, 113)',
        }],
      });
    } catch (error: any) {
      console.error("Error:", error.message);
    }
  };

  useEffect(() => {  
    fetchSales();
  },[]);

  return (
<div className='container'>
  {/* This header is only visible during print */}
  <div className="print-header">
    <h1>Sale Report</h1>
    <p>Generated on: {new Date().toLocaleDateString()}</p>
  </div>

  <div ref={chartRef} style={{ padding: '20px' }}>
    {chartData.datasets.length > 0 ? (
      <Bar options={options} data={chartData} plugins={[logoPlugin]} />
    ) : (
      <p>Loading chart data...</p>
    )}
  </div>
  
  <button onClick={() => handlePrint()}>Print Chart</button>

  <style>{`
    /* Hide the header by default in the browser */
    .print-header {
      display: none;
      text-align: center;
      margin-bottom: 20px;
    }

    @media print {
      @page {
        margin-top: 50px; 
      }

      /* Show the header only when printing */
      .print-header {
        display: block;
      }

      .container { 
        margin: 0; 
      }

      /* Hide the print button so it doesn't appear on paper */
      button {
        display: none;
      }

      canvas { 
        max-width: 100% !important; 
        height: auto !important; 
      }
    }      
  `}</style>
</div>


    // <div className='container'>
    //   <div ref={chartRef} style={{ padding: '20px' }}>
    //     {chartData.datasets.length > 0 ? (
    //       <Bar options={options} data={chartData} plugins={[logoPlugin]} />
    //     ) : (
    //       <p>Loading chart data...</p>
    //     )}
    //   </div>
      
    //   <button onClick={() => handlePrint()}>Print Chart</button>

    //   <style>{`
    //     @media print {
    //       @page {
    //         /* This pushes everything down from the top of the paper */
    //         margin-top: 100px; 
    //       }
    //       .container { 
    //         margin: 0; 
    //       }
    //       canvas { 
    //         max-width: 100% !important; 
    //         height: auto !important; 
    //       }
    //     }      
    //   `}</style>
    // </div>    
  );
}

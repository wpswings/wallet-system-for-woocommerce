import './App.css';
import React from 'react';
import {useState} from 'react';
import { Button} from '@material-ui/core';
import axios from 'axios';
import qs from 'qs';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer } from 'recharts';

const ReportingSystem = () => {
  const [chartdata, setChartData] = useState( 
    [
    { name: 'Debit', amount: parseInt(frontend_ajax_object.user_data_debit) },
    { name: 'Credit', amount: parseInt(frontend_ajax_object.user_data_credit) },
    { name: 'Current',  amount: parseInt(frontend_ajax_object.user_data_current) },
  
    ]
  );



 
  const [fromDate, setFromDate] = useState(null);
  const [toDate, setToDate] = useState(null);
 

  const handleFromDateChange = (e) => {
    setFromDate(e.target.value);
  };

 

  const handleToDateChange = (e) => {
    setToDate(e.target.value);
  };

  const handleFormSubmit = (e) => {
    e.preventDefault();
    var user_id=jQuery('#report_userid').val();
    const user = {  
       'user_id':user_id,
        'fromdate': fromDate,
        'toDate': toDate,
        'action': 'wps_wsfw_filter_chart_data',
        nonce: frontend_ajax_object.wps_standard_nonce,   // pass the nonce here
    };
    
    axios.post(frontend_ajax_object.ajaxurl, qs.stringify(user) )
        .then(res => {
            const data = [

             
              { name: 'Credit', amount:res.data.data.credit },
              { name: 'Debit', amount: res.data.data.debit },
              { name: 'Current',  amount: res.data.data.current_amount },
              
              // Add more data points as needed
            ];
            setChartData(data);
           
           
        }).catch(error=>{
            console.log(error);
    })
    
}


  return (
    <div>


      <label htmlFor="fromDate">From Date:</label>
      <input
        type="date"
        id="fromDate"
        value={fromDate}
        onChange={handleFromDateChange}
      />

      <label htmlFor="toDate">To Date:</label>
      <input
        type="date"
        id="toDate"
        value={toDate}
        onChange={handleToDateChange}
      />
     
       <Button
          onClick={handleFormSubmit}
          variant="contained" color="primary" size="large">
          Search
      </Button>

    

      
      <ResponsiveContainer width="100%" height={400}>
        <BarChart data={chartdata}>
          <CartesianGrid strokeDasharray="3 3" />
          <XAxis dataKey="name" />
          <YAxis />
          <Tooltip />
          <Legend />
          <Bar dataKey="amount" fill="#8884d8" />
        </BarChart>
      </ResponsiveContainer>
    </div>
  );
};

export default ReportingSystem;






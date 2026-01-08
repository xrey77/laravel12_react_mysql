import React from 'react';
import { useState } from 'react';
import axios from 'axios';
import Footer from '../components/Footer';

const api = axios.create({
  baseURL: "http://localhost:8000",
  headers: {'Accept': 'application/json',
            'Content-Type': 'application/json'}
})

const toDecimal = (number: any) => {
  const formatter = new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2, 
    maximumFractionDigits: 2,
  });
  return formatter.format(number);
};

export default function Prodsearch() {
  const [prodsearch, setProdsearch] = useState<any[]>([]);
  const [message, setMessage] = useState<string>('');
  let [searchkey, setSearchkey] = useState<string>('');

  const getProdsearch = (event: any) => {
      event.preventDefault();
      setMessage("please wait .");
      api.get(`/api/productsearch/${searchkey}`)
      .then((res: any) => {
          setProdsearch(res.data.products);
          setTimeout(() => {
            setMessage('');
        }, 1000);
      }, (error: any) => {
          if (error.response) {
            setMessage(error.response.data.message);
          } else {
            setMessage(error.message);            
          }
          setTimeout(() => {
              setMessage('');
              setProdsearch([]);
          }, 3000);
          return;
      });  
  }
   
return (
  <div className="container mb-9">
      <h2 className='text-success embossed mt-2'>Products Search</h2>
      <form onSubmit={getProdsearch} className="row g-3" autoComplete='off'>
          <div className="col-auto">
            <input type="text" required className="form-control-sm" value={searchkey} onChange={e => setSearchkey(e.target.value)} placeholder="enter Product keyword"/>
            <div className='searcMsg text-danger'>{message}</div>
          </div>
          <div className="col-auto">
            <button type="submit" className="btn btn-primary btn-sm mb-3">search</button>
          </div>
      </form>
      <div className="container mb-9">
        <div className="card-group">
      {prodsearch.map((item) => {
              return (
              <div className='col-md-4'>
              <div key={item.id} className="card mx-3 mt-3">
                  <img src={item['productpicture']} className="card-img-top product-size" alt=""/>
                  <div className="card-body">
                    <h5 className="card-title">Descriptions</h5>
                    <p className="card-text desc-h">{item['descriptions']}</p>
                  </div>
                  <div className="card-footer">
                    <p className="card-text text-danger"><span className="text-dark">PRICE :</span>&nbsp;<strong>&#8369;{toDecimal(item['sellprice'])}</strong></p>
                  </div>  
              </div>
              
              </div>
        );    
      })}
        </div>          
        <br/><br/><br/>
      </div>
  {prodsearch.length > 4 ? (
      <Footer/>
  ) : (
    <div className='fixed-bottom'>
      <Footer/>
    </div>

  )}      
  </div>  
  )
}

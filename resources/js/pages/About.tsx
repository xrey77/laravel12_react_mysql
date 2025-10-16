import React from 'react';
import Footer from '../components/Footer';

export default function About() {
  return (
    <>
    <div className='container'>
    <div className='card'>
      <div className='card-body'>
        <img className='about' src="/images/about.png" alt=""/>
        <p className='card-text'>
          <h2>About Us</h2>
          <p className='about-text'>
          Diebold Nixdorf is a technology company that provides integrated self-service solutions, software, and services for the banking and retail industries, aiming to connect digital and physical channels for consumers. It is an innovation partner for many of the world's largest financial institutions and retailers, offering solutions like ATMs, cash recyclers, and software for in-store and online payments and customer engagement.  
          <p><br/>
          <strong>Focus:</strong>
          The company specializes in automating, digitizing, and transforming the customer experience in banking and retail through self-service technology. 
          </p>
          <p>
          <strong>Solutions:</strong>
        Its portfolio includes the DN Series of ATMs and terminals, the Vynamic software suite, and various services for branch automation, cash management, and payments. 
          </p>
          <p>
          <strong>Global reach:</strong>
          Diebold Nixdorf operates in over 100 countries and serves a majority of the world's top financial institutions and retailers. 
          </p>
          <p>
          <strong>Services:</strong>
          In addition to hardware and software, the company provides a range of services including implementation, maintenance, managed services, and advisory services. 

          </p>
          </p>
        </p>
      </div>
    </div>
    <Footer/>
    </div>
    </>
  )
}

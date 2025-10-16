import React from 'react';
import Footer from '../components/Footer';

export default function home() {
  return (
    <>
<div id="carouselExampleCaptions" className="carousel slide">
  <div className="carousel-indicators">
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" className="active" aria-current="true" aria-label="Slide 1"></button>
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="3" aria-label="Slide 4"></button>
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="4" aria-label="Slide 5"></button>

  </div>
  <div className="carousel-inner">
    <div className="carousel-item active">
      <img src="/images/4.png" className="d-block w-100" alt="..."/>
      <div className="carousel-caption d-none d-md-block">
        <h1 className='mb-5 embossed'>Cash Out Self-Service Terminal</h1>
      </div>
    </div>
    <div className="carousel-item">
      <img src="/images/2.png" className="d-block w-100" alt="..."/>
      <div className="carousel-caption d-none d-md-block">
        <h1 className='mb-5 embossed'>Second slide label</h1>
      </div>
    </div>
    <div className="carousel-item">
      <img src="/images/3.png" className="d-block w-100" alt="..."/>
      <div className="carousel-caption d-none d-md-block">
        <h1 className='mb-5 embossed'>Third slide label</h1>
      </div>
    </div>

    <div className="carousel-item">
      <img src="/images/1.png" className="d-block w-100" alt="..."/>
      <div className="carousel-caption d-none d-md-block">
        <h1 className='mb-5 embossed'>Third slide label</h1>
      </div>
    </div>
    <div className="carousel-item">
      <img src="/images/5.png" className="d-block w-100" alt="..."/>
      <div className="carousel-caption d-none d-md-block">
        <h1 className='mb-5 embossed'>Third slide label</h1>
      </div>
    </div>

  </div>
  <button className="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
    <span className="carousel-control-prev-icon" aria-hidden="true"></span>
    <span className="visually-hidden">Previous</span>
  </button>
  <button className="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
    <span className="carousel-control-next-icon" aria-hidden="true"></span>
    <span className="visually-hidden">Next</span>
  </button>
</div>    
<div className='card'>
    <div className='card-body'>
      <h4>Accelerate Your Banking Transformation</h4>
      <p className='home-text'>
      Successfully transform the way people bank with self-service banking solutions that put your customers at the center of everything you do. We are focused on banking products, software and services that engage consumers across banking channels, create personalized banking experiences, accelerate ATM performance and empower your organization to develop operating strategies that harness new opportunities.        
      </p>
      <h4>Create Seamless Experiences, Grow Revenue and Optimize Operational Efficiencies.</h4>
      <p className='home-text'>
      The ATM is a critical channel in a modern connected banking journey. Our end-to-end self-service banking solutions deliver engaging, consistent, research-based design coupled with powerful software, security and services that enable financial institutions to personally connect with consumers at the intersection of physical and digital banking channels.        
      </p>
      <h4>The Results: Innovation that Drives Member Value</h4>
      <p className='home-text'>
      With the Vynamic software portfolio, AFCU has set the foundation for future innovation, including digital receipts, interactive video tellers (IVTs) and cash recycling. This ongoing partnership ensures AFCU’s ability to meet the evolving needs of its members while staying ahead in an increasingly digital banking landscape.

Ready to learn more? Download the full case study now and explore how the credit union is leveraging technology and security to better serve its members.        
      </p>
    </div>
</div>
<Footer/>
    </>
  )
}

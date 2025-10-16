import React from 'react';
import Footer from '../components/Footer';

export default function Contact() {
  return (
    <div className='container'>
    <div className='card'>
      <div className='card-body'>
        <img className='contact' src="/images/contact.png" alt=""/>
        <p>
          <h2>Contact Us</h2><br/>
          <div className='row'>
            <div className='col'>
              <h6>Customer Care & Support</h6>
              <p className='contact-text'>Call us 24/7 at: +1 800-343-2653</p>
              <p className='contact-text'>Banking/General: +49(0) 5251 2020 850</p>
            </div>

            <div className='col'>
              <h6>Global Contact Numbers</h6>
              <p className='contact-text'>Americas +1 330-490-4000</p>
              <p className='contact-text'>Europe, Middle East & Africa +49 5251 69330</p>
            </div>

            <div className='col'>
              <h6>Media Requests</h6>
              <p className='contact-text'>
              For news or interview requests, contact a member of our global media relations team.                
              </p>
            </div>

            <div className='col'>
              <h6>Diebold Nixdorf Careers</h6>
              <p className='contact-text'>
              Interested in a career with Diebold Nixdorf? Check out our current openings from around the world.                
              </p>
            </div>

          </div>
        </p>
      </div>
    </div>
    <Footer/>
    </div>
  )
}

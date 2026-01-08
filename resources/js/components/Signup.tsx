import React, { useState } from 'react'
import jQuery from 'jquery';
import axios from 'axios';

const api = axios.create({
  baseURL: "http://localhost:8000",
  headers: {'Accept': 'application/json',
            'Content-Type': 'application/json'}
})

export default function Signup() {
  const [firstname, setFirstname] = useState('');
  const [lastname, setLastname] = useState('');
  const [email, setEmail] = useState('');
  const [mobile, setMobile] = useState('');
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [message, setMessage] = useState('');

  const submitRegistration = (event: any) => {
    event.preventDefault();
    const data =JSON.stringify({ lastname: lastname, firstname: firstname,email: email, mobile: mobile,
      username: username, password: password });
    api.post("/api/register", data)
    .then((res: any) => {
          setMessage(res.data.message);
          window.setTimeout(() => {
            setMessage('');
          }, 3000);
      }, (error: any) => {
            if (error.response) {
              setMessage(error.response.data.message);
            } else {
              setMessage(error.message);
            }
            window.setTimeout(() => {
              setMessage('');
            }, 3000);
    });
    jQuery("#registerReset").trigger('click');
  }

  const closeRegistration = () => {
    setMessage('');
    setFirstname('')
    setLastname('')
    setPassword('')
    setEmail('')
    setUsername('')
    setMobile('')
    jQuery("#registerReset").trigger('click');
  }  

  return (
<div className="modal fade" id="staticRegister" data-bs-backdrop="static" data-bs-keyboard="false" tabIndex={-1} aria-labelledby="staticRegisterLabel" aria-hidden="true">
  <div className="modal-dialog modal-dialog-centered">
    <div className="modal-content">
      <div className="modal-header bg-info">
        <h1 className="modal-title fs-5" id="staticBackdropLabel">Account Registration</h1>
        <button onClick={closeRegistration} type="button" className="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div className="modal-body">
        <form onSubmit={submitRegistration} autoComplete='off'>
            <div className='row'>
                <div className='col'>
                  <div className="mb-3">
                    <input type="text" required value={firstname} onChange={e => setFirstname(e.target.value)} className="form-control border-info" id="fname" placeholder="enter First Name"/>
                  </div>            
                </div>
                <div className='col'>
                  <div className="mb-3">
                    <input type="text" required value={lastname} onChange={e => setLastname(e.target.value)} className="form-control border-info" id="lname" placeholder="enter Last Name"/>
                  </div>            
                </div>
            </div>

            <div className='row'>
                <div className='col'>
                  <div className="mb-3">
                    <input type="email" required value={email} onChange={e => setEmail(e.target.value)} className="form-control border-info" id="email" placeholder="enter Email Address"/>
                  </div>            
                </div>
                <div className='col'>
                  <div className="mb-3">
                    <input type="text" required value={mobile} onChange={e => setMobile(e.target.value)} className="form-control border-info" id="mobile" placeholder="enter Mobile No."/>
                  </div>            
                </div>
            </div>

            <div className='row'>
                <div className='col'>
                  <div className="mb-3">
                    <input type="text" required value={username} onChange={e => setUsername(e.target.value)} className="form-control border-info" id="uname" placeholder="enter Username"/>
                  </div>            
                </div>
                <div className='col'>
                  <div className="mb-3">
                    <input type="password" required value={password} onChange={e => setPassword(e.target.value)} className="form-control border-info" id="pword" placeholder="enter Password"/>
                  </div>            
                </div>
            </div>
            <button type="submit" className="btn btn-info mx-1">signup</button>
            <button id="registerReset" type="reset" className="btn btn-info">reset</button>
        </form>        
      </div>
      <div className="modal-footer">
        <div className='w-100 text-center text-danger'>{message}</div>
      </div>
    </div>
  </div>
</div>

  )
}


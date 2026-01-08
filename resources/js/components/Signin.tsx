import React, { useState } from 'react'
import axios from 'axios';
import jQuery from 'jquery';
import Mfa from './Mfa';

const api = axios.create({
  baseURL: "http://localhost:8000",
  headers: {'Accept': 'application/json',
            'Content-Type': 'application/json'}
})

export default function Signin() {
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [message, setMessage] = useState('');
  const [isdisable, setIsdisable] = useState(false);

  const submitLogindata = (event: any) => {
    event.preventDefault();
    setMessage('please wait...');
    setIsdisable(true);
    const data =JSON.stringify({ username: username, password: password });
    api.post("/api/login", data)
    .then((res: any) => {
            setMessage(res.data.message);
            if (res.data.qrcodeurl !== null) {
                window.sessionStorage.setItem('USERID',res.data.id);
                window.sessionStorage.setItem('TOKEN',res.data.token);
                window.sessionStorage.setItem('ROLE',res.data.roles);
                window.sessionStorage.setItem('USERPIC',res.data.profilepic);
                setTimeout(() => {
                  jQuery("#loginReset").trigger('click');
                  setMessage('');
                  setIsdisable(false);
                  jQuery("#mfa").trigger('click');
                }, 3000);
            } else {
                window.sessionStorage.setItem('USERID',res.data.id);
                window.sessionStorage.setItem('USERNAME',res.data.username);
                window.sessionStorage.setItem('TOKEN',res.data.token);                        
                window.sessionStorage.setItem('ROLE',res.data.roles);
                window.sessionStorage.setItem('USERPIC',res.data.profilepic);
                // jQuery("#loginReset").trigger('click');
                closeLogin();
                setTimeout(() => {
                  setMessage('');
                  setIsdisable(false);
                }, 3000);
                return;
    
            }
      }, (error: any) => {
            if (error.response) {
              setMessage(error.response.data.message);
            } else {
              setMessage(error.message);
            }
            setTimeout(() => {
              setMessage('');
              setIsdisable(false);
            }, 3000);
            return;
    });
  }

  const closeLogin = () => {
    setMessage('');
    setUsername('');
    setPassword('');
    jQuery("#loginReset").trigger('click');
    window.location.reload();
  }

  const resetLogin = () => {
    setUsername('');
    setPassword('');
    setMessage('');
  }

  return (
    <>
<div className="modal fade" id="staticLogin" data-bs-backdrop="static" data-bs-keyboard="false" tabIndex={-1} aria-labelledby="staticLoginLabel" aria-hidden="true">
  <div className="modal-dialog modal-sm modal-dialog-centered">
    <div className="modal-content">
      <div className="modal-header bg-primary">
        <h1 className="modal-title fs-5 text-white" id="staticLoginLabel">User's SignIn</h1>
        <button id="close" onClick={closeLogin} type="button" className="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div className="modal-body">
        <form onSubmit={submitLogindata} autoComplete='off'>
            <div className="mb-3">
                <input type="text" required value={username} onChange={e => setUsername(e.target.value)} className="form-control border-primary" id="uname" disabled={isdisable} placeholder="enter Username"/>
            </div>            
            <div className="mb-3">
                <input type="password" required value={password} onChange={e => setPassword(e.target.value)} className="form-control border-primary" id="pword" disabled={isdisable} placeholder="enter Password"/>
            </div>            
            <button type="submit" className="btn btn-primary mx-1" disabled={isdisable}>signin</button>
            <button id="loginReset" type="reset" onClick={resetLogin} className="btn btn-primary">reset</button>
            <button id="mfa" type="button" className="btn btn-primary mx-1 d-none" data-bs-toggle="modal" data-bs-target="#staticMfa">mfa</button>            
        </form>
      </div>
      <div className="modal-footer">
        <div className='w-100 text-center text-danger'>{message}</div>
      </div>
    </div>
  </div>
</div>    
<Mfa/>
</>
  )
}

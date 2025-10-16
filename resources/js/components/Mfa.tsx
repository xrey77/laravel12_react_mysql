import React, { useEffect, useState } from "react"
import axios from 'axios';
import jQuery from "jquery";

const api = axios.create({
   baseURL: "http://localhost:8000",
   headers: {'Accept': 'application/json',
             'Content-Type': 'application/json'}
})

export default function Mfa() {
  const [otp, setOtp] = useState<string>('');
  const [message, setMessage] = useState<string>('');
  const [userid, setUserid] = useState<string>('');
  const [token, setToken] = useState<string>('');
  const [isDisabled, setIsdisabled] = useState<boolean>(false);

  useEffect(() => {
    const uid = sessionStorage.getItem('USERID');
    if (uid !== null) {
      setUserid(uid);
    }
    const tokenz = sessionStorage.getItem('TOKEN');
    if (tokenz !== null) {
      setToken(tokenz);
    }

  },[]);

  const submitMfa = async (event: any) => {
    event.preventDefault();
    setMessage('please wait..');      
    setIsdisabled(true);
    const jsonData =JSON.stringify({ id: userid, otp: otp });
    await api.patch("/api/otpvalidation", jsonData, { headers: {
      Authorization: `Bearer ${token}`
    }})
    .then((res) => {
            setMessage(res.data.message);
            sessionStorage.setItem("USERNAME", res.data.username);
            setTimeout(() => {
              setMessage('');
              setOtp('');
              setIsdisabled(false);
              jQuery("#mfaReset").trigger('click');              
              window.location.reload();
            }, 3000);
      }, (error: any) => {
            setMessage(error.response.data.message);
            setTimeout(() => {
              setMessage('');
              setIsdisabled(false);
              setOtp('');
            }, 3000);
            return;
    });              
  }

  const closeMfa = (event: any) => {
    event.preventDefault();
    setMessage('');
    setOtp('');
    setIsdisabled(false);
    sessionStorage.removeItem('USERID');
    sessionStorage.removeItem('USERNAME');
    sessionStorage.removeItem('USERPIC');
    sessionStorage.removeItem('TOKEN');
    location.reload();
  }

  const resetMfa = () =>{
    setOtp('');
    setMessage('');
  }

  return (
    <div className="modal fade" id="staticMfa" data-bs-backdrop="static" data-bs-keyboard="false" tabIndex={-1} aria-labelledby="staticMfaLabel" aria-hidden="true">
      <div className="modal-dialog modal-sm modal-dialog-centered">
        <div className="modal-content">
          <div className="modal-header bg-info">
            <h1 className="modal-title fs-5 text-dark" id="staticMfaLabel">Multi-Factor Authenticator</h1>
            <button onClick={closeMfa} type="button" className="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div className="modal-body">
          <form onSubmit={submitMfa} autoComplete="off">
            <div className="mb-3">
              <input type="text" required={true} value={otp} onChange={e => setOtp(e.target.value)} className="form-control" id="otp" disabled={isDisabled} placeholder="enter 6-digin OTP code"/>
            </div>          
            <div className="mb-3">
              <button type="submit" className="btn btn-info mx-2 text-dark" disabled={isDisabled}>submit</button>
              <button id="mfaReset" onClick={resetMfa} type="reset" className="btn btn-info text-dark">reset</button>
            </div>
          </form>            
          </div>
          <div className="modal-footer">
            <div className="w-100 text-center text-danger">{message}</div>
          </div>
        </div>
      </div>
    </div>    
  )
}
        

import React, { useState, useEffect } from "react";
import axios from "axios";
import jQuery from 'jquery';
import Footer from '../components/Footer';

const mfaApi = axios.create({
  baseURL: "http://127.0.0.1:8000",
  headers: {'Accept': 'application/json',
            'Content-Type': 'application/json',
            'Authorization': 'inherit'},
})

const api = axios.create({
  baseURL: "http://127.0.0.1:8000",
  headers: {'Accept': 'application/json',
          'Content-Type': 'multipart/form-data',
          'Authorization': 'inherit'},
})



interface User {
    id: string,
    firstname: string,
    lastname: string,
    email: string,
    mobile: string,
    username: string,
    two_factor_secret: string,
    two_factor_recovery_codes: string,
    two_factor_confirmed_at: string,
    roles: string,
    profilepic: string,
    isactivated: string,
    isblocked: number,
    mailtoken: number,
    qrcodeurl: string,
    secretkey: string   
}

interface Userdata {
    statuscode: string,
    message: string,
    user: User

}

export default function Profile() {    
    const [userid, setUserid] = useState<string>('');;
    const [lname, setLname] = useState<string>('');
    const [fname, setFname] = useState<string>('');
    const [email, setEmail] = useState<string>('');
    const [mobile, setMobile] = useState<string>('');
    const [userpicture, setUserpicture] = useState<string>('');
    const [token, setToken] = useState<string>('');
    const [newpassword, setNewPassword ] = useState<string>('');
    const [confnewpassword, setConfNewPassword ] = useState<string>('');    
    const [profileMsg, setProfileMsg] = useState<string>('');
    const [showmfa, setShowMfa] = useState<boolean>(false);
    const [showpwd, setShowPwd] = useState<boolean>(false);
    const [showupdate, setShowUpdate] = useState<boolean>(false);
    const [qrcodeurl, setQrcodeurl] = useState<string>('');

    const fetchUserData = (id: any, token: any) => {
        api.get<Userdata>(`/api/getuserid/${id}`,{headers: {
            Authorization: `Bearer ${token}`
        }})
        .then((res) => {
            const data: Userdata = res.data;
            setLname(data.user.lastname); 
            setFname(data.user.firstname); 
            setEmail(data.user.email);
            setMobile(data.user.mobile);
            if (data.user.qrcodeurl === null) {
                setQrcodeurl('http://127.0.0.1:8000/images/qrcode.png');
            } else {
                setQrcodeurl(data.user.qrcodeurl)
            }
            setUserpicture(res.data.user.profilepic);
            });
    };    

    useEffect(() => {
        jQuery("#password").prop('disabled', true);
        let userId: any = sessionStorage.getItem('USERID');
        if (userId != null) {
            setUserid(userId)
        } else {
            setUserid('')
        }
        let xtoken: any = sessionStorage.getItem('TOKEN');
        if (xtoken !== null) {
            setToken(xtoken);
        } else {
            setToken('');
        }
        fetchUserData(userId, xtoken);
        // getCRFtoken();
    },[]) 

    const submitProfile = (e: any) => {
        e.preventDefault();
        const data =JSON.stringify({ id: userid, lastname: lname, firstname: fname, mobile: mobile });
        mfaApi.patch(`/api/updateuser/${userid}`, data, { headers: {
            Authorization: `Bearer ${token}`
        }})
        .then((res: any) => {
            if (res.data.message != null) {
                setProfileMsg(res.data.message);
                let timer: any = setTimeout(() => {
                    setProfileMsg('');
                    clearTimeout(timer);
                },3000);
                return;
            }
        }, (error: any) => {
            setProfileMsg(error.response.data.message);
            let timer: any = setTimeout(() => {
                setProfileMsg('');
                clearTimeout(timer);
            },3000);
            return;
        });
    }

    const cpwdCheckbox = (e: any) => {
        if (e.target.checked) {
            setShowUpdate(true);
            setShowPwd(true);
            // $("#checkChangePassword").prop('disabled', false);
            return;
        } else {
            setNewPassword('');
            setConfNewPassword('');
            setShowPwd(false);
            setShowUpdate(false)
        }
    }

    const changePicture = (event: any) => {
        event.preventDefault();
            var pix = URL.createObjectURL(event.target.files[0]);
            jQuery('#userpic').attr('src', pix);
            const formData = new FormData();
            formData.append('id', userid);
            formData.append('profilepic', event.target.files[0]);
            api.post(`/api/uploadpicture`, formData, {headers: {
                Authorization: `Bearer ${token}`
            }})
            .then((res: any) => {
                setProfileMsg(res.data.message);
                setTimeout(() => {
                    setProfileMsg('');
                },3000);
                return;
            }, (error: any) => {
                setProfileMsg(error.response.data.message);
                setTimeout(() => {
                    setProfileMsg('');
                },3000);
                return;
            });
    
    }

    const mfaCheckbox = (e: any) => {
        if (e.target.checked) {
            setShowMfa(true);
            setShowUpdate(true)
        } else {
            setShowMfa(false);
            setShowUpdate(false)
        }
    }

    const getCRFtoken = () => {
        // const crftoken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        var csrfToken = jQuery('meta[name="csrf-token"]').attr('content');
        // console.log("xcrftoken : ",csrfToken);
        // mfaApi.defaults.withCredentials = true;
        // mfaApi.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
        mfaApi.get('/api/crftoken'), { headers: {
                Authorization: `Bearer ${token}`,
        }}
        .then((res) => {
            console.log("x-csrf-token...............");
            console.log(res.data);
            // mfaApi.post('/user/two-factor-authentication', {headers: {
            //     'X-CSRF-TOKEN': csrfToken
            // }})
            // .then(res => {
            //     alert("stop");
            //     console.log("TWO FACTOR ENABLED.............");
            // }, (error: any) => {
            //     console.log(error.response.data.message);
            // })



        }, (error: any) => {
            setProfileMsg(error.response.data.message);
            // setTimeout(() => {
            //     setProfileMsg('');
            // },3000);
            return;
        });

    }
    const enableMFA = () => {
        // const csrfToken = jQuery('meta[name="csrf-token"]').attr('content');
        const jsonData =JSON.stringify({ Twofactorenabled: true });      
        mfaApi.patch(`/api/enablemfa/${userid}`, jsonData, {headers: {
            Authorization: `Bearer ${token}`
        }})
        .then((res) => {
            setProfileMsg(res.data.message);            
            setTimeout(() => {
                setQrcodeurl(res.data.qrcodeurl);
                setProfileMsg('');
            },6000);

        }, (error: any) => {
            setProfileMsg(error.response.data.message);
            // setTimeout(() => {
            //     setProfileMsg('');
            // },6000);

        })
    }

    const disableMFA = () => {
        const data =JSON.stringify({ Twofactorenabled: false });      
        mfaApi.patch(`/api/enablemfa/${userid}`, data, {headers: {
            Authorization: `Bearer ${token}`
        }})
        .then((res) => {
            setProfileMsg(res.data.message);
            setQrcodeurl('http://127.0.0.1:8000/images/qrcode.png');
            setTimeout(() => {
                setProfileMsg('');
            },3000);
        }, (error: any) => {
            setProfileMsg(error.response.data.message);            
            setTimeout(() => {
                setProfileMsg('');
            },3000);
            return;
        });
    }

    const changePassword = (event: any) => {
        event.preventDefault();
        if (newpassword === '') {
            setProfileMsg("Please enter new Pasword.");
            let timer: any = setTimeout(() => {
                setProfileMsg('');
                clearTimeout(timer);
            },3000);
            return;
        }
        if (confnewpassword === '') {
            setProfileMsg("Please enter new Pasword confirmation.");
            let timer: any = setTimeout(() => {
                setProfileMsg('');
                clearTimeout(timer);
            },3000);
            return;            
        }

        if (newpassword !== confnewpassword) {
            setProfileMsg("new Password does not matched.");
            let timer: any = setTimeout(() => {
                setProfileMsg('');
                clearTimeout(timer);
            },3000);
            return;            
        }

        const data =JSON.stringify({"password": newpassword });
        mfaApi.patch(`/api/changeuserpassword/${userid}`, data, {headers: {
            Authorization: `Bearer ${token}`
        }})
        .then((res: any) => {
            if (res.data.message != null) {
                setProfileMsg(res.data.message);
                let timer: any = setTimeout(() => {
                    setProfileMsg('');
                    clearTimeout(timer);
                },3000);
                return;
            }
        }, (error: any) => {
            setProfileMsg(error.response.data.message);
            let timer: any = setTimeout(() => {
                setProfileMsg('');
                clearTimeout(timer);
            },3000);
            return;
        });        
    }

    return (
      <div className='profile-bg'>
        <div className="card mt-1">
        <div className="card-header bg-success">
            <h3 className="text-white">Profile</h3>
        </div>
        <div className="card-body bg-warning">
        <form encType="multipart/form-data" autoComplete='false'>
                <div className='row'>
                    <div className='col'>
                        <input className="form-control" id="firstname" name="firstname" type="text" value={fname} onChange={e => setFname(e.target.value)} required  />
                        <input className="form-control mt-2" id="lastname" name="lastname" type="text" value={lname} onChange={e => setLname(e.target.value )} required />
                        <input className="form-control mt-2" id="email" name="email" type="email" value={email} onChange={e => setEmail(e.target.value)} readOnly placeholder='email address' />
                        <input id="_token" type="hidden" name="_token" value="{{ csrf_token() }}" />                        
                    </div>
                    <div className='col'>
                        <img id="userpic" src={userpicture} className="userpic" alt="" />
                    </div>
                </div>
        

                <div className='row'>
                    <div className='col'>
                            <input className="form-control mt-2" id="mobileno" name="mobileno" type="text" value={mobile} onChange={e => setMobile(e.target.value)} required />
                    </div>
                    <div className='col'>
                        <input className="userpicture mt-2" onChange={changePicture} type="file"/>
                    </div>
                </div>

                <div className='row'>
                    {/* 2-FACTOR AUTHENTICATION */}
                    <div className='col'>
                            <div className="form-check mt-2">
                                <input onChange={mfaCheckbox} className="form-check-input" type="checkbox" value="" id="checkTwoFactor"/>
                                <label className="form-check-label" htmlFor="checkTwoFactor">
                                    Enable 2-Factor Authentication
                                </label>
                            </div>
                            {
                            showmfa === true ? (

                             <div className='row'>
                                <div className='col-5'>
                                       <img src={qrcodeurl} className="qrCode2" alt=""/>  
                                </div>
                                <div className='col-7'>
                                    <p className='text-danger mfa-pos-1'><strong>Requirements</strong></p>
                                    <p className="mfa-pos-2">You need to install <strong>Google or Microsoft Authenticator</strong> in your Mobile Phone, once installed, click Enable Button below, and <strong>SCAN QR CODE</strong>, next time you login, another dialog window will appear, then enter the <strong>OTP CODE</strong> from your Mobile Phone in order for you to login.</p>
                                    <button onClick={enableMFA} type="button" className='btn btn-primary mfa-btn-1 mx-1'>enable</button>
                                    <button onClick={disableMFA} type="button" className='btn btn-secondary mfa-btn-2'>disable</button>
                                </div>
                             </div>
                            )
                            :
                                null
                            }

                    </div>
                    <div className='col'>
                            {/* CHANGE PASSWORD */}
                            <div className="form-check mt-2">
                            <input onChange={cpwdCheckbox} className="form-check-input" type="checkbox" id="checkChangePassword"/>
                            <label className="form-check-label" htmlFor="checkChangePassword">
                                Change Password
                            </label>
                        </div>
                        { showpwd === true ? (
                            <>
                            <input className="form-control mt-2" type="password" id="newPassword" value={newpassword} onChange={e => setNewPassword(e.target.value)} placeholder='enter new Password'/>
                            <input className="form-control mt-1" type="password" id="confNewPassword" value={confnewpassword} onChange={e => setConfNewPassword(e.target.value)} placeholder='confirm new Password'/>
                            <button onClick={changePassword} className='btn btn-primary mt-2' type="button">change password</button>
                            </>
                        )
                        :
                            null
                        }

                    </div>
                </div> 
                {
                    showupdate === false ? (
                        <button onClick={submitProfile} type='submit' className='btn btn-success text-white mt-2'>update profile</button>
                    )
                    :
                    null
                }
                </form>
        </div>
        <div className="card-footer">
            <div className="w-100 text-danger">{profileMsg}</div>
        </div>
        </div>
        {showmfa ? (
            <Footer/>
        ): (
            <div className="fixed-bottom"><Footer/></div>
        )}
    </div>    
  )
}

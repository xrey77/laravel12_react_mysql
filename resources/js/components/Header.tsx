import React, {useState, useEffect} from 'react'
import Signin from './Signin'
import Signup from './Signup'
import { useNavigate } from 'react-router-dom';
import { Link } from '@inertiajs/react'
import '../../css/app.css';

export default function Header() {
  const [username, setUsername] = useState<string>('');
  const [userpic, setUserpic] = useState<string>('');
  const navigate = useNavigate

  useEffect(() => {
    const usrname = sessionStorage.getItem('USERNAME');    
    if (usrname === null) {
      setUsername('');
    } else {
      setUsername(usrname);
    }
    const usrpic = sessionStorage.getItem("USERPIC");
    if (usrpic === null) {
      setUserpic('/pix.png');
    } else {
      setUserpic(usrpic);
    }
  },[username, userpic]);

  const Logout = () => {
    sessionStorage.removeItem('USERID');
    sessionStorage.removeItem('USERNAME');
    sessionStorage.removeItem('USERPIC');
    sessionStorage.removeItem('TOKEN');
    const navigate = useNavigate();
    navigate('/');
  }


  return (
    <>
<nav className="navbar navbar-expand-lg bg-body-tertiary">
  <div className="container-fluid">
    <Link className="navbar-brand" href="/"><img className="logo" src="/images/logo.png" alt=""/></Link>
    {/* <button className="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"> */}
    <button className="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasWithBothOptions" aria-controls="offcanvasWithBothOptions">   
      <span className="navbar-toggler-icon"></span>
    </button>
    <div className="collapse navbar-collapse" id="navbarSupportedContent">
      <ul className="navbar-nav me-auto mb-2 mb-lg-0">
        <li className="nav-item">
          <Link className="nav-link text-dark" aria-current="page" href="/about">About Us</Link>
        </li>
        <li className="nav-item dropdown">
          <a className="nav-link dropdown-toggle text-dark" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Products
          </a>
          <ul className="dropdown-menu">
            <li><Link className="dropdown-item" href="/productlist">Products List</Link></li>
            <li><Link className="dropdown-item" href="/productcatalog">Products Catalog</Link></li>
            <li><hr className="dropdown-divider"/></li>
            <li><Link className="dropdown-item" href="/productsearch">Product Search</Link></li>
          </ul>
        </li>
        <li className="nav-item">
          <Link className="nav-link text-dark" href="/contact">Contact Us</Link>
        </li>
      </ul>
      { username === '' ? (
          <ul className="navbar-nav mr-auto">
          <li className="nav-item">
              <a className="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#staticLogin">Login</a>
            </li>
            <li className="nav-item">
              <a className="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#staticRegister">Register</a>
            </li>
          </ul>  
      ): (
        <ul className="navbar-nav mr-auto mb-2 mb-lg-0">
          <li className="nav-item dropdown">
          <Link className="nav-link dropdown-toggle" href="/#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><img className="user" src={userpic} alt=""/>&nbsp;{username}</Link>
          <ul className="dropdown-menu">
            <li><Link onClick={Logout} className="dropdown-item" href="/#">Log-Off</Link></li>
            <li><Link className="dropdown-item" href="/profile">Profile</Link></li>
            <li><hr className="dropdown-divider"/></li>
            <li><Link className="dropdown-item" href="/#">Messenger</Link></li>
          </ul>
          </li>
        </ul>        
      )}            
    </div>
  </div>
</nav>
{/*  OFF-CANVAS */}
<div className="offcanvas offcanvas-end" data-bs-scroll="true" tabIndex={-1} id="offcanvasWithBothOptions" aria-labelledby="offcanvasWithBothOptionsLabel">
    <div className="offcanvas-header bg-primary">
      <h5 className="offcanvas-title text-white" id="offcanvasWithBothOptionsLabel">Drawer Menu</h5>
      <button type="button" className="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>    
      </div>
    <div className="offcanvas-body bg-danger">
  
      <ul className="nav flex-column">
        <li className="nav-item" data-bs-dismiss="offcanvas">
          <Link className="nav-link text-white embossed " href="/about">About Us</Link>
        </li>
        <li><hr/></li>
        <li className="nav-item dropdown">
          <Link className="nav-link dropdown-toggle text-white embossed" href="/#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Products
          </Link>
          <ul className="dropdown-menu">
            <li data-bs-dismiss="offcanvas">
              <Link className="dropdown-item" href="/productlist">Product List</Link></li>
            <li data-bs-dismiss="offcanvas">
              <Link className="dropdown-item" href="/productcatalog">Product Catalogs</Link></li>
            <li><hr className="dropdown-divider"/></li>
            <li data-bs-dismiss="offcanvas">
              <Link className="dropdown-item" href="/productsearch">Product Search</Link></li>
          </ul>
        </li>
        <li><hr/></li>
  
        <li className="nav-item" data-bs-dismiss="offcanvas">
          <Link className="nav-link text-white embossed" href="/contact">Contact</Link>  
        </li>
        <li><hr/></li>

        { username === '' ? (
        <ul className="nav flex-column">  
        <li data-bs-dismiss="offcanvas" className="nav-item">
          <Link className="nav-link text-white embossed" aria-current="page" href="/#" data-bs-toggle="modal" data-bs-target="#staticLogin">Login</Link>
        </li>
        <li><hr/></li>
        <li data-bs-dismiss="offcanvas" className="nav-item">
          <Link className="nav-link text-white embossed" aria-current="page" href="/#" data-bs-toggle="modal" data-bs-target="#staticRegister">Register</Link>
        </li>     
        <li><hr/></li>                 
        </ul>
        ):
        <>   
        <ul className="nav">  
          <li className="nav-item dropdown">
            <Link className="nav-link dropdown-toggle" href="/#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <img className="user" src={userpic} alt=""/>&nbsp;<span className="text-white embossed"> {username}</span>
            </Link>
            <ul className="dropdown-menu">
              <li data-bs-dismiss="offcanvas">
                <Link className="dropdown-item" href="/#">Logout</Link></li>
              <li data-bs-dismiss="offcanvas">
                <Link className="dropdown-item" href="/profile">Profile</Link></li>
              <li><hr className="dropdown-divider"/></li>
              <li data-bs-dismiss="offcanvas">
                <Link className="dropdown-item" href="/#">Messenger</Link></li>
            </ul>
          </li>
        </ul>           
        <li><hr/></li>
        </>

        }
      </ul>
  </div>
  </div>
<Signin/>
<Signup/>
</>
  )
}

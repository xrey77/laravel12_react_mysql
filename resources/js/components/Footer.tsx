import React from 'react'

export default function Footer() {
  return (
  <div className='text-center footer'>
        <p>Copyright &copy; {new Date().getFullYear()}-{new Date().getFullYear()+10}, Diebold-Nixdorf, All rights reserved.&trade;</p>
  </div>    
  )
}

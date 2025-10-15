import React, { PropsWithChildren } from 'react';
import Header from '../components/Header';
import Footer from '../components/Footer';

const DefaultLayout: React.FC<PropsWithChildren> = ({ children }) => {
  return (
    <div>
      <Header/>      
      <main style={{ padding: '1rem' }}>
        {children} 
      </main>
      <Footer/>      
    </div>
  );
};

export default DefaultLayout;

import React, { PropsWithChildren } from 'react';
import Header from '../components/Header';

const DefaultLayout: React.FC<PropsWithChildren> = ({ children }) => {
  return (
    <div>
      <Header/>      
      <main>
        {children} 
      </main>
    </div>
  );
};

export default DefaultLayout;

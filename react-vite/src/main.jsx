import React from 'react'
import ReactDOM from 'react-dom/client'
import { RouterProvider } from 'react-router-dom'
import './index.css'
import router from './router'
import { ContextProvider } from './contexts/ContextProvider'
import { DisplayContext } from './contexts/DisplayContext'



ReactDOM.createRoot(document.getElementById('root')).render(
  <React.StrictMode>
    <ContextProvider>
      <DisplayContext>
        <RouterProvider router={router}/>
      </DisplayContext>
    </ContextProvider>
  </React.StrictMode>,
)


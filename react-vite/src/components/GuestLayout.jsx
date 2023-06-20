import React, { useEffect } from 'react'
import { Navigate, Outlet } from 'react-router-dom'
import { useStateContext } from '../contexts/ContextProvider'

export default function GuestLayout() {

    const {token, role, fetchUser} = useStateContext();


    useEffect(() => {
       fetchUser() 
    }, [])

    if (token) {
        if (role === "admin") {
            return <Navigate to="/users"/> ; 
        } else {
            return <Navigate to="/dashboard"/>;  
        }
    }

    return (
        <div>
            <Outlet />
        </div>
    )
}

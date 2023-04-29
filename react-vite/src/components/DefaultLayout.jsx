import axios from 'axios';
import React, { useEffect } from 'react'
import { Link, Navigate, Outlet } from 'react-router-dom'
import axiosClient from '../axios-client';
import { useStateContext } from '../contexts/ContextProvider';
import AdminPanelSettingsOutlinedIcon from '@mui/icons-material/AdminPanelSettingsOutlined';
import LockOpenOutlinedIcon from '@mui/icons-material/LockOpenOutlined';
import SecurityOutlinedIcon from '@mui/icons-material/SecurityOutlined';

export default function DefaultLayout() {

    const {
        user,
        token,
        setUser,
        setToken,
        notification,
        fetchUser,
        role
    } = useStateContext();

    if (!token) {
        return <Navigate replace to='/login' />
    } 

    const onLogout = (ev) => {
        ev.preventDefault();

        axiosClient.post('/logout')
        .then(() => {
            setUser({});
            setToken(null);
        })
    }


    useEffect(() => {
        axiosClient.get('/user')
        .then(({data}) => {
            setUser(data.user)
        })
        fetchUser()
    }, [])

    return (
        <div id='defaultLayout'>
            <aside>
                <Link to='dashboard'> Dashboard </Link>
                <Link to='/users'> Users </Link>
                <Link to='/employes'> List of Employes </Link>
                <Link to='/dettes'> Dette des entreprises </Link>
                <Link to='/finances'> List of Finances </Link>
                <Link to='/creances'> List of creances </Link>
                <Link to='/stagiares'> List of Stagiares </Link>
            </aside>
            <div className='content'> 
                <header>
                    <div>
                        Header
                    </div>
                    <div>
                        {user.name}
                        { role === "admin" && <AdminPanelSettingsOutlinedIcon /> }
                        { role === "editor" && <SecurityOutlinedIcon /> }
                        { role === "basic" && <LockOpenOutlinedIcon /> }
                        <a href='#' onClick={onLogout} className="btn-logout"> Logout </a>
                    </div>
                </header>
                <main>
                    <Outlet name='main' />
                </main>
                {notification &&
                <div className="notification">
                    {notification}
                </div>
                }
            </div>  
        </div>
    )
}


import axios from 'axios';
import React, { useEffect } from 'react'
import { Link, Navigate, Outlet } from 'react-router-dom'
import axiosClient from '../axios-client';
import { useStateContext } from '../contexts/ContextProvider';

export default function DefaultLayout() {

    const {user, token, setUser, setToken, notification} = useStateContext();

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
    }, [])

    return (
        <div id='defaultLayout'>
            <aside>
                <Link to='dashboard'> Dashboard </Link>
                <Link to='/users'> Users </Link>
                <Link to='/employes'> List of Employes </Link>
            </aside>
            <div className='content'> 
                <header>
                    <div>
                        Header
                    </div>
                    <div>
                        {user.name}
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


import axios from 'axios';
import React, { useEffect, useState } from 'react'
import { Link, Navigate, Outlet } from 'react-router-dom'
import axiosClient from '../axios-client';
import { useStateContext } from '../contexts/ContextProvider';
import AdminPanelSettingsOutlinedIcon from '@mui/icons-material/AdminPanelSettingsOutlined';
import LockOpenOutlinedIcon from '@mui/icons-material/LockOpenOutlined';
import SecurityOutlinedIcon from '@mui/icons-material/SecurityOutlined';
import ExpandMoreIcon from '@mui/icons-material/ExpandMore';
import DirectionsBoatIcon from '@mui/icons-material/DirectionsBoat';
import CorporateFareIcon from '@mui/icons-material/CorporateFare';
import ExpandLessIcon from '@mui/icons-material/ExpandLess';

export default function DefaultLayout() {



    const {
        user,
        token,
        setUser,
        setToken,
        notification,
        admission,
        fetchUser,
        role, 
        setRole,
        setFiliale,
        toggleMenu,
        toggleMenue, 
        setToggleDashMenu,
        setToggleMenue,
        toggleDashMenu
    } = useStateContext();

    if (!token) {
        return <Navigate replace to='/login' />
    } 


    const onLogout = (ev) => {
        ev.preventDefault();

        axiosClient.post('/logout')
        .then(() => {
            setUser({});
            setRole("");
            setFiliale(null);
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
            {role && <aside>
                {(role !== "admin" && admission) && <>
                    <span style={{ display: "flex", justifyContent: "space-between" }}
                    onClick={() => toggleMenu(".toggle-dash", false)}>
                        <div> 
                            <DirectionsBoatIcon/>
                            <span style={{ fontWeight: "900", fontSize: "18px",
                            position: "relative", bottom: "5px"  }}> Dashboard </span>  
                        </div>  
                        {toggleDashMenu ? <ExpandMoreIcon /> : <ExpandLessIcon/>}
                    </span>
                    {role === "global" && <Link to='/dashboard' className='toggle-dash'> Dashboard Groupe </Link>}
                    <Link to='/dashboard/RHS' className='toggle-dash'> Dashboard RHS </Link>
                    <Link to='/dashboard/Dette_Creances' className='toggle-dash'> Dettes_Creances </Link>
                    <Link to='/dashboard/Finance' className='toggle-dash'> Dashboard Finance </Link>
                    <span onClick={() => toggleMenu(".toggle-menue", true)} 
                    style={{ display: "flex", justifyContent: "space-between" }}>
                        <div> 
                            <CorporateFareIcon/>
                            <span style={{ fontWeight: "900", fontSize: "18px",
                            position: "relative", bottom: "5px"  }}> Entreprise </span>  
                        </div>  
                        {toggleMenue ? <ExpandMoreIcon /> : <ExpandLessIcon/>}
                    </span>
                    {/* {role === "global" && <Link to='/users' className='toggle-menue'> Users </Link>} */}
                    {(role === "global" || admission?.name === "A1" || admission?.name === "A5") && 
                    <Link to='/employes' className='toggle-menue'> Listes de Employes </Link>}
                    {(role === "global" || admission?.name === "A2" || admission?.name === "A5") && 
                    <Link to='/dettes' className='toggle-menue'> Listes des Dettes </Link>}
                    {(role === "global" || admission?.name === "A4" || admission?.name === "A5") && 
                    <Link to='/finances' className='toggle-menue'> Listes des Finances </Link>}
                    {(role === "global" || admission?.name === "A3" || admission?.name === "A5") && 
                    <Link to='/creances' className='toggle-menue'> Listes des creances </Link>}
                    {(role === "global" || admission?.name === "A1" || admission?.name === "A5") && 
                    <Link to='/formations' className='toggle-menue'> Listes des Formations </Link>}

                </>}
                {role === "admin" && <Link to='/users' className='active'> Users </Link>}
            </aside>}
            <div className='content'> 
                <header>
                    <div>
                        <img src="Gitrama.png" height={"50"} width={"50"} alt="" />
                    </div>
                    <h2 style={{ marginLeft: "-150px", fontWeight: "400" }}> GROUPE D'INFRASTRUCTURES DE TRAVAUX MARITIMES </h2> 

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


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

    const [toggleMenue, setToggleMenue] = useState(false);
    const [toggleDashMenu, setToggleDashMenu] = useState(false)



    const {
        user,
        token,
        setUser,
        setToken,
        notification,
        admission,
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

    function toggleMenu(toggle, Menu=false){

        const menuToggle = document.querySelectorAll(toggle);
        for (let index = 0; index < menuToggle.length; index++) {
            menuToggle[index].classList.toggle('active');
        }
        if (Menu) {
            setToggleMenue(p => !p)
        } else {
            setToggleDashMenu(p => !p)
        }
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
                <span style={{ display: "flex", justifyContent: "space-between" }}
                onClick={() => toggleMenu(".toggle-dash", false)}>
                    <div> 
                         <DirectionsBoatIcon/>
                        <span style={{ fontWeight: "900", fontSize: "18px",
                        position: "relative", bottom: "5px"  }}> Dashboard </span>  
                    </div>  
                    {toggleDashMenu ? <ExpandMoreIcon /> : <ExpandLessIcon/>}
                </span>
                <Link to='/dashboard' className='toggle-dash'> Dashboard Globale </Link>
                <Link to='/dashboard/RHS' className='toggle-dash'> Dashboard RHS </Link>
                <Link to='/dashboard/Finance' className='toggle-dash'> Dashboard Finance </Link>
                <Link to='/dashboard/Dette_Creances' className='toggle-dash'> Dettes_Creances </Link>
                <span onClick={() => toggleMenu(".toggle-menue", true)} 
                style={{ display: "flex", justifyContent: "space-between" }}>
                    <div> 
                        <CorporateFareIcon/>
                        <span style={{ fontWeight: "900", fontSize: "18px",
                        position: "relative", bottom: "5px"  }}> Entreprise </span>  
                    </div>  
                    {toggleMenue ? <ExpandMoreIcon /> : <ExpandLessIcon/>}
                </span>
                {role === "admin" && <Link to='/users' className='toggle-menue'> Users </Link>}
                {(role === "admin" || admission?.name === "A1") && <Link to='/employes' className='toggle-menue'> Listes de Employes </Link>}
                {(role === "admin" || admission?.name === "A2") && <Link to='/dettes' className='toggle-menue'> Listes des Dettes </Link>}
                {(role === "admin" || admission?.name === "A4") && <Link to='/finances' className='toggle-menue'> Listes des Finances </Link>}
                {(role === "admin" || admission?.name === "A3") && <Link to='/creances' className='toggle-menue'> Listes des creances </Link>}
                {/* {(role === "admin" || admission?.name === "A1") && <Link to='/stagiares' className='toggle-menue'> Listes des Stagiares </Link>} */}
                {(role === "admin" || admission?.name === "A1") && <Link to='/formations' className='toggle-menue'> Listes des Formations </Link>}

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


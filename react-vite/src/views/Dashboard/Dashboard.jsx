import React, { useEffect } from 'react';
import DashboardGlobal from './DashboardGlobal';
import { useStateContext } from '../../contexts/ContextProvider';
import Dashboard01 from './Dashboard01';
import { CircularProgress } from '@mui/material';
import Users from '../Users';

const Dashboard = () => {

    const { fetchUser, role } = useStateContext();

    useEffect(() => {
        fetchUser()
    }, [])

    return (
        <>
            {role ? <div>
                {role === "global" && <DashboardGlobal/>}
                {role === "admin" && <Users/> }
                {role === "basic" && <Dashboard01 />}
                {role === "editor" && <Dashboard01 />}
            </div> : <CircularProgress disableShrink /> }
        </>
    );
};

export default Dashboard;
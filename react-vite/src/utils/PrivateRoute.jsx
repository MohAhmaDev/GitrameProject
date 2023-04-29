import React, { useContext, useEffect } from 'react';
import { useStateContext } from '../contexts/ContextProvider';
import { Navigate } from 'react-router-dom';

const PrivateRoute = ({redirectpath = '/dashboard', children}) => {
    
    const { user, role, fetchUser } = useStateContext();


    useEffect(() => {
        fetchUser();
    }, [])

    const admit = ['admin', 'editor']
    if (!admit.includes(role)) {
        return <Navigate to={redirectpath} replace/>;
    } else {
        return children;
    }
};

export default PrivateRoute;
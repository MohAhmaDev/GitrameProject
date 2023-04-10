import { createBrowserRouter, Navigate } from 'react-router-dom'
import Login from './views/Login';
import NotFound from './views/NotFound';
import Users from './views/Users';
import Signup from './views/Signup';
import DefaultLayout from './components/DefaultLayout';
import GuestLayout from './components/GuestLayout';
import Dashboard from './views/Dashboard';
import UserForm from './views/UserForm';
import Employes from './views/Employes';
import EmployesForm from './views/EmployesForm'

const router = createBrowserRouter([
    {
        path: '/',
        element: <DefaultLayout />,
        children: [
            {
                path: '/',
                element: <Navigate to='/users'/>
            },
            {
                path: '/users',
                element: <Users />
            },
            {
                path: '/users/new',
                element: <UserForm key="userCreate"/>
            },
            {
                path: '/users/:id',
                element: <UserForm key="userUpdate" />
            },
            {
                path: '/employes',
                element: <Employes />
            },
            {
                path: '/employes/new',
                element: <EmployesForm key="userCreate"/>
            },
            {
                path: '/employes/:id',
                element: <EmployesForm key="userUpdate" />
            },
            {
                path: '/dashboard',
                element: <Dashboard />
            }
        ]
    },
    {
        path: '/',
        element: <GuestLayout />,
        children: [
            {
                path: '/login',
                element: <Login />
            },
            {
                path: '/Signup',
                element: <Signup />
            }
        ]
    },


    {
        path: '*',
        element: <NotFound />
    }
])


export default router;                  
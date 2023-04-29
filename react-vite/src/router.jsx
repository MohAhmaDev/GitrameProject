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
import Dettes from './views/Dettes';
import DettesForm from './views/DettesForm';
import Finances from './views/Finances';
import FinancesFrom from './views/FinancesFrom';
import { FormsContext } from './contexts/FormsContext'
import PrivateRoute from './utils/PrivateRoute';
import Creances from './views/Creances';
import CreancesForm from './views/CreancesForm';
import EntreprisesForm from './views/EntreprisesForm'
import Stagiares from './views/Stagiares';
import StagiaresForm from './views/StagiaresForm'


const router = createBrowserRouter([
    {
        path: '/',
        element: <FormsContext> 
                    <DefaultLayout /> 
                 </FormsContext>,
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
                path: '/users/filiale/:id',
                element: <UserForm key="userSetFiliale" />
            },
            {
                path: '/employes',
                element: <Employes />  
            },
            {
                path: '/employes/new',
                element: <PrivateRoute redirectpath='/employes'> <EmployesForm key="employe_Create"/> </PrivateRoute>
            },
            {
                path: '/employes/:id',
                element: <PrivateRoute redirectpath='/employes'> <EmployesForm key="employe_Update"/> </PrivateRoute>
            },
            {
                path: '/dettes',
                element: <Dettes />
            },
            {
                path: '/dettes/new',
                element: <PrivateRoute redirectpath='/dettes'> <DettesForm key="dette_Create"/> </PrivateRoute>
            },
            {
                path: '/dettes/:id',
                element: <PrivateRoute redirectpath='/dettes'> <DettesForm key="dette_Update" /> </PrivateRoute>
            },
            {
                path: '/finances',
                element: <Finances />
            },
            {
                path: '/finances/new',
                element: <PrivateRoute redirectpath='/finances'> <FinancesFrom key="finance_Create"/> </PrivateRoute>
            },
            {
                path: '/finances/:id',
                element:  <PrivateRoute redirectpath='/finances'> <FinancesFrom key="finance_Update" /> </PrivateRoute>
            },
            {
                path: '/creances',
                element: <Creances />
            },
            {
                path: '/creances/new',
                element: <PrivateRoute redirectpath='/creances'> <CreancesForm key="creance_Create"/> </PrivateRoute>
            },
            {
                path: '/creances/:id',
                element:  <PrivateRoute redirectpath='/creances'> <CreancesForm key="creance_Update" /> </PrivateRoute>
            },
            {
                path: '/stagiares',
                element: <Stagiares />
            },
            {
                path: '/stagiares/new',
                element: <PrivateRoute redirectpath='/stagiares'> <StagiaresForm key="stagiare_Create"/> </PrivateRoute>
            },
            {
                path: '/stagiares/:id',
                element:  <PrivateRoute redirectpath='/stagiares'> <StagiaresForm key="stagiare_Update" /> </PrivateRoute>
            },
            {
                path: '/entreprise/add',
                element: <PrivateRoute redirectpath='/'> <EntreprisesForm /> </PrivateRoute>,
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
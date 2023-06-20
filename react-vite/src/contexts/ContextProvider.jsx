import { createContext, useContext, useState } from "react";
import axiosClient from '../axios-client';
import { useParams } from "react-router-dom";


const StateContext = createContext({
    user: null,
    token: null,
    filiale: null, 
    role: null,
    admission: null,
    notification: null,
    loading: null,
    setUser: () => {},
    setFiliale: () => {},
    setRole: () => {},
    setToken: () => {},
    setLoading: () => {},
    setAdmission: () => {},
    setNotification: () => {},
    fetchUser: () => {},
    toggleMenue: null,
    setToggleMenue: () => {},
    toggleDashMenu: null,
    setToggleDashMenu: () => {},
    toggleMenu: () => {},
})


export const ContextProvider = ({children = null}) => {

    const [toggleMenue, setToggleMenue] = useState(false);
    const [toggleDashMenu, setToggleDashMenu] = useState(false)
    const [user, setUser] = useState({});
    const [filiale, setFiliale] = useState(null);
    const [role, setRole] = useState("");
    const [loading, setLoading] = useState(false);
    const [admission, setAdmission] = useState(null);
    const [notification, _setNotification] = useState('');
    const [token, _setToken] = useState(localStorage.getItem('ACCESS_TOKEN'));

    const setToken = (token) => {
        _setToken(token);
        if (token) {
            localStorage.setItem('ACCESS_TOKEN', token);
        } else {
            localStorage.removeItem('ACCESS_TOKEN');
        }
    }

    const setNotification = message => {
        _setNotification(message);
    
        setTimeout(() => {
          _setNotification('')
        }, 5000)
    }

    const fetchUser = async () => {
        try {
          const { data } = await axiosClient.get('/user')
          setUser(data.user)
          setRole(data.role)
          setFiliale(data.filiale)
          setAdmission(data.admission)
        } catch (err) {
          console.log(err)
        }
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

    

    return (
        <StateContext.Provider value={{
            user,
            token,
            filiale,
            role,
            loading,
            admission,
            notification,
            setUser,
            setToken,
            setFiliale,
            setRole,
            setLoading,
            setAdmission,
            setNotification,
            fetchUser: fetchUser,
            toggleDashMenu,
            setToggleMenue,
            setToggleDashMenu,
            toggleMenu
        }}>
            {children}
        </StateContext.Provider>
    )
}

export const useStateContext = () => useContext(StateContext);
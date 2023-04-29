import { createContext, useContext, useState } from "react";
import axiosClient from '../axios-client';
import { useParams } from "react-router-dom";


const StateContext = createContext({
    user: null,
    token: null,
    filiale: null, 
    role: null,
    notification: null,
    loading: null,
    setUser: () => {},
    setFiliale: () => {},
    setRole: () => {},
    setToken: () => {},
    setLoading: () => {},
    setNotification: () => {},
    fetchUser: () => {},
})


export const ContextProvider = ({children}) => {

    const [user, setUser] = useState({});
    const [filiale, setFiliale] = useState(null);
    const [role, setRole] = useState("");
    const [loading, setLoading] = useState(false);
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
        } catch (err) {
          console.log(err)
        }
    }


    

    return (
        <StateContext.Provider value={{
            user,
            token,
            filiale,
            role,
            loading,
            notification,
            setUser,
            setToken,
            setFiliale,
            setRole,
            setLoading,
            setNotification,
            fetchUser: fetchUser,
        }}>
            {children}
        </StateContext.Provider>
    )
}

export const useStateContext = () => useContext(StateContext);
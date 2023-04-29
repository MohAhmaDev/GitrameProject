import { createContext, useContext, useState } from "react";
import axiosClient from "../axios-client";

const StateContext = createContext({
    users: null,
    roles: null,
    filiales: null,
    loading: null,
    setUsers: () => {},
    setRoles: () => {},
    setFiliales: () => {},
    setLoading: () => {},
    getUsers: () => {},
    getFiliales: () => {},
    getRoles: () => {},
})


export const DisplayContext = ({children}) => {
    const [users, setUsers] = useState({});
    const [roles, setRoles] = useState(null);
    const [filiales, setFiliales] = useState({});
    const [loading, setLoading] = useState(true);
    

    const getUsers = () => {
        setLoading(true)
        axiosClient.get('/users')
          .then(({ data }) => {
            setLoading(false)
            setUsers(data.data)
          })
          .catch(() => {
            setLoading(false)
          })
    }

    const getRoles = () => {
        axiosClient.get('/roles').then(({data}) => {
          setRoles(data.role)
        }).catch((error) => {
          console.log(error)
        })
    }
    

    const getFiliales = () => {
        axiosClient.get('/filiale').then(({data}) => {
          setFiliales(data.filiale)
        }).catch((error) => {
          console.log(error)
        })
    }

    return (
        <StateContext.Provider value={{
            users,
            roles,
            filiales,
            loading,
            setUsers,
            setRoles,
            setFiliales,
            setLoading,
            getUsers,
            getFiliales,
            getRoles,
        }}>
            {children}
        </StateContext.Provider>
    )
    

}

export const useDisplayContext = () => useContext(StateContext);
import { createContext, useContext, useState } from "react";
import axiosClient from "../axios-client";

const StateContext = createContext({
    users: null,
    roles: null,
    filiales: null,
    admissions: null,
    loading: null,
    setUsers: () => {},
    setRoles: () => {},
    setFiliales: () => {},
    setAdmissions: () => {},
    setLoading: () => {},
    getUsers: () => {},
    getFiliales: () => {},
    getRoles: () => {},
    getAdmissions: () => {}
})


export const DisplayContext = ({children = null}) => {
    const [users, setUsers] = useState({});
    const [roles, setRoles] = useState(null);
    const [filiales, setFiliales] = useState({});
    const [admissions, setAdmissions] = useState({});
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
    
    const getAdmissions = () => {
      axiosClient.get('/admission').then(({data}) => {
        setAdmissions(data.admission)
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
          admissions,
          loading,
          setUsers,
          setRoles,
          setFiliales,
          setAdmissions,
          setLoading,
          getUsers,
          getFiliales,
          getAdmissions,
          getRoles,
        }}>
            {children}
        </StateContext.Provider>
    )
    

}

export const useDisplayContext = () => useContext(StateContext);
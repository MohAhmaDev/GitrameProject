import { createContext, useContext, useState } from "react";
import axiosClient from "../axios-client";


const StateContext = createContext({
    employes: null,
    finances: null,
    dettes: null,
    creances: null,
    entreprises: null,
    stagiare: null,
    loading: null,
    setEmployes: () => {},
    setFinances: () => {},
    setDettes: () => {},
    setCreances: () => {},
    setEntreprises: () => {},
    setStagiare: () => {},
    setLoading: () => {},
    getEmployes: () => {},
    getFinances: () => {},
    getDettes: () => {},
    getCreances: () => {},
    getEntreprises: () => {},
    getStagiares: () => {},
})


export const FormsContext = ({children}) => {

    const [loading, setLoading] = useState(true);
    const [employes, setEmployes] = useState();
    const [stagiare, setStagiare] = useState();
    const [finances, setFinances] = useState({})
    const [dettes, setDettes] = useState({})
    const [creances, setCreances] = useState({})
    const [entreprises, setEntreprises] = useState()


    const getEmployes = () => {
        setLoading(true)
        axiosClient.get(`/employes`)
        .then(({ data }) => {
          setEmployes(data.data)
          setLoading(false)
        })
        .catch((err) => {
          setLoading(false)
        })
    }

    const getStagiares = () => {
      setLoading(true)
      axiosClient.get(`/stagiares`)
      .then(({ data }) => {
        setStagiare(data.data)
        setLoading(false)
      })
      .catch((err) => {
        setLoading(false)
      })
  }


    
    const getFinances = () => {
        setLoading(true)
        axiosClient.get(`/finances`)
        .then(({ data }) => {
          setFinances(data.data)
          setLoading(false)
        })
        .catch((err) => {
          console.log(err)
          setLoading(false)
        })
    }

    const getDettes = () => {
        setLoading(true)
        axiosClient.get(`/dettes`)
        .then(({ data }) => {
          setDettes(data.data)
          setLoading(false)
        })
        .catch((err) => {
          console.log(err)
          setLoading(false)
        })
    }

    const getCreances = () => {
      setLoading(true)
      axiosClient.get(`/creances`)
      .then(({ data }) => {
        setCreances(data.data)
        setLoading(false)
      })
      .catch((err) => {
        console.log(err)
        setLoading(false)
      })      
    }

    const getEntreprises = () => {
        axiosClient.get('/entreprise').then(({data}) => {
          setEntreprises(data.entreprise)
        }).catch((err) => {
          const response = err.response
          setError('server', {
            message: response.data.errors
          }) 
        })
    }

    return (
        <StateContext.Provider value={{
            employes,
            finances,
            dettes,
            creances,
            entreprises,
            stagiare,
            loading,
            setEmployes,
            setFinances,
            setDettes,
            setEntreprises,
            setStagiare,
            setLoading,
            getEmployes,
            getFinances,
            getDettes,
            getCreances,
            getEntreprises,
            getStagiares
        }}>
            {children}
        </StateContext.Provider>
    )

}

export const useFormsContext = () => useContext(StateContext);
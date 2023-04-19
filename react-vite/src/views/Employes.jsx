import React, { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import axiosClient from '../axios-client'
import { useStateContext } from '../contexts/ContextProvider'
import Table from './MUI/Table';



export default function Employe() {
  
  const [filiale, setFiliale] = useState();
  const [role, setRole] = useState(null);
  const [employes, setEmployes] = useState();
  const [loading, setLoading] = useState(true);
  const { setNotification, user, setUser } = useStateContext()

  const columns = [
    { field: "id", headerName: "ID", flex: 0.5 },
    { field: "numero_securite_social", headerName: "NSS", flex: 0.5 },
    { field: "filiale_id", headerName: "Filiale", type: "number", flex: 0.5},  
    { field: "nom", headerName: "Name", flex: 1, cellClassName: "name-column--cell"},
    { field: "fonction", headerName: "Fonction", flex: 1},
    { field: "date_recrutement", headerName: "Date Recrutement", flex: 1},
    { field: "date_naissance", headerName: "Date Naissance", flex: 1},
    { field: "contract", headerName: "contract", flex: 0.5},
    { field: "handicape", headerName: "handicape", flex: 0.5},
    { field: "sexe", headerName: "sexe", flex: 1},
  ]



  const getEmploye = () => {
    setLoading(true)
    axiosClient.get(`/employes`)
    .then(({ data }) => {
      setLoading(false)
      setEmployes(data.data)
      console.log(data)
    })
    .catch((err) => {
      console.log(err)
      setLoading(false)
    })
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


  useEffect(() => {
    getEmploye()
    fetchUser()
  }, [])


  console.log(employes)
  return (
    <div>
      <div style={{ display: 'flex',
       justifyContent: 'space-between', alignItems: 'center'}}>
        <h1>Employes {filiale?.name}</h1>
        {(role && role !== "basic" && !!employes) && 
        <Link to="/employes/new" className='btn-add'> Add new </Link>}
      </div>
      <Table postData={(!loading && !!employes) && employes} postColumns={columns}/>
    </div>
  )
}

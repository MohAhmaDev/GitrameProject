import React, { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import axiosClient from '../axios-client'
import { useStateContext } from '../contexts/ContextProvider'
import Table from './MUI/Table';



export default function Employe() {
  
  const [role, setRole] = useState(null);
  const [employes, setEmployes] = useState();
  const [loading, setLoading] = useState(true);
  const { setNotification, user, setUser } = useStateContext()

  const columns = [
    { field: "id", headerName: "ID", flex: 0.5 },
    { field: "nom", headerName: "Name", flex: 1, cellClassName: "name-column--cell"},
    { field: "date_recrutement", headerName: "Date Recrutement", flex: 1},
    { field: "date_naissance", headerName: "Date Naissance", flex: 1},
    { field: "contract", headerName: "contract", flex: 1},
    { field: "handicape", headerName: "handicape", flex: 1},
    { field: "sexe", headerName: "sexe", flex: 1},
  ]


  useEffect(() => {
    getEmploye()
  }, [])
  
  
  const getEmploye = () => {
    setLoading(true)
    axiosClient.get('/employes')
    .then(({ data }) => {
      setLoading(false)
      setEmployes(data.data)
    })
    .catch((err) => {
      // console.log(err)
      setLoading(false)
    })
  }

  // console.log(employes)
  useEffect(() => {
    axiosClient.get('/user')
    .then(({data}) => {
        setUser(data.user)
        setRole(data.role);
    })
  }, [])


  return (
    <div>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center'}}>
        <h1>Employe</h1>
        {(role && role !== "basic") && <Link to="/employes/new" className='btn-add'> Add new </Link>}
      </div>
      {/* <div className="card animated fadeInDown">
        <table>
          <thead>
          <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Prenom</th>
            <th>Fonction</th>
            <th>Actions</th>
          </tr>
          </thead>
          {loading &&
            <tbody>
            <tr>
              <td colSpan="5" class="text-center">
                Loading...
              </td>
            </tr>
            </tbody>
          }
          {!loading && 
            <tbody>
            {employes.map(e => (
              <tr key={e.id}>
                <td>{e.id}</td>
                <td>{e.nom}</td>
                <td>{e.prenom}</td>
                <td>{e.fonction}</td>
                <td>
                  <Link className="btn-edit" to={'/employes/' + e.id}>Edit</Link>
                  &nbsp;
                  <button onClick={ev => onDelete(e)} className="btn-delete"> Delete </button>
                </td>
              </tr>
            ))}
            </tbody>
          }
        </table>
      </div> */}
      <Table postData={!loading && employes} postColumns={columns}/>
    </div>
  )
}

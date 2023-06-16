import React, { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import axiosClient from '../axios-client'
import { useStateContext } from '../contexts/ContextProvider'
import Table from './MUI/Table';
import { Button, IconButton } from '@mui/material';
import EditOutlinedIcon from '@mui/icons-material/EditOutlined';
import { useFormsContext } from '../contexts/FormsContext';



export default function Employe() {
  

  const [check, setCheck] = useState(false);
  const [poster, setPoster] = useState(false)
  const [employeTD, setEmployeID] = useState();
  const { setNotification, role, filiale,  fetchUser } = useStateContext()
  const { loading, setLoading, employes, getEmployes } = useFormsContext()

  const handleEditRow = (e) => {
    if (employeTD === e.id) {
      setCheck(c => !c);
    } else {
      setEmployeID(e?.id)
      setCheck(true);
    }
  }

  const onDelete = () => {
    if (!window.confirm(`Are you sure you want to delete the employe`))
    {
      return;
    }
    axiosClient.delete(`/employes/${employeTD}`)
    .then(() => {
      setNotification('employe deleted successfully');
      setCheck(false);
      getEmployes()
    })
  }
  const edit = role !== "editor" ? null : {
    field: "edit",
    headerName: "Edit",
    flex: 0.5,
    renderCell: (params) => (
      <IconButton onClick={() => handleEditRow(params.row)}>
        <EditOutlinedIcon />
      </IconButton>
    ),
  } 
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

  if (edit !== null) {
    columns.push(edit);
  }


  useEffect(() => {
    setLoading(true)
    getEmployes()
    fetchUser()
    setLoading(false)
    setPoster(true)
  }, [])


  return (
    <div>
      <div style={{ display: 'flex',
       justifyContent: 'space-between', alignItems: 'center'}}>
        <h1>Employes {filiale?.name}</h1>
        <div>
          {check && <button className='btn-delete' onClick={ev => onDelete()}> delete </button>}
          {check && <Link style={{ marginLeft: "10px" }} to={`/employes/${employeTD}`} className='btn-edit'> Edit </Link>}


          {(role === "editor" && !!employes) && 
          <Link style={{ marginLeft: "10px" }} to="/employes/new" className='btn-add'> Add new </Link>}
        </div>
      </div>
      {
      ((!loading && poster && !!employes) && (Object.keys(employes).length !== 0)) ?
       <Table postData={employes} postColumns={columns}/>:
        <div class="cards-grid"> 
          <div class="card-box">
            <div class="top-img skeleton-anim"></div>
            <div class="content-box">
              <p class="skeleton-text skeleton-anim"></p>
              <p class="skeleton-text skeleton-anim"></p>
              <p class="skeleton-text skeleton-anim"></p>
              <p class="skeleton-text skeleton-anim"></p>
              <p class="skeleton-text skeleton-anim"></p>
              <p class="skeleton-text skeleton-anim"></p>
              <p class="skeleton-text skeleton-anim"></p>
              <p class="skeleton-text skeleton-anim"></p>
              <p class="skeleton-text skeleton-anim"></p>
            </div>
          </div>
        </div>
      }
    </div>
  )
}

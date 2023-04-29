import React, { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import axiosClient from '../axios-client'
import { useStateContext } from '../contexts/ContextProvider'
import Table from './MUI/Table';
import { Button, IconButton } from '@mui/material';
import EditOutlinedIcon from '@mui/icons-material/EditOutlined';
import { useFormsContext } from '../contexts/FormsContext';



export default function Stagiares() {
  

  const [check, setCheck] = useState(false);
  const [poster, setPoster] = useState(false)
  const [stagaireID, setStagiareID] = useState();
  const { setNotification, role, filiale,  fetchUser } = useStateContext()
  const { loading, setLoading, stagiare, getStagiares } = useFormsContext()

  const handleEditRow = (e) => {
    if (stagaireID === e.id) {
      setCheck(c => !c);
    } else {
      setStagiareID(e?.id)
      setCheck(true);
    }
  }

  const onDelete = () => {
    if (!window.confirm(`Are you sure you want to delete this Stagiares`))
    {
      return;
    }
    axiosClient.delete(`/stagiares/${stagaireID}`)
    .then(() => {
      setNotification('employe deleted successfully');
      setCheck(false);
      getStagiares()
    })
  }
  
  const columns = [
    { field: "id", headerName: "ID", flex: 0.5 },
    { field: "numero_securite_social", headerName: "NSS", flex: 0.5 },
    { field: "filiale_id", headerName: "Filiale", type: "number", flex: 0.5},  
    { field: "nom", headerName: "Name", flex: 0.5, cellClassName: "name-column--cell"},
    { field: "domaine_formation", headerName: "Domaine Formation", flex: 0.5},
    { field: "date_naissance", headerName: "Date Naissance", flex: 0.5},
    { field: "intitule_formation", headerName: "intitule_formation", flex: 0.5},
    { field: "lieu_formation", headerName: "Lieux de Formationp", flex: 0.5},
    { field: "montant", headerName: "montant", flex: 0.5},
    {
      field: "edit",
      headerName: "Edit",
      flex: 0.5,
      renderCell: (params) => (
        <IconButton onClick={() => handleEditRow(params.row)}>
          <EditOutlinedIcon />
        </IconButton>
      ),
    },
  ]


  useEffect(() => {
    setLoading(true)
    getStagiares()
    fetchUser()
    setLoading(false)
    setPoster(true)
  }, [])


  return (
    <div>
      <div style={{ display: 'flex',
       justifyContent: 'space-between', alignItems: 'center'}}>
        <h1>Stagiares {filiale?.name}</h1>
        <div>
          {check && <button className='btn-delete' onClick={ev => onDelete()}> delete </button>}
          {check && <Link style={{ marginLeft: "10px" }} to={`/stagiares/${stagaireID}`} className='btn-edit'> Edit </Link>}


          {(role && role !== "basic" && !!stagiare) && 
          <Link style={{ marginLeft: "10px" }} to="/stagiares/new" className='btn-add'> Add new </Link>}
        </div>
      </div>
      {
      ((!loading && poster && !!stagiare) && (Object.keys(stagiare).length !== 0)) ?
       <Table postData={stagiare} postColumns={columns}/>:
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

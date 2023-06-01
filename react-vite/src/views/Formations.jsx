import React, { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import axiosClient from '../axios-client'
import { useStateContext } from '../contexts/ContextProvider'
import Table from './MUI/Table';
import { Button, IconButton } from '@mui/material';
import EditOutlinedIcon from '@mui/icons-material/EditOutlined';
import { useFormsContext } from '../contexts/FormsContext';



const Formations = () => {
    const [check, setCheck] = useState(false);
    const [poster, setPoster] = useState(false)
    const [formationID, setFormationID] = useState();
    const { setNotification, role, filiale,  fetchUser } = useStateContext()
    const { loading, setLoading, formations, getFormations } = useFormsContext()
  
    const handleEditRow = (e) => {
      if (formationID === e.id) {
        setCheck(c => !c);
      } else {
        setFormationID(e?.id)
        setCheck(true);
      }
    }
  
    const onDelete = () => {
      if (!window.confirm(`Are you sure you want to delete this Stagiares`))
      {
        return;
      }
      axiosClient.delete(`/formations/${formationID}`)
      .then(() => {
        setNotification('formation deleted successfully');
        setCheck(false);
        getFormations()
      })
    }
    
    const columns = [
      { field: "id", headerName: "ID", flex: 0.5 },
      { field: "employe", headerName: "Name", flex: 0.5},  
      { field: "domaine_formation", headerName: "Domaine Formation", flex: 0.5},
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
      getFormations()
      fetchUser()
      setLoading(false)
      setPoster(true)
    }, [])
  
    console.log("formations: ", formations)
  
    return (
      <div>
        <div style={{ display: 'flex',
         justifyContent: 'space-between', alignItems: 'center'}}>
          <h1>Formations-Employe {filiale?.name}</h1>
          <div>
            {check && <button className='btn-delete' onClick={ev => onDelete()}> delete </button>}
            {check && <Link style={{ marginLeft: "10px" }} to={`/formations/${formationID}`} className='btn-edit'> Edit </Link>}
  
            {(role && role !== "basic" && !!formations) && 
            <Link style={{ marginLeft: "10px" }} to="/formations/new" className='btn-add'> Add new </Link>}
          </div>
        </div>
        {
        ((!loading && poster && !!formations) && (Object.keys(formations).length !== 0)) ?
         <Table postData={formations} postColumns={columns}/>:
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
};

export default Formations;
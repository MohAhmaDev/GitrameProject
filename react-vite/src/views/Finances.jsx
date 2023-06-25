import React, { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import axiosClient from '../axios-client'
import { useStateContext } from '../contexts/ContextProvider'
import Table from './MUI/Table';
import { useFormsContext } from '../contexts/FormsContext';
import Dettes from './Dettes';
import { IconButton } from '@mui/material';
import EditOutlinedIcon from '@mui/icons-material/EditOutlined';


const Finances = () => {

    const [check, setCheck] = useState(false);
    const [financeID, setFinanceID] = useState()
    const [poster, setPoster] = useState(false)
    const { setNotification, role, filiale,  fetchUser } = useStateContext()
    const { loading, setLoading, getFinances, finances } = useFormsContext()

    const handleEditRow = (e) => {
        if (financeID === e.id) {
          setCheck(c => !c);
        } else {
          setFinanceID(e?.id)
          setCheck(true);
        }
    }

    const onDelete = () => {
        if (!window.confirm(`Are you sure you want to delete the employe`))
        {
          return;
        }
        axiosClient.delete(`/finances/${financeID}`)
        .then(() => {
          setNotification('finances deleted successfully');
          setCheck(false);
          getFinances();
        })
      }

    const edit = role !== "editor" ? null : {
      field: "edit",
      headerName: "Edit",
      flex: 0.25,
      renderCell: (params) => (
        <IconButton onClick={() => handleEditRow(params.row)}>
          <EditOutlinedIcon />
        </IconButton>
      ),
    }
    const columns = [
        { field: "id", headerName: "ID", flex: 0.25 },
        { field: "type_activite", headerName: "Type d'activité", flex: 0.5 },
        { field: "activite", headerName: "Activité", flex: 1},
        { field: "realisation", headerName: "Realisation", flex: 0.5},
        { field: "filiale_id", headerName: "Filiale", flex: 0.25},
        { field: "date_activite", headerName: "Date Activité", flex: 0.5},
        { field: "privision", headerName: "privision", flex: 0.5},
        { field: "compte_scf", headerName: "Compte Scf", flex: 0.5},
    ]

    if (edit !== null) {
      columns.push(edit);
    }
   
    
    useEffect(() => {
      setLoading(true)
      getFinances()
      fetchUser()
      setLoading(false)
      setPoster(true)
    }, [])



    console.log(finances)
    return (
        <div>
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center'}}>
                <h1>finances </h1>
                <div>
                    {check && <button className='btn-delete' onClick={ev => onDelete()}> delete </button>}
                    {check && <Link style={{ marginLeft: "10px" }} to={`/finances/${financeID}`} className='btn-edit'> Edit </Link>}

                    {(role === "editor" && !!finances) && 
                    <Link style={{ marginLeft: "10px" }} to="/finances/new" className='btn-add'> Add new </Link>}
                </div>
            </div>
            {
                ((!loading && poster && !!finances) && (Object.keys(finances).length !== 0)) ?
                <Table postData={finances} postColumns={columns}/>:
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
    );
};

export default Finances;
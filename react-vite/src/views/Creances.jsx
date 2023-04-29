import React, { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import axiosClient from '../axios-client'
import { useStateContext } from '../contexts/ContextProvider'
import Table from './MUI/Table';
import { useFormsContext } from '../contexts/FormsContext';
import { IconButton } from '@mui/material';
import EditOutlinedIcon from '@mui/icons-material/EditOutlined';


const Creances = () => {

    const [check, setCheck] = useState(false);
    const [poster, setPoster] = useState(false)
    const [CreanceID, setCreance] = useState()
    const { setNotification, role, filiale,  fetchUser } = useStateContext()
    const { creances, getCreances, loading, setLoading } = useFormsContext()



    const handleEditRow = (e) => {
        if (CreanceID === e.id) {
          setCheck(c => !c);
        } else {
          setCreance(e?.id)
          setCheck(true);
        }
    }


    const onDelete = () => {
        if (!window.confirm(`Are you sure you want to delete this creances`))
        {
          return;
        }
        axiosClient.delete(`/creances/${CreanceID}`)
        .then(() => {
          setNotification('creances deleted successfully');
          setCheck(false)
          getCreances()
        })
      }

    const columns = [
        { field: "id", headerName: "ID", flex: 0.5 },
        { field: "intitule_projet", headerName: "Intitule Projet", flex: 0.5 },
        { field: "num_fact", headerName: "Numero Facture", flex: 1},
        { field: "num_situation", headerName: "Numéro situation", flex: 1},
        { field: "date_creance", headerName: "Date", flex: 1},
        { field: "anteriorite_creance", headerName: "Date", flex: 1},
        { field: "montant", headerName: "montant", flex: 0.5},
        { field: "creditor", headerName: "créditeur", flex: 0.5},
        { field: "debtor", headerName: "débiteur", flex: 1},
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
      getCreances()
      fetchUser()
      setLoading(false)
      setPoster(true)
    }, [])


    console.log(creances)
    return (
        <div>
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center'}}>
                <h1>creances </h1>
                <div>
                    {check && <button className='btn-delete' onClick={ev => onDelete()}> delete </button>}
                    {check && <Link style={{ marginLeft: "10px" }} to={`/creances/${CreanceID}`} className='btn-edit'> Edit </Link>}

                    {(role && role !== "basic") && 
                    <Link style={{ marginLeft: "10px" }} to="/creances/new" className='btn-add'> Add new </Link>}
                </div>
            </div>
            {
                ((!loading && poster && !!creances) && (Object.keys(creances).length !== 0)) ?
                <Table postData={creances} postColumns={columns}/>:
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

export default Creances;
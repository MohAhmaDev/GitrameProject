import React, { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import axiosClient from '../axios-client'
import { useStateContext } from '../contexts/ContextProvider'
import Table from './MUI/Table';
import { useFormsContext } from '../contexts/FormsContext';
import { IconButton } from '@mui/material';
import EditOutlinedIcon from '@mui/icons-material/EditOutlined';


const Dettes = () => {

    const [check, setCheck] = useState(false);
    const [poster, setPoster] = useState(false)
    const [DetteID, setDetteID] = useState()
    const { setNotification, role, filiale,  fetchUser } = useStateContext()
    const { dettes, getDettes, loading, setLoading } = useFormsContext()



    const handleEditRow = (e) => {
        if (DetteID === e.id) {
          setCheck(c => !c);
        } else {
          setDetteID(e?.id)
          setCheck(true);
        }
    }


    const onDelete = () => {
        if (!window.confirm(`Are you sure you want to delete this dette`))
        {
          return;
        }
        axiosClient.delete(`/dettes/${DetteID}`)
        .then(() => {
          setNotification('dette deleted successfully');
          setCheck(false)
          getDettes()
        })
      }

    const columns = [
        { field: "id", headerName: "ID", flex: 0.5 },
        { field: "intitule_projet", headerName: "Intitule Projet", flex: 0.5 },
        { field: "num_fact", headerName: "Numero Facture", flex: 1},
        { field: "num_situation", headerName: "Numéro situation", flex: 1},
        { field: "date_dettes", headerName: "Date", flex: 1},
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
      getDettes()
      fetchUser()
      setLoading(false)
      setPoster(true)
    }, [])


    console.log(dettes)
    return (
        <div>
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center'}}>
                <h1>Dette </h1>
                <div>
                    {check && <button className='btn-delete' onClick={ev => onDelete()}> delete </button>}
                    {check && <Link style={{ marginLeft: "10px" }} to={`/dettes/${DetteID}`} className='btn-edit'> Edit </Link>}

                    {(role && role !== "basic") && 
                    <Link style={{ marginLeft: "10px" }} to="/dettes/new" className='btn-add'> Add new </Link>}
                </div>
            </div>
            {
                ((!loading && poster && !!dettes) && (Object.keys(dettes).length !== 0)) ?
                <Table postData={dettes} postColumns={columns}/>:
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

export default Dettes;
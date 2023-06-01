import React, { useEffect, useState } from 'react';
import DenseTable from '../MUI/DenseTable';
import { Box, TextField, Button, CircularProgress,
FormControl, Select, InputLabel, MenuItem} from '@mui/material';
import axiosClient from '../../axios-client';
import { useForm, Controller } from "react-hook-form";
import { useDisplayContext } from '../../contexts/DisplayContext';


const Dashboard02 = () => {

    const [data, setData] = useState({
        filiale: "",
        date: ""
    });
    const {filiales, getFiliales, setFiliales} = useDisplayContext()
    const [dash, setDash] = useState({});
    const [rows, setRows] = useState({});
    const [dates, setDates] = useState({});

    const getFinanceDashboard = (req) => {
        axiosClient.post('/finance_dashboard', req).then(({data}) => {
            setDash(data);
        }).catch((err) => {
            console.log(err)
        })
    }

    const getDates = () => {
        axiosClient.get('/finance_years').then(({data}) => {
            setDates(data)
        }).catch((err) => {
            console.log(err)
        })
    }

    useEffect(() => {
        getDates()
        getFiliales();
        getFinanceDashboard(data);
        console.log('load', data)
    }, [data])


    function createData(calcule_Agregats, Montant_Realisation, Montant_Privision, Ecart_Valeur, taux_Realisation) {
        return { calcule_Agregats, Montant_Realisation, Montant_Privision, Ecart_Valeur, taux_Realisation };
    }

    useEffect(() => {
        if (Object.keys(dash).length !== 0) {
            const rows = dash.map(resultat => {
                const Agregat_calculer = resultat.Agregat_calculer;
                const Montant_Realisation =  (resultat.Montant_Realisation);
                const Montant_Privision = (resultat.Montant_Privision);
                const Ecart_Valeur = (resultat.Ecart_Valeur);
                const taux_Realisation = (resultat.taux);
              
                return createData(Agregat_calculer, Montant_Realisation, Montant_Privision, Ecart_Valeur, taux_Realisation);
            });
            console.log("rows", rows) 
            setRows(rows)         
        }
    }, [dash])

    const { handleSubmit, control, 
        register, getValues, watch , setValue, reset, formState, setError, formState: { errors } } = useForm({
        mode: "onChange"
    });

    const onSubmit = () => {
        setData({
            filiale: "",
            date: ""
        })
    }

    return (
        <>
            <h1> Dashboard Finance </h1>
            <div className="card animated fadeInDown">
                <h2 style={{ gridColumn: "span 6" }}> Filtres </h2>
                <form onSubmit={handleSubmit(onSubmit)}>
                <Box m="25px" display="grid" gridTemplateColumns="repeat(6, minmax(0, 1fr))" gap="30px" > 
                    <label style={{ gridColumn: "span 1" }}> Finance : (filitre filiale) </label>
                    <Controller
                        control={control}
                        name="filiale"
                        render={({ field: { onChange } }) => (
                            <FormControl variant="outlined" sx={{ width: "300px" }}>
                            <InputLabel id="demo-simple-select-label"> filiale  </InputLabel>
                            <Select
                                sx={{ gridColumn: "span 2" }}
                                label="filiale"
                                {...register("filiale")}
                                value={data.filiale}
                                onChange={(ev) => setData({...data, filiale: ev.target.value})}
                            >
                                {(Object.keys(filiales).length !== 0) && filiales?.map(filiale => (
                                <MenuItem value={filiale?.id} key={filiale?.id}> {filiale?.name} </MenuItem>
                                ))}
                            </Select>
                            </FormControl>
                        )}
                    /> 
                </Box> 
                <Box m="25px" display="grid" gridTemplateColumns="repeat(6, minmax(0, 1fr))" gap="30px" > 
                    <label style={{ gridColumn: "span 1" }}> Finance : (filitre Annee) </label>
                    <Controller
                        control={control}
                        name="date"
                        render={({ field: { onChange } }) => (
                            <FormControl variant="outlined" sx={{ width: "300px" }}>
                            <InputLabel> annee  </InputLabel>
                            <Select
                                sx={{ gridColumn: "span 2" }}
                                label="annee"
                                {...register("date")}
                                value={data.date}
                                onChange={(ev) => setData({...data, date: ev.target.value})}
                            >
                                {(Object.keys(dates).length !== 0) && dates?.map(date => (
                                <MenuItem value={date?.year} > {date?.year} </MenuItem>
                                ))}
                            </Select>
                            </FormControl>
                        )}
                    /> 
                </Box> 

                
                {(rows && Object.keys(rows).length !== 0) ? <DenseTable data={rows}/> : <CircularProgress disableShrink />}
                <Box display="flex" justifyContent="end" mt="20px" m="10px">
                    <Button type="submit" color="primary" variant="contained">
                        Renaitialiser
                    </Button>
                </Box>                 
                </form>
            </div>
        </>
    );
};

export default Dashboard02;
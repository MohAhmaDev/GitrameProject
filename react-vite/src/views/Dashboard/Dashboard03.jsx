import React, { useEffect, useState } from 'react';
import axiosClient from '../../axios-client';
import { useDisplayContext } from '../../contexts/DisplayContext';
import { Box,
    Button,
    IconButton,
    Typography,
    CircularProgress,
    colors,
    FormControlLabel,
    FormGroup,
    Checkbox,
    FormControl,
    InputLabel,
    Select, 
    MenuItem
} from '@mui/material';
import { useForm, Controller } from "react-hook-form";


const Dashboard03 = () => {

    const [dates, setDates] = useState({});
    const [Form, setForm] = useState({
        filiale: "",
        date: ""
    });
    const [checked, setChecked] = useState({
        creances: true,
        dettess: false,
        creances_vs_dettes: false
    });
    const [data, setData] = useState({});
    const { getFiliales, setFiliales, filiales } = useDisplayContext()
    

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

    const getData = () => {
        axiosClient.post('/dash_creance_dettes').then(({data}) => {
            setData(data)
            }).catch((error) => {
            console.log(error)
        })
    }

    const getDates = () => {
        axiosClient.get('/date_fcreance_dettes').then(({data}) => {
            setDates(data)
        }).catch((err) => {
            console.log(err)
        })
    }

    const getTable = () => {
        const rows = [];
        for (let index = 1; index < 19; index++) {
            const cells = [];
            cells.push(<th key={`header-${index}`} className="F_">F-{index}</th>);
            for (let i = 1; i < 19; i++) {
                const field = data.find(item => item?.ID_Ent_A === index && item?.ID_Ent_B === i);
                const montantCreances = field ? field.Montant_Creances : 0;
                const montantDettes = field ? field.Montant_Dettes : 0;
                const CreancesVSDettes = field ? field.Creances_vs_Dettes : 0;

                cells.push(
                <td key={`cell-${index}-${i}`} className="F_">
                    <ul style={{ listStyle: "none", margin: "0px", padding: "0px" }}>
                        {checked.creances && <li> {montantCreances} </li>}
                        {checked.dettess && <li> {montantDettes} </li>}
                        {checked.creances_vs_dettes && <li> {CreancesVSDettes} </li>}
                    </ul>
                </td>);
            }
            rows.push(<tr key={`row-${index}`}>{cells}</tr>);
        }
        return rows;
    };
    
    useEffect(() => {
        getData();
        getFiliales();
        getDates();
    }, []);
    
    return (
        <div style={{ background: "#fff", padding: "20px", borderRadius: "5px" }}>
            {/* {Object.keys(data).length !== 0 && data?.map(field => (
                field?.ID_Ent_A === 1 && <span key={field?.ID_Ent_B}>[{field?.ID_Ent_B}, {field?.Montant_Creances}, {field?.Montant_Dettes}]</span>
            ))} */}
            <Box padding={"20px"}                 
                display="grid"
                gridTemplateColumns="repeat(12, 1fr)"
                gap="20px"
            >
                <Box
                    padding={"10px"}
                    gridColumn="span 6" 
                >
                    <h2> Filtrer les resultats </h2>
                    <FormGroup>
                        <FormControlLabel control={<Checkbox checked={checked.creances} 
                        onClick={ev => { setChecked({...checked, creances: !checked.creances}) }}/>} label="creances" />
                        <FormControlLabel control={<Checkbox checked={checked.dettess}  
                        onClick={ev => { setChecked({...checked, dettess: !checked.dettess}) }}/>} label="dettes" />
                        <FormControlLabel control={<Checkbox checked={checked.creances_vs_dettes}  
                        onClick={ev => { setChecked({...checked, creances_vs_dettes: !checked.creances_vs_dettes}) }}/>} 
                        label="creances_vs_dettes" />
                    </FormGroup>                    
                </Box>
                <Box>
                    <Box m="25px"> 
                        <Controller
                            control={control}
                            name="filiale"
                            render={({ field: { onChange } }) => (
                                <FormControl variant="outlined" sx={{ width: "300px" }}>
                                <InputLabel id="demo-simple-select-label"> Filtre filiale  </InputLabel>
                                <Select
                                    sx={{ gridColumn: "span 2" }}
                                    label="Filtre filiale"
                                    {...register("filiale")}
                                    value={data.filiale}
                                    onChange={(ev) => setForm({...Form, filiale: ev.target.value})}
                                >
                                    {(Object.keys(filiales).length !== 0) && filiales?.map(filiale => (
                                    <MenuItem value={filiale?.id} key={filiale?.id}> {filiale?.name} </MenuItem>
                                    ))}
                                </Select>
                                </FormControl>
                            )}
                        /> 
                    </Box> 
                    <Box m="25px"> 
                        <Controller
                            control={control}
                            name="date"
                            render={({ field: { onChange } }) => (
                                <FormControl variant="outlined" sx={{ width: "300px" }}>
                                <InputLabel> Filtre Mois  </InputLabel>
                                <Select
                                    sx={{ gridColumn: "span 2" }}
                                    label="Filtre Mois"
                                    {...register("date")}
                                    value={data.date}
                                    onChange={(ev) => setForm({...Form, date: ev.target.value})}
                                >
                                    {(Object.keys(dates).length !== 0) && dates?.map(date => (
                                    <MenuItem value={date?.id} > {date?.date_mois} </MenuItem>
                                    ))}
                                </Select>
                                </FormControl>
                            )}
                        /> 
                    </Box>
                </Box>
                
            </Box>
            {Object.keys(data).length !== 0 &&
            <table className="ftable" id="ftable">
                <thead>
                    <tr>
                        <th className="F_"></th>
                        {[...Array(18)].map((_, i) => (
                            <th key={`header-${i}`} className="F_">F_{i + 1}</th>
                        ))}
                    </tr>
                </thead>
                <tbody>
                    {getTable()}
                </tbody>
            </table>
            }  
        </div>
    );
    
    
};

export default Dashboard03;
 
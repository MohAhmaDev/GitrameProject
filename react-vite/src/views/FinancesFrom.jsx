import React, { useState, useEffect } from "react";
import { useNavigate, useParams } from "react-router-dom";
import axiosClient from "../axios-client";


import { 
  TextField,
  Zoom,
  Box, 
  FormControlLabel,
  FormControl, 
  InputLabel, 
  Select, 
  MenuItem, 
  useMediaQuery,
  FormLabel,
  Button,
  CircularProgress
} from '@mui/material';
import { AdapterDayjs } from '@mui/x-date-pickers/AdapterDayjs';
import { LocalizationProvider } from '@mui/x-date-pickers/LocalizationProvider';
import { DatePicker } from '@mui/x-date-pickers/DatePicker';
import dayjs from "dayjs";
import { useForm, Controller } from "react-hook-form";


import { yupResolver } from '@hookform/resolvers/yup';
import * as yup from "yup";
import { useDisplayContext } from "../contexts/DisplayContext";
import { useStateContext } from "../contexts/ContextProvider";

const FinancesFrom = () => {

    const {id} = useParams();
    const [finance, setFinance] = useState({
        activite: "",
        type_activite: "",
        date_activite: undefined,
        privision: null,
        realisation: null,
        compte_scf: "",
        filiale_id: null
    })
    const {filiales, getFiliales, setFiliales} = useDisplayContext()
    const {setNotification, filiale, role, user, fetchUser} = useStateContext()
    const navigate = useNavigate();
    const [loading, setLoading] = useState(false);




    const userScheme = yup.object({
        "activite": yup.string().required(),
        "type_activite": yup.string().required(),
        "date_activite": yup.date().required(),
        "privision": yup.number().required(),
        "realisation": yup.number().required(),
        "compte_scf": yup.string().required(),
    })

    const { handleSubmit, control, 
        register, getValues, watch , setValue, reset, formState, setError, formState: { errors } } = useForm({
            mode: "onChange",
            resolver: yupResolver(userScheme)
        });
    const {isSubmitting, isValid, isSubmitted, isSubmitSuccessful} = formState    


    useEffect(() => {
        getFiliales()
        fetchUser()
    }, [])
    


    const onSubmit = (data) => {
        if (finance.id) 
        {
          const updateFinance = {
            activite: data.activite,
            type_activite: data.type_activite,
            date_activite: dayjs(data.date_activite).format("YYYY-MM-DD"),
            compte_scf: data.compte_scf,
            privision: data.privision,
            realisation: data.realisation,
            filiale_id: filiale.id ? filiale.id : data.filiale_id,
          }
          axiosClient.put(`/finances/${finance.id}`, updateFinance)
          .then(() => {
            //TODO show notification
            setNotification("finance was successfuly Update")
            navigate('/finances')
          })
          .catch(err => {
            const response = err.response;
            if (response && response.status === 422) {
              setError('server', {
                message: response?.data.errors
              })        
            }
          })
        } 
        else 
        {
          const Add_finance = {
            activite: data.activite,
            type_activite: data.type_activite,
            date_activite: dayjs(data.date_activite).format("YYYY-MM-DD"),
            compte_scf: data.compte_scf,
            privision: data.privision,
            realisation: data.realisation,
            filiale_id: filiale.id ? filiale.id : data.filiale_id,
          }
          axiosClient.post('/finances', Add_finance)
          .then(() => {
            setNotification("la finance a bien été saisite")
            navigate('/finances')
          })
          .catch((err) => {
            console.log(err)
            const response = err.response;
            if (response && response.status === 422) {
              setError('server', {
                message: response.response.data.errors
              })       
            }
          })
        }
    }

    const getFinance = () => {
        setLoading(true)
        axiosClient.get(`finances/${id}`)
        .then(({data}) => {
          setFinance({
            id: id,
            activite: data.data.activite,
            type_activite: data.data.type_activite,
            date_activite: dayjs(data.data.date_activite),
            compte_scf: data.data.compte_scf,
            privision: data.data.privision,
            realisation: data.data.realisation,
            filiale_id: data.data.filiale_id,            
          })
          reset({...data.data})
          setLoading(false)
        })
        .catch((err) => {
          console.log(err)
        })
    }
    
    if (id) {
        useEffect(() => {
            getFinance()
            console.log(finance)
        }, [])
    }


    return (
        <>
            {!loading && <div className="card animated fadeInDown">
                <Box m="20px">
                    <form onSubmit={handleSubmit(onSubmit)}>
                        <Box m="50px" display="grid" gridTemplateColumns="repeat(6, minmax(0, 1fr))" gap="30px" > 
                            <label style={{ gridColumn: "span 1" }}> Information sur le type agregat </label>
                            <FormControl 
                                variant="outlined" 
                                sx={{ gridColumn: "span 2"}}
                                >
                                <InputLabel> agregat type </InputLabel>
                                <Select
                                    label="type activite"
                                    {...register('type_activite')}
                                    onChange={ev => setFinance({...finance, type_activite: ev.target.value})}
                                    value={finance.type_activite}
                                    error={errors.type_activite ? true : false}
                                >
                                    <MenuItem value="consomation"> consomation </MenuItem>
                                    <MenuItem value="vente"> vente </MenuItem>
                                    <MenuItem value="autre"> autre </MenuItem>

                                </Select>
                                {errors.type_activite && <span style={{
                                        color: "#d32f2f",
                                        fontSize: "0.75em",
                                        textAlign: "left",
                                        fontWeight: "400"
                                    }}> {errors.type_activite?.message} </span>}
                            </FormControl>  
                        </Box>   
                        <Box m="50px" display="grid" gridTemplateColumns="repeat(6, minmax(0, 1fr))" gap="30px" > 
                            <label style={{ gridColumn: "span 1" }}> Finace : (ajouter l'Agregat) </label>
                            <TextField
                                label="Agregat"
                                variant="outlined"
                                {...register('activite')}
                                value={finance.activite}
                                onChange={ev => setFinance({...finance, activite: ev.target.value})}
                                error={errors.activite ? true : false}
                                helperText={errors.activite && errors.activite.message}
                                sx={{ gridColumn: "span 2" }}
                            />
                        </Box> 
                        <Box m="50px" display="grid" gridTemplateColumns="repeat(6, minmax(0, 1fr))" gap="30px" > 
                            <label style={{ gridColumn: "span 1" }}> Finance : (ajouter compte csf) </label>
                            <TextField
                                label="compte scf"
                                {...register('compte_scf')}
                                value={finance.compte_scf}
                                onChange={ev => setFinance({...finance, compte_scf: ev.target.value})}
                                error={errors.compte_scf ? true : false}
                                helperText={errors.compte_scf && errors.compte_scf.message}
                                variant="outlined"
                                sx={{ gridColumn: "span 2" }}
                            />
                        </Box> 
                        <Box m="50px" display="grid" gridTemplateColumns="repeat(6, minmax(0, 1fr))" gap="30px" > 
                            <label style={{ gridColumn: "span 1" }}> Finance : (ajouter prévision) </label>
                            <TextField
                                type="number"
                                label="privision"
                                variant="outlined"
                                {...register('privision')}
                                value={finance.privision}
                                onChange={ev => setFinance({...finance, privision: ev.target.value})}
                                error={errors.privision ? true : false}
                                helperText={errors.privision && errors.privision.message}
                                sx={{ gridColumn: "span 2" }}
                            />
                        </Box> 
                        <Box m="50px" display="grid" gridTemplateColumns="repeat(6, minmax(0, 1fr))" gap="30px" > 
                            <label style={{ gridColumn: "span 1" }}> Finance : (ajouter realisation) </label>
                            <TextField
                                type="number"
                                label="realisation"
                                variant="outlined"
                                {...register('realisation')}
                                value={finance.realisation}
                                onChange={ev => setFinance({...finance, realisation: ev.target.value})}
                                error={errors.realisation ? true : false}
                                helperText={errors.realisation && errors.realisation.message}
                                sx={{ gridColumn: "span 2" }}
                            />
                        </Box> 
                        <Box m="50px" display="grid" gridTemplateColumns="repeat(6, minmax(0, 1fr))" gap="30px" >
                            <label style={{ gridColumn: "span 1" }}> Finance : (ajouter date de début) </label>
                            <Controller
                                control={control}
                                name="date_activite"
                                render={({ field: { onChange } }) => (
                                <LocalizationProvider dateAdapter={AdapterDayjs}>
                                <DatePicker
                                    sx={{ gridColumn: "span 2" }}
                                    label="date de l'activite" 
                                    format="DD/MM/YYYY"
                                    value={finance?.date_activite}
                                    onChange={(event) => {
                                        onChange(event);
                                        setFinance({...finance, date_activite: event});
                                    }}
                                    slotProps={{
                                        textField: {
                                            error: errors.date_activite ? true : false,
                                            helperText: errors.date_activite?.message
                                        }
                                    }}
                                />
                                </LocalizationProvider>
                                )}
                            />
                        </Box> 
                        {(role === 'admin') && <Box m="50px" display="grid" gridTemplateColumns="repeat(6, minmax(0, 1fr))" gap="30px" >
                            <label style={{ gridColumn: "span 1" }}> Filiale </label>
                            
                            <Controller
                            control={control}
                            name="filiale_id"
                            render={({ field: { onChange } }) => (
                                <FormControl variant="outlined" sx={{ width: "300px" }}>
                                <InputLabel id="demo-simple-select-label"> filiale  </InputLabel>
                                <Select
                                    sx={{ gridColumn: "span 2" }}
                                    label="filiale_id"
                                    {...register("filiale_id")}
                                    value={finance.filiale_id}
                                    onChange={(ev) => setFinance({...finance, filiale_id: ev.target.value})}
                                >
                                    {(Object.keys(filiales).length !== 0) && filiales?.map(filiale => (
                                    <MenuItem value={filiale?.id} key={filiale?.id}> {filiale?.name} </MenuItem>
                                    ))}
                                </Select>
                                </FormControl>
                            )}
                            />   
                        </Box> }  
                        <Box display="flex" justifyContent="end" mt="20px">
                            <Button disabled={isSubmitting} type="submit" color="primary" variant="contained">
                            {id ? "Modifier" : "créer une nouvelle" } Fianace
                            </Button>
                        </Box> 
                        {errors?.server?.message &&
                        <div className="alert">
                            {Object.keys(errors?.server?.message).map(key => (
                            <p>{errors?.server?.message[key][0]}</p>
                            ))}
                        </div>
                        }
                    </form>
                </Box>
            </div>}
            {loading && <CircularProgress disableShrink /> }
        </>

    );
};

export default FinancesFrom;